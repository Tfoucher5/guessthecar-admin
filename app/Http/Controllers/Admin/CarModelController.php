<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use App\Models\Brand;
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

        // Recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('brand', function($brandQuery) use ($request) {
                      $brandQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filtre par marque
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filtre par difficulté
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Filtre par année
        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }
        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        // Tri
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'brand_name') {
            $query->join('brands', 'models.brand_id', '=', 'brands.id')
                  ->orderBy('brands.name', $sortDirection)
                  ->select('models.*');
        } elseif (in_array($sortField, ['name', 'year', 'difficulty_level', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $models = $query->paginate(20)->withQueryString();
        
        // Données pour les filtres
        $brands = Brand::orderBy('name')->get();
        $difficulties = [
            1 => 'Facile',
            2 => 'Moyen', 
            3 => 'Difficile'
        ];

        return view('admin.models.index', compact('models', 'brands', 'difficulties'));
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
     * Enregistrement d'un nouveau modèle
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

        return view('admin.models.show', compact('model'));
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
                          ->get(['id', 'name']);

        return response()->json($models);
    }
}