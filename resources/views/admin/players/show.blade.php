<x-admin-layout>
    <x-slot name="title">{{ $userScore->username }} - Détails</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1 text-dark">{{ $userScore->username }}</h1>
                    <p class="text-muted mb-0">Profil détaillé du joueur</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.players.edit', $userScore) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        <!-- Informations générales -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white"
                            style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($userScore->username, 0, 1) }}
                        </div>
                        <h4 class="mb-1">{{ $userScore->username }}</h4>
                        <span class="badge {{ $userScore->skill_badge_class }} fs-6">
                            {{ $userScore->skill_level }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-primary mb-0">{{ number_format($userScore->total_points, 0) }}</div>
                                <div class="small text-muted">Points totaux</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success mb-0">{{ $userScore->games_won }}</div>
                                <div class="small text-muted">Parties gagnées</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-warning mb-0">{{ $userScore->best_streak }}</div>
                                <div class="small text-muted">Meilleure série</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-info mb-0">{{ $userScore->success_rate }}%</div>
                                <div class="small text-muted">Taux de réussite</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">ID Discord</span>
                        <code class="small">{{ $userScore->user_id }}</code>
                    </div>

                    @if($userScore->guild_id)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Serveur</span>
                            <code class="small">{{ substr($userScore->guild_id, 0, 12) }}...</code>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Inscription</span>
                        <span class="small">{{ $userScore->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Dernière activité</span>
                        <span class="small">{{ $userScore->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistiques détaillées -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Statistiques détaillées</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Précision marques</span>
                            <span class="small fw-bold">{{ $userScore->brand_accuracy }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $userScore->brand_accuracy }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Précision modèles</span>
                            <span class="small fw-bold">{{ $userScore->model_accuracy }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $userScore->model_accuracy }}%"></div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="h6 mb-0">{{ $userScore->correct_brand_guesses }}</div>
                            <div class="small text-muted">Marques trouvées</div>
                        </div>
                        <div class="col-6">
                            <div class="h6 mb-0">{{ $userScore->correct_model_guesses }}</div>
                            <div class="small text-muted">Modèles trouvés</div>
                        </div>
                        <div class="col-6">
                            <div class="h6 mb-0">{{ $stats['cars_collection_count'] }}</div>
                            <div class="small text-muted">Voitures collectées</div>
                        </div>
                        <div class="col-6">
                            <div class="h6 mb-0">{{ number_format($stats['points_per_game'], 1) }}</div>
                            <div class="small text-muted">Points/partie</div>
                        </div>
                    </div>

                    @if($userScore->best_time)
                        <hr class="my-3">
                        <div class="text-center">
                            <div class="h6 mb-0">{{ gmdate('i:s', $userScore->best_time) }}</div>
                            <div class="small text-muted">Meilleur temps</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sessions récentes -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Sessions récentes
                    </h5>
                    <span class="badge bg-primary">{{ $stats['total_sessions'] }} au total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Voiture</th>
                                    <th>Date</th>
                                    <th>Durée</th>
                                    <th>Tentatives</th>
                                    <th>Statut</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $session->carModel->brand->name ?? 'N/A' }}</div>
                                                <div class="small text-muted">{{ $session->carModel->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $session->started_at->format('d/m/Y') }}</div>
                                            <div class="small text-muted">{{ $session->started_at->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $session->duration_formatted }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>M: {{ $session->attempts_make }}</div>
                                                <div>C: {{ $session->attempts_model }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $session->status_badge_class }}">
                                                {{ $session->status }}
                                            </span>
                                        </td>
                                        <td class="fw-bold">
                                            {{ $session->points_earned > 0 ? '+' . number_format($session->points_earned, 1) : '0' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Aucune session trouvée
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Collection de voitures -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-collection me-2"></i>
                        Collection de voitures
                    </h5>
                    <span class="badge bg-success">{{ $carsFound->count() }} trouvées</span>
                </div>
                <div class="card-body p-0">
                    @if($carsFound->count() > 0)
                        <div class="row g-2 p-3">
                            @foreach($carsFound as $carFound)
                                <div class="col-md-6">
                                    <div class="card card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium">{{ $carFound->carModel->brand->name }}</div>
                                                <div class="small text-muted">{{ $carFound->carModel->name }}</div>
                                            </div>
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
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-collection fs-1 d-block mb-2"></i>
                            Aucune voiture dans la collection
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progression mensuelle -->
            @if($monthlyProgress->count() > 0)
                <div class="card mt-4">
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
            @endif
        </div>
    </div>

    @push('scripts')
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
                                label: 'Parties jouées',
                                data: progressData.map(item => item.games_count),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Points gagnés',
                                data: progressData.map(item => item.points_earned),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
                });
            @endif
        </script>
    @endpush
</x-admin-layout>