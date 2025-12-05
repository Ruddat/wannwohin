<?php

namespace App\Providers;

use App\Services\SeoService;

use App\Services\GeocodeService;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use App\Library\WeatherApiClientLibrary;
use App\Repositories\LocationRepository;
use App\Library\WeatherDataManagerLibrary;
use App\Repositories\EloquentLocationRepository;
use App\Repositories\LocationRepositoryInterface;

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
    public function boot(SeoService $seoService): void
    {


        // Standard-SEO-Daten bereitstellen, falls keine explizit übergeben wurden
        View::share('seo', [
        'title' => 'Wann-Wohin - Deine Reiseplattform',
        'description' => 'Finde die besten Reiseziele, Wetterdaten und Top-Locations für deinen nächsten Urlaub.',
        'canonical' => url('/'),
        'extra_meta' => [],
    ]);

    // Verwende Bootstrap für die Paginator-Darstellung
    Paginator::useBootstrap();


        // Sprache aus der Session setzen
        $locale = Session::get('locale', config('app.locale'));
        App::setLocale($locale);

        require_once app_path('Helpers/IconHelper.php');


    }
}
