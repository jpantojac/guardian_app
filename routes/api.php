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
});

Route::get('/incidents', [IncidentController::class, 'index']);
Route::get('/geojson', [GeoJSONController::class, 'index']);
Route::get('/localidades-geojson', [GeoJSONController::class, 'localidades']);
