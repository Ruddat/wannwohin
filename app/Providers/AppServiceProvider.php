<?php

namespace App\Providers;

use App\Services\GeocodeService;
use Illuminate\Support\ServiceProvider;

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


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
