<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\StatController;
use App\Http\Controllers\Admin\BlogPostController;
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
    Route::get('/cheltuieli/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::resource('cheltuieli', ExpenseController::class)->except(['show'])->parameters(['cheltuieli' => 'expense'])->names('expenses');
    Route::get('/sms-log', SmsLogController::class)->name('sms-logs');
    Route::get('/audit-log', AuditLogController::class)->name('audit-logs');

    Route::get('/facturi/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::resource('facturi', InvoiceController::class)->parameters(['facturi' => 'invoice'])->names('invoices');
    Route::patch('/facturi/{invoice}/plateste', [InvoiceController::class, 'markPaid'])->name('invoices.pay');
    Route::post('/facturi/{invoice}/fgo-sync', [InvoiceController::class, 'syncFgo'])->name('invoices.fgo-sync');
    Route::get('/facturi/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('/abonamente/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');
    Route::resource('abonamente', SubscriptionController::class)->except(['show'])->parameters(['abonamente' => 'subscription'])->names('subscriptions');

    Route::resource('utilizatori', UserController::class)->except(['show'])->parameters(['utilizatori' => 'user'])->names('users');
    Route::resource('pachete-site', SitePackageController::class)->parameters(['pachete-site' => 'sitePackage'])->names('site-packages');
    Route::get('/pagini', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pagini/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::get('/pagini/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');
    Route::put('/pagini/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::get('/statistici', [StatController::class, 'index'])->name('stats.index');
    Route::post('/statistici', [StatController::class, 'store'])->name('stats.store');
    Route::put('/statistici/{stat}', [StatController::class, 'update'])->name('stats.update');
    Route::delete('/statistici/{stat}', [StatController::class, 'destroy'])->name('stats.destroy');
    Route::resource('blog', BlogPostController::class)->except(['show'])->names('blog');

    Route::get('/setari', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/setari', [SettingsController::class, 'update'])->name('settings.update');
});
