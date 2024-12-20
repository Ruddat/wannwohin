<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\FetchDailyWeatherData;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Add Scheduler for FetchDailyWeatherData
Artisan::command('schedule:run', function () {
    $this->call('climate:fetch-daily');
})->purpose('Fetch daily weather data for locations, store it in the climate table, and generate summaries')->dailyAt('14:00');
