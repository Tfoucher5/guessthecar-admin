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

                                    <div class="mb-3">
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
                                                    <!-- Image avec fallback corrigé -->
                                                    <img id="preview-image" src="{{ $brand->logo_url }}" alt="Logo"
                                                        class="img-fluid rounded shadow-sm"
                                                        style="max-width: 80px; max-height: 80px; object-fit: contain; {{ $brand->logo_url ? 'display: block;' : 'display: none;' }}"
                                                        onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">

                                                    <!-- Fallback icon -->
                                                    <div id="fallback-logo"
                                                        class="d-flex align-items-center justify-content-center bg-secondary text-white rounded shadow-sm mx-auto"
                                                        style="width: 80px; height: 80px; {{ $brand->logo_url ? 'display: none;' : 'display: flex;' }}">
                                                        <i class="bi bi-image fs-3"></i>
                                                    </div>
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
                                                    {{ implode(' • ', $details) }}
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
                                                                    {{ $brand->models->count() }}</div>
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
        // Prévisualisation en temps réel
        function updatePreview() {
            const name = document.getElementById('name').value;
            const country = document.getElementById('country').value;
            const logoUrl = document.getElementById('logo_url').value;
            const foundedYear = document.getElementById('founded_year').value;

            // Référencer les bons éléments
            const previewImage = document.getElementById('preview-image');
            const fallbackLogo = document.getElementById('fallback-logo');
            const previewName = document.getElementById('preview-name');
            const previewDetails = document.getElementById('preview-details');

            // Mise à jour du nom
            previewName.textContent = name || 'Nom de la marque';

            // Mise à jour des détails
            let details = [];
            if (country) details.push(country);
            if (foundedYear) details.push(`Fondée en ${foundedYear}`);
            previewDetails.textContent = details.join(' • ');

            // Gestion de l'affichage image/fallback
            if (logoUrl && logoUrl.trim() !== '') {
                console.log('Chargement de l\'image:', logoUrl); // Debug

                // Afficher l'image, masquer le fallback
                previewImage.src = logoUrl;
                previewImage.style.display = 'block';
                fallbackLogo.style.display = 'none';

                // Gérer l'erreur de chargement
                previewImage.onload = function () {
                    console.log('Image chargée avec succès'); // Debug
                };

                previewImage.onerror = function () {
                    console.log('Erreur de chargement de l\'image'); // Debug
                    previewImage.style.display = 'none';
                    fallbackLogo.style.display = 'flex';
                };
            } else {
                // Pas d'URL, afficher le fallback
                previewImage.style.display = 'none';
                fallbackLogo.style.display = 'flex';
            }
        }

        // Initialiser la prévisualisation au chargement
        document.addEventListener('DOMContentLoaded', function () {
            updatePreview();
        });
    </script>
</x-admin-layout>