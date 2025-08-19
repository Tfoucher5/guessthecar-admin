{{-- resources/views/admin/models/show.blade.php --}}
<x-admin-layout>
    <x-slot name="title">{{ $model->brand->name ?? 'Marque' }} {{ $model->name }}</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.models.index') }}" class="text-decoration-none">
                                <i class="bi bi-car-front me-1"></i>Modèles
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $model->brand->name ?? 'Marque' }} {{ $model->name }}
                        </li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 text-dark">
                    {{ $model->brand->name ?? 'Marque inconnue' }} {{ $model->name }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.models.edit', $model) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <form method="POST" action="{{ route('admin.models.destroy', $model) }}" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Informations du modèle -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informations du modèle
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Image du modèle -->
                        @if($model->image_url)
                            <div class="col-12 text-center mb-3">
                                <img src="{{ $model->image_url }}" 
                                     alt="{{ $model->brand->name ?? 'Marque' }} {{ $model->name }}" 
                                     class="img-fluid rounded shadow-sm"
                                     style="max-height: 300px; object-fit: contain;">
                            </div>
                        @endif

                        <!-- Détails -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom du modèle</label>
                            <p class="form-control-plaintext">{{ $model->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Marque</label>
                            <p class="form-control-plaintext">
                                @if($model->brand)
                                    <a href="{{ route('admin.brands.show', $model->brand) }}" class="text-decoration-none">
                                        @if($model->brand->logo_url)
                                            <img src="{{ $model->brand->logo_url }}" 
                                                 alt="{{ $model->brand->name }}" 
                                                 class="me-2"
                                                 style="height: 20px; object-fit: contain;">
                                        @endif
                                        {{ $model->brand->name }}
                                        @if($model->brand->country)
                                            <span class="text-muted">({{ $model->brand->country }})</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-muted">Marque inconnue</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Année</label>
                            <p class="form-control-plaintext">
                                {{ $model->year ?? 'Non spécifiée' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Niveau de difficulté</label>
                            <p class="form-control-plaintext">
                                @php
                                    $difficultyLabels = [1 => 'Facile', 2 => 'Moyen', 3 => 'Difficile'];
                                    $difficultyColors = [1 => 'success', 2 => 'warning', 3 => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $difficultyColors[$model->difficulty_level] ?? 'secondary' }}">
                                    {{ $difficultyLabels[$model->difficulty_level] ?? 'Inconnu' }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de création</label>
                            <p class="form-control-plaintext">
                                {{ $model->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dernière modification</label>
                            <p class="form-control-plaintext">
                                {{ $model->updated_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>

                        @if($model->image_url)
                            <div class="col-12">
                                <label class="form-label fw-bold">URL de l'image</label>
                                <p class="form-control-plaintext">
                                    <a href="{{ $model->image_url }}" target="_blank" class="text-break">
                                        {{ $model->image_url }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-1 text-primary">{{ $stats['times_found'] ?? 0 }}</div>
                                <div class="small text-muted">Fois trouvé</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-1 text-info">{{ $stats['total_sessions'] ?? 0 }}</div>
                                <div class="small text-muted">Sessions totales</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-1 text-success">{{ $stats['success_rate'] ?? 0 }}%</div>
                                <div class="small text-muted">Taux de réussite</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-1 text-warning">{{ $stats['average_attempts'] ?? 0 }}</div>
                                <div class="small text-muted">Tentatives moy.</div>
                            </div>
                        </div>
                        @if(isset($stats['fastest_time']) && $stats['fastest_time'])
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-1 text-success">{{ $stats['fastest_time'] }}s</div>
                                    <div class="small text-muted">Temps le plus rapide</div>
                                </div>
                            </div>
                        @endif
                        @if(isset($stats['slowest_time']) && $stats['slowest_time'])
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-1 text-danger">{{ $stats['slowest_time'] }}s</div>
                                    <div class="small text-muted">Temps le plus lent</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Récentes trouvailles -->
    @if(isset($recentFinds) && $recentFinds->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Récentes trouvailles
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Joueur</th>
                                <th>Tentatives</th>
                                <th>Temps</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentFinds as $find)
                                <tr>
                                    <td>
                                        @if($find->userScore)
                                            <a href="{{ route('admin.players.show', $find->userScore) }}" 
                                               class="text-decoration-none">
                                                {{ $find->userScore->username }}
                                            </a>
                                        @else
                                            <span class="text-muted">Joueur inconnu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $find->attempts_used <= 3 ? 'bg-success' : ($find->attempts_used <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $find->attempts_used }} essais
                                        </span>
                                    </td>
                                    <td>
                                        @if($find->time_taken)
                                            <span class="badge bg-info">{{ $find->time_taken }}s</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $find->found_at->diffForHumans() }}</span>
                                        <br>
                                        <small class="text-muted">{{ $find->found_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>