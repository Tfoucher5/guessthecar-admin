<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">
                    <i class="bi bi-pencil me-2"></i>Modifier le joueur
                </h1>
                <p class="text-muted mb-0">{{ $player->username }} - ID: {{ $player->user_id }}</p>
            </div>
            <div>
                <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
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

    <div class="row g-4">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Informations du joueur
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.players.update', $player) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Nom d'utilisateur -->
                            <div class="col-md-6">
                                <label for="username" class="form-label">
                                    Nom d'utilisateur <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username', $player->username) }}"
                                    required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Nom affiché publiquement sur la plateforme
                                </div>
                            </div>

                            <!-- User ID (lecture seule) -->
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">ID Discord</label>
                                <input type="text" class="form-control" id="user_id" value="{{ $player->user_id }}"
                                    readonly>
                                <div class="form-text">
                                    <i class="bi bi-lock me-1"></i>
                                    Identifiant Discord (non modifiable)
                                </div>
                            </div>

                            <!-- Points totaux -->
                            <div class="col-md-4">
                                <label for="total_points" class="form-label">Points totaux</label>
                                <input type="number" class="form-control @error('total_points') is-invalid @enderror"
                                    id="total_points" name="total_points"
                                    value="{{ old('total_points', $player->total_points) }}" step="0.01">
                                @error('total_points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Parties jouées -->
                            <div class="col-md-4">
                                <label for="games_played" class="form-label">Parties jouées</label>
                                <input type="number" class="form-control @error('games_played') is-invalid @enderror"
                                    id="games_played" name="games_played"
                                    value="{{ old('games_played', $player->games_played) }}" min="0">
                                @error('games_played')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Parties gagnées -->
                            <div class="col-md-4">
                                <label for="games_won" class="form-label">Parties gagnées</label>
                                <input type="number" class="form-control @error('games_won') is-invalid @enderror"
                                    id="games_won" name="games_won" value="{{ old('games_won', $player->games_won) }}"
                                    min="0">
                                @error('games_won')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Série actuelle -->
                            <div class="col-md-6">
                                <label for="current_streak" class="form-label">Série actuelle</label>
                                <input type="number" class="form-control @error('current_streak') is-invalid @enderror"
                                    id="current_streak" name="current_streak"
                                    value="{{ old('current_streak', $player->current_streak) }}" min="0">
                                @error('current_streak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Meilleure série -->
                            <div class="col-md-6">
                                <label for="best_streak" class="form-label">Meilleure série</label>
                                <input type="number" class="form-control @error('best_streak') is-invalid @enderror"
                                    id="best_streak" name="best_streak"
                                    value="{{ old('best_streak', $player->best_streak) }}" min="0">
                                @error('best_streak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Calculs automatiques -->
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-calculator me-2"></i>Statistiques calculées
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Taux de victoire</small>
                                                <strong
                                                    id="win-rate">{{ $player->games_played > 0 ? round(($player->games_won / $player->games_played) * 100, 1) : 0 }}%</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Points par partie</small>
                                                <strong
                                                    id="points-per-game">{{ $player->games_played > 0 ? round($player->total_points / $player->games_played, 1) : 0 }}</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Parties perdues</small>
                                                <strong
                                                    id="games-lost">{{ ($player->games_played ?? 0) - ($player->games_won ?? 0) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="col-lg-4">
            <!-- Statistiques du joueur -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Aperçu du profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($player->username, 0, 1) }}
                    </div>
                    <h5 class="mb-1">{{ $player->username }}</h5>
                    <p class="text-muted small mb-3">Inscrit le {{ $player->created_at->format('d/m/Y') }}</p>

                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="bg-light p-2 rounded">
                                <div class="fw-bold text-primary">{{ number_format($player->total_points, 0) }}</div>
                                <small class="text-muted">Points</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-2 rounded">
                                <div class="fw-bold text-success">{{ $player->games_played ?? 0 }}</div>
                                <small class="text-muted">Parties</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info" onclick="resetStreak()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Remettre série à 0
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="adjustPoints()">
                            <i class="bi bi-plus-minus me-2"></i>Ajuster points
                        </button>
                        <a href="{{ route('admin.sessions.index', ['user_id' => $player->user_id]) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-controller me-2"></i>Voir ses sessions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations système -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informations système
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Créé le :</td>
                            <td>{{ $player->created_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modifié le :</td>
                            <td>{{ $player->updated_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">ID en base :</td>
                            <td><code>{{ $player->id }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mise à jour des statistiques calculées en temps réel
        function updateCalculatedStats() {
            const gamesPlayed = parseInt(document.getElementById('games_played').value) || 0;
            const gamesWon = parseInt(document.getElementById('games_won').value) || 0;
            const totalPoints = parseFloat(document.getElementById('total_points').value) || 0;

            // Taux de victoire
            const winRate = gamesPlayed > 0 ? ((gamesWon / gamesPlayed) * 100).toFixed(1) : 0;
            document.getElementById('win-rate').textContent = winRate + '%';

            // Points par partie
            const pointsPerGame = gamesPlayed > 0 ? (totalPoints / gamesPlayed).toFixed(1) : 0;
            document.getElementById('points-per-game').textContent = pointsPerGame;

            // Parties perdues
            const gamesLost = Math.max(0, gamesPlayed - gamesWon);
            document.getElementById('games-lost').textContent = gamesLost;
        }

        // Actions rapides
        function resetStreak() {
            if (confirm('Êtes-vous sûr de vouloir remettre la série actuelle à 0 ?')) {
                document.getElementById('current_streak').value = 0;
            }
        }

        function adjustPoints() {
            const adjustment = prompt('Ajustement des points (+ ou - valeur) :');
            if (adjustment !== null && !isNaN(adjustment)) {
                const current = parseFloat(document.getElementById('total_points').value) || 0;
                const newValue = current + parseFloat(adjustment);
                document.getElementById('total_points').value = Math.max(0, newValue);
                updateCalculatedStats();
            }
        }

        // Validation : les parties gagnées ne peuvent pas dépasser les parties jouées
        function validateGamesWon() {
            const gamesPlayed = parseInt(document.getElementById('games_played').value) || 0;
            const gamesWon = parseInt(document.getElementById('games_won').value) || 0;

            if (gamesWon > gamesPlayed) {
                document.getElementById('games_won').value = gamesPlayed;
            }
            updateCalculatedStats();
        }

        // Écouter les changements
        document.getElementById('games_played').addEventListener('input', function () {
            validateGamesWon();
        });
        document.getElementById('games_won').addEventListener('input', validateGamesWon);
        document.getElementById('total_points').addEventListener('input', updateCalculatedStats);

        // Initialisation
        updateCalculatedStats();
    </script>
</x-admin-layout>