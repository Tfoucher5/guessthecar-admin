<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\UserScore;
use App\Models\GameSession;
use App\Services\NodeApiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $nodeApi;

    public function __construct(NodeApiService $nodeApi)
    {
        $this->nodeApi = $nodeApi;
    }

    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_brands' => Brand::count(),
            'total_models' => CarModel::count(),
            'total_players' => UserScore::count(),
            'total_games' => GameSession::count(),
            'active_games' => GameSession::inProgress()->count(),
            'completed_games' => GameSession::completed()->count(),
        ];

        // Répartition par difficulté
        $difficultyStats = CarModel::selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->get()
            ->pluck('count', 'difficulty_level');

        // Statistiques des sessions de jeu
        $gameStats = GameSession::getStats();

        // Top joueurs
        $topPlayers = UserScore::topPlayers(5)->get();

        // Marques les plus populaires
        $topBrands = Brand::withModelCount()
            ->orderBy('models_count', 'desc')
            ->limit(5)
            ->get();

        // Sessions récentes
        $recentGames = GameSession::getRecentWithDetails(10);

        // Statut de l'API Node.js
        $apiHealth = $this->nodeApi->getHealth();

        return view('admin.dashboard', compact(
            'stats',
            'difficultyStats',
            'gameStats',
            'topPlayers',
            'topBrands',
            'recentGames',
            'apiHealth'
        ));
    }
}