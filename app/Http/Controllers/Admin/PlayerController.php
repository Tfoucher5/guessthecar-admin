<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserScore;
use App\Models\GameSession;
use App\Models\UserCarFound;
use App\Models\LeaderboardView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Suspendre un joueur
     */
    public function suspend(UserScore $player)
    {
        $player->update(['status' => 'suspended']);

        return response()->json(['success' => true]);
    }

    /**
     * Réactiver un joueur
     */
    public function activate(UserScore $player)
    {
        $player->update(['status' => 'active']);

        return response()->json(['success' => true]);
    }

    /**
     * Réinitialiser la progression d'un joueur
     */
    public function resetProgress(UserScore $player)
    {
        DB::transaction(function () use ($player) {
            // Supprimer les voitures trouvées
            $player->userCarsFound()->delete();

            // Marquer les sessions comme abandonnées
            $player->gameSessions()
                ->where('completed', false)
                ->update(['completed' => false, 'abandoned' => true]);

            // Réinitialiser les statistiques
            $player->update([
                'total_points' => 0,
                'games_played' => 0,
                'games_won' => 0,
                'best_streak' => 0,
                'current_streak' => 0,
                'success_rate' => 0,
            ]);
        });

        return response()->json(['success' => true]);
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
}