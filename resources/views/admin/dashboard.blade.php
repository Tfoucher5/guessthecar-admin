<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">Dashboard Administration</h1>
                <p class="text-muted mb-0">Vue d'ensemble de votre plateforme Car Guesser</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Statut API Discord -->
                <div id="discord-api-status" class="badge bg-warning">
                    <i class="bi bi-arrow-clockwise"></i> Vérification...
                </div>
                <span class="text-muted small">{{ now()->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </x-slot>

    <!-- Stats Laravel + Discord -->
    <div class="row g-4 mb-4">
        <!-- Stats Laravel existantes -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">Marques</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_brands']) }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> Voir toutes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success text-white rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-car-front fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">Modèles</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_models']) }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.models.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> Voir tous
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info text-white rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">Joueurs</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_players']) }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.players.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> Voir tous
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning text-white rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-controller fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">Sessions</div>
                            <div class="h4 mb-0 text-dark">{{ number_format($stats['total_games']) }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> Voir toutes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Discord Bot -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-discord" style="border-color: #5865F2 !important;">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-discord text-primary me-2"></i>
                            Statistiques Discord Bot
                        </h5>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshDiscordStats()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Actualiser
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="discord-stats-loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2 text-muted">Récupération des stats Discord...</p>
                    </div>

                    <div id="discord-stats-content" style="display: none;">
                        <!-- Stats générales Discord -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card bg-discord text-white" style="background-color: #5865F2 !important;">
                                    <div class="card-body text-center">
                                        <i class="bi bi-server fs-1 mb-2"></i>
                                        <h4 class="mb-0" id="discord-guilds">0</h4>
                                        <small>Serveurs Discord</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people-fill fs-1 mb-2"></i>
                                        <h4 class="mb-0" id="discord-users">0</h4>
                                        <small>Utilisateurs Discord</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-chat-dots-fill fs-1 mb-2"></i>
                                        <h4 class="mb-0" id="discord-commands-today">0</h4>
                                        <small>Commandes aujourd'hui</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-controller fs-1 mb-2"></i>
                                        <h4 class="mb-0" id="discord-games-active">0</h4>
                                        <small>Parties en cours</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section détaillée -->
                        <div class="row g-4">
                            <!-- Informations Bot -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="bi bi-robot me-2"></i>Informations Bot
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="bot-info">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-robot text-white fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" id="bot-username">Bot Discord</div>
                                                    <div class="text-muted small" id="bot-status">En attente...</div>
                                                </div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="fw-bold text-success" id="bot-uptime">0s</div>
                                                        <small class="text-muted">Uptime</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="fw-bold text-info" id="bot-memory">0 MB</div>
                                                    <small class="text-muted">Mémoire</small>
                                                </div>
                                            </div>

                                            <!-- Nouvelles données de performance -->
                                            <div class="row text-center mt-3">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="fw-bold text-warning" id="bot-platform">-</div>
                                                        <small class="text-muted">Plateforme</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="fw-bold text-secondary" id="bot-node-version">-</div>
                                                    <small class="text-muted">Node.js</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Commandes populaires -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="bi bi-star-fill me-2"></i>Commandes populaires
                                        </h6>
                                    </div>
                                    <div class="card-body" id="popular-commands">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-chat-dots fs-2"></i>
                                            <p class="mt-2 mb-0">Aucune commande pour le moment</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats des jeux Discord -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="bi bi-controller me-2"></i>Statistiques des jeux
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                                    <div class="h4 text-primary mb-1" id="commands-total">0</div>
                                                    <small class="text-muted">Total commandes</small>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                                    <div class="h4 text-success mb-1" id="games-total">0</div>
                                                    <small class="text-muted">Total parties</small>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                                    <div class="h4 text-info mb-1" id="games-today">0</div>
                                                    <small class="text-muted">Parties aujourd'hui</small>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                                    <div class="h4 text-warning mb-1" id="games-active">0</div>
                                                    <small class="text-muted">Parties actives</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Erreur API -->
                    <div id="discord-stats-error" style="display: none;" class="text-center py-5">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                        <h5 class="mt-3 text-danger">Impossible de récupérer les stats Discord</h5>
                        <p class="text-muted">Vérifiez que l'API Node.js est démarrée et accessible.</p>
                        <button class="btn btn-outline-primary" onclick="refreshDiscordStats()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Réessayer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('admin.brands.create') }}"
                            class="btn btn-outline-primary d-flex align-items-center">
                            <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-plus-lg"></i>
                            </div>
                            <div class="text-start">
                                <div class="fw-medium">Ajouter une marque</div>
                                <small class="text-muted">Créer une nouvelle marque automobile</small>
                            </div>
                        </a>

                        <a href="{{ route('admin.models.create') }}"
                            class="btn btn-outline-success d-flex align-items-center">
                            <div class="bg-success text-white rounded me-3 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-car-front"></i>
                            </div>
                            <div class="text-start">
                                <div class="fw-medium">Ajouter un modèle</div>
                                <small class="text-muted">Créer un nouveau modèle de voiture</small>
                            </div>
                        </a>

                        <a href="#" class="btn btn-outline-info d-flex align-items-center">
                            <div class="bg-info text-white rounded me-3 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="text-start">
                                <div class="fw-medium">Ajouter un utilisateur</div>
                                <small class="text-muted">Créer un nouveau compte admin</small>
                            </div>
                        </a>

                        <button class="btn btn-outline-secondary d-flex align-items-center"
                            onclick="refreshDiscordStats()">
                            <div class="bg-secondary text-white rounded me-3 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-arrow-clockwise"></i>
                            </div>
                            <div class="text-start">
                                <div class="fw-medium">Actualiser stats Discord</div>
                                <small class="text-muted">Recharger les données du bot</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top joueurs (si disponible) -->
    @if(isset($topPlayers) && $topPlayers->count() > 0)
        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-trophy me-2"></i>Top Joueurs
                        </h5>
                        <a href="{{ route('admin.players.index') }}" class="btn btn-outline-primary btn-sm">
                            Voir tous
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($topPlayers->take(5) as $index => $player)
                                <div class="col-md-4 col-lg-2">
                                    <div class="text-center p-3 border rounded">
                                        <div class="mb-2">
                                            @if($index === 0)
                                                <i class="bi bi-trophy-fill text-warning fs-2"></i>
                                            @elseif($index === 1)
                                                <i class="bi bi-award-fill text-secondary fs-2"></i>
                                            @elseif($index === 2)
                                                <i class="bi bi-award-fill text-warning fs-2"
                                                    style="color: #cd7f32 !important;"></i>
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                    style="width: 40px; height: 40px;">
                                                    {{ $index + 1 }}
                                                </div>
                                            @endif
                                        </div>
                                        <h6 class="mb-1">{{ $player->username }}</h6>
                                        <div class="text-primary fw-bold">{{ number_format($player->total_points, 0) }} pts
                                        </div>
                                        <small class="text-muted">{{ $player->games_played ?? 0 }} parties</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Laravel existantes -->
    <div class="row g-4">
        <!-- Répartition par difficulté -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Répartition par difficulté
                    </h5>
                </div>
                <div class="card-body">
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
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-{{ $difficulty['icon'] }} me-2 text-{{ $difficulty['color'] }}"></i>
                                    <span class="fw-medium">{{ $difficulty['label'] }}</span>
                                </div>
                                <span class="text-muted">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $difficulty['color'] }}" style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Activité récente
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentGames->count() > 0)
                        <div class="timeline">
                            @foreach($recentGames->take(5) as $game)
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-controller" style="font-size: 12px;"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium text-dark">{{ $game->userScore->username ?? 'Joueur inconnu' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $game->points_earned + $game->difficulty_points_earned }} points •
                                            {{ $game->questions_answered }} questions
                                        </div>
                                        <div class="text-muted small">
                                            {{ $game->started_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-{{ ($game->points_earned + $game->difficulty_points_earned) > 50 ? 'success' : 'secondary' }}">
                                            {{ $game->points_earned + $game->difficulty_points_earned }}pts
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-primary btn-sm">
                                Voir toutes les sessions
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-clock-history fs-1"></i>
                            <p class="mt-2">Aucune activité récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let discordStatsCache = null;
        let lastDiscordUpdate = null;
        let refreshInterval = null;

        // Configuration de l'API
        const API_BASE_URL = 'http://localhost:3000/api';
        const REFRESH_INTERVAL = 30000; // 30 secondes

        // Fonction pour formater l'uptime
        function formatUptime(seconds) {
            if (!seconds) return '0s';

            const days = Math.floor(seconds / 86400);
            const hours = Math.floor((seconds % 86400) / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);

            if (days > 0) return `${days}j ${hours}h`;
            if (hours > 0) return `${hours}h ${minutes}m`;
            return `${minutes}m`;
        }

        // Fonction pour formater les nombres
        function formatNumber(num) {
            if (!num || isNaN(num)) return '0';
            if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
            return num.toString();
        }

        // Fonction pour formater la mémoire
        function formatMemory(bytes) {
            if (!bytes || isNaN(bytes)) return '0 MB';
            const mb = bytes / (1024 * 1024);
            return Math.round(mb) + ' MB';
        }

        // Récupérer les stats Discord avec gestion d'erreur améliorée
        async function fetchDiscordStats() {
            try {
                console.log('🔄 Récupération des stats Discord...');

                // Tenter d'abord le health check
                const healthResponse = await fetch(`${API_BASE_URL}/health`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    timeout: 10000
                });

                if (!healthResponse.ok) {
                    throw new Error(`Health check failed: ${healthResponse.status}`);
                }

                const healthData = await healthResponse.json();
                console.log('✅ Health check OK:', healthData.status);

                // Récupérer les stats détaillées
                const statsResponse = await fetch(`${API_BASE_URL}/stats/detailed`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    timeout: 15000
                });

                if (!statsResponse.ok) {
                    throw new Error(`Stats request failed: ${statsResponse.status}`);
                }

                const statsData = await statsResponse.json();
                console.log('📊 Stats récupérées:', statsData);

                // Sauvegarder en cache
                discordStatsCache = statsData;
                lastDiscordUpdate = new Date();

                updateDiscordStatsDisplay(statsData);
                updateDiscordStatusBadge(true);

                return statsData;

            } catch (error) {
                console.error('❌ Erreur récupération stats Discord:', error);

                // Essayer l'API Laravel en fallback
                try {
                    const fallbackResponse = await fetch('/admin/api-status');
                    const fallbackData = await fallbackResponse.json();

                    if (fallbackData.status === 'healthy') {
                        console.log('✅ Fallback Laravel API OK');
                        updateDiscordStatusBadge(true, 'Laravel API OK');
                        return;
                    }
                } catch (fallbackError) {
                    console.error('❌ Fallback Laravel API aussi en erreur:', fallbackError);
                }

                showDiscordStatsError(error.message);
                updateDiscordStatusBadge(false, error.message);
            }
        }

        // Mettre à jour l'affichage des stats Discord
        function updateDiscordStatsDisplay(data) {
            console.log('🎨 Mise à jour de l\'affichage avec:', data);

            try {
                // Masquer le loading et l'erreur
                const loadingEl = document.getElementById('discord-stats-loading');
                const errorEl = document.getElementById('discord-stats-error');
                const contentEl = document.getElementById('discord-stats-content');

                if (loadingEl) loadingEl.style.display = 'none';
                if (errorEl) errorEl.style.display = 'none';
                if (contentEl) contentEl.style.display = 'block';

                // Stats principales avec vérification de nullité
                const bot = data.bot || {};
                const api = data.api || {};

                updateElement('discord-guilds', formatNumber(bot.guilds || 0));
                updateElement('discord-users', formatNumber(bot.users || 0));
                updateElement('discord-commands-today', formatNumber(bot.commands?.today || 0));
                updateElement('discord-games-active', formatNumber(bot.games?.active || 0));

                // Infos bot avec fallback
                if (bot.botInfo) {
                    updateElement('bot-username', bot.botInfo.username || bot.botInfo.name || 'Bot Discord');
                }

                const statusText = bot.isOnline ? 'En ligne' : 'Hors ligne';
                const statusClass = bot.isOnline ? 'text-success' : 'text-danger';
                updateElement('bot-status', statusText, statusClass);

                // Performance et système
                updateElement('bot-uptime', formatUptime(bot.uptime || api.uptime || 0));

                if (api.memory?.heapUsed) {
                    updateElement('bot-memory', formatMemory(api.memory.heapUsed));
                } else if (bot.performance?.memoryUsage?.heapUsed) {
                    updateElement('bot-memory', formatMemory(bot.performance.memoryUsage.heapUsed));
                }

                updateElement('bot-platform', api.platform || bot.performance?.platform || '-');
                updateElement('bot-node-version', api.version || bot.performance?.nodeVersion || '-');

                // Stats des jeux avec vérification
                const games = bot.games || {};
                updateElement('commands-total', formatNumber(bot.commands?.total || 0));
                updateElement('games-total', formatNumber(games.total || 0));
                updateElement('games-today', formatNumber(games.today || games.todayStarted || 0));
                updateElement('games-active', formatNumber(games.active || 0));

                // Commandes populaires
                updatePopularCommands(bot.commands?.popular || []);

                console.log('✅ Affichage mis à jour avec succès');

            } catch (error) {
                console.error('❌ Erreur lors de la mise à jour de l\'affichage:', error);
                showDiscordStatsError('Erreur d\'affichage: ' + error.message);
            }
        }

        // Fonction utilitaire pour mettre à jour un élément
        function updateElement(id, value, className = null) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
                if (className) {
                    element.className = className;
                }
            } else {
                console.warn(`⚠️ Élément ${id} introuvable`);
            }
        }

        // Mettre à jour les commandes populaires
        function updatePopularCommands(commands) {
            const container = document.getElementById('popular-commands');

            if (!container) {
                console.warn('⚠️ Container popular-commands introuvable');
                return;
            }

            if (!commands || commands.length === 0) {
                container.innerHTML = `
            <div class="text-center text-muted">
                <i class="bi bi-chat-dots fs-2"></i>
                <p class="mt-2 mb-0">Aucune commande pour le moment</p>
            </div>
        `;
                return;
            }

            let html = '';
            const badgeColors = ['primary', 'success', 'info', 'warning', 'secondary'];

            commands.slice(0, 5).forEach((cmd, index) => {
                const color = badgeColors[index] || 'secondary';
                const count = cmd.count || 0;
                const name = cmd.name || 'Inconnue';

                html += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <span class="badge bg-${color} me-2">${index + 1}</span>
                    <span class="fw-medium">/${name}</span>
                </div>
                <span class="text-muted small">${formatNumber(count)} fois</span>
            </div>
        `;
            });

            container.innerHTML = html;
        }

        // Afficher l'erreur Discord
        function showDiscordStatsError(errorMessage = 'Erreur inconnue') {
            const loadingEl = document.getElementById('discord-stats-loading');
            const contentEl = document.getElementById('discord-stats-content');
            const errorEl = document.getElementById('discord-stats-error');

            if (loadingEl) loadingEl.style.display = 'none';
            if (contentEl) contentEl.style.display = 'none';
            if (errorEl) {
                errorEl.style.display = 'block';

                // Mettre à jour le message d'erreur
                const errorText = errorEl.querySelector('p');
                if (errorText) {
                    errorText.textContent = `Erreur: ${errorMessage}. Vérifiez que l'API Node.js est démarrée et accessible.`;
                }
            }
        }

        // Mettre à jour le badge de statut
        function updateDiscordStatusBadge(isOnline, customMessage = null) {
            const badge = document.getElementById('discord-api-status');
            if (!badge) return;

            if (isOnline) {
                badge.className = 'badge bg-success';
                badge.innerHTML = `<i class="bi bi-check-circle"></i> ${customMessage || 'Discord API OK'}`;
            } else {
                badge.className = 'badge bg-danger';
                badge.innerHTML = `<i class="bi bi-x-circle"></i> ${customMessage || 'Discord API KO'}`;
            }
        }

        // Actualiser les stats Discord
        function refreshDiscordStats() {
            console.log('🔄 Actualisation manuelle des stats Discord');

            const loadingEl = document.getElementById('discord-stats-loading');
            const contentEl = document.getElementById('discord-stats-content');
            const errorEl = document.getElementById('discord-stats-error');

            if (loadingEl) loadingEl.style.display = 'block';
            if (contentEl) contentEl.style.display = 'none';
            if (errorEl) errorEl.style.display = 'none';

            // Mettre à jour le badge pendant le chargement
            updateDiscordStatusBadge(false, 'Vérification...');

            fetchDiscordStats();
        }

        // Initialisation automatique
        function initDiscordStats() {
            console.log('🚀 Initialisation du dashboard Discord');

            // Vérifier que les éléments DOM existent
            const requiredElements = [
                'discord-stats-loading',
                'discord-stats-content',
                'discord-stats-error',
                'discord-api-status'
            ];

            const missingElements = requiredElements.filter(id => !document.getElementById(id));
            if (missingElements.length > 0) {
                console.warn('⚠️ Éléments DOM manquants:', missingElements);
            }

            // Récupérer les stats au démarrage
            fetchDiscordStats();

            // Configurer l'actualisation automatique
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }

            refreshInterval = setInterval(() => {
                console.log('🔄 Actualisation automatique des stats');
                fetchDiscordStats();
            }, REFRESH_INTERVAL);

            console.log(`✅ Actualisation automatique configurée (${REFRESH_INTERVAL / 1000}s)`);
        }

        // Nettoyage à la fermeture
        function cleanupDiscordStats() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
                console.log('🧹 Actualisation automatique arrêtée');
            }
        }

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function () {
            console.log('📄 DOM chargé, initialisation du dashboard');
            initDiscordStats();
        });

        // Nettoyage avant fermeture
        window.addEventListener('beforeunload', cleanupDiscordStats);

        // Export des fonctions pour usage global
        window.refreshDiscordStats = refreshDiscordStats;
        window.fetchDiscordStats = fetchDiscordStats;
    </script>
</x-admin-layout>