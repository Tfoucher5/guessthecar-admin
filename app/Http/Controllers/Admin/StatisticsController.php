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
            'completion_rate' => GameSession::where('completed', true)->count() / max(GameSession::count(), 1) * 100
        ];

        // Évolution mensuelle
        $monthlyEvolution = GameSession::selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                COUNT(*) as sessions,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed,
                AVG(duration_seconds) as avg_duration,
                SUM(points_earned) as total_points
            ')
            ->where('started_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Répartition par difficulté des sessions
        $difficultyStats = GameSession::join('models', 'game_sessions.car_id', '=', 'models.id')
            ->selectRaw('
                models.difficulty_level,
                COUNT(*) as sessions_count,
                AVG(game_sessions.duration_seconds) as avg_duration,
                SUM(CASE WHEN game_sessions.completed = 1 THEN 1 ELSE 0 END) as completed_count
            ')
            ->groupBy('models.difficulty_level')
            ->get();

        // Performance par pays
        $countryPerformance = DB::table('brands')
            ->join('models', 'brands.id', '=', 'models.brand_id')
            ->join('game_sessions', 'models.id', '=', 'game_sessions.car_id')
            ->selectRaw('
                brands.country,
                COUNT(*) as sessions_count,
                AVG(game_sessions.duration_seconds) as avg_duration,
                SUM(CASE WHEN game_sessions.completed = 1 THEN 1 ELSE 0 END) as success_count
            ')
            ->groupBy('brands.country')
            ->orderBy('sessions_count', 'desc')
            ->get();

        return view('admin.statistics.index', compact(
            'generalStats',
            'monthlyEvolution',
            'difficultyStats',
            'countryPerformance'
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

        $callback = function() use ($players) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Username', 'Guild ID', 'Total Points', 'Games Played', 
                'Games Won', 'Success Rate', 'Best Streak', 'Cars Found', 'Created At'
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

        $callback = function() use ($sessions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Session ID', 'Player', 'Brand', 'Model', 'Started At', 
                'Duration', 'Completed', 'Points Earned', 'Attempts Make', 'Attempts Model'
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
                    $session->attempts_make,
                    $session->attempts_model
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCollections()
    {
        $collections = UserCarFound::with(['carModel.brand', 'userScore'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="collections_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($collections) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Player', 'Brand', 'Model', 'Found At', 
                'Attempts Used', 'Time Taken', 'Guild ID'
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
}request->guild_id);
        }

        // Filtre par niveau de compétence
        if ($request->filled('skill_level')) {
            $points = match($request->skill_level) {
                'Expert' => ['>=', 100],
                'Avancé' => ['>=', 50, '<', 100],
                'Intermédiaire' => ['>=', 20, '<', 50],
                'Apprenti' => ['>=', 10, '<', 20],
                'Débutant' => ['<', 10],
                default => null
            };

            if ($points) {
                if (count($points) === 2) {
                    $query->where('total_points', $points[0], $points[1]);
                } else {
                    $query->where('total_points', $points[0], $points[1])
                          ->where('total_points', $points[2], $points[3]);
                }
            }
        }

        // Tri
        $sortField = $request->get('sort', 'total_points');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['username', 'total_points', 'games_played', 'games_won', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('total_points', 'desc');
        }

        $players = $query->paginate(20)->withQueryString();

        // Données pour les filtres
        $guilds = UserScore::selectRaw('guild_id, COUNT(*) as players_count')
            ->whereNotNull('guild_id')
            ->groupBy('guild_id')
            ->orderBy('players_count', 'desc')
            ->get();

        $skillLevels = ['Expert', 'Avancé', 'Intermédiaire', 'Apprenti', 'Débutant'];

        return view('admin.players.index', compact('players', 'guilds', 'skillLevels'));
    }

    public function show(UserScore $userScore)
    {
        $userScore->load(['gameSessions.carModel.brand', 'userCarsFound.carModel.brand']);

        // Sessions récentes
        $sessions = $userScore->gameSessions()
            ->with('carModel.brand')
            ->orderBy('started_at', 'desc')
            ->limit(20)
            ->get();

        // Voitures trouvées
        $carsFound = $userScore->userCarsFound()
            ->with(['carModel.brand'])
            ->orderBy('found_at', 'desc')
            ->limit(20)
            ->get();

        // Statistiques détaillées
        $stats = [
            'total_sessions' => $userScore->gameSessions()->count(),
            'average_session_time' => $userScore->gameSessions()
                ->whereNotNull('duration_seconds')
                ->avg('duration_seconds'),
            'favorite_brand' => $userScore->userCarsFound()
                ->selectRaw('brand_id, COUNT(*) as count')
                ->with('brand')
                ->groupBy('brand_id')
                ->orderBy('count', 'desc')
                ->first(),
            'cars_collection_count' => $userScore->userCarsFound()->count(),
            'points_per_game' => $userScore->games_played > 0 
                ? round($userScore->total_points / $userScore->games_played, 2) 
                : 0,
        ];

        // Progression mensuelle
        $monthlyProgress = $userScore->gameSessions()
            ->selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                COUNT(*) as games_count,
                SUM(points_earned) as points_earned,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_count
            ')
            ->where('started_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.players.show', compact(
            'userScore', 
            'sessions', 
            'carsFound', 
            'stats', 
            'monthlyProgress'
        ));
    }

    public function edit(UserScore $userScore)
    {
        return view('admin.players.edit', compact('userScore'));
    }

    public function update(Request $request, UserScore $userScore)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:32',
            'total_points' => 'nullable|numeric|min:0',
            'games_played' => 'nullable|integer|min:0',
            'games_won' => 'nullable|integer|min:0',
            'best_streak' => 'nullable|integer|min:0',
            'current_streak' => 'nullable|integer|min:0',
        ]);

        // Validation logique
        if ($validated['games_won'] > $validated['games_played']) {
            return back()->withErrors(['games_won' => 'Le nombre de parties gagnées ne peut pas être supérieur au nombre de parties jouées.']);
        }

        $userScore->update($validated);

        return redirect()->route('admin.players.show', $userScore)
            ->with('success', 'Joueur mis à jour avec succès.');
    }

    public function sessions(Request $request)
    {
        $query = GameSession::with(['carModel.brand', 'userScore']);

        // Filtres
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'completed':
                    $query->completed();
                    break;
                case 'abandoned':
                    $query->abandoned();
                    break;
                case 'in_progress':
                    $query->inProgress();
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('guild_id')) {
            $query->where('guild_id', $request->guild_id);
        }

        if ($request->filled('date_from')) {
            $query->where('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('started_at', '<=', $request->date_to . ' 23:59:59');
        }

        $sessions = $query->orderBy('started_at', 'desc')->paginate(20)->withQueryString();

        // Données pour les filtres
        $players = UserScore::orderBy('username')->get();
        $guilds = GameSession::selectRaw('guild_id, COUNT(*) as sessions_count')
            ->whereNotNull('guild_id')
            ->groupBy('guild_id')
            ->orderBy('sessions_count', 'desc')
            ->get();

        return view('admin.sessions.index', compact('sessions', 'players', 'guilds'));
    }

    public function sessionShow(GameSession $gameSession)
    {
        $gameSession->load(['carModel.brand', 'userScore']);

        return view('admin.sessions.show', compact('gameSession'));
    }
}