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
Route::post('/save-lead',    [LeadController::class, 'saveLead']);
Route::post('/update-total', [LeadController::class, 'updateTotal']);

// ── FRONTEND POLLING ──────────────────────────────────────────────────────
Route::get('/progress/{id}', [LeadController::class, 'progress']);
Route::get('/leads/{id}',    [LeadController::class, 'getLeads']);

// ── SCRAPER STATUS (Python polls this every 10 items) ─────────────────────
// Returns: { "stopped": bool, "paused": bool, "status": "RUNNING|PAUSED|STOPPED" }
Route::get('/status/{id}',   [LeadController::class, 'checkStatus']);

// ── SCRAPER CONTROLS (UI buttons → these routes) ──────────────────────────
Route::post('/stop/{id}',    [LeadController::class, 'stop']);
Route::post('/pause/{id}',   [LeadController::class, 'pause']);
Route::post('/resume/{id}',  [LeadController::class, 'resume']);

// ── AI WEBSITE GENERATION ─────────────────────────────────────────────────
Route::post('/generate-bulk-websites', [LeadController::class, 'generateBulkWebsites']);