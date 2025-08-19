<?php
// app/Http/Controllers/Admin/PlayerController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserScore;
use App\Models\GameSession;
use App\Models\UserCarFound;
use App\Models\LeaderboardView;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = UserScore::query();

        // Recherche
        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%')
                ->orWhere('user_id', 'like', '%' . $request->search . '%');
        }

        // Filtre par guild
        if ($request->filled('guild_id')) {
            $query->where('guild_id', $request->guild_id);
        }

        // Filtre par niveau de compétence
        if ($request->filled('skill_level')) {
            $points = match ($request->skill_level) {
                'Expert' => ['>=', 100],
                'Avancé' => ['>=', 50, '<', 100], 'Intermédiaire' => ['>=', 20, '<', 50], 'Apprenti' => ['>=', 10, '<', 20], 'Débutant' =>
                ['<', 10], default => null
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
            return back()->withErrors([
                'games_won' => 'Le nombre de parties gagnées ne peut pas être supérieur au
                nombre de parties jouées.'
            ]);
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