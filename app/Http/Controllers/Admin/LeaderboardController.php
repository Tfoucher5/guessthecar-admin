<?php
// app/Http/Controllers/Admin/LeaderboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardView;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaderboardView::query();

        // Recherche
        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
        }

        // Filtre par niveau de compétence
        if ($request->filled('skill_level')) {
            $query->where('skill_level', $request->skill_level);
        }

        // Filtre par rang
        if ($request->filled('rank_from')) {
            $query->where('ranking', '>=', $request->rank_from);
        }
        if ($request->filled('rank_to')) {
            $query->where('ranking', '<=', $request->rank_to);
        }

        $leaderboard = $query->orderBy('ranking')->paginate(50)->withQueryString();

        $skillLevels = ['Expert', 'Avancé', 'Intermédiaire', 'Apprenti', 'Débutant'];

        return view('admin.leaderboard.index', compact('leaderboard', 'skillLevels'));
    }
}