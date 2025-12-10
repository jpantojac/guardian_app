<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\IncidentWebController;

Route::get('/', function () {
    return view('dashboard');
})->name('home');

// Auth routes
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/report', [IncidentWebController::class, 'create'])->name('report.create');
    Route::post('/report', [IncidentWebController::class, 'store'])->name('report.store');
});
