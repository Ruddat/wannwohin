<?php

namespace App\Providers;

use App\Services\GeocodeService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Library\WeatherApiClientLibrary;
use App\Repositories\LocationRepository;
use App\Library\WeatherDataManagerLibrary;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeocodeService::class, function ($app) {
            return new GeocodeService();
        });

        $this->app->singleton(WeatherApiClientLibrary::class, function ($app) {
            return new WeatherApiClientLibrary();
        });

        $this->app->singleton(WeatherDataManagerLibrary::class, function ($app) {
            return new WeatherDataManagerLibrary();
        });

        $this->app->bind(LocationRepositoryInterface::class, EloquentLocationRepository::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    // Verwende Bootstrap für die Paginator-Darstellung
    Paginator::useBootstrap();
    }
}
