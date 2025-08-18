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
     * Formulaire d'édition d'un joueur
     */
    public function edit(UserScore $userScore)
    {
        // Charger les relations si nécessaire
        $player = $userScore;

        return view('admin.players.edit', compact('player'));
    }

    /**
     * Mise à jour d'un joueur
     */
    public function update(Request $request, UserScore $userScore)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'total_points' => 'nullable|numeric|min:0',
            'games_played' => 'nullable|integer|min:0',
            'games_won' => 'nullable|integer|min:0',
            'current_streak' => 'nullable|integer|min:0',
            'best_streak' => 'nullable|integer|min:0',
        ], [
            'username.required' => 'Le nom d\'utilisateur est obligatoire.',
            'username.max' => 'Le nom d\'utilisateur ne peut pas dépasser 255 caractères.',
            'total_points.numeric' => 'Les points doivent être un nombre.',
            'total_points.min' => 'Les points ne peuvent pas être négatifs.',
            'games_played.integer' => 'Le nombre de parties jouées doit être un entier.',
            'games_played.min' => 'Le nombre de parties ne peut pas être négatif.',
            'games_won.integer' => 'Le nombre de parties gagnées doit être un entier.',
            'games_won.min' => 'Le nombre de parties gagnées ne peut pas être négatif.',
            'current_streak.integer' => 'La série actuelle doit être un entier.',
            'current_streak.min' => 'La série actuelle ne peut pas être négative.',
            'best_streak.integer' => 'La meilleure série doit être un entier.',
            'best_streak.min' => 'La meilleure série ne peut pas être négative.',
        ]);

        // Validation métier : les parties gagnées ne peuvent pas dépasser les parties jouées
        if (isset($validated['games_won']) && isset($validated['games_played'])) {
            if ($validated['games_won'] > $validated['games_played']) {
                return back()
                    ->withInput()
                    ->withErrors(['games_won' => 'Le nombre de parties gagnées ne peut pas dépasser le nombre de parties jouées.']);
            }
        }

        // Validation : la meilleure série ne peut pas être inférieure à la série actuelle
        if (isset($validated['best_streak']) && isset($validated['current_streak'])) {
            if ($validated['current_streak'] > $validated['best_streak']) {
                $validated['best_streak'] = $validated['current_streak'];
            }
        }

        $userScore->update($validated);

        return redirect()
            ->route('admin.players.index')
            ->with('success', 'Joueur mis à jour avec succès.');
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