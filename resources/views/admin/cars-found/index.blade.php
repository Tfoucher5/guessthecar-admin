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