{{-- resources/views/admin/sessions/index.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Sessions de Jeu</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-controller me-2"></i>Sessions de Jeu
                </h1>
                <p class="text-muted mb-0">{{ $sessions->total() ?? 0 }} sessions trouvées</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="refreshStats()" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
                </button>
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

    <!-- Statistiques rapides -->
    @if(isset($stats))
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h5 mb-1 text-primary">{{ number_format($stats['total']) }}</div>
                    <div class="small text-muted">Total</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h5 mb-1 text-warning">{{ number_format($stats['active']) }}</div>
                    <div class="small text-muted">Actives</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h5 mb-1 text-success">{{ number_format($stats['completed']) }}</div>
                    <div class="small text-muted">Terminées</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h5 mb-1 text-danger">{{ number_format($stats['abandoned']) }}</div>
                    <div class="small text-muted">Abandonnées</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h5 mb-1 text-info">{{ number_format($stats['today']) }}</div>
                    <div class="small text-muted">Aujourd'hui</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h5 mb-1 text-secondary">{{ number_format($stats['avg_duration']) }}s</div>
                    <div class="small text-muted">Durée moy.</div>
                </div>
            </div>
        </div>
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
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminées</option>
                        <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>Abandonnées</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="user_id" class="form-label">Utilisateur</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">Tous</option>
                        @if(isset($users) && $users->count() > 0)
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="car_id" class="form-label">Voiture</label>
                    <select class="form-select" id="car_id" name="car_id">
                        <option value="">Toutes</option>
                        @if(isset($cars) && $cars->count() > 0)
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}" {{ request('car_id') == $car->id ? 'selected' : '' }}>
                                    {{ $car->brand->name ?? 'N/A' }} {{ $car->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">Du</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">Au</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des sessions -->
    <div class="card">
        @if(isset($sessions) && $sessions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Voiture</th>
                            <th>Statut</th>
                            <th>Points</th>
                            <th>Durée</th>
                            <th>Début</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                             style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($session->userScore->username ?? 'U', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $session->userScore->username ?? 'Utilisateur inconnu' }}</div>
                                            @if($session->guild_id)
                                                <small class="text-muted">Serveur: {{ substr($session->guild_id, 0, 8) }}...</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($session->carModel && $session->carModel->image_url)
                                            <img src="{{ $session->carModel->image_url }}" 
                                                 alt="{{ $session->carModel->name }}" 
                                                 class="me-2 rounded"
                                                 style="width: 40px; height: 30px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $session->carModel->brand->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $session->carModel->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'secondary';
                                        $statusText = 'Inconnue';
                                        $statusIcon = 'question-circle';
                                        
                                        if ($session->completed) {
                                            $statusClass = 'success';
                                            $statusText = 'Terminée';
                                            $statusIcon = 'check-circle';
                                        } elseif ($session->abandoned) {
                                            $statusClass = 'danger';
                                            $statusText = 'Abandonnée';
                                            $statusIcon = 'x-circle';
                                        } else {
                                            $statusClass = 'warning';
                                            $statusText = 'En cours';
                                            $statusIcon = 'clock';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ number_format($session->points_earned ?? 0) }}</span>
                                </td>
                                <td>
                                    @if($session->duration_seconds)
                                        <span class="badge bg-info">{{ gmdate('H:i:s', $session->duration_seconds) }}</span>
                                    @elseif(!$session->completed && !$session->abandoned)
                                        <span class="text-muted">En cours...</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $session->started_at->diffForHumans() }}
                                        <br>
                                        <span class="text-muted">{{ $session->started_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.sessions.show', $session) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if(!$session->completed && !$session->abandoned)
                                            <button onclick="endSession({{ $session->id }})" 
                                                    class="btn btn-outline-warning" 
                                                    title="Terminer la session">
                                                <i class="bi bi-stop"></i>
                                            </button>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('admin.sessions.destroy', $session) }}" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette session ?')" 
                                              class="d-inline">
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
            @if($sessions->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $sessions->firstItem() }} à {{ $sessions->lastItem() }}
                            sur {{ $sessions->total() }} résultats
                        </div>
                        {{ $sessions->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-controller text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-dark">Aucune session trouvée</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['status', 'user_id', 'date_from']))
                        Aucune session ne correspond à vos critères de recherche.
                    @else
                        Aucune session de jeu n'a encore été créée.
                    @endif
                </p>
                @if(request()->hasAny(['status', 'user_id', 'date_from']))
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endif
    </div>

    <script>
        function refreshStats() {
            location.reload();
        }

        function endSession(sessionId) {
            if (confirm('Êtes-vous sûr de vouloir terminer cette session ?')) {
                fetch(`/admin/sessions/${sessionId}/end`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la fermeture de la session: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la fermeture de la session');
                });
            }
        }

        // Auto-refresh toutes les 30 secondes pour les sessions actives
        if ({{ request('status') === 'active' ? 'true' : 'false' }}) {
            setInterval(refreshStats, 30000);
        }
    </script>
</x-admin-layout>