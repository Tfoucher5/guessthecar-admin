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
            'total_points_earned' => UserScore::sum('total_points') ?? 0
        ];

        // Répartition par difficulté
        $difficultyStats = CarModel::selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->get()
            ->pluck('count', 'difficulty_level')
            ->toArray();

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

        // Marques les plus populaires (par nombre de sessions) - CORRIGÉ
        $topBrands = Brand::selectRaw('
                brands.id,
                brands.name,
                brands.logo_url,
                COUNT(game_sessions.id) as sessions_count
            ')
            ->leftJoin('models', 'brands.id', '=', 'models.brand_id')  // CORRIGÉ: models au lieu de car_models
            ->leftJoin('game_sessions', 'models.id', '=', 'game_sessions.car_id')
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
                COUNT(*) as sessions_count,
                COUNT(DISTINCT user_id) as unique_players,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_sessions
            ')
            ->where('started_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Voitures les plus trouvées - CORRIGÉ
        $mostFoundCars = UserCarFound::selectRaw('
                car_id,
                COUNT(*) as found_count,
                AVG(attempts_used) as avg_attempts
            ')
            ->with(['carModel.brand'])
            ->groupBy('car_id')
            ->orderBy('found_count', 'desc')
            ->limit(5)
            ->get();

        // Activité récente
        $recentActivity = collect();

        // Ajouter les voitures récemment trouvées
        $recentCarsFound = UserCarFound::with(['userScore', 'carModel.brand'])
            ->orderBy('found_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentCarsFound as $carFound) {
            $recentActivity->push([
                'type' => 'car_found',
                'icon' => 'bi-trophy',
                'color' => 'success',
                'message' => ($carFound->userScore->username ?? 'Joueur inconnu') . ' a trouvé',
                'details' => ($carFound->carModel->brand->name ?? 'Marque') . ' ' . ($carFound->carModel->name ?? 'Modèle'),
                'time' => $carFound->found_at,
                'time_human' => $carFound->found_at->diffForHumans(),
            ]);
        }

        // Ajouter les nouveaux joueurs
        $newPlayers = UserScore::orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($newPlayers as $player) {
            $recentActivity->push([
                'type' => 'new_player',
                'icon' => 'bi-person-plus',
                'color' => 'primary',
                'message' => 'Nouveau joueur inscrit',
                'details' => $player->username,
                'time' => $player->created_at,
                'time_human' => $player->created_at->diffForHumans(),
            ]);
        }

        // Trier par date
        $recentActivity = $recentActivity->sortByDesc('time')->take(10);

        // Statistiques du jour
        $todayStats = [
            'sessions' => GameSession::whereDate('started_at', today())->count(),
            'completed_sessions' => GameSession::whereDate('started_at', today())
                ->where('completed', true)->count(),
            'new_players' => UserScore::whereDate('created_at', today())->count(),
            'cars_found' => UserCarFound::whereDate('found_at', today())->count(),
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
     * API pour vérifier le statut de l'API Discord (simulation)
     */
    public function apiStatus()
    {
        // Simulation du statut de l'API
        $status = [
            'discord_api' => 'online',
            'database' => 'online',
            'cache' => 'online',
            'storage' => 'online',
            'last_check' => now()->toISOString(),
        ];

        return response()->json($status);
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
            'total_players' => UserScore::count(),
            'total_points' => UserScore::sum('total_points') ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Récupérer les sessions par heure pour le graphique
     */
    private function getSessionsByHour()
    {
        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $data[$i] = 0;
        }

        $sessions = GameSession::selectRaw('
                HOUR(started_at) as hour,
                COUNT(*) as count
            ')
            ->whereDate('started_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        foreach ($sessions as $session) {
            $data[$session->hour] = $session->count;
        }

        return $data;
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
                'message' => "Baisse d'activité détectée (-" .
                    round((1 - $todaySessions / $yesterdaySessions) * 100) . "%)",
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