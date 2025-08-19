<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserScore;
use App\Models\LeaderboardView;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $query = UserScore::query();

        // Filtres
        if ($request->filled('guild_id')) {
            $query->where('guild_id', $request->guild_id);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'week':
                    $query->whereHas('gameSessions', function ($q) {
                        $q->where('started_at', '>=', now()->subWeek());
                    });
                    break;
                case 'month':
                    $query->whereHas('gameSessions', function ($q) {
                        $q->where('started_at', '>=', now()->subMonth());
                    });
                    break;
                case 'year':
                    $query->whereHas('gameSessions', function ($q) {
                        $q->where('started_at', '>=', now()->subYear());
                    });
                    break;
            }
        }

        // Tri par défaut : points totaux
        $sortField = $request->get('sort', 'total_points');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['total_points', 'games_played', 'games_won', 'success_rate', 'best_streak'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('total_points', 'desc');
        }

        $players = $query->paginate(50)->withQueryString();

        // Ajouter le rang à chaque joueur
        $players->getCollection()->transform(function ($player, $key) use ($players) {
            $player->rank = ($players->currentPage() - 1) * $players->perPage() + $key + 1;
            return $player;
        });

        // Statistiques du leaderboard
        $stats = [
            'total_players' => UserScore::count(),
            'active_players' => UserScore::whereHas('gameSessions', function ($q) {
                $q->where('started_at', '>=', now()->subDays(7));
            })->count(),
            'top_score' => UserScore::max('total_points'),
            'avg_score' => UserScore::avg('total_points'),
        ];

        // Guildes pour les filtres
        $guilds = UserScore::selectRaw('guild_id, COUNT(*) as players_count, AVG(total_points) as avg_points')
            ->whereNotNull('guild_id')
            ->groupBy('guild_id')
            ->orderBy('avg_points', 'desc')
            ->get();

        return view('admin.leaderboard.index', compact('players', 'stats', 'guilds'));
    }

    public function show(UserScore $player)
    {
        return redirect()->route('admin.players.show', $player);
    }

    /**
     * Classement par guildes
     */
    public function guilds(Request $request)
    {
        $query = DB::table('user_scores')
            ->selectRaw('
                guild_id,
                COUNT(*) as players_count,
                SUM(total_points) as total_points,
                AVG(total_points) as avg_points,
                SUM(games_played) as total_games,
                SUM(games_won) as total_wins,
                MAX(total_points) as best_player_score
            ')
            ->whereNotNull('guild_id')
            ->groupBy('guild_id');

        // Filtres
        if ($request->filled('min_players')) {
            $query->having('players_count', '>=', $request->min_players);
        }

        // Tri
        $sortField = $request->get('sort', 'total_points');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['total_points', 'avg_points', 'players_count', 'total_games'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('total_points', 'desc');
        }

        $guilds = $query->paginate(20)->withQueryString();

        // Ajouter le rang à chaque guilde
        $guilds->getCollection()->transform(function ($guild, $key) use ($guilds) {
            $guild->rank = ($guilds->currentPage() - 1) * $guilds->perPage() + $key + 1;
            $guild->success_rate = $guild->total_games > 0
                ? round(($guild->total_wins / $guild->total_games) * 100, 1)
                : 0;
            return $guild;
        });

        return view('admin.leaderboard.guilds', compact('guilds'));
    }

    /**
     * Classement par voitures les plus trouvées
     */
    public function cars(Request $request)
    {
        $query = DB::table('user_cars_found')
            ->join('car_models', 'user_cars_found.car_id', '=', 'car_models.id')
            ->join('brands', 'car_models.brand_id', '=', 'brands.id')
            ->selectRaw('
                car_models.id,
                car_models.name as model_name,
                brands.name as brand_name,
                brands.logo_url,
                car_models.difficulty_level,
                COUNT(*) as found_count,
                AVG(user_cars_found.attempts_used) as avg_attempts,
                MIN(user_cars_found.found_at) as first_found
            ')
            ->groupBy('car_models.id', 'car_models.name', 'brands.name', 'brands.logo_url', 'car_models.difficulty_level');

        // Filtres
        if ($request->filled('difficulty')) {
            $query->where('car_models.difficulty_level', $request->difficulty);
        }

        if ($request->filled('brand_id')) {
            $query->where('brands.id', $request->brand_id);
        }

        $cars = $query->orderBy('found_count', 'desc')
            ->paginate(30)
            ->withQueryString();

        // Ajouter le rang
        $cars->getCollection()->transform(function ($car, $key) use ($cars) {
            $car->rank = ($cars->currentPage() - 1) * $cars->perPage() + $key + 1;
            return $car;
        });

        // Données pour les filtres
        $brands = \App\Models\Brand::orderBy('name')->get();

        return view('admin.leaderboard.cars', compact('cars', 'brands'));
    }

    /**
     * Records et statistiques
     */
    public function records()
    {
        $records = [
            'highest_score' => [
                'player' => UserScore::orderBy('total_points', 'desc')->first(),
                'value' => UserScore::max('total_points')
            ],
            'most_games' => [
                'player' => UserScore::orderBy('games_played', 'desc')->first(),
                'value' => UserScore::max('games_played')
            ],
            'best_streak' => [
                'player' => UserScore::orderBy('best_streak', 'desc')->first(),
                'value' => UserScore::max('best_streak')
            ],
            'most_cars_found' => [
                'player' => UserScore::withCount('userCarsFound')->orderBy('user_cars_found_count', 'desc')->first(),
                'value' => UserScore::withCount('userCarsFound')->max('user_cars_found_count')
            ],
            'fastest_session' => [
                'session' => GameSession::with(['userScore', 'carModel.brand'])
                    ->where('completed', true)
                    ->whereNotNull('duration_seconds')
                    ->orderBy('duration_seconds', 'asc')
                    ->first(),
                'value' => GameSession::where('completed', true)->min('duration_seconds')
            ],
            'longest_session' => [
                'session' => GameSession::with(['userScore', 'carModel.brand'])
                    ->where('completed', true)
                    ->whereNotNull('duration_seconds')
                    ->orderBy('duration_seconds', 'desc')
                    ->first(),
                'value' => GameSession::where('completed', true)->max('duration_seconds')
            ]
        ];

        // Statistiques mensuelles des records
        $monthlyRecords = GameSession::selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                MAX(points_earned) as best_score,
                MIN(CASE WHEN completed = 1 AND duration_seconds > 0 THEN duration_seconds END) as fastest_time,
                COUNT(DISTINCT user_id) as active_players
            ')
            ->where('started_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.leaderboard.records', compact('records', 'monthlyRecords'));
    }

    /**
     * Export du leaderboard
     */
    public function export(Request $request)
    {
        $query = UserScore::query();

        // Appliquer les filtres de la requête
        if ($request->filled('guild_id')) {
            $query->where('guild_id', $request->guild_id);
        }

        $players = $query->orderBy('total_points', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leaderboard_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($players) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Rank',
                'Username',
                'Guild ID',
                'Total Points',
                'Games Played',
                'Games Won',
                'Success Rate',
                'Best Streak',
                'Current Streak'
            ]);

            foreach ($players as $index => $player) {
                fputcsv($file, [
                    $index + 1,
                    $player->username,
                    $player->guild_id,
                    $player->total_points,
                    $player->games_played,
                    $player->games_won,
                    $player->success_rate . '%',
                    $player->best_streak,
                    $player->current_streak
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API pour récupérer le top 10
     */
    public function api()
    {
        $topPlayers = UserScore::orderBy('total_points', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($player, $index) {
                return [
                    'rank' => $index + 1,
                    'username' => $player->username,
                    'total_points' => $player->total_points,
                    'games_played' => $player->games_played,
                    'success_rate' => $player->success_rate
                ];
            });

        return response()->json($topPlayers);
    }
}