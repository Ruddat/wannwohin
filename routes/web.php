<?php

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobeController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NewSearchController;
use App\Http\Controllers\WetterTestController;
use App\Http\Controllers\Frontend\TripController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\Tools\ScreenshotController;
use App\Http\Controllers\Backend\Admin\AuthController;
use App\Http\Controllers\Frontend\StaticPageController;
use App\Http\Controllers\Backend\HeaderWeatherController;
use App\Livewire\Frontend\QuickSearch\SearchResultsComponent;
use App\Livewire\Backend\CountryManager\CountryManagerComponent;
use App\Http\Controllers\Backend\Imports\CountryImportController;
use App\Http\Controllers\Backend\Imports\LocationImportController;
use App\Livewire\Backend\LocationManager\LocationManagerComponent;
use App\Http\Controllers\Backend\Imports\ContinentImportController;
use App\Http\Controllers\Frontend\DetailSearch\DetailSearchController;
use App\Http\Controllers\Backend\Imports\FilterLocationImportController;
use App\Http\Controllers\Frontend\ContinentCountryTable\ContinentController;
use App\Http\Controllers\Frontend\LocationDetails\LocationDetailsController;
use App\Http\Controllers\Frontend\WishlistCompare\WishlistCompareController;



    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Home

// Login
Route::post('/login', [AuthController::class, 'login']);

Route::post('/weather/update', [HeaderWeatherController::class, 'update'])->name('weather.update');

Route::get('/generate-screenshot', [ScreenshotController::class, 'generate']);

// Imports
Route::post('/import-continents', [ContinentImportController::class, 'import'])->name('continents.import');
Route::get('/continent-import', function () {
    return view('excel-import/continent-import');
})->name('continents.upload');

Route::post('/import-countries', [CountryImportController::class, 'import'])->name('countries.import');
Route::get('/country-import', function () {
    return view('excel-import/country-import');
})->name('countries.upload');

Route::post('/import-locations', [LocationImportController::class, 'import'])->name('locations.import');
Route::get('/location-import', function () {
    return view('excel-import/location-import');
})->name('locations.upload');


Route::get('/location-text-import', [FilterLocationImportController::class, 'index'])->name('location-text-import-form');
Route::post('/location-text-import', [FilterLocationImportController::class, 'import'])->name('location-text-import');

Route::get('/search-locations', [NewSearchController::class, 'search'])->name('search.locations');
Route::get('/random-destination', [NewSearchController::class, 'randomDestination'])->name('search.random');


Route::get('/amusement-parks', [LocationDetailsController::class, 'getAmusementParks'])->name('amusement.parks');

//Route::get('/compare-locations', function () {
//    $ids = request('ids') ? explode(',', request('ids')) : [];
//    $locations = \App\Models\WwdeLocation::whereIn('id', $ids)->get();
//
//    return view('compare', compact('locations'));
//})->name('compare');

//Route::get('/compare/{ids}', [WishlistCompareController::class, 'index'])->name('compare');

Route::post('/track-dwell-time', [VisitorController::class, 'trackDwellTime'])->name('track.dwell.time');


    Route::get('/wetter', [WetterTestController::class, 'showWeather']);


    Route::get('/globe', [GlobeController::class, 'show'])->name('globe');
    Route::get('/api/locations', [GlobeController::class, 'index']);


Route::middleware(['web', 'breadcrumbs', 'track-referral', 'weather'])->group(function () {
    Route::get('/', IndexController::class)->name('home');

    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
    Route::get('/explore/results', [ExploreController::class, 'results'])->name('explore.results');

    Route::post('/weather/update', [HeaderWeatherController::class, 'update'])->name('weather.update');

    Route::get('/urlaub/{urlaub_type}/month/{month_id}', [IndexController::class, 'searchResults'])
        ->name('search.results');

    Route::match(['post', 'get'], '/suche', SearchController::class)
        ->name('search');

    Route::get('/detailsuche', [DetailSearchController::class, 'index'])
        ->name('detail_search');


        Route::get('/trips', [TripController::class, 'index'])->name('trips.index');


    Route::get('/detailsuche/ergebnisse', [DetailSearchController::class, 'search'])
        ->name('detail_search_result');

        Route::get('/search-results-alle', [App\Http\Controllers\Frontend\DetailSearch\DetailSearchController::class, 'showSearchResults'])
        ->name('ergebnisse.anzeigen');

        // Impressum
        Route::view('impressum', 'pages.impressum-neu')
        ->name('impressum');

        Route::get('/{page}', [StaticPageController::class, 'show'])
        ->where('page', 'impressum|kontakt|agb|datenschutz')
        ->name('static.page');

    Route::get('/climate-forecast/{locationId}', [DetailSearchController::class, 'predictFutureClimate']);

    Route::get('/search-results', function () {
        $title = 'Suchergebnisse'; // Beispiel für zusätzliche Daten
        return view('frondend.quicksearch.quicksearchresults', compact('title'));
    })->name('search.results');


    Route::get('/details/{continent}/{country}/{location}', [LocationDetailsController::class, 'show'])
        ->where([
            'continent' => '[a-zA-Z0-9-]+',
            'country' => '[a-zA-Z0-9-]+',
            'location' => '[a-zA-Z0-9-]+',
        ])
        ->name('location.details');

    Route::get('/{continentAlias}', [ContinentController::class, 'showCountries'])
        ->name('continent.countries');

    Route::get('/{continentAlias}/{countryAlias}/locations', [ContinentController::class, 'showLocations'])
        ->name('list-country-locations');


        Route::get('/compare/{slugs?}', [WishlistCompareController::class, 'index'])
        ->where('slugs', '.*')
        ->name('compare');


    });

// API-Routen (in einer separaten Datei empfohlen)
Route::get('/api/countries-by-continent/{continent}', function ($continent) {
    return \App\Models\WwdeCountry::where('continent_id', $continent)->get();
})->name('api.countries-by-continent');

Route::get('/change-language/{lang}', [LanguageController::class, 'switch'])->name('change.lang');

//Route::prefix('localization')
//    ->middleware(['web', SetLocale::class])
//    ->group(function() {
//        // Route zum Wechseln der Sprache über die URL
//        Route::get('/change-language', [LanguageController::class, 'switch'])
//            ->name('change.lang');
//    });


// Imports
// Route to display the upload form


Route::get('/country-manager/edit/{id}', CountryManagerComponent::class)->name('country-manager.edit');

Route::get('/location-manager/edit/{id}', LocationManagerComponent::class)->name('location-manager.edit');


