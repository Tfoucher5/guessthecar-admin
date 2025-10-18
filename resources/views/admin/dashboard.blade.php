<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="header-content">
                <h1 class="dashboard-title mb-2">Dashboard Administration</h1>
                <p class="dashboard-subtitle mb-0">Vue d'ensemble de votre plateforme Car Guesser</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div id="discord-api-status" class="status-badge status-loading">
                    <i class="bi bi-arrow-clockwise rotating"></i> Vérification...
                </div>
                <button class="btn-refresh" onclick="refreshDiscordStats()" title="Actualiser">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <span class="time-display">{{ now()->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </x-slot>

    <!-- Statistiques principales - Cards modernes -->
    <div class="stats-grid mb-4">
        <div class="stat-card-modern stat-card-brands">
            <div class="stat-card-bg"></div>
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Marques</div>
                    <div class="stat-value">{{ number_format($stats['total_brands']) }}</div>
                </div>
                <a href="{{ route('admin.brands.index') }}" class="stat-card-link">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="stat-card-shine"></div>
        </div>

        <div class="stat-card-modern stat-card-models">
            <div class="stat-card-bg"></div>
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="bi bi-car-front"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Modèles</div>
                    <div class="stat-value">{{ number_format($stats['total_models']) }}</div>
                </div>
                <a href="{{ route('admin.models.index') }}" class="stat-card-link">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="stat-card-shine"></div>
        </div>

        <div class="stat-card-modern stat-card-players">
            <div class="stat-card-bg"></div>
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Joueurs</div>
                    <div class="stat-value">{{ number_format($stats['total_players']) }}</div>
                </div>
                <a href="{{ route('admin.players.index') }}" class="stat-card-link">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="stat-card-shine"></div>
        </div>

        <div class="stat-card-modern stat-card-sessions">
            <div class="stat-card-bg"></div>
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="bi bi-controller"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Sessions</div>
                    <div class="stat-value">{{ number_format($stats['total_games']) }}</div>
                </div>
                <a href="{{ route('admin.sessions.index') }}" class="stat-card-link">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="stat-card-shine"></div>
        </div>
    </div>

    <!-- Section principale - 2 colonnes -->
    <div class="row g-4 mb-4">
        <!-- Colonne gauche - Discord + Actions -->
        <div class="col-xl-8">
            <!-- Discord Stats - Modern -->
            <div class="modern-card discord-card mb-4">
                <div class="modern-card-header">
                    <div class="card-header-content">
                        <div class="card-title-icon">
                            <i class="bi bi-discord"></i>
                        </div>
                        <h6 class="card-title-text">Discord Bot</h6>
                    </div>
                    <button class="btn-icon-refresh" onclick="refreshDiscordStats()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="modern-card-body">
                    <!-- Loading state -->
                    <div id="discord-stats-loading" class="loading-state">
                        <div class="loading-spinner"></div>
                        <span class="loading-text">Chargement des statistiques...</span>
                    </div>

                    <!-- Content state -->
                    <div id="discord-stats-content" style="display: none;">
                        <!-- Stats principales Discord -->
                        <div class="discord-stats-main">
                            <div class="discord-stat-box">
                                <div class="discord-stat-value" id="discord-guilds">0</div>
                                <div class="discord-stat-label">Serveurs</div>
                                <div class="discord-stat-icon"><i class="bi bi-server"></i></div>
                            </div>
                            <div class="discord-stat-box">
                                <div class="discord-stat-value" id="discord-users">0</div>
                                <div class="discord-stat-label">Utilisateurs</div>
                                <div class="discord-stat-icon"><i class="bi bi-people"></i></div>
                            </div>
                            <div class="discord-stat-box">
                                <div class="discord-stat-value" id="discord-commands-today">0</div>
                                <div class="discord-stat-label">Commandes/jour</div>
                                <div class="discord-stat-icon"><i class="bi bi-terminal"></i></div>
                            </div>
                            <div class="discord-stat-box">
                                <div class="discord-stat-value" id="discord-games-active">0</div>
                                <div class="discord-stat-label">Parties actives</div>
                                <div class="discord-stat-icon"><i class="bi bi-controller"></i></div>
                            </div>
                        </div>

                        <!-- Section détaillée en 3 colonnes -->
                        <div class="discord-details-grid">
                            <!-- Bot info -->
                            <div class="discord-detail-card bot-info-card">
                                <h6 class="detail-card-title">
                                    <i class="bi bi-robot me-2"></i>Bot Info
                                </h6>
                                <div class="bot-profile">
                                    <div class="bot-avatar">
                                        <i class="bi bi-robot"></i>
                                    </div>
                                    <div class="bot-details">
                                        <div class="bot-name" id="bot-username">Bot Discord</div>
                                        <span class="bot-status" id="bot-status">En ligne</span>
                                    </div>
                                </div>
                                <div class="bot-metrics">
                                    <div class="bot-metric">
                                        <div class="metric-label">Uptime</div>
                                        <div class="metric-value uptime" id="bot-uptime">0s</div>
                                    </div>
                                    <div class="bot-metric">
                                        <div class="metric-label">Mémoire</div>
                                        <div class="metric-value memory" id="bot-memory">0 MB</div>
                                    </div>
                                    <div class="bot-metric">
                                        <div class="metric-label">Plateforme</div>
                                        <div class="metric-value platform" id="bot-platform">-</div>
                                    </div>
                                    <div class="bot-metric">
                                        <div class="metric-label">Node.js</div>
                                        <div class="metric-value node" id="bot-node-version">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats des jeux -->
                            <div class="discord-detail-card games-stats-card">
                                <h6 class="detail-card-title">
                                    <i class="bi bi-controller me-2"></i>Statistiques Jeux
                                </h6>
                                <div class="games-stats-grid">
                                    <div class="game-stat-item">
                                        <div class="game-stat-value" id="commands-total">0</div>
                                        <div class="game-stat-label">Total commandes</div>
                                    </div>
                                    <div class="game-stat-item">
                                        <div class="game-stat-value" id="games-total">0</div>
                                        <div class="game-stat-label">Total parties</div>
                                    </div>
                                    <div class="game-stat-item">
                                        <div class="game-stat-value" id="games-today">0</div>
                                        <div class="game-stat-label">Parties/jour</div>
                                    </div>
                                    <div class="game-stat-item">
                                        <div class="game-stat-value" id="games-active">0</div>
                                        <div class="game-stat-label">En cours</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Commandes populaires -->
                            <div class="discord-detail-card popular-commands-card">
                                <h6 class="detail-card-title">
                                    <i class="bi bi-star-fill me-2"></i>Commandes populaires
                                </h6>
                                <div id="popular-commands" class="popular-commands-list">
                                    <div class="no-commands">
                                        <i class="bi bi-chat-dots"></i>
                                        <div class="no-commands-text">Aucune commande</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error state -->
                    <div id="discord-stats-error" style="display: none;" class="error-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span class="error-text">API Discord indisponible</span>
                        <button class="btn-retry" onclick="refreshDiscordStats()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Réessayer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions rapides - Modern Grid -->
            <div class="modern-card actions-card">
                <div class="modern-card-header">
                    <div class="card-header-content">
                        <div class="card-title-icon">
                            <i class="bi bi-lightning"></i>
                        </div>
                        <h6 class="card-title-text">Actions rapides</h6>
                    </div>
                </div>
                <div class="modern-card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.brands.create') }}" class="quick-action-btn brands-action">
                            <i class="bi bi-plus-circle"></i>
                            <span>Nouvelle marque</span>
                        </a>
                        <a href="{{ route('admin.models.create') }}" class="quick-action-btn models-action">
                            <i class="bi bi-car-front"></i>
                            <span>Nouveau modèle</span>
                        </a>
                        <button class="quick-action-btn refresh-action" onclick="refreshDiscordStats()">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span>Actualiser Discord</span>
                        </button>
                        <a href="{{ route('admin.sessions.index') }}" class="quick-action-btn sessions-action">
                            <i class="bi bi-controller"></i>
                            <span>Voir sessions</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite - Top joueurs + Analyses -->
        <div class="col-xl-4">
            <!-- Top Joueurs -->
            @if(isset($topPlayers) && $topPlayers->count() > 0)
                <div class="modern-card leaderboard-card mb-4">
                    <div class="modern-card-header">
                        <div class="card-header-content">
                            <div class="card-title-icon">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <h6 class="card-title-text">Top Joueurs</h6>
                        </div>
                        <a href="{{ route('admin.players.index') }}" class="btn-view-all">
                            Tous <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="modern-card-body">
                        @foreach($topPlayers->take(5) as $index => $player)
                            <div class="leaderboard-item {{ $index < 3 ? 'top-three' : '' }}">
                                <div class="player-rank rank-{{ $index + 1 }}">
                                    @if($index === 0)
                                        <i class="bi bi-trophy-fill"></i>
                                    @elseif($index === 1)
                                        <i class="bi bi-award-fill"></i>
                                    @elseif($index === 2)
                                        <i class="bi bi-award-fill"></i>
                                    @else
                                        <span>{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="player-info">
                                    <div class="player-name">{{ $player->username }}</div>
                                    <div class="player-guild">{{ getGuildName($player->guild_id) }}</div>
                                    <div class="player-games">{{ $player->games_played ?? 0 }} parties</div>
                                </div>
                                <div class="player-score">
                                    <div class="score-value">{{ number_format($player->total_points, 0) }}</div>
                                    <div class="score-label">pts</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Répartition difficulté -->
            <div class="modern-card difficulty-card">
                <div class="modern-card-header">
                    <div class="card-header-content">
                        <div class="card-title-icon">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h6 class="card-title-text">Répartition par difficulté</h6>
                    </div>
                </div>
                <div class="modern-card-body">
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
                        <div class="difficulty-item">
                            <div class="difficulty-header">
                                <div class="difficulty-label">
                                    <i class="bi bi-{{ $difficulty['icon'] }} difficulty-icon icon-{{ $difficulty['color'] }}"></i>
                                    <span>{{ $difficulty['label'] }}</span>
                                </div>
                                <span class="difficulty-stats">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="difficulty-progress">
                                <div class="difficulty-progress-bar bar-{{ $difficulty['color'] }}" style="width: {{ $percentage }}%">
                                    <div class="progress-shine"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    @if(isset($recentGames) && $recentGames->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="modern-card activity-card">
                    <div class="modern-card-header">
                        <div class="card-header-content">
                            <div class="card-title-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h6 class="card-title-text">Activité récente</h6>
                        </div>
                        <a href="{{ route('admin.sessions.index') }}" class="btn-view-all">
                            Toutes les sessions <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="modern-card-body">
                        <div class="activity-grid">
                            @foreach($recentGames->take(6) as $game)
                                <div class="activity-item">
                                    <div class="activity-avatar">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-player">
                                            {{ $game->userScore->username ?? 'Joueur' }}
                                            @if($game->userScore && $game->userScore->guild_id)
                                                <span class="activity-guild">{{ getGuildName($game->guild_id) }}</span>
                                            @endif
                                        </div>
                                        <div class="activity-time">
                                            @php
                                                $gameDate = $game->started_at;
                                                if ($gameDate) {
                                                    $gameDate = $gameDate->setTimezone(config('app.timezone', 'UTC'));
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
                                    <div class="activity-score score-{{ ($game->points_earned + $game->difficulty_points_earned) > 50 ? 'high' : 'low' }}">
                                        {{ $game->points_earned + $game->difficulty_points_earned }}pts
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Styles CSS intégrés -->
    <style>
        /* Force le fond sombre pour cette page */
        body {
            background: #0a0a0f !important;
        }

        /* Assurer que le contenu principal a le bon fond */
        main {
            background: transparent !important;
        }

        /* Header Dashboard */
        .header-content {
            animation: fadeInDown 0.6s ease-out;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-primary), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .dashboard-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-base);
        }

        .status-loading {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.1));
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-online {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-offline {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .rotating {
            animation: rotate-continuous 1.5s linear infinite;
        }

        .btn-refresh {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius-sm);
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-base);
            cursor: pointer;
        }

        .btn-refresh:hover {
            background: var(--dark-card-hover);
            border-color: var(--primary-color);
            box-shadow: var(--shadow-glow);
            transform: rotate(180deg);
        }

        .time-display {
            color: var(--text-muted);
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            background: var(--dark-card);
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--dark-border);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .stat-card-modern {
            position: relative;
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            overflow: hidden;
            transition: var(--transition-base);
            cursor: pointer;
        }

        .stat-card-bg {
            position: absolute;
            inset: -2px;
            border-radius: var(--border-radius-lg);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .stat-card-brands .stat-card-bg {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .stat-card-models .stat-card-bg {
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
        }

        .stat-card-players .stat-card-bg {
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
        }

        .stat-card-sessions .stat-card-bg {
            background: linear-gradient(135deg, var(--warning-color), var(--secondary-color));
        }

        .stat-card-modern:hover .stat-card-bg {
            opacity: 1;
        }

        .stat-card-modern:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-glow-hover);
        }

        .stat-card-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.2));
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: var(--primary-color);
            flex-shrink: 0;
            transition: var(--transition-base);
        }

        .stat-card-modern:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
        }

        .stat-info {
            flex-grow: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-card-link {
            width: 36px;
            height: 36px;
            background: rgba(139, 92, 246, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition-base);
        }

        .stat-card-link:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .stat-card-shine {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .stat-card-modern:hover .stat-card-shine {
            transform: translateX(100%);
        }

        /* Modern Card */
        .modern-card {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            transition: var(--transition-base);
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        .modern-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-glow);
        }

        .modern-card-header {
            padding: 1.25rem 1.5rem;
            background: rgba(139, 92, 246, 0.05);
            border-bottom: 1px solid var(--dark-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.125rem;
        }

        .card-title-text {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .btn-icon-refresh {
            width: 32px;
            height: 32px;
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition-base);
        }

        .btn-icon-refresh:hover {
            background: var(--primary-color);
            color: white;
            transform: rotate(180deg);
        }

        .modern-card-body {
            padding: 1.5rem;
        }

        /* Discord Card Styles */
        .loading-state {
            text-align: center;
            padding: 3rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(139, 92, 246, 0.2);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .error-state {
            text-align: center;
            padding: 3rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .error-state i {
            font-size: 3rem;
            color: var(--danger-color);
        }

        .error-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .btn-retry {
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: var(--border-radius-sm);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-base);
        }

        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        /* Discord Stats Main */
        .discord-stats-main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .discord-stat-box {
            position: relative;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            text-align: center;
            transition: var(--transition-base);
            overflow: hidden;
        }

        .discord-stat-box::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .discord-stat-box:hover::before {
            opacity: 1;
        }

        .discord-stat-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
        }

        .discord-stat-value {
            position: relative;
            z-index: 1;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .discord-stat-label {
            position: relative;
            z-index: 1;
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .discord-stat-icon {
            position: absolute;
            bottom: 8px;
            right: 8px;
            font-size: 2rem;
            color: rgba(139, 92, 246, 0.1);
            z-index: 0;
        }

        /* Discord Details Grid */
        .discord-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .discord-detail-card {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(236, 72, 153, 0.05));
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            padding: 1rem;
            transition: var(--transition-base);
        }

        .discord-detail-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.2);
        }

        .detail-card-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        /* Bot Info Card */
        .bot-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--dark-border);
        }

        .bot-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .bot-details {
            flex-grow: 1;
        }

        .bot-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .bot-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            color: var(--success-color);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bot-metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .bot-metric {
            text-align: center;
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .metric-value {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .metric-value.uptime {
            color: var(--success-color);
        }

        .metric-value.memory {
            color: var(--info-color);
        }

        .metric-value.platform {
            color: var(--warning-color);
        }

        .metric-value.node {
            color: var(--text-secondary);
        }

        /* Games Stats Card */
        .games-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .game-stat-item {
            background: rgba(17, 17, 23, 0.6);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem;
            text-align: center;
            transition: var(--transition-base);
        }

        .game-stat-item:hover {
            background: rgba(139, 92, 246, 0.1);
            transform: scale(1.05);
        }

        .game-stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .game-stat-label {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* Popular Commands */
        .popular-commands-list {
            min-height: 120px;
        }

        .no-commands {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 2rem 1rem;
            color: var(--text-muted);
        }

        .no-commands i {
            font-size: 2rem;
        }

        .no-commands-text {
            font-size: 0.875rem;
        }

        .command-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            transition: var(--transition-fast);
        }

        .command-item:last-child {
            border-bottom: none;
        }

        .command-item:hover {
            transform: translateX(4px);
        }

        .command-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .command-rank {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .command-name {
            font-size: 0.875rem;
            color: var(--text-primary);
            font-family: monospace;
        }

        .command-count {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Quick Actions */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: var(--dark-card-hover);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-base);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .quick-action-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .quick-action-btn:hover::before {
            opacity: 0.1;
        }

        .quick-action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
        }

        .quick-action-btn i {
            font-size: 1.25rem;
            position: relative;
            z-index: 1;
        }

        .quick-action-btn span {
            position: relative;
            z-index: 1;
        }

        /* Leaderboard */
        .leaderboard-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(236, 72, 153, 0.05));
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
            transition: var(--transition-base);
        }

        .leaderboard-item:last-child {
            margin-bottom: 0;
        }

        .leaderboard-item:hover {
            border-color: var(--primary-color);
            transform: translateX(8px);
            box-shadow: var(--shadow-glow);
        }

        .leaderboard-item.top-three {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
        }

        .player-rank {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .player-rank.rank-1 {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
        }

        .player-rank.rank-2 {
            background: linear-gradient(135deg, #d1d5db, #9ca3af);
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(209, 213, 219, 0.4);
        }

        .player-rank.rank-3 {
            background: linear-gradient(135deg, #cd7f32, #b87333);
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(205, 127, 50, 0.4);
        }

        .player-rank:not(.rank-1):not(.rank-2):not(.rank-3) {
            background: var(--dark-card-hover);
            border: 1px solid var(--dark-border);
            color: var(--text-secondary);
        }

        .player-info {
            flex-grow: 1;
        }

        .player-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .player-guild {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.125rem;
        }

        .player-games {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .player-score {
            text-align: right;
        }

        .score-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }

        .score-label {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .btn-view-all {
            padding: 0.5rem 1rem;
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius-sm);
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: var(--transition-base);
            display: inline-flex;
            align-items: center;
        }

        .btn-view-all:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(4px);
        }

        /* Difficulty Card */
        .difficulty-item {
            margin-bottom: 1.25rem;
        }

        .difficulty-item:last-child {
            margin-bottom: 0;
        }

        .difficulty-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .difficulty-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .difficulty-icon {
            font-size: 1.25rem;
        }

        .difficulty-icon.icon-success {
            color: var(--success-color);
        }

        .difficulty-icon.icon-warning {
            color: var(--warning-color);
        }

        .difficulty-icon.icon-danger {
            color: var(--danger-color);
        }

        .difficulty-stats {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .difficulty-progress {
            height: 10px;
            background: rgba(17, 17, 23, 0.6);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .difficulty-progress-bar {
            height: 100%;
            border-radius: 10px;
            position: relative;
            transition: width 0.6s ease;
        }

        .difficulty-progress-bar.bar-success {
            background: linear-gradient(90deg, var(--success-color), #059669);
        }

        .difficulty-progress-bar.bar-warning {
            background: linear-gradient(90deg, var(--warning-color), #d97706);
        }

        .difficulty-progress-bar.bar-danger {
            background: linear-gradient(90deg, var(--danger-color), #dc2626);
        }

        .progress-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shine-progress 2s ease-in-out infinite;
        }

        @keyframes shine-progress {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Activity Card */
        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(236, 72, 153, 0.05));
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            transition: var(--transition-base);
        }

        .activity-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
        }

        .activity-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .activity-details {
            flex-grow: 1;
            min-width: 0;
        }

        .activity-player {
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.25rem;
        }

        .activity-guild {
            color: var(--text-secondary);
            font-weight: 400;
        }

        .activity-time {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .activity-score {
            padding: 0.5rem 0.75rem;
            border-radius: var(--border-radius-sm);
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .activity-score.score-high {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .activity-score.score-low {
            background: linear-gradient(135deg, rgba(156, 163, 175, 0.2), rgba(156, 163, 175, 0.1));
            color: var(--text-secondary);
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }

            .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 1.5rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .discord-stats-main {
                grid-template-columns: repeat(2, 1fr);
            }

            .discord-details-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions-grid {
                grid-template-columns: 1fr;
            }

            .activity-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .header-content {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .discord-stats-main {
                grid-template-columns: 1fr;
            }

            .player-rank {
                width: 32px;
                height: 32px;
            }

            .player-rank.rank-1,
            .player-rank.rank-2,
            .player-rank.rank-3 {
                font-size: 1.25rem;
            }
        }

        /* Animations supplémentaires */
        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rotate-continuous {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Scripts optimisés -->
    <script>
        let discordStatsCache = null;
        let refreshInterval = null;
        const API_BASE_URL = 'http://localhost:3000/api';
        const REFRESH_INTERVAL = 30000;

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

            updateElement('discord-guilds', formatNumber(bot.guilds || 0));
            updateElement('discord-users', formatNumber(bot.users || 0));
            updateElement('discord-commands-today', formatNumber(bot.commands?.today || 0));
            updateElement('discord-games-active', formatNumber(bot.games?.active || 0));

            updateElement('bot-username', bot.botInfo?.username || bot.botInfo?.name || 'Bot Discord');
            updateElement('bot-uptime', formatUptime(bot.uptime || api.uptime || 0));
            updateElement('bot-memory', formatMemory(api.memory?.heapUsed || bot.performance?.memoryUsage?.heapUsed || 0));
            updateElement('bot-platform', api.platform || bot.performance?.platform || '-');
            updateElement('bot-node-version', api.version || bot.performance?.nodeVersion || '-');

            const statusEl = document.getElementById('bot-status');
            if (statusEl) {
                statusEl.textContent = bot.isOnline ? 'En ligne' : 'Hors ligne';
                statusEl.className = `bot-status ${bot.isOnline ? '' : 'offline'}`;
            }

            const games = bot.games || {};
            updateElement('commands-total', formatNumber(bot.commands?.total || 0));
            updateElement('games-total', formatNumber(games.total || 0));
            updateElement('games-today', formatNumber(games.today || games.todayStarted || 0));
            updateElement('games-active', formatNumber(games.active || 0));

            updatePopularCommands(bot.commands?.popular || []);
        }

        function updatePopularCommands(commands) {
            const container = document.getElementById('popular-commands');
            if (!container) return;

            if (!commands || commands.length === 0) {
                container.innerHTML = `
                    <div class="no-commands">
                        <i class="bi bi-chat-dots"></i>
                        <div class="no-commands-text">Aucune commande</div>
                    </div>
                `;
                return;
            }

            let html = '';
            commands.slice(0, 4).forEach((cmd, index) => {
                html += `
                    <div class="command-item">
                        <div class="command-info">
                            <span class="command-rank">${index + 1}</span>
                            <span class="command-name">/${cmd.name || 'cmd'}</span>
                        </div>
                        <span class="command-count">${formatNumber(cmd.count || 0)}</span>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function showDiscordStatsError() {
            document.getElementById('discord-stats-loading').style.display = 'none';
            document.getElementById('discord-stats-content').style.display = 'none';
            document.getElementById('discord-stats-error').style.display = 'flex';
        }

        function updateDiscordStatusBadge(isOnline) {
            const badge = document.getElementById('discord-api-status');
            if (!badge) return;

            if (isOnline) {
                badge.className = 'status-badge status-online';
                badge.innerHTML = '<i class="bi bi-check-circle"></i> Discord OK';
            } else {
                badge.className = 'status-badge status-offline';
                badge.innerHTML = '<i class="bi bi-x-circle"></i> Discord KO';
            }
        }

        function refreshDiscordStats() {
            document.getElementById('discord-stats-loading').style.display = 'flex';
            document.getElementById('discord-stats-content').style.display = 'none';
            document.getElementById('discord-stats-error').style.display = 'none';
            fetchDiscordStats();
        }

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