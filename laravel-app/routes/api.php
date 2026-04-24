<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔥 TEST ROUTE (VERY IMPORTANT FOR DEBUG)
Route::get('/ping', function () {
    return response()->json(['status' => 'API working']);
});

// 🔥 SAVE LEAD (PYTHON → LARAVEL)
Route::post('/save-lead', [LeadController::class, 'saveLead']);

// 🔥 PROGRESS (FRONTEND POLLING)
Route::get('/progress/{id}', [LeadController::class, 'progress']);

// 🔥 GET LEADS (RESULTS PAGE)
Route::get('/leads/{id}', [LeadController::class, 'getLeads']);

// 🔥 STATUS CHECK (PYTHON CONTROL)
Route::get('/status/{id}', [LeadController::class, 'checkStatus']);

// 🔥 CONTROL ACTIONS (BUTTONS)
Route::post('/pause/{id}', [LeadController::class, 'pause']);
Route::post('/resume/{id}', [LeadController::class, 'resume']);
Route::post('/stop/{id}', [LeadController::class, 'stop']);
Route::post('/update-total', [LeadController::class, 'updateTotal']);