{{-- resources/views/admin/cars-found/statistics.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Statistiques des collections</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Statistiques des collections</h1>
                <p class="text-muted mb-0">Analyse des voitures trouvées et des collectionneurs</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cars-found.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <a href="{{ route('admin.cars-found.export') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download"></i> Exporter
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques générales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-collection text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Total trouvées</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($generalStats['total_found']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Collectionneurs</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($generalStats['unique_players']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-car-front text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Voitures trouvées</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($generalStats['unique_cars']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-target text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Tentatives moy.</div>
                            <div class="fs-4 fw-bold text-warning">
                                {{ number_format($generalStats['average_attempts'], 1) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-purple bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-speedometer text-purple fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Voitures totales</div>
                            <div class="fs-4 fw-bold text-purple">
                                {{ number_format($generalStats['total_cars_available']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-dark bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-percent text-dark fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Complétude</div>
                            <div class="fs-4 fw-bold text-dark">{{ $generalStats['completion_rate'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Voitures les plus trouvées -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-star me-2"></i>
                        Top 10 - Voitures les plus trouvées
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Voiture</th>
                                    <th>Trouvée</th>
                                    <th>Tentatives moy.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostFoundCars->take(10) as $index => $car)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-secondary' }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $car->carModel->name ?? 'N/A' }}</div>
                                                <div class="small text-muted">
                                                    {{ $car->carModel->brand->name ?? 'N/A' }}
                                                    @if($car->carModel->year)
                                                        ({{ $car->carModel->year }})
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $car->found_count }}×</span>
                                        </td>
                                        <td>
                                            <span class="small">{{ number_format($car->avg_attempts, 1) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Joueurs les plus actifs -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        Top 10 - Collectionneurs les plus actifs
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Joueur</th>
                                    <th>Voitures</th>
                                    <th>Tentatives moy.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostActiveUsers->take(10) as $index => $user)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-secondary' }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 24px; height: 24px; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($user->userScore->username ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="small">{{ $user->userScore->username ?? 'Inconnu' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $user->cars_found }}</span>
                                        </td>
                                        <td>
                                            <span class="small">{{ number_format($user->avg_attempts, 1) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par difficulté -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Répartition par difficulté
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="difficultyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Marques populaires -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>
                        Top 10 - Marques populaires
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Marque</th>
                                    <th>Collections</th>
                                    <th>Collectionneurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brandPerformance->take(10) as $index => $brand)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-secondary' }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">
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
                                                        echo $flags[$brand->country] ?? '🌍';
                                                    @endphp
                                                </span>
                                                <span class="small fw-medium">{{ $brand->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $brand->found_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-purple">{{ $brand->unique_finders }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Évolution mensuelle -->
        @if($monthlyEvolution->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Évolution des découvertes (12 derniers mois)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Graphique de répartition par difficulté
                const difficultyCtx = document.getElementById('difficultyChart').getContext('2d');
                const difficultyData = @json($difficultyStats->pluck('found_count', 'difficulty_level'));

                new Chart(difficultyCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Niveau 1 (Facile)',
                            'Niveau 2 (Facile)',
                            'Niveau 3 (Moyen)',
                            'Niveau 4 (Difficile)',
                            'Niveau 5 (Expert)'
                        ],
                        datasets: [{
                            data: [
                                difficultyData[1] || 0,
                                difficultyData[2] || 0,
                                difficultyData[3] || 0,
                                difficultyData[4] || 0,
                                difficultyData[5] || 0
                            ],
                            backgroundColor: [
                                '#10b981', // Vert pour facile
                                '#34d399', // Vert clair
                                '#f59e0b', // Orange pour moyen
                                '#ef4444', // Rouge pour difficile
                                '#7c3aed'  // Violet pour expert
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Graphique d'évolution mensuelle
                @if($monthlyEvolution->count() > 0)
                    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
                    const evolutionData = @json($monthlyEvolution);

                    new Chart(evolutionCtx, {
                        type: 'line',
                        data: {
                            labels: evolutionData.map(item => {
                                const [year, month] = item.month.split('-');
                                return new Date(year, month - 1).toLocaleDateString('fr-FR', {
                                    year: 'numeric',
                                    month: 'short'
                                });
                            }),
                            datasets: [{
                                label: 'Voitures trouvées',
                                data: evolutionData.map(item => item.cars_found),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.1,
                                fill: true
                            }, {
                                label: 'Joueurs uniques',
                                data: evolutionData.map(item => item.unique_players),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.1,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                @endif
            });
        </script>
    @endpush

    <style>
        .text-purple {
            color: #7c3aed !important;
        }

        .bg-purple {
            background-color: #7c3aed !important;
        }

        .bg-purple.bg-opacity-10 {
            background-color: rgba(124, 58, 237, 0.1) !important;
        }

        #difficultyChart {
            height: 300px !important;
        }

        #evolutionChart {
            height: 400px !important;
        }
    </style>
</x-admin-layout>