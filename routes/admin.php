<?php

use App\Http\Controllers\Backend\Location\LocationController;
use App\Http\Controllers\Backend\HeaderContent\HeaderContentController;


// admin.php
Route::view('/', 'backend.layout-fluid-vertical')->name('manager');

// Header Manager
Route::prefix('header-manager')->name('header-manager.')->group(function () {
    Route::resource('header_contents', HeaderContentController::class);
});

// Location Manager
Route::prefix('location-manager')->name('location-manager.')->group(function () {
    Route::resource('locations', LocationController::class);
});
