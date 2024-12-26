<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\Frontend\DetailSearch\DetailSearchController;

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/', IndexController::class)->name('home');
Route::match(['post', 'get'],'/suche', SearchController::class)->name('search');
//Route::get('/detailsuche', [DetailSearchController::class, 'index'] )->name('detailSearch');
Route::get('/detailsuche', [DetailSearchController::class, 'index'])->name('detail_search');
Route::get('/detailsuche/ergebnisse', [DetailSearchController::class, 'search'])->name('detail_search_result');

//Route::get('/{continent}/{country}/{location}', LocationController::class)->name('location');

Route::view('impressum', 'pages.impressum')->name('impressum');


Route::get('/api/countries-by-continent/{continent}', function ($continent) {
    return \App\Models\WwdeCountry::where('continent_id', $continent)->get();
});
