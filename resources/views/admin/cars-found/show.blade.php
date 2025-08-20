{{-- resources/views/admin/cars-found/show.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Détail de la trouvaille</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Détail de la trouvaille</h1>
                <p class="text-muted mb-0">
                    {{ $carFound->userScore->username ?? 'Utilisateur inconnu' }} - 
                    {{ $carFound->carModel->brand->name ?? 'N/A' }} {{ $carFound->carModel->name ?? 'N/A' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cars-found.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
                <form method="POST" action="{{ route('admin.cars-found.destroy', $carFound->id) }}" 
                      class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations de la trouvaille
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Joueur -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ strtoupper(substr($carFound->userScore->username ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $carFound->userScore->username ?? 'Utilisateur inconnu' }}</h6>
                                    <small class="text-muted">
                                        Score total: {{ number_format($carFound->userScore->total_points ?? 0) }} pts
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Voiture -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                @if($carFound->carModel->image_url)
                                    <img src="{{ $carFound->carModel->image_url }}" 
                                         alt="{{ $carFound->carModel->name }}"
                                         class="rounded me-3"
                                         style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-3"
                                         style="width: 48px; height: 48px;">
                                        <i class="bi bi-car-front"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $carFound->carModel->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">
                                        {{ $carFound->carModel->year ?? 'N/A' }} • 
                                        <span class="badge {{ $carFound->carModel->difficulty_level <= 2 ? 'bg-success' : ($carFound->carModel->difficulty_level <= 3 ? 'bg-warning' : 'bg-danger') }}">
                                            Niveau {{ $carFound->carModel->difficulty_level ?? 'N/A' }}
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Marque -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="me-3 fs-2">
                                    @php
                                        $flags = [
                                            'France' => '🇫🇷',
                                            'Allemagne' => '🇩🇪',
                                            'Italie' => '🇮🇹',
                                            'Espagne' => '🇪🇸',
                                            'Royaume-Uni' => '🇬🇧',
                                            'États-Unis' => '🇺🇸',
                                            'Japon' => '🇯🇵',
                                            'Corée du Sud' => '🇰🇷',
                                            'Chine' => '🇨🇳',
                                            'Suède' => '🇸🇪',
                                            'Norvège' => '🇳🇴',
                                            'Pays-Bas' => '🇳🇱',
                                            'Belgique' => '🇧🇪',
                                            'Suisse' => '🇨🇭',
                                            'Autriche' => '🇦🇹',
                                            'République tchèque' => '🇨🇿',
                                            'Pologne' => '🇵🇱',
                                            'Russie' => '🇷🇺',
                                            'Inde' => '🇮🇳',
                                            'Brésil' => '🇧🇷',
                                            'Canada' => '🇨🇦',
                                            'Australie' => '🇦🇺',
                                            'Roumanie' => '🇷🇴',
                                            'Malaisie' => '🇲🇾'
                                        ];
                                        echo $flags[$carFound->carModel->brand->country] ?? '🌍';
                                    @endphp
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $carFound->carModel->brand->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ $carFound->carModel->brand->country ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Performance -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="mb-2">Performance</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="fs-4 fw-bold text-primary">{{ $carFound->attempts_used }}</div>
                                            <small class="text-muted">Tentatives</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="fs-4 fw-bold text-success">
                                                @if($carFound->time_taken)
                                                    {{ gmdate('i:s', $carFound->time_taken) }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <small class="text-muted">Temps</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Détails additionnels -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted">Trouvé le</label>
                            <div class="fw-medium">{{ $carFound->found_at->format('d/m/Y à H:i') }}</div>
                            <small class="text-muted">{{ $carFound->found_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Serveur Discord</label>
                            <div class="fw-medium">{{  getGuildName($carFound->guild_id) ?? 'Non spécifié' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">ID de trouvaille</label>
                            <div class="fw-medium">#{{ $carFound->id }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques de la voiture -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiques de cette voiture
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                <div class="fs-5 fw-bold text-primary">{{ $carStats['total_found'] }}</div>
                                <small class="text-muted">Fois trouvée</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                <div class="fs-5 fw-bold text-warning">{{ number_format($carStats['avg_attempts'], 1) }}</div>
                                <small class="text-muted">Tentatives moy.</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                <div class="fs-6 fw-bold text-success">
                                    @if($carStats['fastest_find'])
                                        {{ gmdate('i:s', $carStats['fastest_find']) }}
                                    @else
                                        -
                                    @endif
                                </div>
                                <small class="text-muted">Plus rapide</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                                <div class="fs-6 fw-bold text-danger">
                                    @if($carStats['slowest_find'])
                                        {{ gmdate('i:s', $carStats['slowest_find']) }}
                                    @else
                                        -
                                    @endif
                                </div>
                                <small class="text-muted">Plus lent</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Première trouvaille</span>
                            <span>{{ \Carbon\Carbon::parse($carStats['first_found'])->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Dernière trouvaille</span>
                            <span>{{ \Carbon\Carbon::parse($carStats['last_found'])->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autres trouveurs -->
            @if($otherFinds->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-people me-2"></i>
                            Autres joueurs ({{ $otherFinds->count() }})
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($otherFinds as $otherFind)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 24px; height: 24px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($otherFind->userScore->username ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium small">{{ $otherFind->userScore->username ?? 'Inconnu' }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $otherFind->found_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge bg-secondary">{{ $otherFind->attempts_used }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if($carStats['total_found'] > $otherFinds->count() + 1)
                        <div class="card-footer text-center">
                            <small class="text-muted">
                                Et {{ $carStats['total_found'] - $otherFinds->count() - 1 }} autre(s) joueur(s)
                            </small>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>