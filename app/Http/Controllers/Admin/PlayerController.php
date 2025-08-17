<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserScore;
use App\Models\GameSession;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Liste des joueurs
     */
    public function index(Request $request)
    {
        $query = UserScore::query();

        // Recherche
        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
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

        return view('admin.players.index', compact('players'));
    }

    /**
     * Détails d'un joueur
     */
    public function show(UserScore $userScore)
    {
        // Récupérer les sessions de jeu du joueur
        $sessions = GameSession::where('user_id', $userScore->user_id)
            ->with('carModel.brand')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        // Statistiques du joueur
        $stats = [
            'total_sessions' => GameSession::where('user_id', $userScore->user_id)->count(),
            'completed_sessions' => GameSession::where('user_id', $userScore->user_id)->completed()->count(),
            'abandoned_sessions' => GameSession::where('user_id', $userScore->user_id)->abandoned()->count(),
            'average_duration' => GameSession::where('user_id', $userScore->user_id)
                ->whereNotNull('duration_seconds')
                ->avg('duration_seconds'),
            'best_game_points' => GameSession::where('user_id', $userScore->user_id)
                ->selectRaw('MAX(points_earned + difficulty_points_earned) as max_points')
                ->value('max_points') ?? 0,
        ];

        return view('admin.players.show', compact('userScore', 'sessions', 'stats'));
    }

    /**
     * Liste des sessions de jeu
     */
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

        $sessions = $query->orderBy('started_at', 'desc')->paginate(20)->withQueryString();

        // Liste des joueurs pour le filtre
        $players = UserScore::orderBy('username')->get();

        return view('admin.sessions.index', compact('sessions', 'players'));
    }

    /**
     * Détails d'une session
     */
    public function sessionShow(GameSession $gameSession)
    {
        $gameSession->load(['carModel.brand', 'userScore']);

        return view('admin.sessions.show', compact('gameSession'));
    }
}