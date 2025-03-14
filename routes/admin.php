<?php


use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Admin\AuthController;
use App\Livewire\Backend\SeoMetaComponent\SeoMetaEdit;
use App\Livewire\Backend\SeoMetaComponent\SeoMetaTable;
use App\Livewire\Backend\SeoMetaComponent\VisitorStats;
use App\Livewire\Backend\ParkListManager\ParkFormComponent;
use App\Livewire\Backend\ParkListManager\ParkListComponent;
use App\Livewire\Backend\WeatherManager\ClimateDataManager;
use App\Http\Controllers\Backend\Location\LocationController;
use App\Livewire\Backend\LocationFilters\LocationFilterTable;
use App\Livewire\Backend\WeatherManager\WeatherStationImporter;
use App\Livewire\Backend\CountryManager\CountryManagerComponent;
use App\Livewire\Backend\GalleryManager\GalleryManagerComponent;
use App\Livewire\Backend\LocationFilters\AdvancedLocationFilter;
use App\Livewire\Backend\LocationManager\LocationTableComponent;
use App\Livewire\Backend\AdvertisementManager\ProvidersComponent;
use App\Livewire\Backend\QuickFilterManager\QuickFilterComponent;
use App\Livewire\Backend\WeatherManager\WeatherStationsComponent;
use App\Http\Controllers\Backend\SystemComponent\BackupController;
use App\Livewire\Backend\ElectricManager\ElectricManagerComponent;
use App\Livewire\Backend\LocationManager\LocationManagerComponent;
use App\Livewire\Backend\SiteSettingsComponent\SiteSettingsManager;
use App\Livewire\Backend\ContinentManager\ContinentManagerComponent;
use App\Http\Controllers\Backend\SeoStatistik\VisitorStatsController;
use App\Livewire\Backend\StaticPageManager\StaticPageManagerComponent;
use App\Http\Controllers\Backend\HeaderContent\HeaderContentController;
use App\Livewire\Backend\TranslationManager\TranslationManagerComponent;
use App\Livewire\Backend\AdvertisementManager\AdvertisementBlocksComponent;


// admin.php
Route::prefix('verwaltung')->name('verwaltung.')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/admin/imports-stuff/imports', function () {
            return view('backend.admin.importstuff.import-stuff-to-db');
        })->name('admin.imports-stuff.view');




    // ----------------------------------------------------------------
    // Addons

    Route::get('/addons/imports-pics/imports', function () {
        return view('backend.admin.importimagesandtext.import-images-text-to-db');
    })->name('addons.imports-pics.view');

    // ----------------------------------------------------------------
    // Weather

    // Weather Manager
    Route::prefix('weather-manager')->name('weather-manager.')->group(function () {
        Route::get('/', WeatherStationsComponent::class)->name('index');
    });
    // Weather Importer
    Route::prefix('weather-importer')->name('weather-importer.')->group(function () {
        Route::get('/', WeatherStationImporter::class)->name('index');
    });

    Route::prefix('weather-climate-manager')->name('weather-climate-manager.')->group(function () {
        Route::get('/', ClimateDataManager::class)->name('index');
    });




    // Site Manager Routes
    Route::prefix('site-manager')->name('site-manager.')->group(function () {

        // Header Manager
        Route::resource('header_contents', HeaderContentController::class);

        Route::get('/quick-filter', QuickFilterComponent::class)->name('quick-filter');

        // Park Manager
        Route::prefix('park-manager')->name('park-manager.')->group(function () {
            Route::get('/', ParkListComponent::class)->name('index');
            Route::get('/create', ParkFormComponent::class)->name('create');
            Route::get('/{id}/edit', ParkFormComponent::class)->name('edit');
        });

        // Continent Manager
        Route::prefix('continent-manager')->name('continent-manager.')->group(function () {
            Route::get('/', ContinentManagerComponent::class)->name('index');
        });

        // Country Manager
        Route::prefix('country-manager')->name('country-manager.')->group(function () {
            Route::get('/', CountryManagerComponent::class)->name('index');
            Route::get('/country-manager/edit/{id}', CountryManagerComponent::class)->name('country-manager.edit');
        });


// Location Table Manager
Route::prefix('location-table-manager')->name('location-table-manager.')->group(function () {
    Route::get('/', LocationTableComponent::class)->name('index');
    Route::get('/{locationId}/edit', LocationManagerComponent::class)->name('edit');
});



    });





    // Advertisement Manager
    Route::prefix('advertisement-manager')->name('advertisement-manager.')->group(function () {
        Route::get('/advertisement-blocks', AdvertisementBlocksComponent::class)->name('advertisement-blocks');
        Route::get('/advertisement-providers', ProvidersComponent::class)->name('advertisement-providers');

    });





    // Translation Manager
    Route::prefix('translation-manager')->name('translation-manager.')->group(function () {
        Route::get('/', TranslationManagerComponent::class)->name('index');
    });






// SiteSettings Manager SeoMetaTable Manager
Route::prefix('seo-table-manager')->name('seo-table-manager.')->group(function () {
    Route::get('/seo', SeoMetaTable::class)->name('seo.table');
    Route::get('/seo/edit/{id}', SeoMetaEdit::class)->name('seo.edit');
    Route::get('/visitorstats', VisitorStats::class)->name('visitor.stats');
    Route::get('static-page-manager', StaticPageManagerComponent::class)->name('static-page-manager');
    Route::get('/site-settings', SiteSettingsManager::class)->name('site-settings');
    Route::get('/visitor-stats', [VisitorStatsController::class, 'index'])->name('visitor-stats');
    Route::get('/visitor-stats/data', [VisitorStatsController::class, 'getStats'])->name('visitor-stats.data');

    Route::get('/backups', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backups/run', [BackupController::class, 'run'])->name('backup.run');
    Route::get('/backups/download/{path}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backups/delete/{path}', [BackupController::class, 'delete'])->name('backup.delete');
});





// Location Manager (falls benötigt)
Route::prefix('location-manager')->name('location-manager.')->group(function () {
    Route::get('/', LocationManagerComponent::class)->name('index');
});

// Electric Manager (falls benötigt)
// Electric Manager
Route::prefix('electric-manager')->name('electric-manager.')->group(function () {
    Route::get('/', ElectricManagerComponent::class)->name('index');
});

// Gallerie Manager
Route::prefix('gallery-manager')->name('gallery-manager.')->group(function () {
    Route::get('/', GalleryManagerComponent::class)->name('index');
});


// Gallerie Manager
Route::prefix('filters-table-manager')->name('filters-table-manager.')->group(function () {
    Route::get('/', LocationFilterTable::class)->name('index');
});

// Gallerie Manager
Route::prefix('filters-advanced-location')->name('filters-advanced-location.')->group(function () {
    Route::get('/', AdvancedLocationFilter::class)->name('index');
});



});


Route::get('/admin/commands/artisan', function () {
    return view('backend.admin.commands.command-artisan');
})->name('admin.commands.view');








});



