{{-- resources/views/admin/cars-found/create.blade.php --}}
<x-admin-layout>
    <x-slot name="title">Ajouter une trouvaille</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Ajouter une trouvaille manuellement</h1>
                <p class="text-muted mb-0">Enregistrer qu'un joueur a trouvé une voiture</p>
            </div>
            <div>
                <a href="{{ route('admin.cars-found.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Messages d'erreur -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle trouvaille
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cars-found.store') }}">
                        @csrf

                        <!-- Sélection du joueur -->
                        <div class="mb-4">
                            <label for="user_id" class="form-label required">Joueur</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" 
                                    id="user_id" name="user_id" required>
                                <option value="">Sélectionnez un joueur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}" {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->username }} 
                                        ({{ number_format($user->total_points) }} pts - {{ $user->games_played }} parties)
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Le joueur qui a trouvé la voiture</div>
                        </div>

                        <!-- Sélection de la voiture -->
                        <div class="mb-4">
                            <label for="car_id" class="form-label required">Voiture</label>
                            <select class="form-select @error('car_id') is-invalid @enderror" 
                                    id="car_id" name="car_id" required>
                                <option value="">Sélectionnez une voiture</option>
                                @foreach($cars->groupBy('brand.name') as $brandName => $brandCars)
                                    <optgroup label="{{ $brandName }}">
                                        @foreach($brandCars as $car)
                                            <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                                {{ $car->name }} 
                                                @if($car->year) ({{ $car->year }}) @endif
                                                - Niveau {{ $car->difficulty_level }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('car_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">La voiture qui a été trouvée</div>
                        </div>

                        <div class="row">
                            <!-- Nombre de tentatives -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="attempts_used" class="form-label required">Nombre de tentatives</label>
                                    <input type="number" 
                                           class="form-control @error('attempts_used') is-invalid @enderror" 
                                           id="attempts_used" 
                                           name="attempts_used" 
                                           value="{{ old('attempts_used', 1) }}" 
                                           min="1" 
                                           max="100" 
                                           required>
                                    @error('attempts_used')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Combien de tentatives le joueur a-t-il utilisé ?</div>
                                </div>
                            </div>

                            <!-- Temps pris (optionnel) -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="time_taken" class="form-label">Temps pris (secondes)</label>
                                    <input type="number" 
                                           class="form-control @error('time_taken') is-invalid @enderror" 
                                           id="time_taken" 
                                           name="time_taken" 
                                           value="{{ old('time_taken') }}" 
                                           min="1" 
                                           placeholder="Ex: 120">
                                    @error('time_taken')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Temps total en secondes (optionnel)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Serveur Discord (optionnel) -->
                        <div class="mb-4">
                            <label for="guild_id" class="form-label">Serveur Discord</label>
                            <input type="text" 
                                   class="form-control @error('guild_id') is-invalid @enderror" 
                                   id="guild_id" 
                                   name="guild_id" 
                                   value="{{ old('guild_id') }}" 
                                   placeholder="ID du serveur Discord">
                            @error('guild_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">L'ID du serveur Discord où la voiture a été trouvée (optionnel)</div>
                        </div>

                        <!-- Zone d'informations -->
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    <strong>Information :</strong> Cette fonctionnalité permet d'ajouter manuellement des trouvailles qui n'ont pas été enregistrées automatiquement par le bot. 
                                    Assurez-vous que le joueur n'a pas déjà trouvé cette voiture.
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> Enregistrer la trouvaille
                            </button>
                            <a href="{{ route('admin.cars-found.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Aide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-primary">Nombre de tentatives</h6>
                            <ul class="small mb-0">
                                <li><strong>1-3 tentatives :</strong> Excellent</li>
                                <li><strong>4-5 tentatives :</strong> Bon</li>
                                <li><strong>6+ tentatives :</strong> Peut mieux faire</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Niveaux de difficulté</h6>
                            <ul class="small mb-0">
                                <li><strong>Niveau 1-2 :</strong> Facile</li>
                                <li><strong>Niveau 3 :</strong> Moyen</li>
                                <li><strong>Niveau 4-5 :</strong> Difficile</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation en temps réel
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            function validateForm() {
                const userId = document.getElementById('user_id').value;
                const carId = document.getElementById('car_id').value;
                const attempts = document.getElementById('attempts_used').value;
                
                const isValid = userId && carId && attempts && attempts >= 1 && attempts <= 100;
                
                submitBtn.disabled = !isValid;
                submitBtn.classList.toggle('btn-success', isValid);
                submitBtn.classList.toggle('btn-secondary', !isValid);
            }
            
            // Écouter les changements
            ['user_id', 'car_id', 'attempts_used'].forEach(id => {
                document.getElementById(id).addEventListener('change', validateForm);
                document.getElementById(id).addEventListener('input', validateForm);
            });
            
            // Validation initiale
            validateForm();
            
            // Confirmation avant soumission
            form.addEventListener('submit', function(e) {
                const userSelect = document.getElementById('user_id');
                const carSelect = document.getElementById('car_id');
                const userName = userSelect.options[userSelect.selectedIndex]?.text || '';
                const carName = carSelect.options[carSelect.selectedIndex]?.text || '';
                const attempts = document.getElementById('attempts_used').value;
                
                if (userName && carName) {
                    const message = `Confirmer l'ajout :\n\nJoueur: ${userName}\nVoiture: ${carName}\nTentatives: ${attempts}`;
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>