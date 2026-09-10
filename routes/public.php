<?php

use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\ConfiguratorController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LegalController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\SitemapController;
use Illuminate\Support\Facades\Route;

Route::name('public.')->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/despre', [PageController::class, 'about'])->name('about');
    Route::get('/servicii', [PageController::class, 'services'])->name('services');
    Route::get('/configurator', [ConfiguratorController::class, 'index'])->name('configurator');
    Route::get('/calculator-cablu', [ConfiguratorController::class, 'cable'])->name('cable-calculator');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::get('/termeni', [LegalController::class, 'terms'])->name('terms');
    Route::get('/confidentialitate', [LegalController::class, 'privacy'])->name('privacy');
    Route::post('/cerere-oferta', [ContactController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('lead.store');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: ".route('sitemap'), 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');
