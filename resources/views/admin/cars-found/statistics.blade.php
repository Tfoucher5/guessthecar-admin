{{-- resources/views/admin/leaderboard/index.blade.php --}}
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

{{-- resources/views/admin/cars-found/index.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Collections de voitures</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Collections de voitures</h1>
                <p class="text-muted mb-0">{{ $carsFound->total() }} voitures trouvées</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cars-found.statistics') }}" class="btn btn-primary">
                    <i class="bi bi-graph-up"></i> Statistiques
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                        placeholder="Joueur, marque, modèle...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Marque</label>
                    <select class="form-select" name="brand_id">
                        <option value="">Toutes les marques</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->country_flag }} {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Serveur</label>
                    <select class="form-select" name="guild_id">
                        <option value="">Tous</option>
                        @foreach($guilds as $guild)
                            <option value="{{ $guild->guild_id }}" {{ request('guild_id') === $guild->guild_id ? 'selected' : '' }}>
                                {{ substr($guild->guild_id, 0, 8) }}... ({{ $guild->cars_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
            </form>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.cars-found.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des voitures trouvées -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-collection me-2"></i>
                Voitures trouvées
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Joueur</th>
                            <th>Voiture</th>
                            <th>Marque</th>
                            <th>Date de découverte</th>
                            <th>Tentatives</th>
                            <th>Temps</th>
                            <th>Serveur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carsFound as $carFound)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                            style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            {{ substr($carFound->userScore->username ?? 'U', 0, 1) }}
                                        </div>
                                        <span>{{ $carFound->userScore->username ?? 'Utilisateur inconnu' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ $carFound->carModel->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">
                                            {{ $carFound->carModel->year ?? 'N/A' }} •
                                            <span
                                                class="badge {{ $carFound->carModel->difficulty_badge_class ?? 'bg-secondary' }}">
                                                {{ $carFound->carModel->difficulty_text ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ $carFound->brand->country_flag ?? '🌍' }}</span>
                                        <div>
                                            <div class="fw-medium">{{ $carFound->brand->name ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $carFound->brand->country ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $carFound->found_at->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ $carFound->found_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $carFound->attempts_used <= 3 ? 'bg-success' : ($carFound->attempts_used <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $carFound->attempts_used }} essais
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $carFound->time_taken_formatted }}
                                    </span>
                                </td>
                                <td>
                                    @if($carFound->guild_id)
                                        <code class="small">{{ substr($carFound->guild_id, 0, 8) }}...</code>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-collection fs-1 d-block mb-2"></i>
                                        Aucune voiture trouvée
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($carsFound->hasPages())
            <div class="card-footer">
                {{ $carsFound->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>

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
            </div>
        </div>
    </x-slot>

    <!-- Statistiques générales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card" style="border-left-color: #3b82f6;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total trouvées</div>
                        <div class="stat-value text-primary">{{ number_format($stats['total_found']) }}</div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-collection text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="stat-card" style="border-left-color: #10b981;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Voitures uniques</div>
                        <div class="stat-value text-success">{{ number_format($stats['unique_cars']) }}</div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-car-front text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Collectionneurs</div>
                        <div class="stat-value text-warning">{{ number_format($stats['unique_players']) }}</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-people text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="stat-card" style="border-left-color: #8b5cf6;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Tentatives moy.</div>
                        <div class="stat-value text-info">{{ number_format($stats['average_attempts'], 1) }}</div>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-target text-info fs-4"></i>
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
                        Voitures les plus trouvées
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Voiture</th>
                                    <th>Trouvée</th>
                                    <th>Tentatives moy.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostFoundCars as $car)
                                    <tr>
                                        <td>
                                            <div class="small">
                                                <div class="fw-medium">{{ $car->carModel->brand->name ?? 'N/A' }}</div>
                                                <div class="text-muted">{{ $car->carModel->name ?? 'N/A' }}</div>
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

        <!-- Marques les plus collectionnées -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>
                        Marques populaires
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Marque</th>
                                    <th>Collections</th>
                                    <th>Collectionneurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostFoundBrands as $brandStat)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $brandStat->brand->country_flag ?? '🌍' }}</span>
                                                <span class="small fw-medium">{{ $brandStat->brand->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $brandStat->found_count }}</span>
                                        </td>
                                        <td>
                                            <span class="small">{{ $brandStat->unique_collectors }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top collectionneurs -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        Top collectionneurs
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Collectionneur</th>
                                    <th>Voitures trouvées</th>
                                    <th>Marques différentes</th>
                                    <th>Tentatives moyennes</th>
                                    <th>Efficacité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCollectors as $index => $collector)
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge bg-warning">🏆 #{{ $index + 1 }}</span>
                                            @else
                                                <span class="badge bg-secondary">#{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                                    style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                    {{ substr($collector->userScore->username ?? 'U', 0, 1) }}
                                                </div>
                                                <span
                                                    class="fw-medium">{{ $collector->userScore->username ?? 'Utilisateur inconnu' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ $collector->cars_found }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $collector->brands_collected }} marques</span>
                                        </td>
                                        <td>
                                            <span class="small">{{ number_format($collector->avg_attempts, 1) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $efficiency = $collector->avg_attempts <= 3 ? 'Excellent' : ($collector->avg_attempts <= 5 ? 'Bon' : 'Moyen');
                                                $badgeClass = $collector->avg_attempts <= 3 ? 'bg-success' : ($collector->avg_attempts <= 5 ? 'bg-warning' : 'bg-secondary');
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $efficiency }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>