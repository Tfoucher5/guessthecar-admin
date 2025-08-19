<?php

// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\UserScore;
use App\Models\GameSession;
use App\Models\UserCarFound;
use App\Models\LeaderboardView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_brands' => Brand::count(),
            'total_models' => CarModel::count(),
            'total_players' => UserScore::count(),
            'total_games' => GameSession::count(),
            'active_games' => GameSession::inProgress()->count(),
            'completed_games' => GameSession::completed()->count(),
            'total_cars_found' => UserCarFound::count(),
            'total_points_earned' => UserScore::sum('total_points')
        ];

        // Répartition par difficulté
        $difficultyStats = CarModel::selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->get()
            ->pluck('count', 'difficulty_level');

        // Répartition par pays
        $countryStats = Brand::selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Statistiques des sessions de jeu
        $gameStats = GameSession::getStats();

        // Top joueurs avec classement
        $topPlayers = LeaderboardView::orderBy('ranking')
            ->limit(10)
            ->get();

        // Marques les plus populaires (par nombre de parties)
        $topBrands = Brand::withCount(['gameSessions'])
            ->orderBy('game_sessions_count', 'desc')
            ->limit(5)
            ->get();

        // Sessions récentes
        $recentGames = GameSession::with(['carModel.brand', 'userScore'])
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get();

        // Statistiques par mois (6 derniers mois)
        $monthlyStats = GameSession::selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                COUNT(*) as games_count,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_count,
                AVG(duration_seconds) as avg_duration
            ')
            ->where('started_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Voitures les plus trouvées
        $mostFoundCars = UserCarFound::selectRaw('
                car_id,
                COUNT(*) as found_count,
                AVG(attempts_used) as avg_attempts,
                AVG(time_taken) as avg_time
            ')
            ->with('carModel.brand')
            ->groupBy('car_id')
            ->orderBy('found_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'difficultyStats',
            'countryStats',
            'gameStats',
            'topPlayers',
            'topBrands',
            'recentGames',
            'monthlyStats',
            'mostFoundCars'
        ));
    }

    public function apiStatus()
    {
        try {
            $response = \Http::timeout(10)->get('http://localhost:3000/api/health');

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'healthy',
                    'data' => $data,
                    'timestamp' => now()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'API not responding'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 503);
        }
    }
}