{{-- resources/views/admin/sessions/show.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Session #{{ $gameSession->id }}</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.sessions.index') }}" class="text-decoration-none">
                                <i class="bi bi-controller me-1"></i>Sessions
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Session #{{ $gameSession->id }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 text-dark">Détails de la session</h1>
            </div>
            <div class="d-flex gap-2">
                @if(!$gameSession->completed && !$gameSession->abandoned)
                    <button onclick="endSession({{ $gameSession->id }})" class="btn btn-warning">
                        <i class="bi bi-stop me-2"></i>Terminer
                    </button>
                @endif
                <form method="POST" action="{{ route('admin.sessions.destroy', $gameSession) }}"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette session ?')" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-2"></i>Supprimer
                    </button>
                </form>
                <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
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
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informations de la session
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Statut -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Statut</label>
                            <p class="form-control-plaintext">
                                @php
                                    $statusClass = 'secondary';
                                    $statusText = 'Inconnue';
                                    $statusIcon = 'question-circle';

                                    if ($gameSession->completed) {
                                        $statusClass = 'success';
                                        $statusText = 'Terminée avec succès';
                                        $statusIcon = 'check-circle';
                                    } elseif ($gameSession->abandoned) {
                                        $statusClass = 'danger';
                                        $statusText = 'Abandonnée';
                                        $statusIcon = 'x-circle';
                                    } else {
                                        $statusClass = 'warning';
                                        $statusText = 'En cours';
                                        $statusIcon = 'clock';
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusClass }} fs-6">
                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                </span>
                            </p>
                        </div>

                        <!-- Joueur -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Joueur</label>
                            <p class="form-control-plaintext">
                                @if($gameSession->userScore)
                                    <a href="{{ route('admin.players.show', $gameSession->userScore) }}"
                                        class="text-decoration-none">
                                        <i class="bi bi-person me-2"></i>{{ $gameSession->userScore->username }}
                                    </a>
                                @else
                                    <span class="text-muted">Joueur inconnu</span>
                                @endif
                            </p>
                        </div>

                        <!-- Voiture -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Voiture à deviner</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @if($gameSession->carModel)
                                        <div class="d-flex align-items-center">
                                            @if($gameSession->carModel->image_url)
                                                <img src="{{ $gameSession->carModel->image_url }}"
                                                    alt="{{ $gameSession->carModel->name }}" class="me-3 rounded"
                                                    style="width: 80px; height: 60px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-1">
                                                    {{ $gameSession->carModel->brand->name ?? 'Marque inconnue' }}
                                                    {{ $gameSession->carModel->name }}</h6>
                                                <div class="small text-muted">
                                                    @if($gameSession->carModel->year)
                                                        <span class="me-3"><i
                                                                class="bi bi-calendar me-1"></i>{{ $gameSession->carModel->year }}</span>
                                                    @endif
                                                    @php
                                                        $difficultyLabels = [1 => 'Facile', 2 => 'Moyen', 3 => 'Difficile'];
                                                        $difficultyColors = [1 => 'success', 2 => 'warning', 3 => 'danger'];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $difficultyColors[$gameSession->carModel->difficulty_level] ?? 'secondary' }}">
                                                        {{ $difficultyLabels[$gameSession->carModel->difficulty_level] ?? 'Inconnu' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Voiture inconnue</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de début</label>
                            <p class="form-control-plaintext">
                                {{ $gameSession->started_at->format('d/m/Y à H:i:s') }}
                                <small
                                    class="text-muted d-block">{{ $gameSession->started_at->diffForHumans() }}</small>
                            </p>
                        </div>

                        @if($gameSession->completed_at)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date de fin</label>
                                <p class="form-control-plaintext">
                                    {{ $gameSession->completed_at->format('d/m/Y à H:i:s') }}
                                    <small
                                        class="text-muted d-block">{{ $gameSession->completed_at->diffForHumans() }}</small>
                                </p>
                            </div>
                        @endif

                        <!-- Serveur Discord -->
                        @if($gameSession->guild_id)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Serveur Discord</label>
                                <p class="form-control-plaintext">
                                    <code>{{ $gameSession->guild_id }}</code>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Historique de la session -->
            @if(isset($sessionHistory) && $sessionHistory->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>Historique de la session
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($sessionHistory as $event)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $event['description'] }}</h6>
                                        <p class="timeline-text">{{ $event['details'] }}</p>
                                        <small class="text-muted">{{ $event['timestamp']->format('d/m/Y à H:i:s') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistiques -->
        <div class="col-lg-4">
            <!-- Stats principales -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($sessionStats))
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="h4 mb-1 text-primary">{{ number_format($sessionStats['points_earned']) }}</div>
                                <div class="small text-muted">Points gagnés</div>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-1 text-info">{{ $sessionStats['attempts_used'] }}</div>
                                <div class="small text-muted">Tentatives</div>
                            </div>
                            <div class="col-12">
                                <div class="h4 mb-1 text-warning">{{ $sessionStats['duration_formatted'] }}</div>
                                <div class="small text-muted">Durée</div>
                            </div>
                            @if($sessionStats['success'])
                                <div class="col-12">
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-trophy me-1"></i>Voiture trouvée !
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Résultat -->
            @if($gameSession->userCarFound)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-trophy me-2"></i>Résultat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-3">
                                <i class="bi bi-trophy text-success fs-1"></i>
                            </div>
                            <h6 class="text-success">Voiture trouvée !</h6>
                            <p class="mb-2">
                                <strong>{{ $gameSession->userCarFound->attempts_used }}</strong> tentative(s) utilisée(s)
                            </p>
                            @if($gameSession->userCarFound->time_taken)
                                <p class="mb-2">
                                    Temps : <strong>{{ $gameSession->userCarFound->time_taken }}</strong> secondes
                                </p>
                            @endif
                            <small class="text-muted">
                                Trouvée le {{ $gameSession->userCarFound->found_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-content {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .timeline-title {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .timeline-text {
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>

    <script>
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
    </script>
</x-admin-layout>