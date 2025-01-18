<?php


use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Admin\AuthController;
use App\Livewire\Backend\ParkListManager\ParkFormComponent;
use App\Livewire\Backend\ParkListManager\ParkListComponent;
use App\Http\Controllers\Backend\Location\LocationController;
use App\Livewire\Backend\CountryManager\CountryManagerComponent;
use App\Livewire\Backend\LocationManager\LocationTableComponent;
use App\Livewire\Backend\ElectricManager\ElectricManagerComponent;
use App\Livewire\Backend\LocationManager\LocationManagerComponent;
use App\Livewire\Backend\ContinentManager\ContinentManagerComponent;
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
    });

    // Header Manager
    Route::prefix('header-manager')->name('header-manager.')->group(function () {
        Route::resource('header_contents', HeaderContentController::class);
    });

    // Park Manager
    Route::prefix('park-manager')->name('park-manager.')->group(function () {
        Route::get('/', ParkListComponent::class)->name('index');
        Route::get('/create', ParkFormComponent::class)->name('create');
        Route::get('/{id}/edit', ParkFormComponent::class)->name('edit');
    });

    // Advertisement Manager
    Route::prefix('advertisement-manager')->name('advertisement-manager.')->group(function () {
        Route::get('/advertisement-blocks', AdvertisementBlocksComponent::class)->name('advertisement-blocks');
    });

    // Continent Manager
    Route::prefix('continent-manager')->name('continent-manager.')->group(function () {
        Route::get('/', ContinentManagerComponent::class)->name('index');
    });

    // Country Manager
    Route::prefix('country-manager')->name('country-manager.')->group(function () {
        Route::get('/', CountryManagerComponent::class)->name('index');
    });

    // Translation Manager
    Route::prefix('translation-manager')->name('translation-manager.')->group(function () {
        Route::get('/', TranslationManagerComponent::class)->name('index');
    });

// Location Table Manager
Route::prefix('location-table-manager')->name('location-table-manager.')->group(function () {
    Route::get('/', LocationTableComponent::class)->name('index');
    Route::get('/{locationId}/edit', LocationManagerComponent::class)->name('edit');
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

Route::get('/admin/commands/artisan', function () {
    return view('backend.admin.commands.command-artisan');
})->name('admin.commands.view');

Route::get('/admin/imports-pics/imports', function () {
    return view('backend.admin.importimagesandtext.import-images-text-to-db');
})->name('admin.imports-pics.view');



});
