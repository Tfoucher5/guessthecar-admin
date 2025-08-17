<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-car-front me-2"></i>Gestion des Modèles
                </h1>
                <p class="text-muted mb-0">Gérez les modèles de voitures de votre plateforme</p>
            </div>
            <a href="{{ route('admin.models.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Nouveau modèle
            </a>
        </div>
    </x-slot>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-auto-hide" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-auto-hide" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtres et recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.models.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" name="search" id="search"
                            value="{{ request('search') }}" placeholder="Nom du modèle...">
                    </div>

                    <div class="col-md-3">
                        <label for="brand_id" class="form-label">Marque</label>
                        <select name="brand_id" id="brand_id" class="form-select">
                            <option value="">Toutes les marques</option>
                            @if(isset($brands))
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="difficulty_level" class="form-label">Difficulté</label>
                        <select name="difficulty_level" id="difficulty_level" class="form-select">
                            <option value="">Toutes</option>
                            @if(isset($difficulties))
                                @foreach($difficulties as $level => $label)
                                    <option value="{{ $level }}" {{ request('difficulty_level') == $level ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="year_from" class="form-label">Année min</label>
                        <input type="number" class="form-control" name="year_from" id="year_from"
                            value="{{ request('year_from') }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="1990">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.models.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques rapides -->
    @if(isset($models) && $models->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ $models->total() }}</h4>
                        <small>Total modèles</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ $models->where('difficulty_level', 1)->count() }}</h4>
                        <small>Faciles</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ $models->where('difficulty_level', 2)->count() }}</h4>
                        <small>Moyens</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ $models->where('difficulty_level', 3)->count() }}</h4>
                        <small>Difficiles</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des modèles -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Liste des modèles
                <span class="badge bg-secondary">{{ isset($models) ? $models->total() : 0 }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($models) && $models->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Modèle
                                        @if(request('sort') == 'name')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'brand_name', 'direction' => request('sort') == 'brand_name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Marque
                                        @if(request('sort') == 'brand_name')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Année</th>
                                <th>Difficulté</th>
                                <th>Image</th>
                                <th>Créé le</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($models as $model)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $model->name }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($model->brand && $model->brand->logo_url)
                                                <img src="{{ $model->brand->logo_url }}" class="rounded me-2"
                                                    style="width: 24px; height: 24px; object-fit: cover;"
                                                    alt="{{ $model->brand->name }}">
                                            @endif
                                            <span class="text-dark">{{ $model->brand->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $model->year ?: 'Non spécifiée' }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $difficultyConfig = [
                                                1 => ['class' => 'bg-success', 'text' => 'Facile', 'icon' => 'emoji-smile'],
                                                2 => ['class' => 'bg-warning', 'text' => 'Moyen', 'icon' => 'emoji-neutral'],
                                                3 => ['class' => 'bg-danger', 'text' => 'Difficile', 'icon' => 'emoji-frown']
                                            ];
                                            $config = $difficultyConfig[$model->difficulty_level] ?? ['class' => 'bg-secondary', 'text' => 'Inconnu', 'icon' => 'question'];
                                        @endphp
                                        <span class="badge {{ $config['class'] }}">
                                            <i class="bi bi-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($model->image_url)
                                            <img src="{{ $model->image_url }}" class="rounded"
                                                style="width: 40px; height: 30px; object-fit: cover;" alt="{{ $model->name }}"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                            <span class="text-muted small" style="display: none;">Pas d'image</span>
                                        @else
                                            <span class="text-muted small">Pas d'image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $model->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.models.show', $model) }}" class="btn btn-outline-info"
                                                title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.models.edit', $model) }}" class="btn btn-outline-warning"
                                                title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.models.destroy', $model) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($models->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Affichage de {{ $models->firstItem() }} à {{ $models->lastItem() }}
                                sur {{ $models->total() }} résultats
                            </div>
                            {{ $models->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-car-front text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-dark">Aucun modèle trouvé</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'brand_id', 'difficulty_level', 'year_from']))
                            Aucun modèle ne correspond à vos critères de recherche.
                        @else
                            Commencez par créer votre premier modèle.
                        @endif
                    </p>
                    <a href="{{ route('admin.models.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-lg me-2"></i>Créer un modèle
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>