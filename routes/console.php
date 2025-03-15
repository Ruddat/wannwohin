<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchDailyWeatherData;
use App\Console\Commands\CheckMaintenanceExpiration;
use App\Console\Commands\UpdateLocationDetails;
use App\Console\Commands\FetchMonthlyClimateData; // Neu hinzugefügt

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Regelmäßige Datenaktualisierungen (mehrfach täglich)
// Schedule::command('climate:fetch-daily')->cron('0 */6 * * *'); // Alle 6 Stunden
Schedule::command(UpdateLocationDetails::class, ['--field' => 'time_zone', '--force-timezone'])->cron('0 */4 * * *'); // Alle 4 Stunden
Schedule::command('climate:fetch-monthly')->everyThreeHours()->withoutOverlapping(); // Alle 3 Stunden, neu hinzugefügt

// Tägliche Datenaktualisierungen
Schedule::command(UpdateLocationDetails::class, ['--limit' => 100])->dailyAt('02:30')->withoutOverlapping();
Schedule::command('flights:fetch-prices')->dailyAt('03:00');
Schedule::command('hotels:fetch-prices')->dailyAt('03:30');
Schedule::command('rentalcars:fetch-prices')->dailyAt('04:30');
// Schedule::command(FetchDailyWeatherData::class)->dailyAt('14:00');

// Monatliche Aufgaben
Schedule::command('scrape:travel-warnings')->monthly();
Schedule::command('parks:import')->monthly();
Schedule::command('currency:update-exchange-rates')->monthly();

// Stündliche Aufgaben
Schedule::command('locations:update-history')->hourly();

// Backup-Aufgaben
Schedule::command('backup:run')->dailyAt('02:00');
Schedule::command('backup:clean')->dailyAt('02:15');

// Wartungsmodus-Check
Schedule::command(CheckMaintenanceExpiration::class)->everyFiveMinutes();

// Kommentierte Aufgaben (zur späteren Aktivierung)
//Schedule::command('locations:import-world-cities --format=csv')->hourly();
//Schedule::command('locations:download-continent-images')->dailyAt('00:30');
