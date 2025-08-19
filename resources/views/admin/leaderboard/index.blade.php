{{-- resources/views/admin/leaderboard/index.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Classement Global</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-trophy me-2"></i>Classement Global
                </h1>
                <p class="text-muted mb-0">
                    @if(isset($players))
                        {{ $players->total() }} joueur(s) classé(s)
                    @else
                        Classement des joueurs
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.leaderboard.export', request()->query()) }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-2"></i>Exporter
                </a>
                <button onclick="refreshLeaderboard()" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques générales -->
    @if(isset($stats))
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h4 mb-1 text-primary">{{ number_format($stats['total_players'] ?? 0) }}</div>
                    <div class="small text-muted">Joueurs totaux</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h4 mb-1 text-success">{{ number_format($stats['active_players'] ?? 0) }}</div>
                    <div class="small text-muted">Actifs (7j)</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h4 mb-1 text-warning">{{ number_format($stats['top_score'] ?? 0) }}</div>
                    <div class="small text-muted">Meilleur score</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="h4 mb-1 text-info">{{ number_format($stats['avg_score'] ?? 0, 1) }}</div>
                    <div class="small text-muted">Score moyen</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtres et tri
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Serveur</label>
                    <select class="form-select" name="guild_id">
                        <option value="">Tous les serveurs</option>
                        @if(isset($guilds) && $guilds->count() > 0)
                            @foreach($guilds as $guild)
                                <option value="{{ $guild->guild_id }}" {{ request('guild_id') == $guild->guild_id ? 'selected' : '' }}>
                                    Serveur {{ substr($guild->guild_id, 0, 8) }}... ({{ $guild->players_count }} joueurs)
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Période</label>
                    <select class="form-select" name="period">
                        <option value="">Toute période</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Trier par</label>
                    <select class="form-select" name="sort">
                        <option value="total_points" {{ request('sort') == 'total_points' ? 'selected' : '' }}>Points totaux</option>
                        <option value="games_played" {{ request('sort') == 'games_played' ? 'selected' : '' }}>Parties jouées</option>
                        <option value="games_won" {{ request('sort') == 'games_won' ? 'selected' : '' }}>Parties gagnées</option>
                        <option value="success_rate" {{ request('sort') == 'success_rate' ? 'selected' : '' }}>Taux de réussite</option>
                        <option value="best_streak" {{ request('sort') == 'best_streak' ? 'selected' : '' }}>Meilleure série</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ordre</label>
                    <select class="form-select" name="direction">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Croissant</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Points min.</label>
                    <input type="number" class="form-control" name="min_points" value="{{ request('min_points') }}"
                        placeholder="0">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Classement -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-trophy me-2"></i>
                Classement des joueurs
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($players) && $players->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rang</th>
                                <th>Joueur</th>
                                <th>Points</th>
                                <th>Parties</th>
                                <th>Victoires</th>
                                <th>Taux de réussite</th>
                                <th>Meilleure série</th>
                                <th>Niveau</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($players as $player)
                                <tr class="{{ $player->rank <= 3 ? 'table-warning' : '' }}">
                                    <td>
                                        @if($player->rank <= 3)
                                            @php
                                                $badgeColors = [1 => 'warning', 2 => 'secondary', 3 => 'success'];
                                                $icons = [1 => 'trophy-fill', 2 => 'award-fill', 3 => 'star-fill'];
                                            @endphp
                                            <span class="badge bg-{{ $badgeColors[$player->rank] }} fs-6">
                                                <i class="bi bi-{{ $icons[$player->rank] }} me-1"></i>{{ $player->rank }}
                                            </span>
                                        @else
                                            <span class="fw-medium">{{ $player->rank }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                                 style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($player->username ?? 'U', 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $player->username ?? 'Joueur inconnu' }}</div>
                                                @if($player->guild_id)
                                                    <small class="text-muted">{{ substr($player->guild_id, 0, 8) }}...</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary fs-5">
                                            {{ number_format($player->total_points ?? 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($player->games_played ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">{{ number_format($player->games_won ?? 0) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $successRate = $player->games_played > 0 
                                                ? ($player->games_won / $player->games_played) * 100 
                                                : 0;
                                        @endphp
                                        <span class="badge bg-{{ $successRate >= 70 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger') }}">
                                            {{ number_format($successRate, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $player->best_streak ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $points = $player->total_points ?? 0;
                                            if ($points >= 100) {
                                                $level = 'Expert';
                                                $levelColor = 'primary';
                                            } elseif ($points >= 50) {
                                                $level = 'Avancé';
                                                $levelColor = 'success';
                                            } elseif ($points >= 20) {
                                                $level = 'Intermédiaire';
                                                $levelColor = 'warning';
                                            } elseif ($points >= 10) {
                                                $level = 'Apprenti';
                                                $levelColor = 'info';
                                            } else {
                                                $level = 'Débutant';
                                                $levelColor = 'secondary';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $levelColor }}">{{ $level }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.players.show', $player) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Voir profil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.players.edit', $player) }}" 
                                               class="btn btn-outline-secondary" 
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($players->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Affichage de {{ $players->firstItem() }} à {{ $players->lastItem() }}
                                sur {{ $players->total() }} résultats
                            </div>
                            {{ $players->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-trophy text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-dark">Aucun joueur trouvé</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['guild_id', 'period', 'min_points']))
                            Aucun joueur ne correspond à vos critères de filtrage.
                        @else
                            Aucun joueur n'est encore enregistré dans le classement.
                        @endif
                    </p>
                    @if(request()->hasAny(['guild_id', 'period', 'min_points']))
                        <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser les filtres
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        function refreshLeaderboard() {
            location.reload();
        }

        // Auto-refresh toutes les 2 minutes
        setInterval(refreshLeaderboard, 120000);
    </script>
</x-admin-layout>