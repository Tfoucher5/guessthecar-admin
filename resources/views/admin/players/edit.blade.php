<x-admin-layout>
    <x-slot name="title">Modifier {{ $player->username }}</x-slot>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.players.show', $player) }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1 text-dark">Modifier le joueur</h1>
                    <p class="text-muted mb-0">{{ $player->username }}</p>
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
                    <form method="POST" action="{{ route('admin.players.update', $player) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nom d'utilisateur *</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username', $player->username) }}"
                                    required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="total_points" class="form-label">Points totaux</label>
                                <input type="number" class="form-control @error('total_points') is-invalid @enderror"
                                    id="total_points" name="total_points" step="0.01"
                                    value="{{ old('total_points', $player->total_points) }}">
                                @error('total_points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="games_played" class="form-label">Parties jouées</label>
                                <input type="number" class="form-control @error('games_played') is-invalid @enderror"
                                    id="games_played" name="games_played" min="0"
                                    value="{{ old('games_played', $player->games_played) }}">
                                @error('games_played')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="games_won" class="form-label">Parties gagnées</label>
                                <input type="number" class="form-control @error('games_won') is-invalid @enderror"
                                    id="games_won" name="games_won" min="0"
                                    value="{{ old('games_won', $player->games_won) }}">
                                @error('games_won')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="best_streak" class="form-label">Meilleure série</label>
                                <input type="number" class="form-control @error('best_streak') is-invalid @enderror"
                                    id="best_streak" name="best_streak" min="0"
                                    value="{{ old('best_streak', $player->best_streak) }}">
                                @error('best_streak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="current_streak" class="form-label">Série actuelle</label>
                                <input type="number" class="form-control @error('current_streak') is-invalid @enderror"
                                    id="current_streak" name="current_streak" min="0"
                                    value="{{ old('current_streak', $player->current_streak) }}">
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
                            <a href="{{ route('admin.players.show', $player) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>
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
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations en lecture seule
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID Discord</label>
                            <input type="text" class="form-control" value="{{ $player->user_id }}" readonly>
                        </div>

                        @if($player->guild_id)
                            <div class="col-md-6">
                                <label class="form-label text-muted">Serveur Discord</label>
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                        value="{{ getGuildName($player->guild_id, 30) }}" readonly>
                                    <span class="input-group-text">
                                        <small class="text-muted">{{ $player->guild_id }}</small>
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label text-muted">Date d'inscription</label>
                            <input type="text" class="form-control"
                                value="{{ $player->created_at->format('d/m/Y H:i') }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Dernière mise à jour</label>
                            <input type="text" class="form-control"
                                value="{{ $player->updated_at->format('d/m/Y H:i') }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Taux de réussite</label>
                            <input type="text" class="form-control"
                                value="{{ $player->games_played > 0 ? round(($player->games_won / $player->games_played) * 100, 1) : 0 }}%"
                                readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Niveau de compétence</label>
                            <input type="text" class="form-control" value="{{ $player->skill_level ?? 'Débutant' }}"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions dangereuses -->
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Zone dangereuse
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h6 class="text-danger">Supprimer le joueur</h6>
                            <p class="small text-muted mb-2">
                                Supprime définitivement le joueur et toutes ses données.
                            </p>
                            <form method="POST" action="{{ route('admin.players.destroy', $player) }}" class="d-inline"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash me-1"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Validation en temps réel des parties
            const gamesPlayedInput = document.getElementById('games_played');
            const gamesWonInput = document.getElementById('games_won');

            function validateGames() {
                const played = parseInt(gamesPlayedInput.value) || 0;
                const won = parseInt(gamesWonInput.value) || 0;

                if (won > played) {
                    gamesWonInput.setCustomValidity('Le nombre de parties gagnées ne peut pas être supérieur au nombre de parties jouées.');
                } else {
                    gamesWonInput.setCustomValidity('');
                }
            }

            gamesPlayedInput.addEventListener('input', validateGames);
            gamesWonInput.addEventListener('input', validateGames);
        });
    </script>
</x-admin-layout>