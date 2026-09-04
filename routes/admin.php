<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SitePackageController;
use App\Http\Controllers\Admin\SmsLogController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/rapoarte', ReportController::class)->name('reports');
    Route::get('/sms-log', SmsLogController::class)->name('sms-logs');

    Route::get('/facturi/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::resource('facturi', InvoiceController::class)->parameters(['facturi' => 'invoice'])->names('invoices');
    Route::patch('/facturi/{invoice}/plateste', [InvoiceController::class, 'markPaid'])->name('invoices.pay');
    Route::get('/facturi/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('/abonamente/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');
    Route::resource('abonamente', SubscriptionController::class)->except(['show'])->parameters(['abonamente' => 'subscription'])->names('subscriptions');

    Route::resource('utilizatori', UserController::class)->except(['show'])->parameters(['utilizatori' => 'user'])->names('users');
    Route::resource('pachete-site', SitePackageController::class)->parameters(['pachete-site' => 'sitePackage'])->names('site-packages');

    Route::get('/setari', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/setari', [SettingsController::class, 'update'])->name('settings.update');
});
