<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WEB\Football\FootballMatchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Football Score System Routes
Route::prefix('football')->name('football.')->group(function () {
    
    // Public routes (live scores can be viewed without authentication)
    Route::get('/live-scores/{id?}', [FootballMatchController::class, 'liveScore'])->name('live-scores');
    Route::get('/matches/{id}/live-data', [FootballMatchController::class, 'getLiveMatchData'])->name('matches.live-data');
    
    // Protected routes (require authentication for management)
    Route::middleware(['auth'])->group(function () {
        
        // Main football matches resource routes
        Route::resource('matches', FootballMatchController::class)->names([
            'index' => 'matches.index',
            'create' => 'matches.create',
            'store' => 'matches.store',
            'show' => 'matches.show',
            'edit' => 'matches.edit',
            'update' => 'matches.update',
            'destroy' => 'matches.destroy'
        ]);
        
        // Additional match management routes
        Route::post('/matches/{id}/update-score', [FootballMatchController::class, 'updateScore'])->name('matches.update-score');
        Route::post('/matches/{id}/update-status', [FootballMatchController::class, 'updateStatus'])->name('matches.update-status');
        Route::post('/matches/{id}/update-time', [FootballMatchController::class, 'updateMatchTime'])->name('matches.update-time');
        Route::post('/matches/{id}/simulate-goal', [FootballMatchController::class, 'simulateGoal'])->name('matches.simulate-goal');
        
        // AJAX routes for DataTables
        Route::get('/matches-data', [FootballMatchController::class, 'getAjaxMatchData'])->name('matches.ajax-data');
        
        // Live viewing and control routes
        Route::get('/matches/{id}/live', [FootballMatchController::class, 'liveScore'])->name('matches.live');
        Route::get('/matches/{id}/control-panel', [FootballMatchController::class, 'controlPanel'])->name('matches.control-panel');
    });
});

require __DIR__.'/auth.php';
