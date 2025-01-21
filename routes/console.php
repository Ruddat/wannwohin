<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\FetchDailyWeatherData;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


//Schedule::command('climate:fetch-daily')->dailyAt('14:00');
Schedule::command('climate:fetch-daily')->cron('0 */4 * * *'); // Alle 4 Stunden
//Schedule::command('locations:import-world-cities --format=csv')->hourly();
Schedule::command('locations:download-continent-images')->dailyAt('00:30');
Schedule::command('scrape:travel-warnings')->dailyAt('01:30');
Schedule::command('update:location-details')->dailyAt('02:30');
Schedule::command('parks:import')->dailyAt('00:30');
Schedule::command('locations:update-history')->hourly();


// Schedule::command(FetchDailyWeatherData::class)->dailyAt('14:00');
