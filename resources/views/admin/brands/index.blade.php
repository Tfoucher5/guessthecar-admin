<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-building me-2"></i>Gestion des marques
                </h1>
                <p class="text-muted mb-0">Gérez les marques automobiles de votre plateforme</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle marque
                </a>
            </div>
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

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtres et recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.brands.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" name="search" id="search"
                            value="{{ request('search') }}" placeholder="Nom de marque ou pays...">
                    </div>

                    <div class="col-md-3">
                        <label for="country" class="form-label">Pays</label>
                        <select name="country" id="country" class="form-select">
                            <option value="">Tous les pays</option>
                            @if(isset($countries))
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des marques -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Liste des marques
                <span class="badge bg-secondary">{{ $brands->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($brands->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Marque
                                        @if(request('sort') == 'name')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'country', 'direction' => request('sort') == 'country' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Pays
                                        @if(request('sort') == 'country')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Modèles</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'founded_year', 'direction' => request('sort') == 'founded_year' && request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-decoration-none text-dark">
                                        Fondation
                                        @if(request('sort') == 'founded_year')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Créé le</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($brand->logo_url)
                                                <div class="me-3"
                                                    style="width: 50px; height: 50px; border: 1px solid #dee2e6; border-radius: 4px; background-color: #f8f9fa; padding: 2px; overflow: hidden; flex-shrink: 0;">
                                                    <img src="{{ $brand->logo_url }}"
                                                        style="width: 100%; height: 100%; object-fit: contain; object-position: center;"
                                                        alt="{{ $brand->name }}"
                                                        onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;background-color:#6c757d;color:white;display:flex;align-items:center;justify-content:center;border-radius:2px;font-weight:bold;\'>{{ substr($brand->name, 0, 1) }}</div>';">
                                                </div>
                                            @else
                                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3"
                                                    style="width: 50px; height: 50px; font-size: 1.2rem; flex-shrink: 0;">
                                                    {{ substr($brand->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong class="text-dark">{{ $brand->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($brand->country)
                                            <span class="badge bg-light text-dark">{{ $brand->country }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $brand->models_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($brand->founded_year)
                                            {{ $brand->founded_year }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $brand->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.brands.edit', $brand) }}"
                                                class="btn btn-outline-warning btn-sm" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette marque ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
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

                <!-- Pagination -->
                @if($brands->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Affichage de {{ $brands->firstItem() }} à {{ $brands->lastItem() }}
                                sur {{ $brands->total() }} résultats
                            </div>
                            {{ $brands->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-dark">Aucune marque trouvée</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'country']))
                            Aucune marque ne correspond à vos critères de recherche.
                        @else
                            Commencez par ajouter votre première marque automobile.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'country']))
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i>Créer une marque
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>