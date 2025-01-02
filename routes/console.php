<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\FetchDailyWeatherData;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('climate:fetch-daily')->dailyAt('14:00');
Schedule::command('locations:import-world-cities --format=csv')->hourly();
Schedule::command('locations:download-continent-images')->dailyAt('00:30');
Schedule::command('parks:import')->dailyAt('00:30');
Schedule::command('locations:update-history')->monthly();


// Schedule::command(FetchDailyWeatherData::class)->dailyAt('14:00');
