<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Dashboard Administration</h1>
                <p class="text-muted mb-0">Vue d'ensemble de votre plateforme Car Guesser</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div id="discord-api-status" class="badge bg-warning">
                    <i class="bi bi-arrow-clockwise"></i> Vérification...
                </div>
                <button class="btn btn-outline-secondary btn-sm" onclick="refreshDiscordStats()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <span class="text-muted small">{{ now()->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques principales - Layout compact -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="bi bi-building text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Marques</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_brands']) }}</div>
                        </div>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="bi bi-car-front text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Modèles</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_models']) }}</div>
                        </div>
                        <a href="{{ route('admin.models.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Joueurs</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_players']) }}</div>
                        </div>
                        <a href="{{ route('admin.players.index') }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="bi bi-controller text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Sessions</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_games']) }}</div>
                        </div>
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section principale - 2 colonnes -->
    <div class="row g-4 mb-4">
        <!-- Colonne gauche - Discord + Actions -->
        <div class="col-xl-8">
            <!-- Discord Stats - Compact -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 text-primary">
                            <i class="bi bi-discord me-2"></i>Discord Bot
                        </h6>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshDiscordStats()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Loading state -->
                    <div id="discord-stats-loading" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        <span class="text-muted small">Chargement...</span>
                    </div>

                    <!-- Content state -->
                    <div id="discord-stats-content" style="display: none;">
                        <!-- Stats principales Discord -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-3 col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-primary" id="discord-guilds">0</div>
                                    <small class="text-muted">Serveurs</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-success" id="discord-users">0</div>
                                    <small class="text-muted">Utilisateurs</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-info" id="discord-commands-today">0</div>
                                    <small class="text-muted">Commandes/jour</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-warning" id="discord-games-active">0</div>
                                    <small class="text-muted">Parties actives</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section détaillée en 3 colonnes -->
                        <div class="row g-3">
                            <!-- Bot info -->
                            <div class="col-md-4">
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <h6 class="text-primary mb-2">
                                        <i class="bi bi-robot me-1"></i>Bot Info
                                    </h6>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-robot text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium" id="bot-username">Bot Discord</div>
                                            <span class="badge bg-success" id="bot-status">En ligne</span>
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="small text-muted">Uptime</div>
                                            <div class="fw-bold text-success" id="bot-uptime">0s</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted">Mémoire</div>
                                            <div class="fw-bold text-info" id="bot-memory">0 MB</div>
                                        </div>
                                    </div>
                                    <div class="row text-center mt-2">
                                        <div class="col-6">
                                            <div class="small text-muted">Plateforme</div>
                                            <div class="fw-bold text-warning" id="bot-platform">-</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted">Node.js</div>
                                            <div class="fw-bold text-secondary" id="bot-node-version">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats des jeux -->
                            <div class="col-md-4">
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <h6 class="text-success mb-2">
                                        <i class="bi bi-controller me-1"></i>Statistiques Jeux
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-center p-1 bg-white rounded">
                                                <div class="h6 text-primary mb-0" id="commands-total">0</div>
                                                <small class="text-muted">Total commandes</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-1 bg-white rounded">
                                                <div class="h6 text-success mb-0" id="games-total">0</div>
                                                <small class="text-muted">Total parties</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-1 bg-white rounded">
                                                <div class="h6 text-info mb-0" id="games-today">0</div>
                                                <small class="text-muted">Parties/jour</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-1 bg-white rounded">
                                                <div class="h6 text-warning mb-0" id="games-active">0</div>
                                                <small class="text-muted">En cours</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Commandes populaires -->
                            <div class="col-md-4">
                                <div class="bg-info bg-opacity-10 rounded p-2">
                                    <h6 class="text-info mb-2">
                                        <i class="bi bi-star-fill me-1"></i>Commandes populaires
                                    </h6>
                                    <div id="popular-commands">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-chat-dots"></i>
                                            <div class="small">Aucune commande</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error state -->
                    <div id="discord-stats-error" style="display: none;" class="text-center py-3">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <span class="text-muted small ms-2">API Discord indisponible</span>
                        <button class="btn btn-outline-primary btn-sm ms-2" onclick="refreshDiscordStats()">
                            Réessayer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions rapides - Grid moderne -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="{{ route('admin.brands.create') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="bi bi-plus-circle me-2"></i>Nouvelle marque
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.models.create') }}"
                                class="btn btn-outline-success w-100 text-start">
                                <i class="bi bi-car-front me-2"></i>Nouveau modèle
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-info w-100 text-start" onclick="refreshDiscordStats()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Actualiser Discord
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.sessions.index') }}"
                                class="btn btn-outline-warning w-100 text-start">
                                <i class="bi bi-controller me-2"></i>Voir sessions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite - Top joueurs + Analyses -->
        <div class="col-xl-4">
            <!-- Top Joueurs -->
            @if(isset($topPlayers) && $topPlayers->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-trophy me-2"></i>Top Joueurs
                            </h6>
                            <a href="{{ route('admin.players.index') }}" class="btn btn-outline-primary btn-sm">
                                Tous <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @foreach($topPlayers->take(5) as $index => $player)
                            <div class="d-flex align-items-center {{ $loop->last ? '' : 'mb-3' }}">
                                <div class="me-3">
                                    @if($index === 0)
                                        <i class="bi bi-trophy-fill text-warning fs-5"></i>
                                    @elseif($index === 1)
                                        <i class="bi bi-award-fill text-secondary fs-5"></i>
                                    @elseif($index === 2)
                                        <i class="bi bi-award-fill fs-5" style="color: #cd7f32;"></i>
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px; font-size: 0.8rem;">
                                            {{ $index + 1 }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $player->username }} | {{ getGuildName($player->guild_id) }}</div>
                                    <small class="text-muted">{{ $player->games_played ?? 0 }} parties</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">{{ number_format($player->total_points, 0) }}</div>
                                    <small class="text-muted">pts</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Répartition difficulté - Compact -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Répartition par difficulté
                    </h6>
                </div>
                <div class="card-body p-3">
                    @php
                        $difficulties = [
                            1 => ['label' => 'Facile', 'color' => 'success', 'icon' => 'emoji-smile'],
                            2 => ['label' => 'Moyen', 'color' => 'warning', 'icon' => 'emoji-neutral'],
                            3 => ['label' => 'Difficile', 'color' => 'danger', 'icon' => 'emoji-frown']
                        ];
                        $total = $stats['total_models'] > 0 ? $stats['total_models'] : 1;
                    @endphp

                    @foreach($difficulties as $level => $difficulty)
                        @php
                            $count = $difficultyStats[$level] ?? 0;
                            $percentage = ($count / $total) * 100;
                        @endphp
                        <div class="{{ $loop->last ? '' : 'mb-2' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-{{ $difficulty['icon'] }} me-2 text-{{ $difficulty['color'] }}"></i>
                                    <span class="small">{{ $difficulty['label'] }}</span>
                                </div>
                                <span class="small text-muted">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-{{ $difficulty['color'] }}" style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente - Full width -->
    @if(isset($recentGames) && $recentGames->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>Activité récente
                            </h6>
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-primary btn-sm">
                                Toutes les sessions <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            @foreach($recentGames->take(6) as $game)
                                <div class="col-md-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-medium text-truncate">
                                                    {{ $game->userScore->username ?? 'Joueur' }}
                                                    @if($game->userScore && $game->userScore->guild_id)
                                                        | {{ getGuildName($game->guild_id ) }}
                                                    @endif
                                                </div>
                                                <div class="small text-muted">
                                                    @php
                                                        // S'assurer que la date est dans le bon timezone
                                                        $gameDate = $game->started_at;
                                                        if ($gameDate) {
                                                            // Forcer le timezone si nécessaire
                                                            $gameDate = $gameDate->setTimezone(config('app.timezone', 'UTC'));

                                                            // Calculer la différence manuellement si diffForHumans() pose problème
                                                            $diffInMinutes = now()->diffInMinutes($gameDate);

                                                            if ($diffInMinutes < 1) {
                                                                $timeAgo = 'À l\'instant';
                                                            } elseif ($diffInMinutes < 60) {
                                                                $timeAgo = 'Il y a ' . $diffInMinutes . ' min';
                                                            } elseif ($diffInMinutes < 1440) {
                                                                $hours = floor($diffInMinutes / 60);
                                                                $timeAgo = 'Il y a ' . $hours . 'h';
                                                            } else {
                                                                $days = floor($diffInMinutes / 1440);
                                                                $timeAgo = 'Il y a ' . $days . 'j';
                                                            }
                                                        } else {
                                                            $timeAgo = 'Date inconnue';
                                                        }
                                                    @endphp
                                                    {{ $timeAgo }}
                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-{{ ($game->points_earned + $game->difficulty_points_earned) > 50 ? 'success' : 'secondary' }}">
                                                {{ $game->points_earned + $game->difficulty_points_earned }}pts
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Scripts optimisés -->
    <script>
        let discordStatsCache = null;
        let refreshInterval = null;
        const API_BASE_URL = 'http://localhost:3000/api';
        const REFRESH_INTERVAL = 30000;

        // Fonctions utilitaires
        function formatNumber(num) {
            if (!num || isNaN(num)) return '0';
            if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
            return num.toString();
        }

        function formatUptime(seconds) {
            if (!seconds) return '0s';
            const days = Math.floor(seconds / 86400);
            const hours = Math.floor((seconds % 86400) / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            if (days > 0) return `${days}j ${hours}h`;
            if (hours > 0) return `${hours}h ${minutes}m`;
            return `${minutes}m`;
        }

        function formatMemory(bytes) {
            if (!bytes || isNaN(bytes)) return '0 MB';
            return Math.round(bytes / (1024 * 1024)) + ' MB';
        }

        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        }

        // Récupération des stats Discord
        async function fetchDiscordStats() {
            try {
                const response = await fetch(`${API_BASE_URL}/stats/detailed`, { timeout: 10000 });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                discordStatsCache = data;
                updateDiscordStatsDisplay(data);
                updateDiscordStatusBadge(true);
            } catch (error) {
                console.error('Discord API error:', error);
                showDiscordStatsError();
                updateDiscordStatusBadge(false);
            }
        }

        function updateDiscordStatsDisplay(data) {
            document.getElementById('discord-stats-loading').style.display = 'none';
            document.getElementById('discord-stats-error').style.display = 'none';
            document.getElementById('discord-stats-content').style.display = 'block';

            const bot = data.bot || {};
            const api = data.api || {};

            // Stats principales
            updateElement('discord-guilds', formatNumber(bot.guilds || 0));
            updateElement('discord-users', formatNumber(bot.users || 0));
            updateElement('discord-commands-today', formatNumber(bot.commands?.today || 0));
            updateElement('discord-games-active', formatNumber(bot.games?.active || 0));

            // Bot info
            updateElement('bot-username', bot.botInfo?.username || bot.botInfo?.name || 'Bot Discord');
            updateElement('bot-uptime', formatUptime(bot.uptime || api.uptime || 0));
            updateElement('bot-memory', formatMemory(api.memory?.heapUsed || bot.performance?.memoryUsage?.heapUsed || 0));
            updateElement('bot-platform', api.platform || bot.performance?.platform || '-');
            updateElement('bot-node-version', api.version || bot.performance?.nodeVersion || '-');

            // Status du bot
            const statusEl = document.getElementById('bot-status');
            if (statusEl) {
                statusEl.textContent = bot.isOnline ? 'En ligne' : 'Hors ligne';
                statusEl.className = `badge ${bot.isOnline ? 'bg-success' : 'bg-danger'}`;
            }

            // Stats des jeux
            const games = bot.games || {};
            updateElement('commands-total', formatNumber(bot.commands?.total || 0));
            updateElement('games-total', formatNumber(games.total || 0));
            updateElement('games-today', formatNumber(games.today || games.todayStarted || 0));
            updateElement('games-active', formatNumber(games.active || 0));

            // Commandes populaires
            updatePopularCommands(bot.commands?.popular || []);
        }

        function updatePopularCommands(commands) {
            const container = document.getElementById('popular-commands');
            if (!container) return;

            if (!commands || commands.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots"></i>
                        <div class="small">Aucune commande</div>
                    </div>
                `;
                return;
            }

            let html = '';
            const badgeColors = ['primary', 'success', 'info', 'warning', 'secondary'];

            commands.slice(0, 4).forEach((cmd, index) => {
                const color = badgeColors[index] || 'secondary';
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-${color} me-1" style="font-size: 0.6rem;">${index + 1}</span>
                            <span class="small">/${cmd.name || 'cmd'}</span>
                        </div>
                        <span class="small text-muted">${formatNumber(cmd.count || 0)}</span>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function showDiscordStatsError() {
            document.getElementById('discord-stats-loading').style.display = 'none';
            document.getElementById('discord-stats-content').style.display = 'none';
            document.getElementById('discord-stats-error').style.display = 'block';
        }

        function updateDiscordStatusBadge(isOnline) {
            const badge = document.getElementById('discord-api-status');
            if (!badge) return;

            if (isOnline) {
                badge.className = 'badge bg-success';
                badge.innerHTML = '<i class="bi bi-check-circle"></i> Discord OK';
            } else {
                badge.className = 'badge bg-danger';
                badge.innerHTML = '<i class="bi bi-x-circle"></i> Discord KO';
            }
        }

        function refreshDiscordStats() {
            document.getElementById('discord-stats-loading').style.display = 'block';
            document.getElementById('discord-stats-content').style.display = 'none';
            document.getElementById('discord-stats-error').style.display = 'none';
            fetchDiscordStats();
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function () {
            fetchDiscordStats();
            refreshInterval = setInterval(fetchDiscordStats, REFRESH_INTERVAL);
        });

        window.addEventListener('beforeunload', function () {
            if (refreshInterval) clearInterval(refreshInterval);
        });

        window.refreshDiscordStats = refreshDiscordStats;
    </script>
</x-admin-layout>