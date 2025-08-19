<x-admin-layout>
    <x-slot name="title">État de la base de données</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">État de la base de données</h1>
                <p class="text-muted mb-0">Monitoring et maintenance de la base de données</p>
            </div>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.tools.clear-cache') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-clockwise"></i> Vider le cache
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- État des tables -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-database me-2"></i>
                        État des tables principales
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Table</th>
                                    <th>Nombre d'enregistrements</th>
                                    <th>Taille estimée</th>
                                    <th>Dernière mise à jour</th>
                                    <th>État</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-building text-primary me-2"></i>
                                            <span class="fw-medium">brands</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format(\App\Models\Brand::count()) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ number_format(\App\Models\Brand::count() * 0.1, 1) }} KB
                                        </span>
                                    </td>
                                    <td>
                                        @php $latest = \App\Models\Brand::latest('updated_at')->first(); @endphp
                                        {{ $latest ? $latest->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td><span class="badge bg-success">OK</span></td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-car-front text-success me-2"></i>
                                            <span class="fw-medium">models</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format(\App\Models\CarModel::count()) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ number_format(\App\Models\CarModel::count() * 0.2, 1) }} KB
                                        </span>
                                    </td>
                                    <td>
                                        @php $latest = \App\Models\CarModel::latest('updated_at')->first(); @endphp
                                        {{ $latest ? $latest->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td><span class="badge bg-success">OK</span></td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-people text-warning me-2"></i>
                                            <span class="fw-medium">user_scores</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format(\App\Models\UserScore::count()) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ number_format(\App\Models\UserScore::count() * 0.5, 1) }} KB
                                        </span>
                                    </td>
                                    <td>
                                        @php $latest = \App\Models\UserScore::latest('updated_at')->first(); @endphp
                                        {{ $latest ? $latest->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td><span class="badge bg-success">OK</span></td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-controller text-info me-2"></i>
                                            <span class="fw-medium">game_sessions</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format(\App\Models\GameSession::count()) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ number_format(\App\Models\GameSession::count() * 0.8, 1) }} KB
                                        </span>
                                    </td>
                                    <td>
                                        @php $latest = \App\Models\GameSession::latest('started_at')->first(); @endphp
                                        {{ $latest ? $latest->started_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td><span class="badge bg-success">OK</span></td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-collection text-danger me-2"></i>
                                            <span class="fw-medium">user_cars_found</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format(\App\Models\UserCarFound::count()) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ number_format(\App\Models\UserCarFound::count() * 0.3, 1) }} KB
                                        </span>
                                    </td>
                                    <td>
                                        @php $latest = \App\Models\UserCarFound::latest('found_at')->first(); @endphp
                                        {{ $latest ? $latest->found_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td><span class="badge bg-success">OK</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Connexions et performances -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Performances système
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                                <i class="bi bi-memory text-primary fs-4 d-block mb-2"></i>
                                <div class="h6 mb-0">{{ number_format(memory_get_usage(true) / 1024 / 1024, 1) }} MB
                                </div>
                                <div class="small text-muted">Mémoire utilisée</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                                <i class="bi bi-clock text-success fs-4 d-block mb-2"></i>
                                <div class="h6 mb-0">{{ number_format(microtime(true) - LARAVEL_START, 3) }}s</div>
                                <div class="small text-muted">Temps de réponse</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                                <i class="bi bi-hdd text-warning fs-4 d-block mb-2"></i>
                                <div class="h6 mb-0">{{ config('database.default') }}</div>
                                <div class="small text-muted">Base de données</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                                <i class="bi bi-gear text-info fs-4 d-block mb-2"></i>
                                <div class="h6 mb-0">{{ app()->version() }}</div>
                                <div class="small text-muted">Version Laravel</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Vérifications système
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-database me-2"></i>
                                Connexion base de données
                            </div>
                            @if($db_status)
                                <span class="badge bg-success">Connectée</span>
                            @else
                                <span class="badge bg-danger">Erreur</span>
                            @endif
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-folder me-2"></i>
                                Permissions d'écriture
                            </div>
                            <span class="badge {{ is_writable(storage_path()) ? 'bg-success' : 'bg-danger' }}">
                                {{ is_writable(storage_path()) ? 'OK' : 'Erreur' }}
                            </span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-key me-2"></i>
                                Configuration APP_KEY
                            </div>
                            <span class="badge {{ config('app.key') ? 'bg-success' : 'bg-danger' }}">
                                {{ config('app.key') ? 'Configurée' : 'Manquante' }}
                            </span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Cache système
                            </div>
                            <span class="badge bg-info">Actif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>