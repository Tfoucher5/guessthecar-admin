<x-admin-layout>
    <div class="container-fluid py-4">
        <!-- En-tête et statistiques -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">
                        <i class="bi bi-controller me-2"></i>
                        Sessions de jeu
                    </h2>
                    <button onclick="refreshStats()" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
                    </button>
                </div>

                <!-- Statistiques générales -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                                        <p class="mb-0 opacity-75">Sessions totales</p>
                                    </div>
                                    <i class="bi bi-play-circle fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ number_format($stats['completed'] ?? 0) }}</h3>
                                        <p class="mb-0 opacity-75">Terminées</p>
                                    </div>
                                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ number_format($stats['active'] ?? 0) }}</h3>
                                        <p class="mb-0 opacity-75">En cours</p>
                                    </div>
                                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ number_format($stats['today'] ?? 0) }}</h3>
                                        <p class="mb-0 opacity-75">Aujourd'hui</p>
                                    </div>
                                    <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.sessions.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nom d'utilisateur...">
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminées</option>
                            <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandonnées</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="user_id" class="form-label">Utilisateur</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Tous</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
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

                    <div class="col-md-1 d-flex align-items-end">
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
            @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Statut</th>
                                <th>Score</th>
                                <th>Tentatives</th>
                                <th>Durée</th>
                                <th>Voitures trouvées</th>
                                <th>Date</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($session->user->avatar_url)
                                                <img src="{{ $session->user->avatar_url }}" 
                                                     alt="{{ $session->user->name }}" 
                                                     class="rounded-circle me-2 dashboard-img">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2 dashboard-img">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $session->user->name }}</div>
                                                <small class="text-muted">{{ $session->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($session->status)
                                            @case('active')
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-hourglass-split me-1"></i>En cours
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Terminée
                                                </span>
                                                @break
                                            @case('abandoned')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Abandonnée
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $session->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary fs-5">{{ number_format($session->score ?? 0) }}</span>
                                        @if($session->previous_best_score && $session->score > $session->previous_best_score)
                                            <i class="bi bi-arrow-up text-success ms-1" title="Nouveau record !"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            Marque: {{ $session->attempts_brand ?? 0 }} | Modèle: {{ $session->attempts_model ?? 0 }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($session->started_at && $session->ended_at)
                                            @php
                                                $duration = $session->started_at->diffInMinutes($session->ended_at);
                                                $hours = floor($duration / 60);
                                                $minutes = $duration % 60;
                                            @endphp
                                            <span class="text-dark">
                                                @if($hours > 0){{ $hours }}h @endif{{ $minutes }}min
                                            </span>
                                        @elseif($session->started_at)
                                            @php
                                                $duration = $session->started_at->diffInMinutes(now());
                                                $hours = floor($duration / 60);
                                                $minutes = $duration % 60;
                                            @endphp
                                            <span class="text-warning">
                                                @if($hours > 0){{ $hours }}h @endif{{ $minutes }}min
                                                <small class="d-block">en cours</small>
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($session->found_cars && $session->found_cars->count() > 0)
                                                <div class="me-2">
                                                    <span class="badge bg-success">{{ $session->found_cars->count() }}</span>
                                                </div>
                                                <div class="d-flex flex-wrap" style="max-width: 120px;">
                                                    @foreach($session->found_cars->take(3) as $foundCar)
                                                        @if($foundCar->car_model && $foundCar->car_model->image_url)
                                                            <img src="{{ $foundCar->car_model->image_url }}" 
                                                                 alt="{{ $foundCar->car_model->name }}" 
                                                                 class="rounded me-1 mb-1 session-car-image"
                                                                 title="{{ $foundCar->car_model->brand->name }} {{ $foundCar->car_model->name }}">
                                                        @else
                                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center me-1 mb-1 session-car-image"
                                                                 title="{{ $foundCar->car_model->brand->name ?? 'Marque' }} {{ $foundCar->car_model->name ?? 'Modèle' }}">
                                                                <i class="bi bi-car-front-fill text-muted small"></i>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    @if($session->found_cars->count() > 3)
                                                        <small class="text-muted">+{{ $session->found_cars->count() - 3 }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Aucune</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $session->started_at ? $session->started_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                        @if($session->ended_at)
                                            <div class="text-muted small">
                                                Fin: {{ $session->ended_at->format('H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.sessions.show', $session) }}" 
                                               class="btn btn-outline-primary"
                                               title="Voir les détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($session->status === 'active')
                                                <button class="btn btn-outline-warning" 
                                                        onclick="endSession({{ $session->id }})"
                                                        title="Terminer la session">
                                                    <i class="bi bi-stop"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-danger" 
                                                    onclick="deleteSession({{ $session->id }})"
                                                    title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
                </div>
            @endif
        </div>
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
                        alert('Erreur lors de la fermeture de la session');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la fermeture de la session');
                });
            }
        }

        function deleteSession(sessionId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette session ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/sessions/${sessionId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
                
                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-admin-layout>