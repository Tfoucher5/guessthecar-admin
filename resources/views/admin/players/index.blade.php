<x-admin-layout>
    <x-slot name="title">Gestion des joueurs</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Gestion des joueurs</h1>
                <p class="text-muted mb-0">{{ $players->total() }} joueurs au total</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-trophy"></i> Classement
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
                        placeholder="Nom du joueur ou ID...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Serveur Discord</label>
                    <select class="form-select" name="guild_id">
                        <option value="">Tous les serveurs</option>
                        @foreach($guilds as $guild)
                            <option value="{{ $guild->guild_id }}" {{ request('guild_id') === $guild->guild_id ? 'selected' : '' }}>
                                @php
                                    $guildName = getGuildName($guild->guild_id);
                                @endphp
                                {{ strlen($guildName) > 15 ? substr($guildName, 0, 15) . '...' : $guildName }}
                                ({{ $guild->players_count }} joueurs)
                            </option>
                        @endforeach
                    </select>
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

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des joueurs -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des joueurs</h5>
            <div class="d-flex gap-2">
                <div class="btn-group btn-group-sm">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_points', 'direction' => 'desc']) }}"
                        class="btn btn-outline-secondary {{ request('sort') === 'total_points' ? 'active' : '' }}">
                        Points
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'games_played', 'direction' => 'desc']) }}"
                        class="btn btn-outline-secondary {{ request('sort') === 'games_played' ? 'active' : '' }}">
                        Parties
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}"
                        class="btn btn-outline-secondary {{ request('sort') === 'created_at' ? 'active' : '' }}">
                        Récent
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Joueur</th>
                            <th>Serveur</th>
                            <th>Points totaux</th>
                            <th>Parties</th>
                            <th>Taux réussite</th>
                            <th>Niveau</th>
                            <th>Dernière activité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white"
                                            style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($player->username, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $player->username }}</div>
                                            <div class="small text-muted">ID: {{ $player->user_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($player->guild_id)
                                        <span class="badge bg-info">
                                            @php
                                                $guildName = getGuildName($player->guild_id);
                                            @endphp
                                            {{ strlen($guildName) > 15 ? substr($guildName, 0, 15) . '...' : $guildName }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ number_format($player->total_points, 0) }}</div>
                                    @if($player->total_difficulty_points > 0)
                                        <div class="small text-muted">
                                            +{{ number_format($player->total_difficulty_points, 0) }} bonus
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-medium">{{ $player->games_played }}</span>
                                        <span class="text-muted">/ {{ $player->games_won }} gagnées</span>
                                    </div>
                                    @if($player->best_streak > 0)
                                        <div class="small text-success">
                                            🔥 {{ $player->best_streak }} série max
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $player->success_rate }}%"></div>
                                        </div>
                                        <span class="small fw-medium">{{ $player->success_rate }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $player->skill_badge_class }}">
                                        {{ $player->skill_level }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $player->updated_at->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ $player->updated_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.players.show', $player) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.players.edit', $player) }}"
                                            class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        Aucun joueur trouvé
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($players->hasPages())
            <div class="card-footer">
                {{ $players->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>