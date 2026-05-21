<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── DEBUG ──────────────────────────────────────────────────────────────────
Route::get('/ping', function () {
    return response()->json(['status' => 'API working']);
});

// ── LEAD DATA (Python → Laravel) ──────────────────────────────────────────
// No auth — Python script calls these directly without session
Route::post('/save-lead',    [LeadController::class, 'saveLead']);
Route::post('/update-total', [LeadController::class, 'updateTotal']);

// ── FRONTEND POLLING + CONTROLS (needs web session for auth()->id()) ───────
Route::middleware('web')->group(function () {

    Route::get('/progress/{id}', [LeadController::class, 'progress']);
    Route::get('/leads/{id}',    [LeadController::class, 'getLeads']);
    Route::get('/status/{id}',   [LeadController::class, 'checkStatus']);

    Route::post('/stop/{id}',    [LeadController::class, 'stop']);
    Route::post('/pause/{id}',   [LeadController::class, 'pause']);
    Route::post('/resume/{id}',  [LeadController::class, 'resume']);

    Route::post('/generate-bulk-websites', [LeadController::class, 'generateBulkWebsites']);

});