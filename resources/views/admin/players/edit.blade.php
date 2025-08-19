<x-admin-layout>
    <x-slot name="title">Modifier {{ $userScore->username }}</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.players.show', $userScore) }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1 text-dark">Modifier le joueur</h1>
                    <p class="text-muted mb-0">{{ $userScore->username }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Informations du joueur
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.players.update', $userScore) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nom d'utilisateur *</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username', $userScore->username) }}"
                                    required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="total_points" class="form-label">Points totaux</label>
                                <input type="number" class="form-control @error('total_points') is-invalid @enderror"
                                    id="total_points" name="total_points" step="0.01"
                                    value="{{ old('total_points', $userScore->total_points) }}">
                                @error('total_points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="games_played" class="form-label">Parties jouées</label>
                                <input type="number" class="form-control @error('games_played') is-invalid @enderror"
                                    id="games_played" name="games_played" min="0"
                                    value="{{ old('games_played', $userScore->games_played) }}">
                                @error('games_played')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="games_won" class="form-label">Parties gagnées</label>
                                <input type="number" class="form-control @error('games_won') is-invalid @enderror"
                                    id="games_won" name="games_won" min="0"
                                    value="{{ old('games_won', $userScore->games_won) }}">
                                @error('games_won')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="best_streak" class="form-label">Meilleure série</label>
                                <input type="number" class="form-control @error('best_streak') is-invalid @enderror"
                                    id="best_streak" name="best_streak" min="0"
                                    value="{{ old('best_streak', $userScore->best_streak) }}">
                                @error('best_streak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="current_streak" class="form-label">Série actuelle</label>
                                <input type="number" class="form-control @error('current_streak') is-invalid @enderror"
                                    id="current_streak" name="current_streak" min="0"
                                    value="{{ old('current_streak', $userScore->current_streak) }}">
                                @error('current_streak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> La modification de ces données peut affecter le classement et
                            les statistiques du joueur.
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.players.show', $userScore) }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations en lecture seule -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Informations en lecture seule</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID Discord</label>
                            <input type="text" class="form-control" value="{{ $userScore->user_id }}" readonly>
                        </div>

                        @if($userScore->guild_id)
                            <div class="col-md-6">
                                <label class="form-label text-muted">ID du serveur</label>
                                <input type="text" class="form-control" value="{{ $userScore->guild_id }}" readonly>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label text-muted">Date d'inscription</label>
                            <input type="text" class="form-control"
                                value="{{ $userScore->created_at->format('d/m/Y H:i') }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Dernière mise à jour</label>
                            <input type="text" class="form-control"
                                value="{{ $userScore->updated_at->format('d/m/Y H:i') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>