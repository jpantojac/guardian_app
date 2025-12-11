<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\IncidentWebController;

Route::get('/', function () {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    session(['captcha_result' => $num1 + $num2]);
    return view('dashboard', compact('num1', 'num2'));
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

    // Profile routes
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
