<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Search\SearchController;
use App\Livewire\Frontend\QuickSearch\SearchResultsComponent;
use App\Http\Controllers\Frontend\DetailSearch\DetailSearchController;
use App\Http\Controllers\Frontend\LocationDetails\LocationDetailsController;

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/', IndexController::class)->name('home');
Route::get('/urlaub/{urlaub_type}/month/{month_id}', [IndexController::class, 'searchResults'])->name('search.results');


Route::match(['post', 'get'],'/suche', SearchController::class)->name('search');
//Route::get('/detailsuche', [DetailSearchController::class, 'index'] )->name('detailSearch');
Route::get('/detailsuche', [DetailSearchController::class, 'index'])->name('detail_search');
Route::get('/detailsuche/ergebnisse', [DetailSearchController::class, 'search'])->name('detail_search_result');


Route::get('/search-results', SearchResultsComponent::class)->name('search.results');


Route::get('/details/{continent}/{country}/{location}', [LocationDetailsController::class, 'show'])
    ->where([
        'continent' => '[a-zA-Z0-9-]+',
        'country' => '[a-zA-Z0-9-]+',
        'location' => '[a-zA-Z0-9-]+',
    ])
    ->name('location.details');

// Route::get('/{continent}/{country}/{location}', LocationDetailsController::class)->name('location');



Route::view('impressum', 'pages.impressum-neu')->name('impressum');


Route::get('/api/countries-by-continent/{continent}', function ($continent) {
    return \App\Models\WwdeCountry::where('continent_id', $continent)->get();
});
