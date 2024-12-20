<?php

use App\Http\Controllers\DetailSearchController;
use App\Http\Controllers\DetailSearchResultControllerDelete;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ListCountryLocationsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Search\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('test', 'pages.detailSearch.clima_box.custom_slider5')->name('impressum');


Route::get('/', IndexController::class)->name('home');
Route::match(['post', 'get'],'/suche', SearchController::class)->name('search');
Route::get('/detailsuche', [DetailSearchController::class, 'index'] )->name('detailSearch');
Route::get('/detailsucheresult', [SearchController::class, 'detailsSearchResult'] )->name('detail_search_result');
Route::get('/{continent}/{country}/{location}', LocationController::class)->name('location');

Route::get('/{continent}/{country}/', [ListCountryLocationsController::class, 'locations'])->name('list-country-locations');
Route::get('/{continent}/', [\App\Http\Controllers\CountryController::class, 'getCountriesByContinent'])->name('list-continent-countries');
Route::view('impressum', 'pages.impressum')->name('impressum');

//Route::get('/urlaub/{urlaub_type}/{ddd}/', [SearchController::class, 'searchByType']);

Route::get('/urlaub/{urlaub_type}', 'App\Http\Controllers\Search\SearchController@searchByType');
//Route::get('/aboutus', 'App\Http\Controllers\SmsController@aboutus');

//Route::get('/test/sss/', [SearchController::class, 'searchByType']);
//Route::view('/urlaub/{urlaub_type}', 'pages.impressum');

Route::get('/greeting/{dd}/{ss}/', function () {
    return 'Hello World';
});

//Route::get('/urlaub/{urlaub_type}/month/{month_id}', function ($postId, $commentId) {
//    return 'Hello World';
//});

Route::get('/urlaub/{urlaub_type}/month/{month_id}', 'App\Http\Controllers\Search\SearchController@searchByType');



Route::post('/quick_serach/count', [SearchController::class, 'searchResultCount'])->name('quick_serach');
Route::post('/details_search/count', [SearchController::class, 'detailsSearchResultCount'])->name('details-search.count');
Route::post('/spezielle/{special}', [SearchController::class, 'findSpecial'])->name('spezielle');

//Route::post('/offers/createDealFromOffer',  [App\Http\Controllers\OfferController::class, 'createDealFromOffer']);
