<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'times_found' => $model->user_found_cars()->count(),
            'success_rate' => $this->calculateSuccessRate($model),
            'average_attempts' => $this->calculateAverageAttempts($model),
        ];

        return view('admin.models.show', compact('model', 'stats'));
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
        // Vérifier s'il y a des données liées
        if ($model->user_found_cars()->count() > 0) {
            return redirect()
                ->route('admin.models.index')
                ->with('error', 'Impossible de supprimer un modèle qui a été trouvé par des utilisateurs.');
        }

        $model->delete();

        return redirect()
            ->route('admin.models.index')
            ->with('success', 'Modèle supprimé avec succès.');
    }

    /**
     * API pour récupérer les modèles d'une marque (pour AJAX)
     */
    public function getByBrand(Request $request, $brandId)
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
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($request->models as $modelData) {
            $modelData['brand_id'] = $request->brand_id;

            // Vérifier les doublons
            $exists = CarModel::where('name', $modelData['name'])
                ->where('brand_id', $modelData['brand_id'])
                ->exists();

            if (!$exists) {
                CarModel::create($modelData);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Import terminé : {$created} modèles créés";
        if ($skipped > 0) {
            $message .= ", {$skipped} modèles ignorés (doublons)";
        }

        return redirect()
            ->route('admin.models.index')
            ->with('success', $message);
    }

    /**
     * Calcul du taux de réussite
     */
    private function calculateSuccessRate(CarModel $model)
    {
        // À implémenter selon votre logique métier
        $totalAttempts = $model->user_found_cars()->sum('attempts_used') ?? 0;
        $successfulFinds = $model->user_found_cars()->count();

        if ($totalAttempts == 0)
            return 0;

        return round(($successfulFinds / $totalAttempts) * 100, 1);
    }

    /**
     * Calcul de la moyenne des tentatives
     */
    private function calculateAverageAttempts(CarModel $model)
    {
        return $model->user_found_cars()->avg('attempts_used') ?? 0;
    }
}