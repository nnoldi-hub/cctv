<?php

use App\Http\Controllers\Technical\DashboardController;
use App\Http\Controllers\Technical\EquipmentController;
use App\Http\Controllers\Technical\InstallationController;
use App\Http\Controllers\Technical\TicketController;
use App\Http\Controllers\Technical\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('tehnic')->name('technical.')->group(function () {
    Route::middleware('role_or_permission:admin|tehnic|suport|equipment.view|equipment.manage|installations.view|installations.manage')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
    });

    Route::middleware('role_or_permission:admin|tehnic|suport')->group(function () {
        Route::resource('tichete', TicketController::class)->parameters(['tichete' => 'ticket'])->names('tickets');
        Route::patch('/tichete/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
        Route::post('/tichete/{ticket}/comentarii', [TicketController::class, 'addComment'])->name('tickets.comments');
    });

    Route::middleware('role_or_permission:admin|tehnic|equipment.view|equipment.manage')->group(function () {
        Route::resource('echipamente', EquipmentController::class)->except(['show'])->parameters(['echipamente' => 'equipment'])->names('equipment');
        Route::patch('/echipamente/{equipment}/stoc', [EquipmentController::class, 'adjustStock'])->name('equipment.stock');
        Route::resource('servicii', ServiceController::class)->except(['show'])->parameters(['servicii' => 'service'])->names('services');
    });

    Route::middleware('role_or_permission:admin|tehnic|installations.view|installations.manage')->group(function () {
        Route::resource('instalari', InstallationController::class)->parameters(['instalari' => 'installation'])->names('installations');
        Route::patch('/instalari/{installation}/status', [InstallationController::class, 'updateStatus'])->name('installations.status');
        Route::patch('/instalari/{installation}/checklist', [InstallationController::class, 'updateChecklist'])->name('installations.checklist');
        Route::get('/instalari/{installation}/pdf', [InstallationController::class, 'pdf'])->name('installations.pdf');
    });
});
