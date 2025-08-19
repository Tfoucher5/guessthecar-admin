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

        // Filtres de recherche
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('country', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('founded_year_from')) {
            $query->where('founded_year', '>=', $request->founded_year_from);
        }

        if ($request->filled('founded_year_to')) {
            $query->where('founded_year', '<=', $request->founded_year_to);
        }

        $brands = $query->orderBy('name')->paginate(15);

        // Statistiques
        $stats = [
            'total' => Brand::count(),
            'with_logo' => Brand::whereNotNull('logo_url')->count(),
            'by_country' => Brand::selectRaw('country, COUNT(*) as count')
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'recent' => Brand::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $countries = Brand::whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('admin.brands.index', compact('brands', 'stats', 'countries'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Sauvegarde d'une nouvelle marque
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
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
            'year_range' => [
                'min' => $brand->models->min('year'),
                'max' => $brand->models->max('year'),
            ],
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

    /**
     * Import en masse de marques
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'brands' => 'required|array|min:1',
            'brands.*.name' => 'required|string|max:255',
            'brands.*.country' => 'nullable|string|max:100',
            'brands.*.logo_url' => 'nullable|url|max:500',
            'brands.*.founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($request->brands as $brandData) {
            // Vérifier les doublons
            $exists = Brand::where('name', $brandData['name'])->exists();

            if (!$exists) {
                Brand::create($brandData);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Import terminé : {$created} marques créées";
        if ($skipped > 0) {
            $message .= ", {$skipped} marques ignorées (doublons)";
        }

        return redirect()
            ->route('admin.brands.index')
            ->with('success', $message);
    }

    /**
     * API pour récupérer toutes les marques (pour AJAX)
     */
    public function api()
    {
        $brands = Brand::orderBy('name')->get(['id', 'name', 'country', 'logo_url']);
        return response()->json($brands);
    }

    /**
     * Validation d'URL de logo
     */
    public function validateLogo(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        // Tester si l'URL est accessible
        try {
            $headers = get_headers($request->url, 1);
            $isValid = strpos($headers[0], '200') !== false;

            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'URL valide' : 'URL non accessible'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Erreur lors de la validation de l\'URL'
            ]);
        }
    }
}