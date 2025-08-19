<?php

// routes/web.php
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\CarsFoundController;
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

    // Dashboard principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api-status', [DashboardController::class, 'apiStatus'])->name('api.status');

    // Gestion des marques
    Route::resource('brands', BrandController::class);
    Route::get('brands/{brand}/models', [BrandController::class, 'models'])->name('brands.models');

    // Gestion des modèles
    Route::resource('models', CarModelController::class);
    Route::post('models/bulk-import', [CarModelController::class, 'bulkImport'])->name('models.bulk-import');
    Route::get('models/export', [CarModelController::class, 'export'])->name('models.export');

    // Gestion des joueurs
    Route::get('players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('players/{userScore}', [PlayerController::class, 'show'])->name('players.show');
    Route::get('players/{userScore}/edit', [PlayerController::class, 'edit'])->name('players.edit');
    Route::put('players/{userScore}', [PlayerController::class, 'update'])->name('players.update');
    Route::delete('players/{userScore}', [PlayerController::class, 'destroy'])->name('players.destroy');

    // Sessions de jeu
    Route::get('sessions', [PlayerController::class, 'sessions'])->name('sessions.index');
    Route::get('sessions/{gameSession}', [PlayerController::class, 'sessionShow'])->name('sessions.show');
    Route::delete('sessions/{gameSession}', [PlayerController::class, 'sessionDestroy'])->name('sessions.destroy');

    // Classement global
    Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('leaderboard/guilds', [LeaderboardController::class, 'guilds'])->name('leaderboard.guilds');
    Route::get('leaderboard/cars', [LeaderboardController::class, 'cars'])->name('leaderboard.cars');
    Route::get('leaderboard/records', [LeaderboardController::class, 'records'])->name('leaderboard.records');
    Route::get('leaderboard/export', [LeaderboardController::class, 'export'])->name('leaderboard.export');
    Route::get('leaderboard/api', [LeaderboardController::class, 'api'])->name('leaderboard.api');

    // Voitures trouvées / Collection
    Route::get('cars-found', [CarsFoundController::class, 'index'])->name('cars-found.index');
    Route::get('cars-found/{userCarFound}', [CarsFoundController::class, 'show'])->name('cars-found.show');
    Route::get('cars-found-statistics', [CarsFoundController::class, 'statistics'])->name('cars-found.statistics');

    // Statistiques avancées
    Route::get('statistics', function () {
        return view('admin.statistics.index');
    })->name('statistics.index');

    // Outils d'administration
    Route::prefix('tools')->name('tools.')->group(function () {
        Route::get('database-status', function () {
            return view('admin.tools.database-status');
        })->name('database-status');

        Route::get('system-info', function () {
            return view('admin.tools.system-info');
        })->name('system-info');

        Route::post('clear-cache', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            return back()->with('success', 'Cache vidé avec succès');
        })->name('clear-cache');
    });

});

// Profile routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});