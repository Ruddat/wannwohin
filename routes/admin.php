<?php


use App\Livewire\Backend\ParkListManager\ParkFormComponent;
use App\Livewire\Backend\ParkListManager\ParkListComponent;
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


// Park Manager
Route::prefix('park-manager')->name('park-manager.')->group(function () {
    Route::get('/', ParkListComponent::class)->name('index');
    Route::get('/create', ParkFormComponent::class)->name('create');
    Route::get('/{id}/edit', ParkFormComponent::class)->name('edit');
});
