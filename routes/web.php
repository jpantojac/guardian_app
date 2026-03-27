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
    Route::post('/api/incidents/{incident}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::post('/api/comments/{comment}/reactions', [\App\Http\Controllers\CommentController::class, 'toggleReaction'])->name('comments.reaction');

    // Profile routes
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/incidents', [\App\Http\Controllers\ProfileController::class, 'incidents'])->name('profile.incidents');

    // Internal API routes (using Web Session)
    Route::get('/api/incidents/{incident}', [\App\Http\Controllers\Api\IncidentController::class, 'show']);
});

// Admin routes
Route::middleware(['auth', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    });
});

// Legal routes (Ley 1581 and T&C)
Route::view('/privacidad', 'legal.privacidad')->name('legal.privacidad');
Route::view('/terminos', 'legal.terminos')->name('legal.terminos');
