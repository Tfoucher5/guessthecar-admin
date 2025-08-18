<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Routes d'authentification (Breeze)
require __DIR__ . '/auth.php';

// Routes admin protégées
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion des marques
    Route::resource('brands', BrandController::class);

    // Gestion des modèles
    Route::resource('models', CarModelController::class);

    // Gestion des joueurs (lecture seule)
    // Gestion des joueurs - AVEC édition
    Route::get('players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('players/{userScore}', [PlayerController::class, 'show'])->name('players.show');
    Route::get('players/{userScore}/edit', [PlayerController::class, 'edit'])->name('players.edit');
    Route::put('players/{userScore}', [PlayerController::class, 'update'])->name('players.update');

    // Sessions de jeu
    Route::get('sessions', [PlayerController::class, 'sessions'])->name('sessions.index');
    Route::get('sessions/{gameSession}', [PlayerController::class, 'sessionShow'])->name('sessions.show');

    // API Status
    Route::get('api-status', function () {
        $nodeApi = app(\App\Services\NodeApiService::class);
        return response()->json($nodeApi->getHealth());
    })->name('api.status');

    Route::get('discord-stats', function () {
        return view('admin.discord-stats');
    })->name('discord.stats');

});

// Profile routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});