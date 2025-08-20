<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserScore;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CarModel;

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
                'Expert' => [100, null],
                'Avancé' => [50, 99],
                'Intermédiaire' => [20, 49],
                'Apprenti' => [10, 19],
                'Débutant' => [0, 9],
                default => null
            };

            if ($points) {
                $query->where('total_points', '>=', $points[0]);
                if ($points[1] !== null) {
                    $query->where('total_points', '<=', $points[1]);
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

        // Statistiques globales
        $stats = [
            'total' => UserScore::count(),
            'active_today' => GameSession::whereDate('started_at', today())->distinct('user_id')->count(),
            'avg_points' => UserScore::avg('total_points') ?? 0,
            'total_points' => UserScore::sum('total_points'),
        ];

        return view('admin.players.index', compact('players', 'guilds', 'skillLevels', 'stats'));
    }

    public function show(UserScore $player)
    {
        $player->load(['gameSessions.carModel.brand', 'userCarsFound.carModel.brand']);

        // Sessions récentes
        $recentSessions = $player->gameSessions()
            ->with('carModel.brand')
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get();

        // Collection de voitures
        $collection = $player->userCarsFound()
            ->with(['carModel.brand'])
            ->orderBy('found_at', 'desc')
            ->get();

        // Statistiques détaillées
        $stats = [
            'total_sessions' => $player->gameSessions()->count(),
            'best_score' => $player->gameSessions()->max('points_earned') ?? 0,
            'avg_score' => $player->gameSessions()->avg('points_earned') ?? 0,
            'cars_found' => $player->userCarsFound()->count(),
            'completion_rate' => $player->games_played > 0
                ? round(($player->games_won / $player->games_played) * 100, 1)
                : 0,
            'avg_session_time' => $player->gameSessions()
                ->whereNotNull('duration_seconds')
                ->avg('duration_seconds') ?? 0,
            'total_playtime' => $player->gameSessions()
                ->whereNotNull('duration_seconds')
                ->sum('duration_seconds') ?? 0,
            'sessions_this_week' => $player->gameSessions()
                ->where('started_at', '>=', now()->subWeek())
                ->count(),
        ];

        // Activité récente
        $recentActivity = collect();

        // Ajouter les sessions récentes
        $player->gameSessions()
            ->orderBy('started_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($session) use ($recentActivity) {
                $recentActivity->push((object) [
                    'type' => $session->completed ? 'session_completed' : 'session_started',
                    'description' => $session->completed
                        ? 'Session terminée avec ' . ($session->points_earned ?? 0) . ' points'
                        : 'Session démarrée',
                    'details' => ($session->carModel->brand->name ?? '') . ' ' . ($session->carModel->name ?? ''),
                    'created_at' => $session->started_at
                ]);
            });

        // Ajouter les voitures trouvées récemment
        $player->userCarsFound()
            ->orderBy('found_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($carFound) use ($recentActivity) {
                $recentActivity->push((object) [
                    'type' => 'car_found',
                    'description' => 'Voiture trouvée',
                    'details' => ($carFound->carModel->brand->name ?? '') . ' ' . ($carFound->carModel->name ?? ''),
                    'created_at' => $carFound->found_at
                ]);
            });

        $recentActivity = $recentActivity->sortByDesc('created_at')->take(10);

        // Progression mensuelle
        $monthlyProgress = $player->gameSessions()
            ->selectRaw('
                DATE_FORMAT(started_at, "%Y-%m") as month,
                COUNT(*) as sessions_count,
                AVG(IFNULL(points_earned, 0)) as avg_score,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_count
            ')
            ->where('started_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'sessions_count' => $item->sessions_count,
                    'avg_score' => round($item->avg_score, 1),
                    'cars_found' => 0, // À calculer si nécessaire
                ];
            });

        // Total des voitures disponibles pour calculer le pourcentage
        $totalCarsAvailable = \App\Models\CarModel::count();

        return view('admin.players.show', compact(
            'player',
            'recentSessions',
            'collection',
            'stats',
            'recentActivity',
            'monthlyProgress',
            'totalCarsAvailable'
        ));
    }

    public function edit(UserScore $player)
    {
        return view('admin.players.edit', compact('player'));
    }

    public function update(Request $request, UserScore $player)
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
        if (isset($validated['games_won']) && isset($validated['games_played'])) {
            if ($validated['games_won'] > $validated['games_played']) {
                return back()->withErrors([
                    'games_won' => 'Le nombre de parties gagnées ne peut pas être supérieur au nombre de parties jouées.'
                ]);
            }
        }

        $player->update($validated);

        return redirect()->route('admin.players.show', $player)
            ->with('success', 'Joueur mis à jour avec succès.');
    }

    public function destroy(UserScore $player)
    {
        // Vérifier s'il y a des sessions en cours
        if ($player->gameSessions()->where('completed', false)->exists()) {
            return redirect()->route('admin.players.index')
                ->with('error', 'Impossible de supprimer un joueur ayant des sessions en cours.');
        }

        $player->delete();

        return redirect()->route('admin.players.index')
            ->with('success', 'Joueur supprimé avec succès.');
    }

    /**
     * Exporter les données d'un joueur
     */
    public function export(UserScore $player)
    {
        $data = [
            'player' => $player->toArray(),
            'sessions' => $player->gameSessions()->with('carModel.brand')->get()->toArray(),
            'collection' => $player->userCarsFound()->with('carModel.brand')->get()->toArray(),
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="player_' . $player->user_id . '_export_' . date('Y-m-d') . '.json"',
        ];

        return response()->json($data, 200, $headers);
    }

    /**
     * Afficher la liste des sessions de jeu
     */
    public function sessions(Request $request)
    {
        $query = GameSession::with(['userScore', 'carModel.brand']);

        // Filtres
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('completed', false)->where('abandoned', false);
                    break;
                case 'completed':
                    $query->where('completed', true);
                    break;
                case 'abandoned':
                    $query->where('abandoned', true);
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('car_id')) {
            $query->where('car_id', $request->car_id);
        }

        if ($request->filled('date_from')) {
            $query->where('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('started_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Tri
        $sortField = $request->get('sort', 'started_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['started_at', 'points_earned', 'duration_seconds'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('started_at', 'desc');
        }

        $sessions = $query->paginate(20)->withQueryString();

        // Données pour les filtres
        $users = UserScore::select('user_id', 'username')
            ->whereHas('gameSessions')
            ->orderBy('username')
            ->get();

        $cars = CarModel::with('brand')
            ->whereHas('gameSessions')
            ->orderBy('name')
            ->get();

        // Statistiques
        $stats = [
            'total' => GameSession::count(),
            'active' => GameSession::where('completed', false)->where('abandoned', false)->count(),
            'completed' => GameSession::where('completed', true)->count(),
            'abandoned' => GameSession::where('abandoned', true)->count(),
            'today' => GameSession::whereDate('started_at', today())->count(),
            'avg_duration' => GameSession::whereNotNull('duration_seconds')->avg('duration_seconds') ?? 0,
        ];

        return view('admin.sessions.index', compact('sessions', 'users', 'cars', 'stats'));
    }

    /**
     * Afficher une session spécifique
     */
    public function sessionShow(GameSession $gameSession)
    {
        $gameSession->load([
            'userScore',
            'carModel.brand',
            'userCarFound'
        ]);

        // Historique des actions de cette session (simulation)
        $sessionHistory = collect([
            [
                'action' => 'session_started',
                'description' => 'Session démarrée',
                'timestamp' => $gameSession->started_at,
                'details' => 'Voiture: ' . ($gameSession->carModel->brand->name ?? 'N/A') . ' ' . ($gameSession->carModel->name ?? 'N/A')
            ]
        ]);

        if ($gameSession->completed) {
            $sessionHistory->push([
                'action' => 'session_completed',
                'description' => 'Session terminée avec succès',
                'timestamp' => $gameSession->completed_at ?? $gameSession->updated_at,
                'details' => 'Points gagnés: ' . ($gameSession->points_earned ?? 0)
            ]);
        }

        if ($gameSession->userCarFound) {
            $sessionHistory->push([
                'action' => 'car_found',
                'description' => 'Voiture trouvée',
                'timestamp' => $gameSession->userCarFound->found_at,
                'details' => 'Tentatives utilisées: ' . $gameSession->userCarFound->attempts_used
            ]);
        }

        $sessionHistory = $sessionHistory->sortBy('timestamp');

        // Statistiques de la session
        $sessionStats = [
            'duration' => $gameSession->duration_seconds,
            'duration_formatted' => $gameSession->duration_seconds
                ? gmdate('H:i:s', $gameSession->duration_seconds)
                : 'En cours',
            'attempts_used' => $gameSession->userCarFound->attempts_used ?? 0,
            'points_earned' => $gameSession->points_earned ?? 0,
            'success' => $gameSession->completed && $gameSession->userCarFound,
        ];

        return view('admin.sessions.show', compact('gameSession', 'sessionHistory', 'sessionStats'));
    }

    /**
     * Supprimer une session
     */
    public function sessionDestroy(GameSession $gameSession)
    {
        try {
            // Supprimer les données liées si nécessaire
            if ($gameSession->userCarFound) {
                $gameSession->userCarFound->delete();
            }

            $gameSession->delete();

            return redirect()
                ->route('admin.sessions.index')
                ->with('success', 'Session supprimée avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.sessions.index')
                ->with('error', 'Erreur lors de la suppression de la session.');
        }
    }

    /**
     * Terminer une session active
     */
    public function endSession(GameSession $gameSession)
    {
        if ($gameSession->completed || $gameSession->abandoned) {
            return response()->json([
                'success' => false,
                'message' => 'Cette session est déjà terminée.'
            ]);
        }

        $gameSession->update([
            'completed' => false,
            'abandoned' => true,
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($gameSession->started_at)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session terminée avec succès.'
        ]);
    }

    /**
     * Réinitialiser une session
     */
    public function resetSession(GameSession $gameSession)
    {
        $gameSession->update([
            'completed' => false,
            'abandoned' => false,
            'completed_at' => null,
            'points_earned' => 0,
            'duration_seconds' => null
        ]);

        // Supprimer la voiture trouvée associée si elle existe
        if ($gameSession->userCarFound) {
            $gameSession->userCarFound->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Session réinitialisée avec succès.'
        ]);
    }

    /**
     * Export des sessions en CSV
     */
    public function exportSessions(Request $request)
    {
        $query = GameSession::with(['userScore', 'carModel.brand']);

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('completed', false)->where('abandoned', false);
                    break;
                case 'completed':
                    $query->where('completed', true);
                    break;
                case 'abandoned':
                    $query->where('abandoned', true);
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('started_at', '<=', $request->date_to . ' 23:59:59');
        }

        $sessions = $query->orderBy('started_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sessions_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($sessions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Joueur',
                'Voiture (Marque)',
                'Voiture (Modèle)',
                'Statut',
                'Points Gagnés',
                'Durée (secondes)',
                'Date de Début',
                'Date de Fin',
                'Serveur'
            ]);

            foreach ($sessions as $session) {
                $status = 'En cours';
                if ($session->completed) {
                    $status = 'Terminée';
                } elseif ($session->abandoned) {
                    $status = 'Abandonnée';
                }

                fputcsv($file, [
                    $session->id,
                    $session->userScore->username ?? 'Inconnu',
                    $session->carModel->brand->name ?? 'N/A',
                    $session->carModel->name ?? 'N/A',
                    $status,
                    $session->points_earned ?? 0,
                    $session->duration_seconds ?? 0,
                    $session->started_at->format('Y-m-d H:i:s'),
                    $session->completed_at ? $session->completed_at->format('Y-m-d H:i:s') : '',
                    $session->guild_id ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}