<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-people me-2"></i>Gestion des Joueurs
                </h1>
                <p class="text-muted mb-0">Consultez et modifiez les informations des joueurs de votre plateforme</p>
            </div>
            <!-- BOUTON EXPORTER SUPPRIMÉ -->
        </div>
    </x-slot>

    <!-- Messages de succès/erreur -->
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

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtres et recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.players.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" name="search" id="search"
                            value="{{ request('search') }}" placeholder="Nom d'utilisateur...">
                    </div>

                    <div class="col-md-4">
                        <label for="sort" class="form-label">Trier par</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="total_points" {{ request('sort') == 'total_points' ? 'selected' : '' }}>Points
                                totaux</option>
                            <option value="username" {{ request('sort') == 'username' ? 'selected' : '' }}>Nom
                                d'utilisateur</option>
                            <option value="games_played" {{ request('sort') == 'games_played' ? 'selected' : '' }}>Parties
                                jouées</option>
                            <option value="games_won" {{ request('sort') == 'games_won' ? 'selected' : '' }}>Parties
                                gagnées</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date
                                d'inscription</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques rapides -->
    @if(isset($players) && $players->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ $players->total() }}</h4>
                        <small>Total joueurs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-trophy fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ number_format($players->sum('total_points')) }}</h4>
                        <small>Points totaux</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-controller fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ number_format($players->sum('games_played')) }}</h4>
                        <small>Parties jouées</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-percent fs-1 mb-2"></i>
                        @php
                            $totalPlayed = $players->sum('games_played');
                            $totalWon = $players->sum('games_won');
                            $winRate = $totalPlayed > 0 ? round(($totalWon / $totalPlayed) * 100, 1) : 0;
                        @endphp
                        <h4 class="mb-0">{{ $winRate }}%</h4>
                        <small>Taux de victoire moyen</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des joueurs -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Liste des joueurs
                @if(isset($players))
                    <span class="badge bg-secondary">{{ $players->total() }}</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($players) && $players->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => request('sort') == 'username' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Joueur
                                        @if(request('sort') == 'username')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_points', 'direction' => request('sort') == 'total_points' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Points
                                        @if(request('sort') == 'total_points')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'games_played', 'direction' => request('sort') == 'games_played' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Parties
                                        @if(request('sort') == 'games_played')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Taux de victoire</th>
                                <th>Série actuelle</th>
                                <th>Meilleure série</th>
                                <th>Inscrit le</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($players as $index => $player)
                                <tr>
                                    <td>
                                        @php
                                            $globalRank = ($players->currentPage() - 1) * $players->perPage() + $index + 1;
                                        @endphp
                                        @if($globalRank <= 3)
                                            @if($globalRank === 1)
                                                <i class="bi bi-trophy-fill text-warning fs-5"></i>
                                            @elseif($globalRank === 2)
                                                <i class="bi bi-award-fill text-secondary fs-5"></i>
                                            @else
                                                <i class="bi bi-award-fill fs-5" style="color: #cd7f32;"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">{{ $globalRank }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($player->username, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark">{{ $player->username }}</strong>
                                                <div class="text-muted small">ID: {{ $player->user_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ number_format($player->total_points, 0) }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-dark">{{ $player->games_played ?? 0 }}</strong>
                                            <div class="text-success small">
                                                Gagnées: {{ $player->games_won ?? 0 }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $played = $player->games_played ?? 0;
                                            $won = $player->games_won ?? 0;
                                            $winRate = $played > 0 ? round(($won / $played) * 100, 1) : 0;
                                        @endphp
                                        <span
                                            class="badge {{ $winRate >= 70 ? 'bg-success' : ($winRate >= 40 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $winRate }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-info fw-bold">{{ $player->current_streak ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ $player->best_streak ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted small">{{ $player->created_at ? $player->created_at->format('d/m/Y') : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <!-- BOUTON SHOW SUPPRIMÉ, SEUL EDIT RESTE -->
                                        <a href="{{ route('admin.players.edit', $player) }}"
                                            class="btn btn-outline-warning btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
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
                    <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-dark">Aucun joueur trouvé</h4>
                    <p class="text-muted">
                        @if(request('search'))
                            Aucun joueur ne correspond à votre recherche.
                        @else
                            Aucun joueur n'est encore inscrit sur la plateforme.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>