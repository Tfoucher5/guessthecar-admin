<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserScore;
use App\Models\LeaderboardView;
use App\Models\GameSession;
use App\Models\UserCarFound;
use App\Models\Brand;
use App\Models\CarModel;
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

        if ($request->filled('min_points')) {
            $query->where('total_points', '>=', $request->min_points);
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
            'top_score' => UserScore::max('total_points') ?? 0,
            'avg_score' => UserScore::avg('total_points') ?? 0,
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
        return redirect()->route('admin.players.show', $player)
            ->with('info', 'Redirection vers le profil détaillé du joueur.');
    }

    /**
     * Classement par guildes/serveurs
     */
    public function guilds(Request $request)
    {
        $guilds = UserScore::selectRaw('
                guild_id,
                COUNT(*) as total_players,
                SUM(total_points) as total_points,
                AVG(total_points) as avg_points,
                SUM(games_played) as total_games,
                SUM(games_won) as total_wins,
                MAX(total_points) as best_player_score,
                COUNT(CASE WHEN total_points >= 100 THEN 1 END) as experts,
                COUNT(CASE WHEN total_points >= 50 AND total_points < 100 THEN 1 END) as advanced
            ')
            ->whereNotNull('guild_id')
            ->groupBy('guild_id')
            ->orderBy('avg_points', 'desc')
            ->paginate(20);

        // Calculer le taux de victoire pour chaque guilde
        $guilds->getCollection()->transform(function ($guild) {
            $guild->win_rate = $guild->total_games > 0
                ? round(($guild->total_wins / $guild->total_games) * 100, 1)
                : 0;
            $guild->rank = 0; // Sera calculé dans la vue
            return $guild;
        });

        return view('admin.leaderboard.guilds', compact('guilds'));
    }

    /**
     * Classement par voitures les plus trouvées - CORRIGÉ
     */
    public function cars(Request $request)
    {
        $query = DB::table('user_cars_found')
            ->join('models', 'user_cars_found.car_id', '=', 'models.id')  // CORRIGÉ: models au lieu de car_models
            ->join('brands', 'models.brand_id', '=', 'brands.id')
            ->selectRaw('
                models.id,
                models.name as model_name,
                brands.name as brand_name,
                brands.logo_url,
                models.difficulty_level,
                models.image_url,
                COUNT(*) as found_count,
                AVG(user_cars_found.attempts_used) as avg_attempts,
                MIN(user_cars_found.found_at) as first_found,
                COUNT(DISTINCT user_cars_found.user_id) as unique_finders
            ')
            ->groupBy('models.id', 'models.name', 'brands.name', 'brands.logo_url', 'models.difficulty_level', 'models.image_url');

        // Filtres
        if ($request->filled('difficulty')) {
            $query->where('models.difficulty_level', $request->difficulty);
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
        $brands = Brand::orderBy('name')->get();

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
                'value' => UserScore::max('total_points') ?? 0
            ],
            'most_games' => [
                'player' => UserScore::orderBy('games_played', 'desc')->first(),
                'value' => UserScore::max('games_played') ?? 0
            ],
            'best_streak' => [
                'player' => UserScore::orderBy('best_streak', 'desc')->first(),
                'value' => UserScore::max('best_streak') ?? 0
            ],
            'most_cars_found' => [
                'player' => UserScore::withCount('userCarsFound')->orderBy('user_cars_found_count', 'desc')->first(),
                'value' => UserScore::withCount('userCarsFound')->max('user_cars_found_count') ?? 0
            ],
            'fastest_session' => [
                'session' => GameSession::with(['userScore', 'carModel.brand'])
                    ->where('completed', true)
                    ->whereNotNull('duration_seconds')
                    ->orderBy('duration_seconds', 'asc')
                    ->first(),
                'value' => GameSession::where('completed', true)->min('duration_seconds') ?? 0
            ],
            'longest_session' => [
                'session' => GameSession::with(['userScore', 'carModel.brand'])
                    ->where('completed', true)
                    ->whereNotNull('duration_seconds')
                    ->orderBy('duration_seconds', 'desc')
                    ->first(),
                'value' => GameSession::where('completed', true)->max('duration_seconds') ?? 0
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

        if ($request->filled('min_points')) {
            $query->where('total_points', '>=', $request->min_points);
        }

        $players = $query->orderBy('total_points', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leaderboard_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($players) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Rang',
                'Username',
                'Guild ID',
                'Total Points',
                'Parties Jouées',
                'Parties Gagnées',
                'Taux de Réussite (%)',
                'Meilleure Série',
                'Série Actuelle',
                'Date Inscription'
            ]);

            foreach ($players as $index => $player) {
                $successRate = $player->games_played > 0
                    ? round(($player->games_won / $player->games_played) * 100, 1)
                    : 0;

                fputcsv($file, [
                    $index + 1,
                    $player->username,
                    $player->guild_id,
                    $player->total_points,
                    $player->games_played,
                    $player->games_won,
                    $successRate,
                    $player->best_streak,
                    $player->current_streak,
                    $player->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API pour les mises à jour en temps réel
     */
    public function api(Request $request)
    {
        $topPlayers = UserScore::orderBy('total_points', 'desc')
            ->limit(10)
            ->get(['username', 'total_points', 'games_played', 'games_won']);

        $stats = [
            'total_players' => UserScore::count(),
            'active_players' => UserScore::whereHas('gameSessions', function ($q) {
                $q->where('started_at', '>=', now()->subDays(7));
            })->count(),
            'top_score' => UserScore::max('total_points') ?? 0,
            'avg_score' => round(UserScore::avg('total_points') ?? 0, 1),
        ];

        return response()->json([
            'top_players' => $topPlayers,
            'stats' => $stats,
            'updated_at' => now()->toISOString(),
        ]);
    }
}