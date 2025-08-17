<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-controller me-2"></i>Sessions de jeu
                </h1>
                <p class="text-muted mb-0">Analysez les parties jouées sur votre plateforme</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="exportSessions()">
                    <i class="bi bi-download me-2"></i>Exporter
                </button>
                <button class="btn btn-outline-info" onclick="refreshStats()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtres et recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sessions.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminées
                            </option>
                            <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandonnées
                            </option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En
                                cours</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="user_id" class="form-label">Joueur</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">Tous les joueurs</option>
                            @if(isset($players))
                                @foreach($players as $player)
                                    <option value="{{ $player->user_id }}" {{ request('user_id') == $player->user_id ? 'selected' : '' }}>
                                        {{ $player->username }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Depuis</label>
                        <input type="date" class="form-control" name="date_from" id="date_from"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques rapides -->
    @if(isset($sessions) && $sessions->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ $sessions->where('completed', true)->count() }}</h4>
                        <small>Terminées</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ $sessions->where('abandoned', true)->count() }}</h4>
                        <small>Abandonnées</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-1 mb-2"></i>
                        @php
                            $avgDuration = $sessions->where('duration_seconds', '>', 0)->avg('duration_seconds');
                            $minutes = $avgDuration ? floor($avgDuration / 60) : 0;
                            $seconds = $avgDuration ? $avgDuration % 60 : 0;
                        @endphp
                        <h4 class="mb-0">{{ sprintf('%02d:%02d', $minutes, $seconds) }}</h4>
                        <small>Durée moyenne</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up fs-1 mb-2"></i>
                        @php
                            $total = $sessions->count();
                            $completed = $sessions->where('completed', true)->count();
                            $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        <h4 class="mb-0">{{ $rate }}%</h4>
                        <small>Taux de réussite</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des sessions -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Liste des sessions
                <span class="badge bg-secondary">{{ isset($sessions) ? $sessions->total() : 0 }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($sessions) && $sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Joueur</th>
                                <th>Voiture</th>
                                <th>Statut</th>
                                <th>Durée</th>
                                <th>Points</th>
                                <th>Tentatives</th>
                                <th>Démarré le</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($session->userScore->username ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <strong
                                                    class="text-dark">{{ $session->userScore->username ?? 'Joueur supprimé' }}</strong>
                                                <div class="text-muted small">ID: {{ $session->user_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-dark">{{ $session->carModel->brand->name ?? 'N/A' }}
                                                {{ $session->carModel->name ?? 'N/A' }}</strong>
                                            <div class="text-muted small">
                                                @if($session->carModel)
                                                    {{ $session->carModel->year ? $session->carModel->year : 'Année inconnue' }}
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'completed' => ['class' => 'bg-success', 'icon' => 'check-circle', 'text' => 'Terminée'],
                                                'abandoned' => ['class' => 'bg-danger', 'icon' => 'x-circle', 'text' => 'Abandonnée'],
                                                'in_progress' => ['class' => 'bg-primary', 'icon' => 'play-circle', 'text' => 'En cours'],
                                                'timeout' => ['class' => 'bg-warning', 'icon' => 'clock', 'text' => 'Expirée']
                                            ];
                                            $status = $session->status ?? 'in_progress';
                                            $config = $statusConfig[$status] ?? $statusConfig['in_progress'];
                                        @endphp
                                        <span class="badge {{ $config['class'] }}">
                                            <i class="bi bi-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $session->formatted_duration ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong
                                                class="text-primary">{{ number_format($session->total_points ?? 0, 0) }}</strong>
                                        </div>
                                        <div class="text-muted small">
                                            Base: {{ number_format($session->points_earned ?? 0, 0) }}
                                            + Bonus: {{ number_format($session->difficulty_points_earned ?? 0, 0) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-dark">{{ $session->total_attempts ?? 0 }}</strong>
                                        </div>
                                        <div class="text-muted small">
                                            M: {{ $session->attempts_make ?? 0 }} | Mo: {{ $session->attempts_model ?? 0 }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted small">{{ $session->started_at ? $session->started_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.sessions.show', $session) }}"
                                            class="btn btn-outline-info btn-sm" title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($sessions->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Affichage de {{ $sessions->firstItem() }} à {{ $sessions->lastItem() }}
                                sur {{ $sessions->total() }} résultats
                            </div>
                            {{ $sessions->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-controller text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-dark">Aucune session trouvée</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'user_id', 'date_from']))
                            Aucune session ne correspond à vos critères de recherche.
                        @else
                            Aucune session de jeu n'a encore été créée.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function exportSessions() {
            window.open('{{ route("admin.sessions.index") }}?export=csv', '_blank');
        }

        function refreshStats() {
            location.reload();
        }
    </script>
</x-admin-layout>