<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\GeoJSONController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::post('/incidents', [IncidentController::class, 'store']);
    // Add other protected routes
});

Route::get('/incidents', [IncidentController::class, 'index']);
Route::get('/incidents/{incident}', [IncidentController::class, 'show']);
Route::get('/geojson', [GeoJSONController::class, 'index']);
