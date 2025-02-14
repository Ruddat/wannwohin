<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\FetchDailyWeatherData;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('climate:fetch-daily')->cron('0 */6 * * *'); // Alle 4 Stunden
//Schedule::command('locations:import-world-cities --format=csv')->hourly();
//Schedule::command('locations:download-continent-images')->dailyAt('00:30');
Schedule::command('scrape:travel-warnings')->monthly();
Schedule::command('update:location-details')->dailyAt('02:30');
Schedule::command('flights:fetch-prices')->dailyAt('03:00');
Schedule::command('hotels:fetch-prices')->dailyAt('3:30');
Schedule::command('rentalcars:fetch-prices')->dailyAt('04:30');

Schedule::command('parks:import')->monthly();
Schedule::command('locations:update-history')->hourly();
Schedule::command('currency:update-exchange-rates')->monthly();


// Schedule::command(FetchDailyWeatherData::class)->dailyAt('14:00');
