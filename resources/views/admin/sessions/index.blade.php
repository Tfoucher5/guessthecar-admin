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
                <!-- BOUTON EXPORTER SUPPRIMÉ -->
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
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminées</option>
                            <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandonnées</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
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
                        <h4 class="mb-0">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</h4>
                        <small>Durée moyenne</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-trophy fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ number_format($sessions->sum('points_earned') + $sessions->sum('difficulty_points_earned')) }}</h4>
                        <small>Points totaux</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des sessions -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Historique des sessions
                @if(isset($sessions))
                    <span class="badge bg-secondary">{{ $sessions->total() }}</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($sessions) && $sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Joueur</th>
                                <th>Véhicule</th>
                                <th>Statut</th>
                                <th>Durée</th>
                                <th>Points</th>
                                <th>Tentatives</th>
                                <th>Commencée le</th>
                                <!-- COLONNE ACTIONS SUPPRIMÉE -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 32px; height: 32px; font-size: 0.9rem;">
                                                {{ $session->userScore ? substr($session->userScore->username, 0, 1) : 'U' }}
                                            </div>
                                            <div>
                                                <strong class="text-dark">{{ $session->userScore->username ?? 'Utilisateur inconnu' }}</strong>
                                                <div class="text-muted small">ID: {{ $session->user_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($session->carModel && $session->carModel->image_url)
                                                <div class="me-2" style="width: 40px; height: 40px; border: 1px solid #dee2e6; border-radius: 4px; background-color: #f8f9fa; padding: 2px; overflow: hidden; flex-shrink: 0;">
                                                    <img src="{{ $session->carModel->image_url }}" 
                                                         style="width: 100%; height: 100%; object-fit: contain; object-position: center;"
                                                         alt="{{ $session->carModel->name }}"
                                                         onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;background-color:#6c757d;color:white;display:flex;align-items:center;justify-content:center;border-radius:2px;font-size:1rem;\'><i class=\'bi bi-car-front\'></i></div>';">
                                                </div>
                                            @else
                                                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;">
                                                    <i class="bi bi-car-front"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong class="text-dark">{{ $session->carModel->name ?? 'Modèle inconnu' }}</strong>
                                                <div class="text-muted small">{{ $session->carModel->brand->name ?? 'Marque inconnue' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'completed' => ['class' => 'bg-success', 'icon' => 'check-circle', 'text' => 'Terminée'],
                                                'abandoned' => ['class' => 'bg-danger', 'icon' => 'x-circle', 'text' => 'Abandonnée'],
                                                'in_progress' => ['class' => 'bg-warning', 'icon' => 'clock', 'text' => 'En cours']
                                            ];
                                            
                                            if ($session->completed) {
                                                $config = $statusConfig['completed'];
                                            } elseif ($session->abandoned) {
                                                $config = $statusConfig['abandoned'];
                                            } else {
                                                $config = $statusConfig['in_progress'];
                                            }
                                        @endphp
                                        <span class="badge {{ $config['class'] }}">
                                            <i class="bi bi-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($session->duration_seconds)
                                            @php
                                                $minutes = floor($session->duration_seconds / 60);
                                                $seconds = $session->duration_seconds % 60;
                                            @endphp
                                            <span class="text-dark">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @php
                                                $totalPoints = ($session->points_earned ?? 0) + ($session->difficulty_points_earned ?? 0);
                                            @endphp
                                            <strong class="text-primary">{{ number_format($totalPoints, 0) }}</strong>
                                        </div>
                                        <div class="text-muted small">
                                            Base: {{ number_format($session->points_earned ?? 0, 0) }}
                                            + Bonus: {{ number_format($session->difficulty_points_earned ?? 0, 0) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @php
                                                $totalAttempts = ($session->attempts_make ?? 0) + ($session->attempts_model ?? 0);
                                            @endphp
                                            <strong class="text-dark">{{ $totalAttempts }}</strong>
                                        </div>
                                        <div class="text-muted small">
                                            Marque: {{ $session->attempts_make ?? 0 }} | Modèle: {{ $session->attempts_model ?? 0 }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $session->started_at ? $session->started_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                        @if($session->ended_at)
                                            <div class="text-muted small">
                                                Fin: {{ $session->ended_at->format('H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <!-- PLUS DE COLONNE ACTIONS -->
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
        function refreshStats() {
            location.reload();
        }

        // FONCTION EXPORT SUPPRIMÉE
    </script>
</x-admin-layout>