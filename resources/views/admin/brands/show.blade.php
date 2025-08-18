<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-building me-2"></i>{{ $brand->name }}
                </h1>
                <p class="text-muted mb-0">Détails de la marque automobile</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Modifier
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

    <div class="row g-4">
        <!-- Informations principales -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informations de la marque
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        @if($brand->logo_url)
                            <img src="{{ $brand->logo_url }}" 
                                 alt="{{ $brand->name }}"
                                 class="rounded-circle border border-3 border-light shadow"
                                 style="width: 120px; height: 120px; object-fit: cover;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="bg-primary text-white rounded-circle border border-3 border-light shadow d-none align-items-center justify-content-center mx-auto" 
                                 style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                {{ substr($brand->name, 0, 1) }}
                            </div>
                        @else
                            <div class="bg-primary text-white rounded-circle border border-3 border-light shadow d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                {{ substr($brand->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Détails -->
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-medium text-muted ps-0">Nom :</td>
                                    <td class="text-end pe-0">
                                        <strong>{{ $brand->name }}</strong>
                                    </td>
                                </tr>
                                @if($brand->country)
                                <tr>
                                    <td class="fw-medium text-muted ps-0">Pays :</td>
                                    <td class="text-end pe-0">
                                        <span class="badge bg-info">{{ $brand->country }}</span>
                                    </td>
                                </tr>
                                @endif
                                @if($brand->founded_year)
                                <tr>
                                    <td class="fw-medium text-muted ps-0">Fondée en :</td>
                                    <td class="text-end pe-0">
                                        <strong>{{ $brand->founded_year }}</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-medium text-muted ps-0">Modèles :</td>
                                    <td class="text-end pe-0">
                                        <span class="badge bg-success fs-6">{{ $stats['total_models'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted ps-0">Créée le :</td>
                                    <td class="text-end pe-0">
                                        <small class="text-muted">{{ $brand->created_at->format('d/m/Y à H:i') }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques et actions -->
        <div class="col-lg-8">
            <!-- Statistiques des modèles -->
            @if($stats['total_models'] > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bar-chart me-2"></i>Statistiques des modèles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-car-front fs-1 text-primary mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['total_models'] }}</h4>
                                    <small class="text-muted">Total modèles</small>
                                </div>
                            </div>
                            
                            @if($stats['by_difficulty']->count() > 0)
                                @foreach([1 => 'Facile', 2 => 'Moyen', 3 => 'Difficile'] as $level => $label)
                                    @if($stats['by_difficulty']->has($level))
                                        <div class="col-md-4">
                                            <div class="text-center p-3 bg-light rounded">
                                                @if($level == 1)
                                                    <i class="bi bi-emoji-smile fs-1 text-success mb-2"></i>
                                                @elseif($level == 2)
                                                    <i class="bi bi-emoji-neutral fs-1 text-warning mb-2"></i>
                                                @else
                                                    <i class="bi bi-emoji-frown fs-1 text-danger mb-2"></i>
                                                @endif
                                                <h4 class="mb-0">{{ $stats['by_difficulty'][$level] }}</h4>
                                                <small class="text-muted">{{ $label }}</small>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.models.create', ['brand_id' => $brand->id]) }}" 
                                   class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Ajouter un modèle
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.models.index', ['brand_id' => $brand->id]) }}" 
                                   class="btn btn-outline-info btn-lg">
                                    <i class="bi bi-list me-2"></i>
                                    Voir tous les modèles
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.brands.edit', $brand) }}" 
                                   class="btn btn-outline-warning btn-lg">
                                    <i class="bi bi-pencil me-2"></i>
                                    Modifier la marque
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                @if($stats['total_models'] == 0)
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette marque ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-lg w-100">
                                            <i class="bi bi-trash me-2"></i>
                                            Supprimer la marque
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-outline-danger btn-lg" disabled title="Impossible de supprimer : cette marque a des modèles associés">
                                        <i class="bi bi-shield-exclamation me-2"></i>
                                        Suppression bloquée
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des modèles -->
    @if($brand->models->count() > 0)
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-car-front me-2"></i>Modèles de {{ $brand->name }}
                    <span class="badge bg-secondary ms-2">{{ $brand->models->count() }}</span>
                </h5>
                <a href="{{ route('admin.models.create', ['brand_id' => $brand->id]) }}" 
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-plus me-1"></i>Nouveau modèle
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Nom du modèle</th>
                                <th>Année</th>
                                <th>Difficulté</th>
                                <th>Créé le</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brand->models as $model)
                                <tr>
                                    <td>
                                        @if($model->image_url)
                                            <img src="{{ $model->image_url }}" 
                                                 alt="{{ $model->name }}"
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="bg-secondary text-white rounded d-none align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                <i class="bi bi-car-front"></i>
                                            </div>
                                        @else
                                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                <i class="bi bi-car-front"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $model->name }}</strong>
                                    </td>
                                    <td>
                                        @if($model->year)
                                            <span class="badge bg-light text-dark">{{ $model->year }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($model->difficulty_level == 1)
                                            <span class="badge bg-success">
                                                <i class="bi bi-emoji-smile me-1"></i>Facile
                                            </span>
                                        @elseif($model->difficulty_level == 2)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-emoji-neutral me-1"></i>Moyen
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-emoji-frown me-1"></i>Difficile
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $model->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.models.edit', $model) }}" 
                                               class="btn btn-outline-warning btn-sm" 
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-car-front text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-dark">Aucun modèle</h4>
                <p class="text-muted mb-4">Cette marque n'a encore aucun modèle associé.</p>
                <a href="{{ route('admin.models.create', ['brand_id' => $brand->id]) }}" 
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Ajouter le premier modèle
                </a>
            </div>
        </div>
    @endif
</x-admin-layout>