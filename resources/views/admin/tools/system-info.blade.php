<x-admin-layout>
    <x-slot name="title">Informations système</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Informations système</h1>
                <p class="text-muted mb-0">Configuration et état du serveur</p>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        <!-- Configuration PHP -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-code me-2"></i>
                        Configuration PHP
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Version PHP</label>
                            <input type="text" class="form-control" value="{{ phpversion() }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Limite mémoire</label>
                            <input type="text" class="form-control" value="{{ ini_get('memory_limit') }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Temps d'exécution max</label>
                            <input type="text" class="form-control" value="{{ ini_get('max_execution_time') }}s"
                                readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Taille upload max</label>
                            <input type="text" class="form-control" value="{{ ini_get('upload_max_filesize') }}"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Laravel -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Configuration Laravel
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label text-muted">Version Laravel</label>
                            <input type="text" class="form-control" value="{{ app()->version() }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Environnement</label>
                            <input type="text" class="form-control" value="{{ app()->environment() }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Mode debug</label>
                            <input type="text" class="form-control"
                                value="{{ config('app.debug') ? 'Activé' : 'Désactivé' }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">Timezone</label>
                            <input type="text" class="form-control" value="{{ config('app.timezone') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Extensions PHP -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-puzzle me-2"></i>
                        Extensions PHP requises
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'xml', 'curl', 'zip', 'gd', 'json'];
                        @endphp
                        @foreach($extensions as $ext)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="d-flex align-items-center">
                                    @if(extension_loaded($ext))
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>{{ $ext }}</span>
                                    @else
                                        <i class="bi bi-x-circle text-danger me-2"></i>
                                        <span class="text-danger">{{ $ext }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>