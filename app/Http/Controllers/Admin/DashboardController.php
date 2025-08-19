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
            'active_games' => GameSession::where('completed', false)->where('abandoned', false)->count(),
            'completed_games' => GameSession::where('completed', true)->count(),
            'total_cars_found' => UserCarFound::count(),
            'total_points_earned' => UserScore::sum('total_points')
        ];

        // Répartition par difficulté
        $difficultyStats = CarModel::selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->get()
            ->pluck('count', 'difficulty_level');

        // Répartition par pays des marques
        $countryStats = Brand::selectRaw('country, COUNT(*) as count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top joueurs
        $topPlayers = UserScore::orderBy('total_points', 'desc')
            ->limit(10)
            ->get();

        // Marques les plus populaires (par nombre de sessions)
        $topBrands = Brand::selectRaw('
                brands.id,
                brands.name,
                brands.logo_url,
                COUNT(game_sessions.id) as sessions_count
            ')
            ->join('car_models', 'brands.id', '=', 'car_models.brand_id')
            ->join('game_sessions', 'car_models.id', '=', 'game_sessions.car_id')
            ->groupBy('brands.id', 'brands.name', 'brands.logo_url')
            ->orderBy('sessions_count', 'desc')
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
                AVG(IFNULL(duration_seconds, 0)) as avg_duration,
                SUM(IFNULL(points_earned, 0)) as total_points
            ')
            ->where('started_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Voitures les plus trouvées
        $mostFoundCars = UserCarFound::selectRaw('
                car_id,
                COUNT(*) as found_count
            ')
            ->with(['carModel.brand'])
            ->groupBy('car_id')
            ->orderBy('found_count', 'desc')
            ->limit(10)
            ->get();

        // Activité récente (dernières 24h)
        $recentActivity = collect();

        // Nouvelles sessions
        GameSession::where('started_at', '>=', now()->subDay())
            ->with(['userScore', 'carModel.brand'])
            ->get()
            ->each(function ($session) use ($recentActivity) {
                $recentActivity->push([
                    'type' => 'session_started',
                    'message' => $session->userScore->username . ' a commencé une session',
                    'details' => ($session->carModel->brand->name ?? '') . ' ' . ($session->carModel->name ?? ''),
                    'time' => $session->started_at,
                    'icon' => 'bi-play-circle'
                ]);
            });

        // Voitures trouvées récemment
        UserCarFound::where('found_at', '>=', now()->subDay())
            ->with(['userScore', 'carModel.brand'])
            ->get()
            ->each(function ($carFound) use ($recentActivity) {
                $recentActivity->push([
                    'type' => 'car_found',
                    'message' => $carFound->userScore->username . ' a trouvé une voiture',
                    'details' => ($carFound->carModel->brand->name ?? '') . ' ' . ($carFound->carModel->name ?? ''),
                    'time' => $carFound->found_at,
                    'icon' => 'bi-star-fill'
                ]);
            });

        $recentActivity = $recentActivity->sortByDesc('time')->take(15);

        // Statistiques d'aujourd'hui
        $todayStats = [
            'sessions' => GameSession::whereDate('started_at', today())->count(),
            'new_players' => UserScore::whereDate('created_at', today())->count(),
            'cars_found' => UserCarFound::whereDate('found_at', today())->count(),
            'points_earned' => GameSession::whereDate('started_at', today())->sum('points_earned') ?? 0
        ];

        // Données pour les graphiques
        $chartData = [
            'sessions_by_hour' => $this->getSessionsByHour(),
            'difficulty_distribution' => $difficultyStats,
            'monthly_growth' => $monthlyStats,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'difficultyStats',
            'countryStats',
            'topPlayers',
            'topBrands',
            'recentGames',
            'monthlyStats',
            'mostFoundCars',
            'recentActivity',
            'todayStats',
            'chartData'
        ));
    }

    /**
     * API pour récupérer les statistiques en temps réel
     */
    public function stats()
    {
        $stats = [
            'active_sessions' => GameSession::where('completed', false)->where('abandoned', false)->count(),
            'players_online' => GameSession::where('started_at', '>=', now()->subMinutes(30))
                ->distinct('user_id')
                ->count(),
            'sessions_today' => GameSession::whereDate('started_at', today())->count(),
            'cars_found_today' => UserCarFound::whereDate('found_at', today())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Récupérer les sessions par heure pour le graphique
     */
    private function getSessionsByHour()
    {
        return GameSession::selectRaw('
                HOUR(started_at) as hour,
                COUNT(*) as count
            ')
            ->whereDate('started_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();
    }

    /**
     * Notifications pour l'admin
     */
    public function notifications()
    {
        $notifications = [];

        // Sessions bloquées (plus de 2h)
        $stuckSessions = GameSession::where('completed', false)
            ->where('abandoned', false)
            ->where('started_at', '<', now()->subHours(2))
            ->count();

        if ($stuckSessions > 0) {
            $notifications[] = [
                'type' => 'warning',
                'message' => "$stuckSessions session(s) bloquée(s) depuis plus de 2h",
                'action' => route('admin.sessions.index', ['status' => 'active'])
            ];
        }

        // Baisse d'activité
        $todaySessions = GameSession::whereDate('started_at', today())->count();
        $yesterdaySessions = GameSession::whereDate('started_at', yesterday())->count();

        if ($yesterdaySessions > 0 && $todaySessions < ($yesterdaySessions * 0.5)) {
            $notifications[] = [
                'type' => 'info',
                'message' => "Baisse d'activité détectée (-" . round((1 - $todaySessions / $yesterdaySessions) * 100) . "%)",
                'action' => route('admin.statistics.index')
            ];
        }

        // Nouveaux joueurs
        $newPlayers = UserScore::whereDate('created_at', today())->count();
        if ($newPlayers > 0) {
            $notifications[] = [
                'type' => 'success',
                'message' => "$newPlayers nouveau(x) joueur(s) aujourd'hui",
                'action' => route('admin.players.index')
            ];
        }

        return response()->json($notifications);
    }

    /**
     * Mise à jour rapide des données du dashboard
     */
    public function refresh()
    {
        $data = [
            'active_sessions' => GameSession::where('completed', false)->where('abandoned', false)->count(),
            'sessions_today' => GameSession::whereDate('started_at', today())->count(),
            'cars_found_today' => UserCarFound::whereDate('found_at', today())->count(),
            'new_players_today' => UserScore::whereDate('created_at', today())->count(),
            'last_activity' => GameSession::latest('started_at')->first()?->started_at?->diffForHumans(),
        ];

        return response()->json($data);
    }
}