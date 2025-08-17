<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-building me-2"></i>Gestion des Marques
                </h1>
                <p class="text-muted mb-0">Gérez les marques automobiles de votre plateforme</p>
            </div>
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Nouvelle marque
            </a>
        </div>
    </x-slot>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-auto-hide" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-auto-hide" role="alert">
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
            <form method="GET" action="{{ route('admin.brands.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Nom de marque ou pays...">
                    </div>
                    
                    <div class="col-md-4">
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
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                        Marque
                                        @if(request('sort') == 'name')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'country', 'direction' => request('sort') == 'country' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                        Pays
                                        @if(request('sort') == 'country')
                                            <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Modèles</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'founded_year', 'direction' => request('sort') == 'founded_year' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
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
                                                <img src="{{ $brand->logo_url }}" 
                                                     class="rounded me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;"
                                                     alt="{{ $brand->name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="bg-primary text-white rounded d-none align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                    {{ substr($brand->name, 0, 1) }}
                                                </div>
                                            @else
                                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                    {{ substr($brand->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <strong class="text-dark">{{ $brand->name }}</strong>
                                        </div>
                                    </td>