<?php

namespace App\Console\Commands;

use App\Models\SpotPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UpdateSpotPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-spot-prices {date? : The date to fetch spot prices for (format: YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates spot prices for a given date';

    private $eliaUrl = 'https://griddata.elia.be/eliabecontrols.prod/interface/Interconnections/daily/auctionresults/';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $response = Http::get($this->eliaUrl.$this->argument('date'));

        // Assuming the response is JSON and not XML as your comment suggests
        $spotPriceData = json_decode($response->body(), true);

        $transformedData = $this->transformData(collect($spotPriceData));

        // Validation is now within the transformData method
        SpotPrice::upsert(
            $transformedData,
            ['timestamp'], // Unique columns
            ['price_in_eurocent'] // Columns to update
        );

        $this->info("Spot prices updated successfully for date: {$this->argument('date')}");

        return Command::SUCCESS;
    }

    private function transformData(Collection $spotPriceData): array
    {
        $validatedData = $spotPriceData->map(function ($item) {
            $priceInCents = (int) ($item['price'] * 100);

            return [
                'timestamp' => Carbon::parse($item['dateTime']),
                'price_in_eurocent' => $priceInCents,
            ];
        })->toArray();

        // Perform validation on the transformed data
        $validator = Validator::make($validatedData, [
            '*.timestamp' => 'required|date',
            '*.price_in_eurocent' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $this->error('The spot prices update has failed.');

            return Command::FAILURE;
        }

        return $validator->validated();
    }
}
