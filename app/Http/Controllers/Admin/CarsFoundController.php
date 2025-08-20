<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserCarFound;
use App\Models\UserScore;
use App\Models\CarModel;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarsFoundController extends Controller
{
    /**
     * Afficher la liste des voitures trouvées
     */
    public function index(Request $request)
    {
        $query = UserCarFound::with(['userScore', 'carModel.brand']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('userScore', function ($subQ) use ($request) {
                    $subQ->where('username', 'like', '%' . $request->search . '%');
                })->orWhereHas('carModel', function ($subQ) use ($request) {
                    $subQ->where('name', 'like', '%' . $request->search . '%');
                })->orWhereHas('carModel.brand', function ($subQ) use ($request) {
                    $subQ->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('brand_id')) {
            $query->whereHas('carModel', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }

        if ($request->filled('difficulty')) {
            $query->whereHas('carModel', function ($q) use ($request) {
                $q->where('difficulty_level', $request->difficulty);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('found_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('found_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('guild_id')) {
            $query->where('guild_id', $request->guild_id);
        }

        // Tri
        $sortField = $request->get('sort', 'found_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['found_at', 'attempts_used', 'time_taken'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('found_at', 'desc');
        }

        $carsFound = $query->paginate(30)->withQueryString();

        // Données pour les filtres
        $users = UserScore::orderBy('username')->get();
        $brands = Brand::orderBy('name')->get();
        $guilds = UserCarFound::select('guild_id')
            ->whereNotNull('guild_id')
            ->distinct()
            ->orderBy('guild_id')
            ->get();

        // Statistiques générales
        $stats = [
            'total' => UserCarFound::count(),
            'today' => UserCarFound::whereDate('found_at', today())->count(),
            'this_week' => UserCarFound::where('found_at', '>=', now()->subWeek())->count(),
            'avg_attempts' => UserCarFound::avg('attempts_used') ?? 0,
            'unique_players' => UserCarFound::distinct('user_id')->count(),
            'unique_cars' => UserCarFound::distinct('car_id')->count(),
        ];

        return view('admin.cars-found.index', compact('carsFound', 'users', 'brands', 'guilds', 'stats'));
    }

    /**
     * Afficher le détail d'une trouvaille
     */
    public function show($id)
    {
        $carFound = UserCarFound::with(['userScore', 'carModel.brand'])->findOrFail($id);

        // Statistiques pour cette voiture spécifique
        $carStats = [
            'total_found' => UserCarFound::where('car_id', $carFound->car_id)->count(),
            'avg_attempts' => UserCarFound::where('car_id', $carFound->car_id)->avg('attempts_used'),
            'fastest_find' => UserCarFound::where('car_id', $carFound->car_id)
                ->whereNotNull('time_taken')
                ->min('time_taken'),
            'slowest_find' => UserCarFound::where('car_id', $carFound->car_id)
                ->whereNotNull('time_taken')
                ->max('time_taken'),
            'first_found' => UserCarFound::where('car_id', $carFound->car_id)->min('found_at'),
            'last_found' => UserCarFound::where('car_id', $carFound->car_id)->max('found_at'),
        ];

        // Autres joueurs qui ont trouvé cette voiture
        $otherFinds = UserCarFound::where('car_id', $carFound->car_id)
            ->where('id', '!=', $carFound->id)
            ->with('userScore')
            ->orderBy('found_at', 'asc')
            ->limit(10)
            ->get();

        return view('admin.cars-found.show', compact('carFound', 'carStats', 'otherFinds'));
    }

    /**
     * Formulaire d'ajout manuel d'une trouvaille
     */
    public function create()
    {
        $users = UserScore::orderBy('username')->get();
        $cars = CarModel::with('brand')->orderBy('name')->get();

        return view('admin.cars-found.create', compact('users', 'cars'));
    }

    /**
     * Enregistrer une nouvelle trouvaille manuellement
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user_scores,user_id',
            'car_id' => 'required|exists:models,id',
            'attempts_used' => 'required|integer|min:1|max:100',
            'time_taken' => 'nullable|integer|min:1',
            'guild_id' => 'nullable|string|max:50',
        ]);

        // Vérifier si cette combinaison existe déjà
        $exists = UserCarFound::where('user_id', $request->user_id)
            ->where('car_id', $request->car_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Ce joueur a déjà trouvé cette voiture.');
        }

        UserCarFound::create([
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'found_at' => now(),
            'attempts_used' => $request->attempts_used,
            'time_taken' => $request->time_taken,
            'guild_id' => $request->guild_id,
        ]);

        return redirect()->route('admin.cars-found.index')
            ->with('success', 'Trouvaille ajoutée manuellement avec succès.');
    }

    /**
     * Supprimer une trouvaille
     */
    public function destroy($id)
    {
        $carFound = UserCarFound::findOrFail($id);
        $carFound->delete();

        return redirect()->route('admin.cars-found.index')
            ->with('success', 'Entrée supprimée avec succès.');
    }

    /**
     * Statistiques avancées des voitures trouvées
     */
    public function statistics()
    {
        // Top des voitures les plus trouvées
        $mostFoundCars = UserCarFound::selectRaw('
                car_id,
                COUNT(*) as found_count,
                AVG(attempts_used) as avg_attempts,
                MIN(found_at) as first_found
            ')
            ->with(['carModel.brand'])
            ->groupBy('car_id')
            ->orderBy('found_count', 'desc')
            ->limit(20)
            ->get();

        // Top des joueurs les plus actifs
        $mostActiveUsers = UserCarFound::selectRaw('
                user_id,
                COUNT(*) as cars_found,
                AVG(attempts_used) as avg_attempts,
                MIN(found_at) as first_find,
                MAX(found_at) as latest_find
            ')
            ->with('userScore')
            ->groupBy('user_id')
            ->orderBy('cars_found', 'desc')
            ->limit(20)
            ->get();

        // Répartition par difficulté
        $difficultyStats = UserCarFound::join('models', 'user_cars_found.car_id', '=', 'models.id')
            ->selectRaw('
                models.difficulty_level,
                COUNT(*) as found_count,
                AVG(user_cars_found.attempts_used) as avg_attempts
            ')
            ->groupBy('models.difficulty_level')
            ->orderBy('models.difficulty_level')
            ->get();

        // Évolution mensuelle
        $monthlyEvolution = UserCarFound::selectRaw('
                DATE_FORMAT(found_at, "%Y-%m") as month,
                COUNT(*) as cars_found,
                COUNT(DISTINCT user_id) as unique_players,
                AVG(attempts_used) as avg_attempts
            ')
            ->where('found_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Performance par marque
        $brandPerformance = UserCarFound::join('models', 'user_cars_found.car_id', '=', 'models.id')
            ->join('brands', 'models.brand_id', '=', 'brands.id')
            ->selectRaw('
                brands.id,
                brands.name,
                brands.logo_url,
                brands.country,
                COUNT(*) as found_count,
                AVG(user_cars_found.attempts_used) as avg_attempts,
                COUNT(DISTINCT user_cars_found.user_id) as unique_finders
            ')
            ->groupBy('brands.id', 'brands.name', 'brands.logo_url', 'brands.country')
            ->orderBy('found_count', 'desc')
            ->limit(15)
            ->get();

        // Statistiques générales
        $generalStats = [
            'total_found' => UserCarFound::count(),
            'unique_players' => UserCarFound::distinct('user_id')->count(),
            'unique_cars' => UserCarFound::distinct('car_id')->count(),
            'average_attempts' => UserCarFound::avg('attempts_used'),
            'total_cars_available' => CarModel::count(),
            'completion_rate' => CarModel::count() > 0 ?
                round((UserCarFound::distinct('car_id')->count() / CarModel::count()) * 100, 1) : 0,
        ];

        return view('admin.cars-found.statistics', compact(
            'mostFoundCars',
            'mostActiveUsers',
            'difficultyStats',
            'monthlyEvolution',
            'brandPerformance',
            'generalStats'
        ));
    }

    /**
     * Export des données
     */
    public function export(Request $request)
    {
        $query = UserCarFound::with(['userScore', 'carModel.brand']);

        // Appliquer les filtres de la requête
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('brand_id')) {
            $query->whereHas('carModel', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('found_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('found_at', '<=', $request->date_to . ' 23:59:59');
        }

        $carsFound = $query->orderBy('found_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cars_found_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($carsFound) {
            $file = fopen('php://output', 'w');

            // Entêtes CSV
            fputcsv($file, [
                'ID',
                'Joueur',
                'Marque',
                'Modèle',
                'Difficulté',
                'Trouvé le',
                'Tentatives utilisées',
                'Temps (secondes)',
                'Serveur Discord'
            ]);

            // Données
            foreach ($carsFound as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->userScore->username ?? 'Inconnu',
                    $item->carModel->brand->name ?? 'N/A',
                    $item->carModel->name ?? 'N/A',
                    $item->carModel->difficulty_level ?? 'N/A',
                    $item->found_at->format('Y-m-d H:i:s'),
                    $item->attempts_used,
                    $item->time_taken,
                    $item->guild_id ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Supprimer en masse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:user_cars_found,id'
        ]);

        $count = UserCarFound::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.cars-found.index')
            ->with('success', "$count entrée(s) supprimée(s) avec succès.");
    }

    /**
     * API pour récupérer les dernières trouvailles (pour dashboard)
     */
    public function recent()
    {
        $recent = UserCarFound::with(['userScore', 'carModel.brand'])
            ->orderBy('found_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'player' => $item->userScore->username ?? 'Inconnu',
                    'car' => ($item->carModel->brand->name ?? '') . ' ' . ($item->carModel->name ?? ''),
                    'difficulty' => $item->carModel->difficulty_level ?? 0,
                    'attempts' => $item->attempts_used,
                    'found_at' => $item->found_at->format('H:i'),
                    'time_ago' => $item->found_at->diffForHumans(),
                ];
            });

        return response()->json($recent);
    }

    /**
     * Recherche rapide (AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = UserCarFound::with(['userScore', 'carModel.brand'])
            ->where(function ($q) use ($query) {
                $q->whereHas('userScore', function ($subQ) use ($query) {
                    $subQ->where('username', 'like', '%' . $query . '%');
                })->orWhereHas('carModel', function ($subQ) use ($query) {
                    $subQ->where('name', 'like', '%' . $query . '%');
                })->orWhereHas('carModel.brand', function ($subQ) use ($query) {
                    $subQ->where('name', 'like', '%' . $query . '%');
                });
            })
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->userScore->username . ' - ' .
                        ($item->carModel->brand->name ?? '') . ' ' .
                        ($item->carModel->name ?? ''),
                    'found_at' => $item->found_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json($results);
    }

    /**
     * Convertir le nom du pays en emoji drapeau
     */
    private function getCountryFlag($country)
    {
        $flags = [
            'France' => '🇫🇷',
            'Allemagne' => '🇩🇪',
            'Italie' => '🇮🇹',
            'Espagne' => '🇪🇸',
            'Royaume-Uni' => '🇬🇧',
            'États-Unis' => '🇺🇸',
            'Japon' => '🇯🇵',
            'Corée du Sud' => '🇰🇷',
            'Chine' => '🇨🇳',
            'Suède' => '🇸🇪',
            'Norvège' => '🇳🇴',
            'Pays-Bas' => '🇳🇱',
            'Belgique' => '🇧🇪',
            'Suisse' => '🇨🇭',
            'Autriche' => '🇦🇹',
            'République tchèque' => '🇨🇿',
            'Pologne' => '🇵🇱',
            'Russie' => '🇷🇺',
            'Inde' => '🇮🇳',
            'Brésil' => '🇧🇷',
            'Canada' => '🇨🇦',
            'Australie' => '🇦🇺',
            'Roumanie' => '🇷🇴',
            'Malaisie' => '🇲🇾',
        ];

        return $flags[$country] ?? '🌍';
    }
}