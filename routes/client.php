<?php

use App\Http\Controllers\Client\PortalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'client.portal'])->prefix('client')->name('client.')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/tichete', [PortalController::class, 'tickets'])->name('tickets.index');
    Route::post('/tichete', [PortalController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/lucrari', [PortalController::class, 'works'])->name('works.index');
    Route::get('/facturi', [PortalController::class, 'invoices'])->name('invoices.index');
    Route::get('/facturi/{invoice}/pdf', [PortalController::class, 'invoicePdf'])->name('invoices.pdf');
    Route::get('/abonament', [PortalController::class, 'subscriptions'])->name('subscriptions.index');
    Route::get('/echipamente', [PortalController::class, 'equipment'])->name('equipment.index');
    Route::get('/notificari', [PortalController::class, 'notifications'])->name('notifications.index');
    Route::patch('/notificari/{notification}/citita', [PortalController::class, 'readNotification'])->name('notifications.read');
});
