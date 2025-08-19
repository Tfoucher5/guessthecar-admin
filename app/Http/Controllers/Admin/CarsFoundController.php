<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserCarFound;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;

class CarsFoundController extends Controller
{
    public function index(Request $request)
    {
        $query = UserCarFound::with(['carModel.brand', 'userScore']);

        // Recherche
        if ($request->filled('search')) {
            $query->whereHas('userScore', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%');
            })->orWhereHas('carModel', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('brand', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filtres
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('guild_id')) {
            $query->where('guild_id', $request->guild_id);
        }

        if ($request->filled('date_from')) {
            $query->where('found_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('found_at', '<=', $request->date_to . ' 23:59:59');
        }

        $carsFound = $query->orderBy('found_at', 'desc')->paginate(20)->withQueryString();

        // Données pour les filtres
        $brands = Brand::orderBy('name')->get();
        $guilds = UserCarFound::selectRaw('guild_id, COUNT(*) as cars_count')
            ->whereNotNull('guild_id')
            ->groupBy('guild_id')
            ->orderBy('cars_count', 'desc')
            ->get();

        return view('admin.cars-found.index', compact('carsFound', 'brands', 'guilds'));
    }

    public function show(UserCarFound $userCarFound)
    {
        $userCarFound->load(['carModel.brand', 'userScore']);

        return view('admin.cars-found.show', compact('userCarFound'));
    }

    public function statistics(Request $request)
    {
        // Statistiques générales
        $stats = [
            'total_found' => UserCarFound::count(),
            'unique_cars' => UserCarFound::distinct('car_id')->count(),
            'unique_players' => UserCarFound::distinct('user_id')->count(),
            'average_attempts' => UserCarFound::avg('attempts_used'),
            'average_time' => UserCarFound::avg('time_taken')
        ];

        // Top voitures les plus trouvées
        $mostFoundCars = UserCarFound::selectRaw('
    car_id,
    COUNT(*) as found_count,
    AVG(attempts_used) as avg_attempts,
    AVG(time_taken) as avg_time
    ')
            ->with('carModel.brand')
            ->groupBy('car_id')
            ->orderBy('found_count', 'desc')
            ->limit(20)
            ->get();

        // Top marques les plus collectionnées
        $mostFoundBrands = UserCarFound::selectRaw('
    brand_id,
    COUNT(*) as found_count,
    COUNT(DISTINCT user_id) as unique_collectors
    ')
            ->with('brand')
            ->groupBy('brand_id')
            ->orderBy('found_count', 'desc')
            ->limit(15)
            ->get();

        // Collectionneurs les plus actifs
        $topCollectors = UserCarFound::selectRaw('
    user_id,
    COUNT(*) as cars_found,
    COUNT(DISTINCT brand_id) as brands_collected,
    AVG(attempts_used) as avg_attempts
    ')
            ->with('userScore')
            ->groupBy('user_id')
            ->orderBy('cars_found', 'desc')
            ->limit(15)
            ->get();

        return view('admin.cars-found.statistics', compact(
            'stats',
            'mostFoundCars',
            'mostFoundBrands',
            'topCollectors'
        ));
    }
}