<x-admin-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Marques</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Modifier {{ $brand->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>
                            Modifier la marque : {{ $brand->name }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Formulaire principal -->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Nom de la marque <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $brand->name) }}" required
                                            oninput="updatePreview()">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="country" class="form-label">Pays d'origine</label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                                            id="country" name="country" value="{{ old('country', $brand->country) }}"
                                            placeholder="Ex: France, Allemagne, Japon..." oninput="updatePreview()">
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="founded_year" class="form-label">Année de fondation</label>
                                        <input type="number"
                                            class="form-control @error('founded_year') is-invalid @enderror"
                                            id="founded_year" name="founded_year"
                                            value="{{ old('founded_year', $brand->founded_year) }}" min="1800"
                                            max="{{ date('Y') }}" placeholder="Ex: 1886" oninput="updatePreview()">
                                        @error('founded_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="logo_url" class="form-label">URL du logo</label>
                                        <input type="url" class="form-control @error('logo_url') is-invalid @enderror"
                                            id="logo_url" name="logo_url"
                                            value="{{ old('logo_url', $brand->logo_url) }}"
                                            placeholder="https://example.com/logo.png" oninput="updatePreview()">
                                        @error('logo_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Formats recommandés: PNG, JPG, SVG (max 500 caractères)
                                        </div>
                                    </div>

                                    <!-- Boutons d'action -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg me-1"></i>
                                            Enregistrer les modifications
                                        </button>
                                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-x-lg me-1"></i>
                                            Annuler
                                        </a>
                                    </div>
                                </div>

                                <!-- Prévisualisation -->
                                <div class="col-lg-6">
                                    <div class="sticky-top" style="top: 20px;">
                                        <h6 class="text-muted mb-3">
                                            <i class="bi bi-eye me-1"></i>
                                            Prévisualisation
                                        </h6>

                                        <div class="card bg-light border-dashed">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <!-- Image simple sans fallback -->
                                                    <img id="preview-image" src="{{ $brand->logo_url }}"
                                                        alt="Logo de la marque"
                                                        class="img-fluid rounded shadow-sm mx-auto d-block"
                                                        style="max-width: 80px; max-height: 80px; object-fit: contain;">
                                                </div>

                                                <h6 id="preview-name" class="fw-bold text-dark mb-1">
                                                    {{ $brand->name ?: 'Nom de la marque' }}
                                                </h6>

                                                <p id="preview-details" class="text-muted small mb-0">
                                                    @php
                                                        $details = [];
                                                        if ($brand->country)
                                                            $details[] = $brand->country;
                                                        if ($brand->founded_year)
                                                            $details[] = 'Fondée en ' . $brand->founded_year;
                                                    @endphp
                                                    {{ implode(' • ', $details) ?: 'Informations de la marque' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Statistiques de la marque -->
                                        @if($brand->models->count() > 0)
                                            <div class="card mt-3">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3">
                                                        <i class="bi bi-bar-chart me-1"></i>
                                                        Statistiques actuelles
                                                    </h6>
                                                    <div class="row text-center">
                                                        <div class="col-6">
                                                            <div class="border-end">
                                                                <div class="fw-bold text-primary fs-4">
                                                                    {{ $brand->models->count() }}
                                                                </div>
                                                                <small class="text-muted">Modèles</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="fw-bold text-success fs-4">
                                                                {{ $brand->models->where('created_at', '>=', now()->subDays(30))->count() }}
                                                            </div>
                                                            <small class="text-muted">Ce mois</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Actions rapides -->
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <h6 class="card-title text-muted mb-3">
                                                    <i class="bi bi-tools me-1"></i>
                                                    Actions rapides
                                                </h6>
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('admin.models.index', ['brand_id' => $brand->id]) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-car-front me-1"></i>
                                                        Voir les modèles
                                                    </a>
                                                    <a href="{{ route('admin.models.create') }}?brand_id={{ $brand->id }}"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="bi bi-plus-lg me-1"></i>
                                                        Ajouter un modèle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prévisualisation en temps réel - VERSION SIMPLIFIÉE
        function updatePreview() {
            const name = document.getElementById('name').value;
            const country = document.getElementById('country').value;
            const logoUrl = document.getElementById('logo_url').value;
            const foundedYear = document.getElementById('founded_year').value;

            // Mise à jour du nom
            document.getElementById('preview-name').textContent = name || 'Nom de la marque';

            // Mise à jour des détails
            let details = [];
            if (country) details.push(country);
            if (foundedYear) details.push(`Fondée en ${foundedYear}`);
            document.getElementById('preview-details').textContent = details.length > 0 ? details.join(' • ') : 'Informations de la marque';

            // Mise à jour de l'image - SIMPLE
            document.getElementById('preview-image').src = logoUrl;
        }

        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', function () {
            updatePreview();
        });

        // Validation du formulaire
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('form').addEventListener('submit', function (e) {
                const name = document.getElementById('name').value.trim();

                if (!name) {
                    e.preventDefault();
                    alert('Le nom de la marque est obligatoire.');
                    document.getElementById('name').focus();
                    return false;
                }

                // Confirmation pour les modifications importantes
                const originalName = '{{ $brand->name }}';
                if (name !== originalName) {
                    if (!confirm(`Êtes-vous sûr de vouloir renommer "${originalName}" en "${name}" ?`)) {
                        e.preventDefault();
                        return false;
                    }
                }

                return true;
            });
        });
    </script>

    <style>
        .border-dashed {
            border: 2px dashed #dee2e6 !important;
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 1020;
        }

        .card-body .btn {
            transition: all 0.2s ease;
        }

        .card-body .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-admin-layout>