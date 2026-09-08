<?php

use App\Http\Controllers\Sales\ClientController;
use App\Http\Controllers\Sales\DashboardController;
use App\Http\Controllers\Sales\ActivityController;
use App\Http\Controllers\Sales\OfferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin|vanzari'])->prefix('vanzari')->name('sales.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/clienti/export', [ClientController::class, 'export'])->name('clients.export');
    Route::resource('clienti', ClientController::class)->parameters(['clienti' => 'client'])->names('clients');
    Route::patch('/clienti/{client}/pipeline', [ClientController::class, 'updatePipeline'])->name('clients.pipeline');
    Route::get('/activitati', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activitati/creeaza', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activitati', [ActivityController::class, 'store'])->name('activities.store');
    Route::patch('/activitati/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.status');
    Route::delete('/activitati/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::get('/oferte/export', [OfferController::class, 'export'])->name('offers.export');
    Route::resource('oferte', OfferController::class)->parameters(['oferte' => 'offer'])->names('offers');
    Route::patch('/oferte/{offer}/status', [OfferController::class, 'updateStatus'])->name('offers.status');
    Route::get('/oferte/{offer}/pdf', [OfferController::class, 'pdf'])->name('offers.pdf');
});
