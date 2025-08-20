<?php

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
    Route::get('players/{player}', [PlayerController::class, 'show'])->name('players.show');
    Route::get('players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
    Route::put('players/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::delete('players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');
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

    // Voitures trouvées / Collections
    Route::prefix('cars-found')->name('cars-found.')->group(function () {
        Route::get('/', [CarsFoundController::class, 'index'])->name('index');
        Route::get('create', [CarsFoundController::class, 'create'])->name('create');
        Route::post('/', [CarsFoundController::class, 'store'])->name('store');
        Route::get('statistics', [CarsFoundController::class, 'statistics'])->name('statistics'); // AVANT {id}
        Route::get('export', [CarsFoundController::class, 'export'])->name('export');
        Route::post('bulk-delete', [CarsFoundController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('api/recent', [CarsFoundController::class, 'recent'])->name('api.recent');
        Route::get('api/search', [CarsFoundController::class, 'search'])->name('api.search');
        Route::get('{id}', [CarsFoundController::class, 'show'])->name('show'); // APRÈS statistics
        Route::delete('{id}', [CarsFoundController::class, 'destroy'])->name('destroy');
    });

    // Statistiques avancées
    Route::get('statistics', function () {
        return view('admin.statistics.index');
    })->name('statistics.index');

    // Outils d'administration
    Route::prefix('tools')->name('tools.')->group(function () {
        Route::get('cache-clear', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            return redirect()->back()->with('success', 'Cache vidé avec succès');
        })->name('cache.clear');

        Route::get('optimize', function () {
            \Artisan::call('optimize');
            return redirect()->back()->with('success', 'Application optimisée avec succès');
        })->name('optimize');
    });

    // Profile utilisateur admin
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});