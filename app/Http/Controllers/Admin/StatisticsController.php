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
                ? (GameSession::where('completed', true)->count() / GameSession::count()) * 100
                : 0
        ];

        // Évolution mensuelle
        $monthlyEvolution = GameSession::selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                COUNT(*) as sessions,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed,
                AVG(IFNULL(duration_seconds, 0)) as avg_duration,
                SUM(IFNULL(points_earned, 0)) as total_points
            ')
            ->where('started_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Répartition par difficulté des sessions
        $difficultyStats = GameSession::join('car_models', 'game_sessions.car_id', '=', 'car_models.id')
            ->selectRaw('
                car_models.difficulty_level,
                COUNT(*) as sessions_count,
                AVG(IFNULL(game_sessions.duration_seconds, 0)) as avg_duration,
                SUM(CASE WHEN game_sessions.completed = 1 THEN 1 ELSE 0 END) as completed_count
            ')
            ->groupBy('car_models.difficulty_level')
            ->get();

        // Performance par pays
        $countryPerformance = DB::table('brands')
            ->join('car_models', 'brands.id', '=', 'car_models.brand_id')
            ->join('game_sessions', 'car_models.id', '=', 'game_sessions.car_id')
            ->selectRaw('
                brands.country,
                COUNT(*) as sessions_count,
                AVG(IFNULL(game_sessions.duration_seconds, 0)) as avg_duration,
                SUM(CASE WHEN game_sessions.completed = 1 THEN 1 ELSE 0 END) as success_count
            ')
            ->whereNotNull('brands.country')
            ->groupBy('brands.country')
            ->orderBy('sessions_count', 'desc')
            ->limit(10)
            ->get();

        // Top marques les plus jouées
        $topBrands = Brand::selectRaw('
                brands.id,
                brands.name,
                brands.logo_url,
                COUNT(game_sessions.id) as sessions_count,
                AVG(IFNULL(game_sessions.duration_seconds, 0)) as avg_duration
            ')
            ->join('car_models', 'brands.id', '=', 'car_models.brand_id')
            ->join('game_sessions', 'car_models.id', '=', 'game_sessions.car_id')
            ->groupBy('brands.id', 'brands.name', 'brands.logo_url')
            ->orderBy('sessions_count', 'desc')
            ->limit(10)
            ->get();

        // Statistiques de performance des joueurs
        $playerStats = [
            'avg_points_per_player' => UserScore::avg('total_points') ?? 0,
            'top_performer' => UserScore::orderBy('total_points', 'desc')->first(),
            'most_active' => UserScore::orderBy('games_played', 'desc')->first(),
            'best_success_rate' => UserScore::orderBy('success_rate', 'desc')->first()
        ];

        return view('admin.statistics.index', compact(
            'generalStats',
            'monthlyEvolution',
            'difficultyStats',
            'countryPerformance',
            'topBrands',
            'playerStats'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'players');

        switch ($type) {
            case 'players':
                return $this->exportPlayers();
            case 'sessions':
                return $this->exportSessions();
            case 'collections':
                return $this->exportCollections();
            default:
                return back()->with('error', 'Type d\'export invalide');
        }
    }

    private function exportPlayers()
    {
        $players = UserScore::with(['gameSessions', 'userCarsFound'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="players_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($players) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Username',
                'Guild ID',
                'Total Points',
                'Games Played',
                'Games Won',
                'Success Rate',
                'Best Streak',
                'Cars Found',
                'Created At'
            ]);

            // Données
            foreach ($players as $player) {
                fputcsv($file, [
                    $player->user_id,
                    $player->username,
                    $player->guild_id,
                    $player->total_points,
                    $player->games_played,
                    $player->games_won,
                    $player->success_rate,
                    $player->best_streak,
                    $player->userCarsFound->count(),
                    $player->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportSessions()
    {
        $sessions = GameSession::with(['carModel.brand', 'userScore'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sessions_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($sessions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Session ID',
                'Player',
                'Brand',
                'Model',
                'Started At',
                'Duration (seconds)',
                'Completed',
                'Points Earned',
                'Guild ID'
            ]);

            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->id,
                    $session->userScore->username ?? 'Unknown',
                    $session->carModel->brand->name ?? 'N/A',
                    $session->carModel->name ?? 'N/A',
                    $session->started_at->format('Y-m-d H:i:s'),
                    $session->duration_seconds,
                    $session->completed ? 'Yes' : 'No',
                    $session->points_earned,
                    $session->guild_id
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCollections()
    {
        $collections = UserCarFound::with(['userScore', 'carModel.brand'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="collections_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($collections) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Player',
                'Brand',
                'Model',
                'Found At',
                'Attempts Used',
                'Time Taken',
                'Guild ID'
            ]);

            foreach ($collections as $item) {
                fputcsv($file, [
                    $item->userScore->username ?? 'Unknown',
                    $item->carModel->brand->name ?? 'N/A',
                    $item->carModel->name ?? 'N/A',
                    $item->found_at->format('Y-m-d H:i:s'),
                    $item->attempts_used,
                    $item->time_taken,
                    $item->guild_id
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API endpoint pour récupérer les statistiques en temps réel
     */
    public function api(Request $request)
    {
        $period = $request->get('period', 'week');

        $stats = [];

        switch ($period) {
            case 'today':
                $stats = $this->getTodayStats();
                break;
            case 'week':
                $stats = $this->getWeekStats();
                break;
            case 'month':
                $stats = $this->getMonthStats();
                break;
            case 'year':
                $stats = $this->getYearStats();
                break;
        }

        return response()->json($stats);
    }

    private function getTodayStats()
    {
        return [
            'sessions' => GameSession::whereDate('started_at', today())->count(),
            'players' => GameSession::whereDate('started_at', today())->distinct('user_id')->count(),
            'completed' => GameSession::whereDate('started_at', today())->where('completed', true)->count(),
            'points_earned' => GameSession::whereDate('started_at', today())->sum('points_earned')
        ];
    }

    private function getWeekStats()
    {
        return [
            'sessions' => GameSession::where('started_at', '>=', now()->subWeek())->count(),
            'players' => GameSession::where('started_at', '>=', now()->subWeek())->distinct('user_id')->count(),
            'completed' => GameSession::where('started_at', '>=', now()->subWeek())->where('completed', true)->count(),
            'points_earned' => GameSession::where('started_at', '>=', now()->subWeek())->sum('points_earned')
        ];
    }

    private function getMonthStats()
    {
        return [
            'sessions' => GameSession::where('started_at', '>=', now()->subMonth())->count(),
            'players' => GameSession::where('started_at', '>=', now()->subMonth())->distinct('user_id')->count(),
            'completed' => GameSession::where('started_at', '>=', now()->subMonth())->where('completed', true)->count(),
            'points_earned' => GameSession::where('started_at', '>=', now()->subMonth())->sum('points_earned')
        ];
    }

    private function getYearStats()
    {
        return [
            'sessions' => GameSession::where('started_at', '>=', now()->subYear())->count(),
            'players' => GameSession::where('started_at', '>=', now()->subYear())->distinct('user_id')->count(),
            'completed' => GameSession::where('started_at', '>=', now()->subYear())->where('completed', true)->count(),
            'points_earned' => GameSession::where('started_at', '>=', now()->subYear())->sum('points_earned')
        ];
    }
}