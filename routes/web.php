<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (tidak perlu login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Landing page
    Route::get('/', function () {
        return view('landing');
    })->name('landing');

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (wajib login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Units
    Route::get('/units/{id}', [UnitController::class, 'show'])->name('units.show');

    // Lessons
    Route::get('/lessons/{id}', [LessonController::class, 'show'])->name('lessons.show');

    // Game (Word Matching)
    Route::get('/game/{lesson_id}/play', [GameController::class, 'play'])->name('game.play');
    Route::post('/game/{lesson_id}/submit', [GameController::class, 'submit'])->name('game.submit');
    Route::get('/game/{lesson_id}/result', [GameController::class, 'result'])->name('game.result');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
