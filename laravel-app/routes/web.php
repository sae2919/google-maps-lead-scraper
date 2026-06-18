<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Models\Lead;
use App\Models\Search;

/*
|--------------------------------------------------------------------------
| Delete All Searches
|--------------------------------------------------------------------------
*/

Route::match(['GET', 'POST'], '/delete-all-searches', function () {

    Lead::query()->delete();

    Search::query()->delete();

    return redirect()->back()->with(
        'success',
        'All searches deleted successfully.'
    );

})->name('delete.all.searches');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return view('welcome');

})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('dashboard')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/', function () {

            if (auth()->user()->role === 'admin') {

                return redirect('/admin/dashboard');
            }

            return app(
                \App\Http\Controllers\LeadController::class
            )->dashboard();

        })->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Search Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/search', [LeadController::class, 'searchPage'])
            ->name('search.page');

        Route::post('/search', [LeadController::class, 'search'])
            ->middleware('throttle:20,1')
            ->name('search.start');

        Route::get('/results/{id}', [LeadController::class, 'results'])
            ->name('results.show');

        Route::get('/history', [LeadController::class, 'history'])
            ->name('history.index');

        Route::get('/export/{id}', [LeadController::class, 'export'])
            ->name('export.leads');

        /*
        |--------------------------------------------------------------------------
        | Search Controls
        |--------------------------------------------------------------------------
        */

        Route::post('/pause-search/{id}', [LeadController::class, 'pause'])
            ->name('search.pause');

        Route::post('/resume-search/{id}', [LeadController::class, 'resume'])
            ->name('search.resume');

        Route::post('/stop-search/{id}', [LeadController::class, 'stop'])
            ->name('search.stop');

        /*
        |--------------------------------------------------------------------------
        | AI Website Generation
        |--------------------------------------------------------------------------
        */

        Route::get('/generate', [LeadController::class, 'generatePage'])
            ->name('generate.page');

        Route::post('/generate', [LeadController::class, 'generateFromForm'])
            ->name('generate.submit');

        Route::post('/generate-site/{lead}', [LeadController::class, 'generateSite'])
            ->name('site.generate');

        /*
        |--------------------------------------------------------------------------
        | Bulk Website Generator
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/generate-bulk-websites',
            [LeadController::class, 'generateBulkWebsites']
        )->name('generate-bulk-websites');

        Route::get('/generated-sites', [LeadController::class, 'generatedSites'])
            ->name('site.index');

        Route::get(
            '/generated-site/{slug}',
            [LeadController::class, 'viewGeneratedSite']
        )->name('site.view');

        Route::post('/generated-site/{site}/regenerate', [LeadController::class, 'regenerateSite'])
            ->name('site.regenerate');

        Route::delete('/delete-search/{id}', [LeadController::class, 'deleteSearch'])
            ->name('search.delete');

        Route::post('/delete-search/{id}', [LeadController::class, 'deleteSearch'])
            ->name('search.delete');

        Route::get('/api/dashboard-stats', [LeadController::class, 'dashboardStats'])
            ->name('dashboard.stats');
    });
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */

        Route::post('/update-user/{id}', [AdminController::class, 'updateUser'])
            ->name('user.update');

        Route::post('/toggle-role/{id}', [AdminController::class, 'toggleRole'])
            ->name('user.toggleRole');

        Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser'])
            ->name('user.delete');

    });

/*
|--------------------------------------------------------------------------
| SEO Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/sitemap.xml',
    [LeadController::class, 'sitemap']
);

Route::get(
    '/robots.txt',
    function () {

        return response(
            "User-agent: *\n"
            . "Allow: /\n\n"
            . "Sitemap: "
            . url('/sitemap.xml'),
            200,
            ['Content-Type' => 'text/plain']
        );

    }
);

Route::get(
    '/{city}/{service}',
    [LeadController::class, 'seoPage']
)->where([
    'city' => '[A-Za-z0-9\-]+',
    'service' => '[A-Za-z0-9\-]+',
]);

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';