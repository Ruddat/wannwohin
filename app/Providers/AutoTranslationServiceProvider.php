<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Services\AutoTranslationService;
use App\Repositories\TranslationRepository;

class AutoTranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('autotranslate', function ($app) {
            return new AutoTranslationService($app->make(TranslationRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Blade Directive für AutoTranslationService
        Blade::directive('autotranslate', function ($expression) {
            return "<?php echo app(\App\Services\AutoTranslationService::class)->trans($expression); ?>";
        });

        // Logging zur Überprüfung der aktuellen Sprache
        // Log::info("Aktuelle Sprache: " . App::getLocale());

    }
}
