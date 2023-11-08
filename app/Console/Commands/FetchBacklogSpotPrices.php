<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FetchBacklogSpotPrices extends Command
{
    protected $signature = 'app:fetch-backlog-spot-prices
                            {start_date? : The start date for fetching spot prices (format: YYYY-MM-DD)}
                            {end_date? : The end date for fetching spot prices (format: YYYY-MM-DD)}';

    protected $description = 'Fetches the backlog of spot prices for a given date range or the last two years if no range is provided';

    public function handle()
    {
        $startDate = $this->argument('start_date')
                        ? new Carbon($this->argument('start_date'))
                        : now()->subYears(2)->startOfDay();
        $endDate = $this->argument('end_date')
                        ? new Carbon($this->argument('end_date'))
                        : now()->endOfDay();

        $date = $startDate;
        while ($date->lte($endDate)) {
            // Call the UpdateSpotPrices command for each date
            $this->call('app:update-spot-prices', [
                'date' => $date->toDateString(),
            ]);

            // Increase the date by one day
            $date->addDay();
        }

        return 0;
    }
}
