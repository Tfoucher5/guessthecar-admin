<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-pencil me-2"></i>Modifier la marque
                </h1>
                <p class="text-muted mb-0">{{ $brand->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-outline-info">
                    <i class="bi bi-eye me-2"></i>Voir détails
                </a>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-building me-2"></i>Informations de la marque
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Nom de la marque -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nom de la marque <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $brand->name) }}" placeholder="Ex: BMW, Mercedes-Benz..."
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pays -->
                    <div class="col-md-6">
                        <label for="country" class="form-label">Pays d'origine</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror" id="country"
                            name="country" value="{{ old('country', $brand->country) }}"
                            placeholder="Ex: Allemagne, France, Japon...">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- URL du logo -->
                    <div class="col-md-8">
                        <label for="logo_url" class="form-label">URL du logo</label>
                        <input type="url" class="form-control @error('logo_url') is-invalid @enderror" id="logo_url"
                            name="logo_url" value="{{ old('logo_url', $brand->logo_url) }}"
                            placeholder="https://exemple.com/logo.png">
                        @error('logo_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Ajoutez l'URL d'une image pour le logo de la marque
                        </div>
                    </div>

                    <!-- Année de fondation -->
                    <div class="col-md-4">
                        <label for="founded_year" class="form-label">Année de fondation</label>
                        <input type="number" class="form-control @error('founded_year') is-invalid @enderror"
                            id="founded_year" name="founded_year"
                            value="{{ old('founded_year', $brand->founded_year) }}" min="1800" max="{{ date('Y') }}"
                            placeholder="Ex: 1916">
                        @error('founded_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Prévisualisation du logo -->
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-eye me-2"></i>Prévisualisation
                                </h6>
                                <div id="logo-preview">
                                    <div class="d-flex align-items-center">
                                        <img id="preview-image" src="{{ $brand->logo_url }}"
                                            alt="Prévisualisation du logo" class="rounded me-3"
                                            style="width: 60px; height: 60px; object-fit: contain; {{ $brand->logo_url ? '' : 'display: none;' }}">
                                        <div>
                                            <h5 id="preview-name" class="mb-1">{{ $brand->name }}</h5>
                                            <small id="preview-details" class="text-muted">
                                                @php
                                                    $details = [];
                                                    if ($brand->country)
                                                        $details[] = $brand->country;
                                                    if ($brand->founded_year)
                                                        $details[] = "Fondée en {$brand->founded_year}";
                                                @endphp
                                                {{ implode(' • ', $details) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="col-12">
                        <div class="card bg-info bg-opacity-10 border-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="bi bi-info-circle me-2"></i>Informations supplémentaires
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Modèles associés</small>
                                        <strong>{{ $brand->models()->count() }} modèle(s)</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Créée le</small>
                                        <strong>{{ $brand->created_at->format('d/m/Y à H:i') }}</strong>
                                    </div>
                                </div>
                                @if($brand->models()->count() > 0)
                                    <div class="mt-2">
                                        <a href="{{ route('admin.models.index', ['brand_id' => $brand->id]) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-car-front me-1"></i>Voir les modèles
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div>
                        @if($brand->models()->count() == 0)
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette marque ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash me-2"></i>Supprimer
                                </button>
                            </form>
                        @else
                            <small class="text-muted">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Impossible de supprimer : cette marque a des modèles associés
                            </small>
                        @endif
                    </div>

                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-2"></i>Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Prévisualisation en temps réel
        function updatePreview() {
            const name = document.getElementById('name').value;
            const country = document.getElementById('country').value;
            const logoUrl = document.getElementById('logo_url').value;
            const foundedYear = document.getElementById('founded_year').value;

            const previewImageContainer = document.getElementById('preview-image-container');
            const previewImage = document.getElementById('preview-image');
            const fallbackLogo = document.getElementById('fallback-logo');
            const previewName = document.getElementById('preview-name');
            const previewDetails = document.getElementById('preview-details');

            previewName.textContent = name || 'Nom de la marque';
            fallbackLogo.textContent = name ? name.charAt(0).toUpperCase() : 'M';

            let details = [];
            if (country) details.push(country);
            if (foundedYear) details.push(`Fondée en ${foundedYear}`);
            previewDetails.textContent = details.join(' • ');

            if (logoUrl) {
                previewImage.src = logoUrl;
                previewImageContainer.style.display = 'block';
                fallbackLogo.style.display = 'none';
                previewImage.onerror = function () {
                    previewImageContainer.style.display = 'none';
                    fallbackLogo.style.display = 'flex';
                };
            } else {
                previewImageContainer.style.display = 'none';
                fallbackLogo.style.display = 'flex';
            }
        }

        // Écouter les changements sur tous les champs
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('country').addEventListener('input', updatePreview);
        document.getElementById('logo_url').addEventListener('input', updatePreview);
        document.getElementById('founded_year').addEventListener('input', updatePreview);

        // Prévisualisation initiale
        updatePreview();
    </script>
</x-admin-layout>