<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\UserScore;
use App\Models\GameSession;
use App\Models\UserCarFound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $generalStats = [
            'total_brands' => Brand::count(),
            'total_models' => CarModel::count(),
            'total_players' => UserScore::count(),
            'total_sessions' => GameSession::count(),
            'total_collections' => UserCarFound::count(),
            'completion_rate' => GameSession::count() > 0
                ? round((GameSession::where('completed', true)->count() / GameSession::count()) * 100, 2)
                : 0,
            'total_points' => UserScore::sum('total_points'),
            'average_session_duration' => GameSession::whereNotNull('duration_seconds')->avg('duration_seconds'),
        ];

        // Statistiques par période
        $periodStats = [
            'today' => $this->getTodayStats(),
            'week' => $this->getWeekStats(),
            'month' => $this->getMonthStats(),
            'year' => $this->getYearStats(),
        ];

        // Top des marques les plus populaires
        $topBrands = Brand::selectRaw('
                brands.id,
                brands.name,
                brands.country,
                brands.logo_url,
                COUNT(game_sessions.id) as sessions_count,
                COUNT(DISTINCT user_cars_found.user_id) as unique_players
            ')
            ->leftJoin('car_models', 'brands.id', '=', 'car_models.brand_id')
            ->leftJoin('game_sessions', 'car_models.id', '=', 'game_sessions.car_id')
            ->leftJoin('user_cars_found', 'car_models.id', '=', 'user_cars_found.car_id')
            ->groupBy('brands.id', 'brands.name', 'brands.country', 'brands.logo_url')
            ->orderBy('sessions_count', 'desc')
            ->limit(10)
            ->get();

        // Répartition par difficulté
        $difficultyStats = CarModel::selectRaw('
                difficulty_level,
                COUNT(*) as total_models,
                COUNT(DISTINCT user_cars_found.car_id) as found_models,
                AVG(user_cars_found.attempts_used) as avg_attempts
            ')
            ->leftJoin('user_cars_found', 'car_models.id', '=', 'user_cars_found.car_id')
            ->groupBy('difficulty_level')
            ->get();

        // Évolution mensuelle (12 derniers mois)
        $monthlyEvolution = GameSession::selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                COUNT(*) as sessions_count,
                COUNT(DISTINCT user_id) as unique_players,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_sessions
            ')
            ->where('started_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top joueurs
        $topPlayers = UserScore::selectRaw('
                username,
                total_points,
                games_played,
                games_won,
                best_streak,
                created_at,
                ROUND((games_won / NULLIF(games_played, 0)) * 100, 2) as win_rate
            ')
            ->orderBy('total_points', 'desc')
            ->limit(10)
            ->get();

        // Statistiques par pays
        $countryStats = Brand::selectRaw('
                country,
                COUNT(*) as brands_count,
                COUNT(DISTINCT car_models.id) as models_count,
                COUNT(DISTINCT game_sessions.id) as sessions_count
            ')
            ->leftJoin('car_models', 'brands.id', '=', 'car_models.brand_id')
            ->leftJoin('game_sessions', 'car_models.id', '=', 'game_sessions.car_id')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('sessions_count', 'desc')
            ->limit(10)
            ->get();

        // Modèles les plus difficiles à trouver
        $hardestModels = CarModel::selectRaw('
                car_models.id,
                car_models.name,
                brands.name as brand_name,
                car_models.difficulty_level,
                COUNT(game_sessions.id) as total_attempts,
                COUNT(user_cars_found.id) as successful_finds,
                ROUND((COUNT(user_cars_found.id) / NULLIF(COUNT(game_sessions.id), 0)) * 100, 2) as success_rate
            ')
            ->join('brands', 'car_models.brand_id', '=', 'brands.id')
            ->leftJoin('game_sessions', 'car_models.id', '=', 'game_sessions.car_id')
            ->leftJoin('user_cars_found', 'car_models.id', '=', 'user_cars_found.car_id')
            ->groupBy('car_models.id', 'car_models.name', 'brands.name', 'car_models.difficulty_level')
            ->having('total_attempts', '>=', 5) // Au moins 5 tentatives
            ->orderBy('success_rate', 'asc')
            ->limit(10)
            ->get();

        // Activité récente (24h)
        $recentActivity = [
            'new_players' => UserScore::whereDate('created_at', today())->count(),
            'active_sessions' => GameSession::where('started_at', '>=', now()->subHours(24))->count(),
            'cars_found' => UserCarFound::whereDate('found_at', today())->count(),
            'peak_hour' => $this->getPeakHour(),
        ];

        return view('admin.statistics.index', compact(
            'generalStats',
            'periodStats',
            'topBrands',
            'difficultyStats',
            'monthlyEvolution',
            'topPlayers',
            'countryStats',
            'hardestModels',
            'recentActivity'
        ));
    }

    /**
     * API endpoint pour récupérer les statistiques en temps réel
     */
    public function api(Request $request)
    {
        $period = $request->get('period', 'week');

        $stats = match ($period) {
            'today' => $this->getTodayStats(),
            'week' => $this->getWeekStats(),
            'month' => $this->getMonthStats(),
            'year' => $this->getYearStats(),
            default => $this->getWeekStats(),
        };

        return response()->json($stats);
    }

    /**
     * Export des statistiques en CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'general');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="statistics_' . $type . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'players':
                    $this->exportPlayersStats($file);
                    break;
                case 'brands':
                    $this->exportBrandsStats($file);
                    break;
                case 'sessions':
                    $this->exportSessionsStats($file);
                    break;
                default:
                    $this->exportGeneralStats($file);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Statistiques d'aujourd'hui
     */
    private function getTodayStats()
    {
        return [
            'sessions' => GameSession::whereDate('started_at', today())->count(),
            'players' => GameSession::whereDate('started_at', today())->distinct('user_id')->count(),
            'completed' => GameSession::whereDate('started_at', today())->where('completed', true)->count(),
            'points_earned' => GameSession::whereDate('started_at', today())->sum('points_earned'),
            'cars_found' => UserCarFound::whereDate('found_at', today())->count(),
        ];
    }

    /**
     * Statistiques de la semaine
     */
    private function getWeekStats()
    {
        return [
            'sessions' => GameSession::where('started_at', '>=', now()->subWeek())->count(),
            'players' => GameSession::where('started_at', '>=', now()->subWeek())->distinct('user_id')->count(),
            'completed' => GameSession::where('started_at', '>=', now()->subWeek())->where('completed', true)->count(),
            'points_earned' => GameSession::where('started_at', '>=', now()->subWeek())->sum('points_earned'),
            'cars_found' => UserCarFound::where('found_at', '>=', now()->subWeek())->count(),
        ];
    }

    /**
     * Statistiques du mois
     */
    private function getMonthStats()
    {
        return [
            'sessions' => GameSession::where('started_at', '>=', now()->subMonth())->count(),
            'players' => GameSession::where('started_at', '>=', now()->subMonth())->distinct('user_id')->count(),
            'completed' => GameSession::where('started_at', '>=', now()->subMonth())->where('completed', true)->count(),
            'points_earned' => GameSession::where('started_at', '>=', now()->subMonth())->sum('points_earned'),
            'cars_found' => UserCarFound::where('found_at', '>=', now()->subMonth())->count(),
        ];
    }

    /**
     * Statistiques de l'année
     */
    private function getYearStats()
    {
        return [
            'sessions' => GameSession::where('started_at', '>=', now()->subYear())->count(),
            'players' => GameSession::where('started_at', '>=', now()->subYear())->distinct('user_id')->count(),
            'completed' => GameSession::where('started_at', '>=', now()->subYear())->where('completed', true)->count(),
            'points_earned' => GameSession::where('started_at', '>=', now()->subYear())->sum('points_earned'),
            'cars_found' => UserCarFound::where('found_at', '>=', now()->subYear())->count(),
        ];
    }

    /**
     * Trouver l'heure de pointe
     */
    private function getPeakHour()
    {
        $hourStats = GameSession::selectRaw('HOUR(started_at) as hour, COUNT(*) as count')
            ->where('started_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        return $hourStats ? $hourStats->hour . 'h' : 'N/A';
    }

    /**
     * Export des statistiques générales
     */
    private function exportGeneralStats($file)
    {
        fputcsv($file, ['Statistique', 'Valeur']);

        $stats = [
            'Total Marques' => Brand::count(),
            'Total Modèles' => CarModel::count(),
            'Total Joueurs' => UserScore::count(),
            'Total Sessions' => GameSession::count(),
            'Sessions Complétées' => GameSession::where('completed', true)->count(),
            'Total Points' => UserScore::sum('total_points'),
            'Voitures Trouvées' => UserCarFound::count(),
        ];

        foreach ($stats as $label => $value) {
            fputcsv($file, [$label, $value]);
        }
    }

    /**
     * Export des statistiques des joueurs
     */
    private function exportPlayersStats($file)
    {
        fputcsv($file, ['Username', 'Points Totaux', 'Parties Jouées', 'Parties Gagnées', 'Taux de Réussite', 'Meilleure Série']);

        UserScore::orderBy('total_points', 'desc')->chunk(100, function ($players) use ($file) {
            foreach ($players as $player) {
                fputcsv($file, [
                    $player->username,
                    $player->total_points,
                    $player->games_played,
                    $player->games_won,
                    $player->games_played > 0 ? round(($player->games_won / $player->games_played) * 100, 2) . '%' : '0%',
                    $player->best_streak,
                ]);
            }
        });
    }

    /**
     * Export des statistiques des marques
     */
    private function exportBrandsStats($file)
    {
        fputcsv($file, ['Marque', 'Pays', 'Nombre de Modèles', 'Sessions Totales', 'Joueurs Uniques']);

        Brand::with(['models'])->chunk(50, function ($brands) use ($file) {
            foreach ($brands as $brand) {
                $sessionsCount = GameSession::whereHas('carModel', function ($q) use ($brand) {
                    $q->where('brand_id', $brand->id);
                })->count();

                $uniquePlayersCount = GameSession::whereHas('carModel', function ($q) use ($brand) {
                    $q->where('brand_id', $brand->id);
                })->distinct('user_id')->count();

                fputcsv($file, [
                    $brand->name,
                    $brand->country ?? 'N/A',
                    $brand->models->count(),
                    $sessionsCount,
                    $uniquePlayersCount,
                ]);
            }
        });
    }

    /**
     * Export des statistiques des sessions
     */
    private function exportSessionsStats($file)
    {
        fputcsv($file, ['Date', 'Sessions Totales', 'Sessions Complétées', 'Taux de Complétion', 'Joueurs Actifs']);

        $stats = GameSession::selectRaw('
                DATE(started_at) as date,
                COUNT(*) as total_sessions,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_sessions,
                COUNT(DISTINCT user_id) as unique_players
            ')
            ->where('started_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        foreach ($stats as $stat) {
            $completionRate = $stat->total_sessions > 0
                ? round(($stat->completed_sessions / $stat->total_sessions) * 100, 2) . '%'
                : '0%';

            fputcsv($file, [
                $stat->date,
                $stat->total_sessions,
                $stat->completed_sessions,
                $completionRate,
                $stat->unique_players,
            ]);
        }
    }
}