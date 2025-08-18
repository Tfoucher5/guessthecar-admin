<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-pencil me-2"></i>Modifier le modèle
                </h1>
                <p class="text-muted mb-0">{{ $model->brand->name }} {{ $model->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.show', $model->brand) }}" class="btn btn-outline-info">
                    <i class="bi bi-building me-2"></i>Voir la marque
                </a>
                <a href="{{ route('admin.models.index') }}" class="btn btn-outline-secondary">
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
                <i class="bi bi-car-front me-2"></i>Informations du modèle
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.models.update', $model) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Nom du modèle -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nom du modèle <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $model->name) }}"
                            placeholder="Ex: Golf GTI, Civic Type R..." required>
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
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $model->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Année -->
                    <div class="col-md-4">
                        <label for="year" class="form-label">Année</label>
                        <input type="number" class="form-control @error('year') is-invalid @enderror" id="year"
                            name="year" value="{{ old('year', $model->year) }}" min="1900" max="{{ date('Y') + 1 }}"
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
                                    <option value="{{ $level }}" {{ old('difficulty_level', $model->difficulty_level) == $level ? 'selected' : '' }}>
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
                            <small class="text-muted">Niveau actuel:
                                {{ $difficulties[$model->difficulty_level] ?? 'Non défini' }}</small>
                        </div>
                    </div>

                    <!-- URL de l'image -->
                    <div class="col-12">
                        <label for="image_url" class="form-label">URL de l'image</label>
                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url"
                            name="image_url" value="{{ old('image_url', $model->image_url) }}"
                            placeholder="https://exemple.com/image.jpg">
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
                                <div id="model-preview">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div id="preview-image-container"
                                                style="width: 120px; height: 120px; border: 1px solid #dee2e6; border-radius: 8px; background-color: white; padding: 4px; overflow: hidden; margin: 0 auto; {{ $model->image_url ? '' : 'display: none;' }}">
                                                <img id="preview-image" src="{{ $model->image_url }}"
                                                    alt="Prévisualisation"
                                                    style="width: 100%; height: 100%; object-fit: contain; object-position: center; border-radius: 4px;">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <h5 id="preview-name" class="mb-2">{{ $model->name }}</h5>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span id="preview-brand"
                                                    class="badge bg-primary fs-6">{{ $model->brand->name }}</span>
                                                @if($model->year)
                                                    <span id="preview-year"
                                                        class="badge bg-info fs-6">{{ $model->year }}</span>
                                                @else
                                                    <span id="preview-year" class="badge bg-info fs-6"
                                                        style="display: none;"></span>
                                                @endif
                                                <span id="preview-difficulty"
                                                    class="badge fs-6 
                                                    {{ $model->difficulty_level == 1 ? 'bg-success' : ($model->difficulty_level == 2 ? 'bg-warning' : 'bg-danger') }}">
                                                    <i
                                                        class="bi {{ $model->difficulty_level == 1 ? 'bi-emoji-smile' : ($model->difficulty_level == 2 ? 'bi-emoji-neutral' : 'bi-emoji-frown') }} me-1"></i>
                                                    {{ $difficulties[$model->difficulty_level] ?? 'Non défini' }}
                                                </span>
                                            </div>
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
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Créé le</small>
                                        <strong>{{ $model->created_at->format('d/m/Y à H:i') }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Modifié le</small>
                                        <strong>{{ $model->updated_at->format('d/m/Y à H:i') }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">ID du modèle</small>
                                        <strong>#{{ $model->id }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.models.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire de suppression séparé -->
    <div class="card mt-3 border-danger">
        <div class="card-header bg-danger text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>Zone de danger
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                La suppression de ce modèle est définitive et irréversible.
                Toutes les données associées seront perdues.
            </p>
            <form action="{{ route('admin.models.destroy', $model) }}" method="POST"
                onsubmit="return confirm('Êtes-vous vraiment sûr de vouloir supprimer ce modèle ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>Supprimer définitivement ce modèle
                </button>
            </form>
        </div>
    </div>

    <script>
        // Configuration des niveaux de difficulté
        const difficultyInfo = {
            1: {
                class: 'alert-success',
                icon: 'bi-emoji-smile',
                text: 'Facile - Modèle populaire, facile à deviner',
                badge: 'bg-success'
            },
            2: {
                class: 'alert-warning',
                icon: 'bi-emoji-neutral',
                text: 'Moyen - Difficulté modérée pour les joueurs',
                badge: 'bg-warning'
            },
            3: {
                class: 'alert-danger',
                icon: 'bi-emoji-frown',
                text: 'Difficile - Modèle rare ou très spécialisé',
                badge: 'bg-danger'
            }
        };

        // Mise à jour de l'indicateur de difficulté
        function updateDifficultyIndicator() {
            const level = document.getElementById('difficulty_level').value;
            const indicator = document.getElementById('difficulty-indicator');

            if (level && difficultyInfo[level]) {
                const info = difficultyInfo[level];
                indicator.className = `alert ${info.class} border`;
                indicator.innerHTML = `<i class="bi ${info.icon} me-2"></i>${info.text}`;
            } else {
                indicator.className = 'alert alert-light border';
                indicator.innerHTML = '<small class="text-muted">Sélectionnez un niveau de difficulté</small>';
            }
        }

        // Prévisualisation du modèle
        function updatePreview() {
            const name = document.getElementById('name').value;
            const brandSelect = document.getElementById('brand_id');
            const brandName = brandSelect.options[brandSelect.selectedIndex]?.text || '';
            const year = document.getElementById('year').value;
            const imageUrl = document.getElementById('image_url').value;
            const difficultyLevel = document.getElementById('difficulty_level').value;

            const previewImageContainer = document.getElementById('preview-image-container');
            const previewImage = document.getElementById('preview-image');
            const previewPlaceholder = document.getElementById('preview-placeholder');
            const previewName = document.getElementById('preview-name');
            const previewBrand = document.getElementById('preview-brand');
            const previewYear = document.getElementById('preview-year');
            const previewDifficulty = document.getElementById('preview-difficulty');

            previewName.textContent = name || 'Nom du modèle';

            // Marque
            if (brandName && brandName !== 'Sélectionner une marque') {
                previewBrand.textContent = brandName;
                previewBrand.style.display = 'inline';
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
                previewDifficulty.className = `badge fs-6 ${info.badge}`;
                previewDifficulty.innerHTML = `<i class="bi ${info.icon} me-1"></i>${difficultyLevel == 1 ? 'Facile' : difficultyLevel == 2 ? 'Moyen' : 'Difficile'}`;
                previewDifficulty.style.display = 'inline';
            }

            // Image
            if (imageUrl) {
                previewImage.src = imageUrl;
                if (previewImageContainer) previewImageContainer.style.display = 'block';
                if (previewPlaceholder) previewPlaceholder.style.display = 'none';
                previewImage.onerror = function () {
                    if (previewImageContainer) previewImageContainer.style.display = 'none';
                    if (previewPlaceholder) previewPlaceholder.style.display = 'flex';
                };
            } else {
                if (previewImageContainer) previewImageContainer.style.display = 'none';
                if (previewPlaceholder) previewPlaceholder.style.display = 'flex';
            }
        }

        // Écouter les changements sur tous les champs
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('name').addEventListener('input', updatePreview);
            document.getElementById('brand_id').addEventListener('change', updatePreview);
            document.getElementById('year').addEventListener('input', updatePreview);
            document.getElementById('image_url').addEventListener('input', updatePreview);
            document.getElementById('difficulty_level').addEventListener('change', function () {
                updateDifficultyIndicator();
                updatePreview();
            });

            // Initialisation
            updateDifficultyIndicator();
            updatePreview();
        });
    </script>
</x-admin-layout>