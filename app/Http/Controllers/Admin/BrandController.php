<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    /**
     * Affichage de la liste des marques
     */
    public function index(Request $request)
    {
        $query = Brand::withCount('models');

        // Recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('country', 'like', '%' . $request->search . '%');
            });
        }

        // Filtre par pays
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Tri
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortField, ['name', 'country', 'founded_year', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $brands = $query->paginate(15)->withQueryString();

        // Liste des pays pour le filtre
        $countries = Brand::distinct('country')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->pluck('country')
            ->sort();

        return view('admin.brands.index', compact('brands', 'countries'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Enregistrement d'une nouvelle marque
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'country' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url|max:500',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ], [
            'name.required' => 'Le nom de la marque est obligatoire.',
            'name.unique' => 'Cette marque existe déjà.',
            'logo_url.url' => 'L\'URL du logo doit être valide.',
            'founded_year.min' => 'L\'année de fondation ne peut pas être antérieure à 1800.',
            'founded_year.max' => 'L\'année de fondation ne peut pas être dans le futur.',
        ]);

        Brand::create($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marque créée avec succès.');
    }

    /**
     * Affichage d'une marque spécifique
     */
    public function show(Brand $brand)
    {
        $brand->load([
            'models' => function ($query) {
                $query->orderBy('name');
            }
        ]);

        // Statistiques de la marque
        $stats = [
            'total_models' => $brand->models->count(),
            'by_difficulty' => $brand->models->groupBy('difficulty_level')->map->count(),
            'latest_model' => $brand->models->sortByDesc('created_at')->first(),
        ];

        return view('admin.brands.show', compact('brand', 'stats'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Mise à jour d'une marque
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands')->ignore($brand->id)],
            'country' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url|max:500',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ], [
            'name.required' => 'Le nom de la marque est obligatoire.',
            'name.unique' => 'Cette marque existe déjà.',
            'logo_url.url' => 'L\'URL du logo doit être valide.',
            'founded_year.min' => 'L\'année de fondation ne peut pas être antérieure à 1800.',
            'founded_year.max' => 'L\'année de fondation ne peut pas être dans le futur.',
        ]);

        $brand->update($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marque mise à jour avec succès.');
    }

    /**
     * Suppression d'une marque
     */
    public function destroy(Brand $brand)
    {
        // Vérifier s'il y a des modèles associés
        if ($brand->models()->count() > 0) {
            return redirect()
                ->route('admin.brands.index')
                ->with('error', 'Impossible de supprimer une marque qui a des modèles associés.');
        }

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marque supprimée avec succès.');
    }
}