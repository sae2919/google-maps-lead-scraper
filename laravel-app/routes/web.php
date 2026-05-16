<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WebsiteController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| 🔓 PUBLIC & AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();
    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        ['name'  => $googleUser->getName(), 'password' => bcrypt(uniqid())]
    );
    Auth::login($user);
    return redirect('/dashboard');
});

Route::get('/', function () {
    if (!auth()->check()) return redirect('/login');
    return auth()->user()->role === 'admin'
        ? redirect('/admin/dashboard')
        : redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| 🔐 PROTECTED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ── Dashboard ────────────────────────────────────────────────────────
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return app(\App\Http\Controllers\LeadController::class)->dashboard();
    })->name('dashboard');
    Route::get('/search-page', fn() => view('welcome'))->name('search.page');

    // ── Scraper Operations ───────────────────────────────────────────────
    Route::post('/search',           [LeadController::class, 'search']);
    Route::get('/results/{id}',      [LeadController::class, 'results']);
    Route::get('/history',           [LeadController::class, 'history']);

    // These are duplicated in api.php too — kept here for web middleware
    Route::get('/api/progress/{id}', [LeadController::class, 'progress']);
    Route::get('/api/leads/{id}',    [LeadController::class, 'getLeads']);

    // ── Scraper Controls ─────────────────────────────────────────────────
    Route::post('/stop-search/{id}',   [LeadController::class, 'stop']);
    Route::post('/pause-search/{id}',  [LeadController::class, 'pause']);
    Route::post('/resume-search/{id}', [LeadController::class, 'resume']);

    // ── Data Management ──────────────────────────────────────────────────
    Route::get('/export/{id}',         [LeadController::class, 'export']);
    Route::post('/export-filtered',    [LeadController::class, 'exportFiltered']);
    Route::post('/delete/{id}',        [LeadController::class, 'delete']);
    Route::post('/delete-all',         [LeadController::class, 'deleteAll']);

    // ── Profile ──────────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── AI Website Generator ─────────────────────────────────────────────
    Route::get('/generate',  fn() => view('website.form'))->name('website.form');
    Route::post('/generate', [WebsiteController::class, 'generate'])->name('website.generate');

    // Bulk Generation (called from results page JS)
    Route::post('/generate-bulk-websites', [LeadController::class, 'generateBulkWebsites']);
});

/*
|--------------------------------------------------------------------------
| 🌐 PUBLIC ROUTES — No auth required
|--------------------------------------------------------------------------
*/

// Lead-based AI sites (scraped from Google Maps)
Route::get('/sites/{id}', [WebsiteController::class, 'show'])->name('website.show');

// User-generated sites (saved to generated_sites table)
Route::get('/site/{slug}', [WebsiteController::class, 'serveSite'])->name('site.show');

// Contact form submissions from generated sites
Route::post('/contact-submit', [LeadController::class, 'contactSubmit']);

/*
|--------------------------------------------------------------------------
| 🛡️ ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard',          [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/update-user/{id}',  [AdminController::class, 'updateUser']);
    Route::post('/admin/toggle-role/{id}',  [AdminController::class, 'toggleRole']);
    Route::post('/admin/delete-user/{id}',  [AdminController::class, 'deleteUser']);
});

/*
|--------------------------------------------------------------------------
| 🔧 SCAFFOLDING & SEO
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

Route::get('/seo/{city}/{service}', [LeadController::class, 'seoPage']);
Route::post('/delete-search/{id}', [LeadController::class, 'deleteSearch']);
