<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\GameSession;
use App\Models\UserCarFound;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CarModelController extends Controller
{
    /**
     * Affichage de la liste des modèles
     */
    public function index(Request $request)
    {
        $query = CarModel::with('brand');

        // Filtres de recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('brand', function ($brandQuery) use ($request) {
                        $brandQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }

        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        $models = $query->orderBy('name')->paginate(15);
        $brands = Brand::orderBy('name')->get();

        // Statistiques
        $stats = [
            'total' => CarModel::count(),
            'by_difficulty' => CarModel::selectRaw('difficulty_level, COUNT(*) as count')
                ->groupBy('difficulty_level')
                ->pluck('count', 'difficulty_level')
                ->toArray(),
            'recent' => CarModel::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.models.index', compact('models', 'brands', 'stats'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $difficulties = [
            1 => 'Facile',
            2 => 'Moyen',
            3 => 'Difficile'
        ];

        return view('admin.models.create', compact('brands', 'difficulties'));
    }

    /**
     * Sauvegarde d'un nouveau modèle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'difficulty_level' => 'required|integer|in:1,2,3',
            'image_url' => 'nullable|url|max:500',
        ], [
            'name.required' => 'Le nom du modèle est obligatoire.',
            'brand_id.required' => 'La marque est obligatoire.',
            'brand_id.exists' => 'La marque sélectionnée n\'existe pas.',
            'year.min' => 'L\'année ne peut pas être antérieure à 1900.',
            'year.max' => 'L\'année ne peut pas être dans le futur.',
            'difficulty_level.required' => 'Le niveau de difficulté est obligatoire.',
            'difficulty_level.in' => 'Le niveau de difficulté doit être 1, 2 ou 3.',
            'image_url.url' => 'L\'URL de l\'image doit être valide.',
        ]);

        // Vérifier les doublons
        $exists = CarModel::where('name', $validated['name'])
            ->where('brand_id', $validated['brand_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Ce modèle existe déjà pour cette marque.']);
        }

        CarModel::create($validated);

        return redirect()
            ->route('admin.models.index')
            ->with('success', 'Modèle créé avec succès.');
    }

    /**
     * Affichage d'un modèle spécifique
     */
    public function show(CarModel $model)
    {
        $model->load('brand');

        // Statistiques du modèle
        $stats = [
            'times_found' => UserCarFound::where('car_id', $model->id)->count(),
            'total_sessions' => GameSession::where('car_id', $model->id)->count(),
            'success_rate' => $this->calculateSuccessRate($model),
            'average_attempts' => $this->calculateAverageAttempts($model),
            'fastest_time' => UserCarFound::where('car_id', $model->id)
                ->whereNotNull('time_taken')
                ->min('time_taken'),
            'slowest_time' => UserCarFound::where('car_id', $model->id)
                ->whereNotNull('time_taken')
                ->max('time_taken'),
        ];

        // Récentes trouvailles
        $recentFinds = UserCarFound::where('car_id', $model->id)
            ->with('userScore')
            ->orderBy('found_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.models.show', compact('model', 'stats', 'recentFinds'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(CarModel $model)
    {
        $brands = Brand::orderBy('name')->get();
        $difficulties = [
            1 => 'Facile',
            2 => 'Moyen',
            3 => 'Difficile'
        ];

        return view('admin.models.edit', compact('model', 'brands', 'difficulties'));
    }

    /**
     * Mise à jour d'un modèle
     */
    public function update(Request $request, CarModel $model)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'difficulty_level' => 'required|integer|in:1,2,3',
            'image_url' => 'nullable|url|max:500',
        ], [
            'name.required' => 'Le nom du modèle est obligatoire.',
            'brand_id.required' => 'La marque est obligatoire.',
            'brand_id.exists' => 'La marque sélectionnée n\'existe pas.',
            'year.min' => 'L\'année ne peut pas être antérieure à 1900.',
            'year.max' => 'L\'année ne peut pas être dans le futur.',
            'difficulty_level.required' => 'Le niveau de difficulté est obligatoire.',
            'difficulty_level.in' => 'Le niveau de difficulté doit être 1, 2 ou 3.',
            'image_url.url' => 'L\'URL de l\'image doit être valide.',
        ]);

        // Vérifier les doublons (sauf le modèle actuel)
        $exists = CarModel::where('name', $validated['name'])
            ->where('brand_id', $validated['brand_id'])
            ->where('id', '!=', $model->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Ce modèle existe déjà pour cette marque.']);
        }

        $model->update($validated);

        return redirect()
            ->route('admin.models.index')
            ->with('success', 'Modèle mis à jour avec succès.');
    }

    /**
     * Suppression d'un modèle
     */
    public function destroy(CarModel $model)
    {
        try {
            // Vérifier s'il y a des données liées
            $hasGameSessions = GameSession::where('car_id', $model->id)->exists();
            $hasUserFound = UserCarFound::where('car_id', $model->id)->exists();

            if ($hasGameSessions || $hasUserFound) {
                return redirect()
                    ->route('admin.models.index')
                    ->with('error', 'Impossible de supprimer un modèle qui a été utilisé dans des sessions de jeu ou trouvé par des utilisateurs.');
            }

            $model->delete();

            return redirect()
                ->route('admin.models.index')
                ->with('success', 'Modèle supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.models.index')
                ->with('error', 'Erreur lors de la suppression du modèle.');
        }
    }

    /**
     * API pour récupérer les modèles d'une marque (pour AJAX)
     */
    public function getByBrand($brandId)
    {
        $models = CarModel::where('brand_id', $brandId)
            ->orderBy('name')
            ->get(['id', 'name', 'year', 'difficulty_level']);

        return response()->json($models);
    }

    /**
     * Import en masse de modèles
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'models' => 'required|array|min:1',
            'models.*.name' => 'required|string|max:255',
            'models.*.year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'models.*.difficulty_level' => 'required|integer|in:1,2,3',
            'models.*.image_url' => 'nullable|url|max:500',
        ]);

        $brandId = $request->brand_id;
        $modelsData = $request->models;
        $created = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($modelsData as $modelData) {
                // Vérifier si le modèle existe déjà
                $exists = CarModel::where('name', $modelData['name'])
                    ->where('brand_id', $brandId)
                    ->exists();

                if (!$exists) {
                    CarModel::create([
                        'name' => $modelData['name'],
                        'brand_id' => $brandId,
                        'year' => $modelData['year'] ?? null,
                        'difficulty_level' => $modelData['difficulty_level'],
                        'image_url' => $modelData['image_url'] ?? null,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.models.index')
                ->with('success', "Import terminé : $created modèle(s) créé(s), $skipped ignoré(s) (doublons).");

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->route('admin.models.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Export des modèles en CSV
     */
    public function export(Request $request)
    {
        $query = CarModel::with('brand');

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('brand', function ($brandQuery) use ($request) {
                        $brandQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        $models = $query->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="car_models_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($models) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Nom',
                'Marque',
                'Année',
                'Difficulté',
                'URL Image',
                'Date de création'
            ]);

            foreach ($models as $model) {
                fputcsv($file, [
                    $model->id,
                    $model->name,
                    $model->brand->name ?? 'N/A',
                    $model->year,
                    $model->difficulty_level,
                    $model->image_url,
                    $model->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculer le taux de succès pour un modèle
     */
    private function calculateSuccessRate(CarModel $model)
    {
        $totalSessions = GameSession::where('car_id', $model->id)->count();

        if ($totalSessions == 0) {
            return 0;
        }

        $successfulSessions = UserCarFound::where('car_id', $model->id)->count();

        return round(($successfulSessions / $totalSessions) * 100, 2);
    }

    /**
     * Calculer le nombre moyen de tentatives pour un modèle
     */
    private function calculateAverageAttempts(CarModel $model)
    {
        $avgAttempts = UserCarFound::where('car_id', $model->id)->avg('attempts_used');

        return $avgAttempts ? round($avgAttempts, 2) : 0;
    }
}