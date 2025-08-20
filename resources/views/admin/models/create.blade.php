<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau modèle
                </h1>
                <p class="text-muted mb-0">Ajoutez un nouveau modèle de voiture</p>
            </div>
            <div>
                <a href="{{ route('admin.models.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-car-front me-2"></i>Informations du modèle
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.models.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <!-- Nom du modèle -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nom du modèle <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" placeholder="Ex: Golf GTI, Civic Type R..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Marque -->
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label">
                            Marque <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id"
                            name="brand_id" required>
                            <option value="">Sélectionner une marque</option>
                            @if(isset($brands))
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', request('brand_id')) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Vous ne trouvez pas la marque ?
                            <a href="{{ route('admin.brands.create') }}" target="_blank" class="text-decoration-none">
                                Créez-la d'abord
                            </a>
                        </div>
                    </div>

                    <!-- Année -->
                    <div class="col-md-4">
                        <label for="year" class="form-label">Année</label>
                        <input type="number" class="form-control @error('year') is-invalid @enderror" id="year"
                            name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}"
                            placeholder="Ex: 2020">
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Niveau de difficulté -->
                    <div class="col-md-4">
                        <label for="difficulty_level" class="form-label">
                            Niveau de difficulté <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('difficulty_level') is-invalid @enderror"
                            id="difficulty_level" name="difficulty_level" required>
                            <option value="">Choisir le niveau</option>
                            @if(isset($difficulties))
                                @foreach($difficulties as $level => $label)
                                    <option value="{{ $level }}" {{ old('difficulty_level') == $level ? 'selected' : '' }}>
                                        @if($level == 1)
                                            🟢 {{ $label }}
                                        @elseif($level == 2)
                                            🟡 {{ $label }}
                                        @else
                                            🔴 {{ $label }}
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('difficulty_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Indicateur de difficulté -->
                    <div class="col-md-4">
                        <label class="form-label">Impact sur le jeu</label>
                        <div id="difficulty-indicator" class="alert alert-light border">
                            <small class="text-muted">Sélectionnez un niveau de difficulté</small>
                        </div>
                    </div>

                    <!-- URL de l'image -->
                    <div class="col-12">
                        <label for="image_url" class="form-label">URL de l'image</label>
                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url"
                            name="image_url" value="{{ old('image_url') }}" placeholder="https://exemple.com/image.jpg">
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Ajoutez l'URL d'une image haute qualité du modèle
                        </div>
                    </div>

                    <!-- Prévisualisation -->
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-eye me-2"></i>Prévisualisation
                                </h6>
                                <div id="model-preview" class="d-none">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <!-- Image simplifiée comme dans edit.blade.php -->
                                            <img id="preview-image" src="" alt="Prévisualisation"
                                                class="img-fluid rounded shadow-sm mx-auto d-block"
                                                style="max-width: 120px; max-height: 120px; object-fit: contain;">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 id="preview-name" class="mb-2">Nom du modèle</h5>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span id="preview-brand" class="badge bg-primary fs-6"></span>
                                                <span id="preview-year" class="badge bg-info fs-6"></span>
                                                <span id="preview-difficulty" class="badge fs-6"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="no-preview" class="text-muted text-center py-3">
                                    <i class="bi bi-image me-2"></i>
                                    Remplissez les champs pour voir la prévisualisation
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOUTONS D'ACTION AJOUTÉS -->
                    <div class="col-12">
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>
                                Créer le modèle
                            </button>
                            <a href="{{ route('admin.models.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i>
                                Annuler
                            </a>
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Configuration des niveaux de difficulté
        const difficultyInfo = {
            1: {
                class: 'alert-success',
                icon: 'bi-emoji-smile',
                text: 'Facile - Modèle populaire, facile à deviner'
            },
            2: {
                class: 'alert-warning',
                icon: 'bi-emoji-neutral',
                text: 'Moyen - Difficulté modérée pour les joueurs'
            },
            3: {
                class: 'alert-danger',
                icon: 'bi-emoji-frown',
                text: 'Difficile - Modèle rare ou très spécialisé'
            }
        };

        // Fonction pour mettre à jour l'indicateur de difficulté
        function updateDifficultyIndicator() {
            const difficultyLevel = document.getElementById('difficulty_level').value;
            const indicator = document.getElementById('difficulty-indicator');

            if (difficultyLevel && difficultyInfo[difficultyLevel]) {
                const info = difficultyInfo[difficultyLevel];
                indicator.className = `alert ${info.class} border`;
                indicator.innerHTML = `<i class="bi ${info.icon} me-2"></i>${info.text}`;
            } else {
                indicator.className = 'alert alert-light border';
                indicator.innerHTML = '<small class="text-muted">Sélectionnez un niveau de difficulté</small>';
            }
        }

        // Fonction pour mettre à jour la prévisualisation - SIMPLIFIÉE
        function updatePreview() {
            const name = document.getElementById('name').value;
            const brandSelect = document.getElementById('brand_id');
            const brandName = brandSelect.options[brandSelect.selectedIndex].text;
            const year = document.getElementById('year').value;
            const imageUrl = document.getElementById('image_url').value;
            const difficultyLevel = document.getElementById('difficulty_level').value;

            const previewDiv = document.getElementById('model-preview');
            const noPreviewDiv = document.getElementById('no-preview');
            const previewImage = document.getElementById('preview-image');
            const previewName = document.getElementById('preview-name');
            const previewBrand = document.getElementById('preview-brand');
            const previewYear = document.getElementById('preview-year');
            const previewDifficulty = document.getElementById('preview-difficulty');

            if (name || brandName !== 'Sélectionner une marque' || year || difficultyLevel) {
                previewDiv.classList.remove('d-none');
                noPreviewDiv.classList.add('d-none');

                previewName.textContent = name || 'Nom du modèle';

                // Marque
                if (brandName && brandName !== 'Sélectionner une marque') {
                    previewBrand.textContent = brandName;
                    previewBrand.style.display = 'inline';
                } else {
                    previewBrand.style.display = 'none';
                }

                // Année
                if (year) {
                    previewYear.textContent = year;
                    previewYear.style.display = 'inline';
                } else {
                    previewYear.style.display = 'none';
                }

                // Difficulté
                if (difficultyLevel && difficultyInfo[difficultyLevel]) {
                    const info = difficultyInfo[difficultyLevel];
                    previewDifficulty.className = `badge fs-6 ${info.class.replace('alert-', 'bg-')}`;
                    previewDifficulty.innerHTML = `<i class="bi ${info.icon} me-1"></i>${difficultyLevel == 1 ? 'Facile' : difficultyLevel == 2 ? 'Moyen' : 'Difficile'}`;
                    previewDifficulty.style.display = 'inline';
                } else {
                    previewDifficulty.style.display = 'none';
                }

                // Image - VERSION SIMPLIFIÉE comme dans edit.blade.php
                previewImage.src = imageUrl;
            } else {
                previewDiv.classList.add('d-none');
                noPreviewDiv.classList.remove('d-none');
            }
        }

        // Écouter les changements sur tous les champs
        document.addEventListener('DOMContentLoaded', function () {
            const nameField = document.getElementById('name');
            const brandField = document.getElementById('brand_id');
            const yearField = document.getElementById('year');
            const imageField = document.getElementById('image_url');
            const difficultyField = document.getElementById('difficulty_level');

            if (nameField) nameField.addEventListener('input', updatePreview);
            if (brandField) brandField.addEventListener('change', updatePreview);
            if (yearField) yearField.addEventListener('input', updatePreview);
            if (imageField) imageField.addEventListener('input', updatePreview);
            if (difficultyField) {
                difficultyField.addEventListener('change', function () {
                    updatePreview();
                    updateDifficultyIndicator();
                });
            }

            // Validation du formulaire
            document.querySelector('form').addEventListener('submit', function (e) {
                const name = document.getElementById('name').value.trim();
                const brandId = document.getElementById('brand_id').value;
                const difficultyLevel = document.getElementById('difficulty_level').value;

                if (!name) {
                    e.preventDefault();
                    alert('Le nom du modèle est obligatoire.');
                    document.getElementById('name').focus();
                    return false;
                }

                if (!brandId) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une marque.');
                    document.getElementById('brand_id').focus();
                    return false;
                }

                if (!difficultyLevel) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un niveau de difficulté.');
                    document.getElementById('difficulty_level').focus();
                    return false;
                }

                return true;
            });

            // Réinitialisation du formulaire
            document.querySelector('button[type="reset"]').addEventListener('click', function () {
                setTimeout(function () {
                    updatePreview();
                    updateDifficultyIndicator();
                }, 10);
            });

            // Initialisation
            updatePreview();
            updateDifficultyIndicator();
        });
    </script>
</x-admin-layout>