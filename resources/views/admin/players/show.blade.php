<x-admin-layout>
    <div class="container-fluid py-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.players.index') }}">Joueurs</a></li>
                        <li class="breadcrumb-item active">{{ $player->username }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 text-white"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                        <div>
                            <h2 class="mb-1">{{ $player->username }}</h2>
                            <p class="text-muted mb-0">Discord ID: {{ $player->user_id }}</p>
                            <small class="text-muted">
                                Membre depuis {{ $player->created_at->format('d/m/Y') }}
                                @if($player->guild_id)
                                    • Serveur: {{ substr($player->guild_id, 0, 8) }}...
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Modifier
                        </a>
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
                                <h3 class="mb-0">{{ number_format($player->total_points) }}</h3>
                                <p class="mb-0 opacity-75">Points totaux</p>
                            </div>
                            <i class="bi bi-trophy fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($player->games_played) }}</h3>
                                <p class="mb-0 opacity-75">Parties jouées</p>
                            </div>
                            <i class="bi bi-controller fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($collection->count()) }}</h3>
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
                                <h3 class="mb-0">{{ number_format($player->best_streak) }}</h3>
                                <p class="mb-0 opacity-75">Meilleure série</p>
                            </div>
                            <i class="bi bi-lightning fs-1 opacity-50"></i>
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
                        <a href="{{ route('admin.sessions.index', ['user_id' => $player->user_id]) }}" 
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
                                            <th>Voiture</th>
                                            <th>Statut</th>
                                            <th>Points</th>
                                            <th>Durée</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentSessions as $session)
                                            <tr>
                                                <td>
                                                    <div>{{ $session->started_at->format('d/m/Y H:i') }}</div>
                                                    @if($session->completed_at)
                                                        <small class="text-muted">Fin: {{ $session->completed_at->format('H:i') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->carModel)
                                                        <div class="fw-medium">{{ $session->carModel->brand->name ?? 'N/A' }}</div>
                                                        <div class="small text-muted">{{ $session->carModel->name }}</div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->completed)
                                                        <span class="badge bg-success">Terminée</span>
                                                    @elseif($session->abandoned)
                                                        <span class="badge bg-danger">Abandonnée</span>
                                                    @else
                                                        <span class="badge bg-warning">En cours</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ number_format($session->points_earned ?? 0) }}</span>
                                                </td>
                                                <td>
                                                    @if($session->duration_seconds)
                                                        @php
                                                            $hours = floor($session->duration_seconds / 3600);
                                                            $minutes = floor(($session->duration_seconds % 3600) / 60);
                                                        @endphp
                                                        @if($hours > 0){{ $hours }}h @endif{{ $minutes }}min
                                                    @else
                                                        <span class="text-muted">N/A</span>
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
                <!-- Statistiques détaillées -->
                <div class="card">
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
                                    <div class="h5 mb-0 text-primary">{{ number_format($player->games_won) }}</div>
                                    <small class="text-muted">Parties gagnées</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-success">{{ $player->success_rate }}%</div>
                                    <small class="text-muted">Taux de réussite</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-warning">{{ number_format($player->current_streak) }}</div>
                                    <small class="text-muted">Série actuelle</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h5 mb-0 text-info">{{ $player->skill_level }}</div>
                                    <small class="text-muted">Niveau</small>
                                </div>
                            </div>
                        </div>

                        @if($player->brand_accuracy > 0 || $player->model_accuracy > 0)
                        <hr>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h6 mb-0 text-primary">{{ $player->brand_accuracy }}%</div>
                                    <small class="text-muted">Précision marques</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h6 mb-0 text-success">{{ $player->model_accuracy }}%</div>
                                    <small class="text-muted">Précision modèles</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Collection de voitures -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-collection me-2"></i>
                            Collection ({{ $collection->count() }})
                        </h5>
                        @if($collection->count() > 0 && $totalCarsAvailable > 0)
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
                                                @if($carFound->carModel && $carFound->carModel->image_url)
                                                    <img src="{{ $carFound->carModel->image_url }}" 
                                                         alt="{{ $carFound->carModel->name }}" 
                                                         class="img-fluid rounded mb-1"
                                                         style="max-height: 40px; object-fit: contain;">
                                                @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-1"
                                                         style="height: 40px;">
                                                        <i class="bi bi-car-front-fill text-white"></i>
                                                    </div>
                                                @endif
                                                <div class="small fw-medium">{{ $carFound->carModel->brand->name ?? 'Marque' }}</div>
                                                <div class="small text-muted">{{ $carFound->carModel->name ?? 'Modèle' }}</div>
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
                            @if(method_exists(app(App\Http\Controllers\Admin\PlayerController::class), 'export'))
                            <button class="btn btn-outline-warning" onclick="exportData({{ $player->id }})">
                                <i class="bi bi-download me-2"></i>Exporter données
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression mensuelle -->
        @if($monthlyProgress && $monthlyProgress->count() > 0)
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
            @if($monthlyProgress && $monthlyProgress->count() > 0)
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('progressChart').getContext('2d');
                    const progressData = @json($monthlyProgress);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: progressData.map(item => item.month),
                            datasets: [{
                                label: 'Sessions',
                                data: progressData.map(item => item.sessions_count),
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
                                        text: 'Sessions'
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

            function exportData(playerId) {
                window.open(`/admin/players/${playerId}/export`, '_blank');
            }
        </script>
    @endpush
</x-admin-layout>