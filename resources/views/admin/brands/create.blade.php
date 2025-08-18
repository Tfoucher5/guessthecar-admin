<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle marque
                </h1>
                <p class="text-muted mb-0">Ajoutez une nouvelle marque automobile</p>
            </div>
            <div>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-building me-2"></i>Informations de la marque
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.brands.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <!-- Nom de la marque -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nom de la marque <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" placeholder="Ex: BMW, Mercedes-Benz..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pays -->
                    <div class="col-md-6">
                        <label for="country" class="form-label">Pays d'origine</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror" id="country"
                            name="country" value="{{ old('country') }}" placeholder="Ex: Allemagne, France, Japon...">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- URL du logo -->
                    <div class="col-md-8">
                        <label for="logo_url" class="form-label">URL du logo</label>
                        <input type="url" class="form-control @error('logo_url') is-invalid @enderror" id="logo_url"
                            name="logo_url" value="{{ old('logo_url') }}" placeholder="https://exemple.com/logo.png">
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
                            id="founded_year" name="founded_year" value="{{ old('founded_year') }}" min="1800"
                            max="{{ date('Y') }}" placeholder="Ex: 1916">
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
                                <div id="logo-preview" class="d-none">
                                    <div class="d-flex align-items-center">
                                        <img id="preview-image" src="" alt="Prévisualisation du logo"
                                            class="rounded me-3" style="width: 60px; height: 60px; object-fit: contain;">
                                        <div>
                                            <h5 id="preview-name" class="mb-1">Nom de la marque</h5>
                                            <small id="preview-details" class="text-muted"></small>
                                        </div>
                                    </div>
                                </div>
                                <div id="no-preview" class="text-muted">
                                    <i class="bi bi-image me-2"></i>
                                    Remplissez les champs pour voir la prévisualisation
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-2"></i>Créer la marque
                    </button>
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

            const previewDiv = document.getElementById('logo-preview');
            const noPreviewDiv = document.getElementById('no-preview');
            const previewImageContainer = document.getElementById('preview-image-container');
            const previewImage = document.getElementById('preview-image');
            const previewPlaceholder = document.getElementById('preview-placeholder');
            const previewName = document.getElementById('preview-name');
            const previewDetails = document.getElementById('preview-details');

            console.log('updatePreview called', { name, logoUrl }); // Debug

            if (name || logoUrl) {
                previewDiv.classList.remove('d-none');
                noPreviewDiv.classList.add('d-none');

                previewName.textContent = name || 'Nom de la marque';
                if (previewPlaceholder) {
                    previewPlaceholder.textContent = name ? name.charAt(0).toUpperCase() : 'M';
                }

                let details = [];
                if (country) details.push(country);
                if (foundedYear) details.push(`Fondée en ${foundedYear}`);
                previewDetails.textContent = details.join(' • ');

                if (logoUrl) {
                    console.log('Loading image:', logoUrl); // Debug
                    previewImage.src = logoUrl;
                    if (previewImageContainer) previewImageContainer.style.display = 'block';
                    if (previewPlaceholder) previewPlaceholder.style.display = 'none';

                    previewImage.onload = function () {
                        console.log('Image loaded successfully'); // Debug
                    };

                    previewImage.onerror = function () {
                        console.log('Image failed to load'); // Debug
                        if (previewImageContainer) previewImageContainer.style.display = 'none';
                        if (previewPlaceholder) previewPlaceholder.style.display = 'flex';
                    };
                } else {
                    if (previewImageContainer) previewImageContainer.style.display = 'none';
                    if (previewPlaceholder) previewPlaceholder.style.display = 'flex';
                }
            } else {
                previewDiv.classList.add('d-none');
                noPreviewDiv.classList.remove('d-none');
            }
        }

        // Écouter les changements sur tous les champs
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('name').addEventListener('input', updatePreview);
            document.getElementById('country').addEventListener('input', updatePreview);
            document.getElementById('logo_url').addEventListener('input', updatePreview);
            document.getElementById('founded_year').addEventListener('input', updatePreview);

            // Prévisualisation initiale
            updatePreview();
        });
    </script>
</x-admin-layout>