<x-admin-layout>
    <div class="container-fluid py-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.players.index') }}">Joueurs</a></li>
                        <li class="breadcrumb-item active">{{ $player->name }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        @if($player->avatar_url)
                            <img src="{{ $player->avatar_url }}" 
                                 alt="{{ $player->name }}" 
                                 class="rounded-circle me-3"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 text-white"
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-person fs-1"></i>
                            </div>
                        @endif
                        <div>
                            <h2 class="mb-1">{{ $player->name }}</h2>
                            <p class="text-muted mb-0">{{ $player->email }}</p>
                            <small class="text-muted">
                                Membre depuis {{ $player->created_at->format('d/m/Y') }}
                                @if($player->last_login_at)
                                    • Dernière connexion: {{ $player->last_login_at->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Modifier
                        </a>
                        @if($player->status === 'active')
                            <button class="btn btn-outline-warning" onclick="suspendPlayer({{ $player->id }})">
                                <i class="bi bi-pause me-2"></i>Suspendre
                            </button>
                        @else
                            <button class="btn btn-outline-success" onclick="activatePlayer({{ $player->id }})">
                                <i class="bi bi-play me-2"></i>Activer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques générales -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($stats['total_sessions'] ?? 0) }}</h3>
                                <p class="mb-0 opacity-75">Sessions totales</p>
                            </div>
                            <i class="bi bi-controller fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($stats['best_score'] ?? 0) }}</h3>
                                <p class="mb-0 opacity-75">Meilleur score</p>
                            </div>
                            <i class="bi bi-trophy fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($stats['cars_found'] ?? 0) }}</h3>
                                <p class="mb-0 opacity-75">Voitures trouvées</p>
                            </div>
                            <i class="bi bi-collection fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($stats['total_playtime'] ?? 0) }}h</h3>
                                <p class="mb-0 opacity-75">Temps de jeu</p>
                            </div>
                            <i class="bi bi-clock fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sessions récentes -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Sessions récentes
                        </h5>
                        <a href="{{ route('admin.sessions.index', ['user_id' => $player->id]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            Voir toutes
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($recentSessions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Score</th>
                                            <th>Durée</th>
                                            <th>Voitures</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentSessions as $session)
                                            <tr>
                                                <td>
                                                    <div>{{ $session->started_at ? $session->started_at->format('d/m/Y H:i') : 'N/A' }}</div>
                                                    @if($session->ended_at)
                                                        <small class="text-muted">Fin: {{ $session->ended_at->format('H:i') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($session->status)
                                                        @case('active')
                                                            <span class="badge bg-warning">En cours</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge bg-success">Terminée</span>
                                                            @break
                                                        @case('abandoned')
                                                            <span class="badge bg-danger">Abandonnée</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ $session->status }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ number_format($session->score ?? 0) }}</span>
                                                    @if($session->score == $stats['best_score'])
                                                        <i class="bi bi-star-fill text-warning ms-1" title="Meilleur score"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->started_at && $session->ended_at)
                                                        @php
                                                            $duration = $session->started_at->diffInMinutes($session->ended_at);
                                                            $hours = floor($duration / 60);
                                                            $minutes = $duration % 60;
                                                        @endphp
                                                        @if($hours > 0){{ $hours }}h @endif{{ $minutes }}min
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->found_cars && $session->found_cars->count() > 0)
                                                        <span class="badge bg-success">{{ $session->found_cars->count() }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-controller fs-1 d-block mb-2"></i>
                                Aucune session de jeu
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activité récente -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-activity me-2"></i>
                            Activité récente
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentActivity->count() > 0)
                            <div class="timeline">
                                @foreach($recentActivity as $activity)
                                    <div class="timeline-item d-flex align-items-start mb-3">
                                        <div class="timeline-marker bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 32px; height: 32px; min-width: 32px;">
                                            @switch($activity->type)
                                                @case('car_found')
                                                    <i class="bi bi-check text-white small"></i>
                                                    @break
                                                @case('session_started')
                                                    <i class="bi bi-play text-white small"></i>
                                                    @break
                                                @case('session_completed')
                                                    <i class="bi bi-flag text-white small"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-circle text-white small"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fw-medium">{{ $activity->description }}</div>
                                                    @if($activity->details)
                                                        <small class="text-muted">{{ $activity->details }}</small>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-activity fs-1 d-block mb-2"></i>
                                Aucune activité récente
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar droite -->
            <div class="col-lg-4">
                <!-- Collection de voitures -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-collection me-2"></i>
                            Collection ({{ $collection->count() }})
                        </h5>
                        @if($collection->count() > 0)
                            <span class="badge bg-primary">{{ number_format(($collection->count() / $totalCarsAvailable) * 100, 1) }}%</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($collection->count() > 0)
                            <div class="row g-2">
                                @foreach($collection->take(12) as $carFound)
                                    <div class="col-4">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2 text-center">
                                                @if($carFound->car_model && $carFound->car_model->image_url)
                                                    <img src="{{ $carFound->car_model->image_url }}" 
                                                         alt="{{ $carFound->car_model->name }}" 
                                                         class="img-fluid rounded mb-1"
                                                         style="max-height: 40px; object-fit: contain;">
                                                @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-1"
                                                         style="height: 40px;">
                                                        <i class="bi bi-car-front-fill text-white"></i>
                                                    </div>
                                                @endif
                                                <div class="small fw-medium">{{ $carFound->car_model->brand->name ?? 'Marque' }}</div>
                                                <div class="small text-muted">{{ $carFound->car_model->name ?? 'Modèle' }}</div>
                                                <div class="text-end">
                                                    <div class="small text-muted">{{ $carFound->found_at->format('d/m') }}</div>
                                                    @if($carFound->attempts_used > 0)
                                                        <div class="small text-success">{{ $carFound->attempts_used }} essais</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($collection->count() > 12)
                                    <div class="col-12 text-center mt-2">
                                        <small class="text-muted">Et {{ $collection->count() - 12 }} autres voitures...</small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-collection fs-1 d-block mb-2"></i>
                                Aucune voiture dans la collection
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistiques détaillées -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>
                            Statistiques détaillées
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-primary">{{ number_format($stats['avg_score'] ?? 0) }}</div>
                                    <small class="text-muted">Score moyen</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-success">{{ number_format($stats['completion_rate'] ?? 0, 1) }}%</div>
                                    <small class="text-muted">Taux de complétion</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-warning">{{ number_format($stats['avg_session_time'] ?? 0) }}min</div>
                                    <small class="text-muted">Durée moy. session</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-info">{{ number_format($stats['sessions_this_week'] ?? 0) }}</div>
                                    <small class="text-muted">Cette semaine</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Répartition par difficulté -->
                        <h6 class="mb-3">Voitures par difficulté</h6>
                        @foreach(['1' => 'Facile', '2' => 'Moyen', '3' => 'Difficile'] as $level => $label)
                            @php
                                $count = $collection->where('car_model.difficulty_level', $level)->count();
                                $total = $collection->count();
                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">{{ $label }}</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                        <div class="progress-bar bg-{{ $level == 1 ? 'success' : ($level == 2 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="small text-muted">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>
                            Actions rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="sendMessage({{ $player->id }})">
                                <i class="bi bi-envelope me-2"></i>Envoyer un message
                            </button>
                            <button class="btn btn-outline-info" onclick="resetProgress({{ $player->id }})">
                                <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser progression
                            </button>
                            <button class="btn btn-outline-warning" onclick="exportData({{ $player->id }})">
                                <i class="bi bi-download me-2"></i>Exporter données
                            </button>
                            @if($player->status === 'active')
                                <button class="btn btn-outline-danger" onclick="suspendPlayer({{ $player->id }})">
                                    <i class="bi bi-ban me-2"></i>Suspendre compte
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression mensuelle -->
        @if($monthlyProgress->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up me-2"></i>
                                Progression mensuelle
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="progressChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
        <script>
            @if($monthlyProgress->count() > 0)
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('progressChart').getContext('2d');

                    const progressData = @json($monthlyProgress);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: progressData.map(item => item.month),
                            datasets: [{
                                label: 'Voitures trouvées',
                                data: progressData.map(item => item.cars_found),
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.4
                            }, {
                                label: 'Score moyen',
                                data: progressData.map(item => item.avg_score),
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.4,
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Voitures trouvées'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Score moyen'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
                });
            @endif

            function sendMessage(playerId) {
                // Implémenter l'envoi de message
                alert('Fonctionnalité à implémenter : Envoi de message');
            }

            function resetProgress(playerId) {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser toute la progression de ce joueur ? Cette action est irréversible.')) {
                    fetch(`/admin/players/${playerId}/reset-progress`, {
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
                            alert('Erreur lors de la réinitialisation');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la réinitialisation');
                    });
                }
            }

            function exportData(playerId) {
                window.open(`/admin/players/${playerId}/export`, '_blank');
            }

            function suspendPlayer(playerId) {
                if (confirm('Êtes-vous sûr de vouloir suspendre ce joueur ?')) {
                    fetch(`/admin/players/${playerId}/suspend`, {
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
                            alert('Erreur lors de la suspension');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la suspension');
                    });
                }
            }

            function activatePlayer(playerId) {
                if (confirm('Êtes-vous sûr de vouloir réactiver ce joueur ?')) {
                    fetch(`/admin/players/${playerId}/activate`, {
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
                            alert('Erreur lors de l\'activation');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de l\'activation');
                    });
                }
            }
        </script>
    @endpush
</x-admin-layout>