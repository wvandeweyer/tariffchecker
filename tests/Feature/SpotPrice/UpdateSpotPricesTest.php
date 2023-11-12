<?php

use App\Models\SpotPrice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

it('executes the daily update command successfully with mocked HTTP', function () {

    $fakeResponse = [
        ['dateTime' => '2023-11-05T23:00:00Z', 'price' => 19.32, 'isVisible' => true],
        ['dateTime' => '2023-11-06T00:00:00Z', 'price' => 13.87, 'isVisible' => true],
        ['dateTime' => '2023-11-06T01:00:00Z', 'price' => 5.7, 'isVisible' => true],
        ['dateTime' => '2023-11-06T02:00:00Z', 'price' => 2.53, 'isVisible' => true],
        ['dateTime' => '2023-11-06T03:00:00Z', 'price' => 4.3, 'isVisible' => true],
        ['dateTime' => '2023-11-06T04:00:00Z', 'price' => 16.93, 'isVisible' => true],
        ['dateTime' => '2023-11-06T05:00:00Z', 'price' => 73.05, 'isVisible' => true],
        ['dateTime' => '2023-11-06T06:00:00Z', 'price' => 96.21, 'isVisible' => true],
        ['dateTime' => '2023-11-06T07:00:00Z', 'price' => 99.59, 'isVisible' => true],
        ['dateTime' => '2023-11-06T08:00:00Z', 'price' => 79.85, 'isVisible' => true],
        ['dateTime' => '2023-11-06T09:00:00Z', 'price' => 61.85, 'isVisible' => true],
        ['dateTime' => '2023-11-06T10:00:00Z', 'price' => 58.04, 'isVisible' => true],
        ['dateTime' => '2023-11-06T11:00:00Z', 'price' => 58.05, 'isVisible' => true],
        ['dateTime' => '2023-11-06T12:00:00Z', 'price' => 66.98, 'isVisible' => true],
        ['dateTime' => '2023-11-06T13:00:00Z', 'price' => 80.29, 'isVisible' => true],
        ['dateTime' => '2023-11-06T14:00:00Z', 'price' => 99.67, 'isVisible' => true],
        ['dateTime' => '2023-11-06T15:00:00Z', 'price' => 112.44, 'isVisible' => true],
        ['dateTime' => '2023-11-06T16:00:00Z', 'price' => 138.14, 'isVisible' => true],
        ['dateTime' => '2023-11-06T17:00:00Z', 'price' => 151.35, 'isVisible' => true],
        ['dateTime' => '2023-11-06T18:00:00Z', 'price' => 139.93, 'isVisible' => true],
        ['dateTime' => '2023-11-06T19:00:00Z', 'price' => 111.81, 'isVisible' => true],
        ['dateTime' => '2023-11-06T20:00:00Z', 'price' => 106.29, 'isVisible' => true],
        ['dateTime' => '2023-11-06T21:00:00Z', 'price' => 99.56, 'isVisible' => true],
        ['dateTime' => '2023-11-06T22:00:00Z', 'price' => 89.86, 'isVisible' => true],
    ];

    // Fake the HTTP request
    Http::fake([
        'griddata.elia.be/*' => Http::response($fakeResponse, 200),
    ]);

    $this->artisan('app:update-spot-prices')
        ->assertExitCode(0);

    $storedPrices = SpotPrice::all();
    expect($storedPrices->count())->toBe(24);

    foreach ($storedPrices as $index => $storedPrice) {
        $expectedDate = Carbon::parse($fakeResponse[$index]['dateTime']);
        expect($storedPrice->timestamp)->toEqual($expectedDate);

        $expectedPrice = (int) ($fakeResponse[$index]['price'] * 100);

        expect($storedPrice->price_in_eurocent)->toEqual($expectedPrice);
    }

});
