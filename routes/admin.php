<?php


use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Admin\AuthController;
use App\Livewire\Backend\ParkListManager\ParkFormComponent;
use App\Livewire\Backend\ParkListManager\ParkListComponent;
use App\Http\Controllers\Backend\Location\LocationController;
use App\Http\Controllers\Backend\HeaderContent\HeaderContentController;
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

    // Advertisement Manager
    Route::prefix('advertisement-manager')->name('advertisement-manager.')->group(function () {
        Route::get('/advertisement-blocks', AdvertisementBlocksComponent::class)->name('advertisement-blocks');
    });
});
