{{-- resources/views/components/admin-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}GuessTheCar - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --bs-primary: #3b82f6;
            --bs-primary-rgb: 59, 130, 246;
            --bs-success: #10b981;
            --bs-warning: #f59e0b;
            --bs-danger: #ef4444;
            --bs-info: #06b6d4;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand:hover {
            color: white;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.125rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .content-wrapper {
            padding: 2rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats cards */
        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border-left: 4px solid var(--bs-primary);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8fafc;
            color: #374151;
            padding: 1rem 0.75rem;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #e5e7eb;
        }

        .table-hover tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
        }

        /* Badges */
        .badge {
            border-radius: 0.375rem;
            font-weight: 500;
        }

        /* Form controls */
        .form-control,
        .form-select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            transition: border-color 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Status indicators */
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-online {
            background-color: var(--bs-success);
        }

        .status-offline {
            background-color: var(--bs-danger);
        }

        .status-pending {
            background-color: var(--bs-warning);
        }

        /* Loading animation */
        .loading-spinner {
            border: 2px solid #f3f4f6;
            border-top: 2px solid var(--bs-primary);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.5rem;
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: var(--bs-success);
        }

        .alert-danger {
            border-left-color: var(--bs-danger);
        }

        .alert-warning {
            border-left-color: var(--bs-warning);
        }

        .alert-info {
            border-left-color: var(--bs-info);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="bg-white text-primary rounded d-flex align-items-center justify-content-center"
                style="width: 32px; height: 32px; font-size: 1.2rem;">
                🚗
            </div>
            GuessTheCar Admin
        </a>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                        href="{{ route('admin.brands.index') }}">
                        <i class="bi bi-building"></i>
                        <span>Marques</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.models.*') ? 'active' : '' }}"
                        href="{{ route('admin.models.index') }}">
                        <i class="bi bi-car-front"></i>
                        <span>Modèles</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.players.*') ? 'active' : '' }}"
                        href="{{ route('admin.players.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Joueurs</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}"
                        href="{{ route('admin.sessions.index') }}">
                        <i class="bi bi-controller"></i>
                        <span>Sessions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.leaderboard.*') ? 'active' : '' }}"
                        href="{{ route('admin.leaderboard.index') }}">
                        <i class="bi bi-trophy"></i>
                        <span>Classement</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cars-found.*') ? 'active' : '' }}"
                        href="{{ route('admin.cars-found.index') }}">
                        <i class="bi bi-collection"></i>
                        <span>Collections</span>
                    </a>
                </li>

                <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 1rem 1.5rem;">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}"
                        href="{{ route('admin.statistics.index') }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Statistiques</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tools.*') ? 'active' : '' }}"
                        href="{{ route('admin.tools.database-status') }}">
                        <i class="bi bi-tools"></i>
                        <span>Outils</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cars-found.*') ? 'active' : '' }}"
                        href="{{ route('admin.cars-found.index') }}">
                        <i class="bi bi-collection"></i>
                        <span>Collections</span>
                        @if(isset($stats) && $stats['today'] > 0)
                            <span class="badge bg-success ms-auto">{{ $stats['today'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary d-md-none me-3" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    @isset($header)
                        {{ $header }}
                    @else
                        <h1 class="h4 mb-0">Administration</h1>
                    @endisset
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- API Status -->
                    <div id="api-status" class="d-flex align-items-center">
                        <div class="loading-spinner me-2"></div>
                        <small class="text-muted">Vérification API...</small>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center"
                            type="button" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profil
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // API Status Check
        document.addEventListener('DOMContentLoaded', function () {
            checkApiStatus();
            setInterval(checkApiStatus, 30000); // Check every 30 seconds
        });

        async function checkApiStatus() {
            const statusElement = document.getElementById('api-status');

            try {
                const response = await fetch('{{ route("admin.api.status") }}');
                const data = await response.json();

                const isHealthy = data.status === 'healthy';
                const statusClass = isHealthy ? 'status-online' : 'status-offline';
                const statusText = isHealthy ? 'API OK' : 'API KO';

                statusElement.innerHTML = `
                    <div class="status-indicator ${statusClass}"></div>
                    <small class="text-muted">${statusText}</small>
                `;
            } catch (error) {
                statusElement.innerHTML = `
                    <div class="status-indicator status-offline"></div>
                    <small class="text-muted">API KO</small>
                `;
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isToggleButton = event.target.closest('[onclick="toggleSidebar()"]');

            if (!isClickInsideSidebar && !isToggleButton && window.innerWidth <= 768) {
                sidebar.classList.remove('show');
            }
        });

        // Utility functions for tables
        function sortTable(column, direction = 'asc') {
            const url = new URL(window.location);
            url.searchParams.set('sort', column);
            url.searchParams.set('direction', direction);
            window.location = url;
        }

        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }
    </script>

    @stack('scripts')
</body>

</html>