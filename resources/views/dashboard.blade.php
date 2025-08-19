{{-- resources/views/admin/dashboard.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Dashboard Administration</h1>
                <p class="text-muted mb-0">Vue d'ensemble de votre plateforme GuessTheCar</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary">{{ now()->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card" style="border-left-color: #3b82f6;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Marques</div>
                        <div class="stat-value text-primary">{{ number_format($stats['total_brands']) }}</div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-building text-primary fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.brands.index') }}" class="text-decoration-none small">
                        Voir toutes <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card" style="border-left-color: #10b981;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Modèles de voitures</div>
                        <div class="stat-value text-success">{{ number_format($stats['total_models']) }}</div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-car-front text-success fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.models.index') }}" class="text-decoration-none small">
                        Gérer les modèles <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Joueurs actifs</div>
                        <div class="stat-value text-warning">{{ number_format($stats['total_players']) }}</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-people text-warning fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.players.index') }}" class="text-decoration-none small">
                        Voir les joueurs <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card" style="border-left-color: #8b5cf6;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Sessions de jeu</div>
                        <div class="stat-value text-info">{{ number_format($stats['total_games']) }}</div>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-controller text-info fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="small text-muted">
                        {{ $stats['active_games'] }} en cours
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques supplémentaires -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-lg-6">
            <div class="stat-card" style="border-left-color: #ef4444;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Voitures trouvées</div>
                        <div class="stat-value text-danger">{{ number_format($stats['total_cars_found']) }}</div>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-collection text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6">
            <div class="stat-card" style="border-left-color: #06b6d4;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Points totaux</div>
                        <div class="stat-value text-cyan">{{ number_format($stats['total_points_earned']) }}</div>
                    </div>
                    <div class="bg-cyan bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-trophy text-cyan fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="stat-card" style="border-left-color: #10b981;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Taux de réussite</div>
                        <div class="stat-value text-success">
                            {{ $stats['total_games'] > 0 ? round(($stats['completed_games'] / $stats['total_games']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-graph-up text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Répartition par difficulté -->
        <div class="col-xl-4 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Répartition par difficulté
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="difficultyChart" height="200"></canvas>
                    <div class="mt-3">
                        @foreach([1 => 'Facile', 2 => 'Moyen', 3 => 'Difficile'] as $level => $label)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">
                                    <span
                                        class="badge {{ $level === 1 ? 'bg-success' : ($level === 2 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $label }}
                                    </span>
                                </span>
                                <span class="fw-bold">{{ $difficultyStats[$level] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Top joueurs -->
        <div class="col-xl-4 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        Top Joueurs
                    </h5>
                    <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Joueur</th>
                                    <th>Points</th>
                                    <th>Niveau</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPlayers as $player)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">#{{ $player->ranking }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                                    style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                    {{ substr($player->username, 0, 1) }}
                                                </div>
                                                <span class="fw-medium">{{ $player->username }}</span>
                                            </div>
                                        </td>
                                        <td class="fw-bold">{{ number_format($player->total_points, 0) }}</td>
                                        <td>
                                            <span class="badge {{ $player->skill_badge_class }}">
                                                {{ $player->skill_level }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Aucun joueur trouvé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marques populaires -->
        <div class="col-xl-4 col-lg-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>
                        Marques populaires
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($topBrands as $brand)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <span class="me-2" style="font-size: 1.2em;">{{ $brand->country_flag }}</span>
                                <div>
                                    <div class="fw-medium">{{ $brand->name }}</div>
                                    <div class="small text-muted">{{ $brand->country }}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ $brand->game_sessions_count }}</div>
                                <div class="small text-muted">parties</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Aucune donnée disponible</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions récentes -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Sessions récentes
                    </h5>
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir toutes
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Joueur</th>
                                    <th>Voiture</th>
                                    <th>Début</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                    <th>Points</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentGames as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                                    style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                    {{ substr($session->userScore->username ?? 'U', 0, 1) }}
                                                </div>
                                                <span>{{ $session->userScore->username ?? 'Utilisateur inconnu' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $session->carModel->brand->name ?? 'N/A' }}</div>
                                                <div class="small text-muted">{{ $session->carModel->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div>{{ $session->started_at->format('d/m/Y') }}</div>
                                                <div class="small text-muted">{{ $session->started_at->format('H:i') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $session->duration_formatted }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $session->status_badge_class }}">
                                                {{ $session->status }}
                                            </span>
                                        </td>
                                        <td class="fw-bold">
                                            {{ $session->points_earned > 0 ? '+' . number_format($session->points_earned, 1) : '0' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.sessions.show', $session) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Aucune session récente
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Graphique de répartition par difficulté
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('difficultyChart').getContext('2d');

                const difficultyData = @json($difficultyStats);
                const labels = ['Facile', 'Moyen', 'Difficile'];
                const data = [
                    difficultyData[1] || 0,
                    difficultyData[2] || 0,
                    difficultyData[3] || 0
                ];
                const colors = ['#10b981', '#f59e0b', '#ef4444'];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>