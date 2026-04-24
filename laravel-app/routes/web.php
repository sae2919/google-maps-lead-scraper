<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// 🔥 GOOGLE AUTH
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {

    $googleUser = Socialite::driver('google')->user();

    $user = User::updateOrCreate([
        'email' => $googleUser->getEmail()
    ], [
        'name' => $googleUser->getName(),
        'password' => bcrypt(uniqid()),
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

// 🔥 HOME ROUTE
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }

    return auth()->user()->role === 'admin'
        ? redirect('/admin/dashboard')
        : redirect('/dashboard');
});

// 🔥 PROTECTED ROUTES
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [LeadController::class, 'dashboard'])->name('dashboard');

    // Search Page
    Route::get('/search-page', function () {
        return view('welcome');
    })->name('search.page');

    // Start Scraping
    Route::post('/search', [LeadController::class, 'search']);

    // Results Page
    Route::get('/results/{id}', [LeadController::class, 'results']);

    // History
    Route::get('/history', [LeadController::class, 'history']);

    // Export
    Route::get('/export/{id}', [LeadController::class, 'export']);

    // Delete
    Route::post('/delete/{id}', [LeadController::class, 'delete']);
    Route::post('/delete-all', [LeadController::class, 'deleteAll']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔥 ADMIN ROUTES
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::post('/admin/update-user/{id}', [AdminController::class, 'updateUser']);
    Route::post('/admin/toggle-role/{id}', [AdminController::class, 'toggleRole']);
    Route::post('/admin/delete-user/{id}', [AdminController::class, 'deleteUser']);
});
Route::post('/export-filtered', [LeadController::class, 'exportFiltered']);

require __DIR__.'/auth.php';