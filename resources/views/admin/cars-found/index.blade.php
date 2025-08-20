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
                <a href="{{ route('admin.cars-found.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Ajouter une trouvaille
                </a>
                <a href="{{ route('admin.cars-found.statistics') }}" class="btn btn-primary">
                    <i class="bi bi-graph-up"></i> Statistiques
                </a>
                <a href="{{ route('admin.cars-found.export', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Exporter
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques rapides -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-collection text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Total trouvées</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-calendar-day text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Aujourd'hui</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($stats['today']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-calendar-week text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Cette semaine</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($stats['this_week']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-target text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Tentatives moy.</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($stats['avg_attempts'], 1) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-purple bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people text-purple fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Joueurs uniques</div>
                            <div class="fs-4 fw-bold text-purple">{{ number_format($stats['unique_players']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-dark bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-car-front text-dark fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-6 text-muted">Voitures uniques</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($stats['unique_cars']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                        placeholder="Joueur, marque, modèle...">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Joueur</label>
                    <select class="form-select" name="user_id">
                        <option value="">Tous les joueurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                                {{ $user->username }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
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
                    <label class="form-label">Difficulté</label>
                    <select class="form-select" name="difficulty">
                        <option value="">Toutes difficultés</option>
                        <option value="1" {{ request('difficulty') == '1' ? 'selected' : '' }}>Facile</option>
                        <option value="2" {{ request('difficulty') == '2' ? 'selected' : '' }}>Moyen</option>
                        <option value="3" {{ request('difficulty') == '3' ? 'selected' : '' }}>Difficile</option>
                        <option value="4" {{ request('difficulty') == '4' ? 'selected' : '' }}>Expert</option>
                        <option value="5" {{ request('difficulty') == '5' ? 'selected' : '' }}>Légendaire</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Serveur</label>
                    <select class="form-select" name="guild_id">
                        <option value="">Tous</option>
                        @foreach($guilds as $guild)
                            <option value="{{ $guild->guild_id }}" {{ request('guild_id') === $guild->guild_id ? 'selected' : '' }}>
                                {{ $guild->guild_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Actions</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filtrer
                        </button>
                        <a href="{{ route('admin.cars-found.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des trouvailles -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-collection me-2"></i>
                    Voitures trouvées
                </h5>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-sort-down"></i> Trier par
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'found_at', 'direction' => 'desc']) }}">Plus récent</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'found_at', 'direction' => 'asc']) }}">Plus ancien</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'attempts_used', 'direction' => 'asc']) }}">Moins de tentatives</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'attempts_used', 'direction' => 'desc']) }}">Plus de tentatives</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if($carsFound->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Joueur</th>
                            <th>Voiture</th>
                            <th>Marque</th>
                            <th>Trouvé le</th>
                            <th>Tentatives</th>
                            <th>Temps</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carsFound as $carFound)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $carFound->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 32px; height: 32px; font-size: 0.875rem;">
                                            {{ strtoupper(substr($carFound->userScore->username ?? 'U', 0, 1)) }}
                                        </div>
                                        <span>{{ $carFound->userScore->username ?? 'Utilisateur inconnu' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ $carFound->carModel->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">
                                            {{ $carFound->carModel->year ?? 'N/A' }} •
                                            <span class="badge {{ $carFound->carModel->difficulty_level <= 2 ? 'bg-success' : ($carFound->carModel->difficulty_level <= 3 ? 'bg-warning' : 'bg-danger') }}">
                                                Niveau {{ $carFound->carModel->difficulty_level ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ $carFound->carModel->brand->country_flag ?? '🌍' }}</span>
                                        <div>
                                            <div class="fw-medium">{{ $carFound->carModel->brand->name ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $carFound->carModel->brand->country ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $carFound->found_at->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ $carFound->found_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $carFound->attempts_used <= 3 ? 'bg-success' : ($carFound->attempts_used <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $carFound->attempts_used }}
                                    </span>
                                </td>
                                <td>
                                    @if($carFound->time_taken)
                                        <span class="small">{{ gmdate('i:s', $carFound->time_taken) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.cars-found.show', $carFound->id) }}" 
                                           class="btn btn-outline-primary" title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.cars-found.destroy', $carFound->id) }}" 
                                              class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?')">>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($carsFound->hasPages())
                <div class="card-footer">
                    {{ $carsFound->links() }}
                </div>
            @endif
        @else
            <div class="card-body text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-collection fs-1 d-block mb-3"></i>
                    <h5>Aucune voiture trouvée</h5>
                    <p>Aucune trouvaille ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.cars-found.index') }}" class="btn btn-primary">
                        Voir toutes les trouvailles
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de suppression en masse -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppression en masse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer les <span id="selectedCount">0</span> entrée(s) sélectionnée(s) ?</p>
                    <p class="text-danger small">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" action="{{ route('admin.cars-found.bulk-delete') }}" id="bulkDeleteForm">
                        @csrf
                        <input type="hidden" name="ids" id="selectedIds">
                        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            
            // Sélectionner/désélectionner tout
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
            
            // Gérer la sélection individuelle
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllCheckbox();
                    updateBulkDeleteButton();
                });
            });
            
            function updateSelectAllCheckbox() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === rowCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
            }
            
            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const selectedCount = checkedBoxes.length;
                
                if (selectedCount > 0) {
                    if (!document.getElementById('bulkDeleteBtn')) {
                        const actionDiv = document.querySelector('.card-header .d-flex');
                        const button = document.createElement('button');
                        button.id = 'bulkDeleteBtn';
                        button.className = 'btn btn-danger btn-sm';
                        button.innerHTML = '<i class="bi bi-trash"></i> Supprimer la sélection';
                        button.setAttribute('data-bs-toggle', 'modal');
                        button.setAttribute('data-bs-target', '#bulkDeleteModal');
                        actionDiv.appendChild(button);
                        
                        button.addEventListener('click', function() {
                            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
                            document.getElementById('selectedCount').textContent = selectedCount;
                            document.getElementById('selectedIds').value = JSON.stringify(selectedIds);
                        });
                    }
                } else {
                    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                    if (bulkDeleteBtn) {
                        bulkDeleteBtn.remove();
                    }
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>