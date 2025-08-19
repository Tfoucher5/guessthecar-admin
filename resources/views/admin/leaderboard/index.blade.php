<x-admin-layout>
    <x-slot name="title">Classement général</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Classement général</h1>
                <p class="text-muted mb-0">{{ $leaderboard->total() }} joueurs classés</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cars-found.statistics') }}" class="btn btn-outline-primary">
                    <i class="bi bi-graph-up"></i> Statistiques
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                        placeholder="Nom du joueur...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Niveau de compétence</label>
                    <select class="form-select" name="skill_level">
                        <option value="">Tous les niveaux</option>
                        @foreach($skillLevels as $level)
                            <option value="{{ $level }}" {{ request('skill_level') === $level ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Rang minimum</label>
                    <input type="number" class="form-control" name="rank_from" value="{{ request('rank_from') }}"
                        placeholder="1">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Rang maximum</label>
                    <input type="number" class="form-control" name="rank_to" value="{{ request('rank_to') }}"
                        placeholder="100">
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
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Joueur</th>
                            <th>Points</th>
                            <th>Parties</th>
                            <th>Taux de réussite</th>
                            <th>Tentatives moy.</th>
                            <th>Temps moyen</th>
                            <th>Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $player)
                            <tr class="{{ $player->ranking <= 3 ? 'table-warning' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($player->ranking == 1)
                                            <span class="badge bg-warning text-dark fs-6">🥇 #{{ $player->ranking }}</span>
                                        @elseif($player->ranking == 2)
                                            <span class="badge bg-secondary fs-6">🥈 #{{ $player->ranking }}</span>
                                        @elseif($player->ranking == 3)
                                            <span class="badge bg-warning text-dark fs-6">🥉 #{{ $player->ranking }}</span>
                                        @else
                                            <span class="badge bg-primary">#{{ $player->ranking }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white"
                                            style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($player->username, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $player->username }}</div>
                                            @if($player->current_streak > 0)
                                                <div class="small text-success">
                                                    🔥 {{ $player->current_streak }} série
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold text-primary">{{ number_format($player->total_points, 0) }}
                                        </div>
                                        @if($player->total_difficulty_points > 0)
                                            <div class="small text-muted">
                                                +{{ number_format($player->total_difficulty_points, 0) }} bonus
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-medium">{{ $player->games_played }}</span>
                                        <span class="text-muted">/ {{ $player->games_won }} gagnées</span>
                                    </div>
                                    @if($player->best_streak > 0)
                                        <div class="small text-warning">
                                            ⭐ {{ $player->best_streak }} record
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 50px; height: 8px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $player->success_rate }}%"></div>
                                        </div>
                                        <span class="small fw-medium">{{ $player->success_rate }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ number_format($player->average_attempts, 1) }}
                                    </span>
                                </td>
                                <td>
                                    @if($player->average_time_seconds > 0)
                                        <span class="badge bg-info">
                                            {{ gmdate('i:s', $player->average_time_seconds) }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $player->skill_badge_class }}">
                                        {{ $player->skill_level }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-trophy fs-1 d-block mb-2"></i>
                                        Aucun joueur dans le classement
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($leaderboard->hasPages())
            <div class="card-footer">
                {{ $leaderboard->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>