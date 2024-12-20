<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DetailSearchController;
use App\Http\Controllers\Search\SearchController;

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/', IndexController::class)->name('home');
Route::match(['post', 'get'],'/suche', SearchController::class)->name('search');
Route::get('/detailsuche', [DetailSearchController::class, 'index'] )->name('detailSearch');

Route::get('/{continent}/{country}/{location}', LocationController::class)->name('location');

Route::view('impressum', 'pages.impressum')->name('impressum');
