<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Car Guesser') }} - Administration</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --bs-primary: #667eea;
            --bs-primary-rgb: 102, 126, 234;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link.active {
            background-color: var(--bs-primary) !important;
            color: white !important;
            border-radius: 0.375rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover {
            background-color: #5a67d8;
            border-color: #5a67d8;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.75em;
        }

        .status-online {
            color: #10b981;
        }

        .status-offline {
            color: #ef4444;
        }

        .status-pending {
            color: #f59e0b;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <div class="bg-white text-primary rounded me-2 d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; font-size: 1.5rem;">
                    🚗
                </div>
                Car Guesser Admin
            </a>

            <!-- Toggle button for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                            href="{{ route('admin.brands.index') }}">
                            <i class="bi bi-building me-1"></i> Marques
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.models.*') ? 'active' : '' }}"
                            href="{{ route('admin.models.index') }}">
                            <i class="bi bi-car-front me-1"></i> Modèles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.players.*') ? 'active' : '' }}"
                            href="{{ route('admin.players.index') }}">
                            <i class="bi bi-people me-1"></i> Joueurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}"
                            href="{{ route('admin.sessions.index') }}">
                            <i class="bi bi-controller me-1"></i> Sessions
                        </a>
                    </li>
                    @if(Route::has('admin.users.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">
                                <i class="bi bi-person-gear me-1"></i> Utilisateurs
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- User dropdown and API status -->
                <ul class="navbar-nav">
                    <!-- API Status -->
                    <li class="nav-item me-3">
                        <span id="api-status" class="badge bg-warning">
                            <i class="bi bi-arrow-clockwise"></i> Vérification...
                        </span>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                style="width: 32px; height: 32px; font-size: 0.875rem;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i> Profil
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    @isset($header)
        <div class="bg-white border-bottom">
            <div class="container-fluid py-3">
                {{ $header }}
            </div>
        </div>
    @endisset

    <!-- Main Content -->
    <main class="container-fluid py-4">
        {{ $slot }}
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        // API Status Check
        document.addEventListener('DOMContentLoaded', function () {
            const statusElement = document.getElementById('api-status');

            fetch('/admin/api-status')
                .then(response => response.json())
                .then(data => {
                    const isHealthy = data.status === 'healthy';
                    statusElement.className = `badge ${isHealthy ? 'bg-success' : 'bg-danger'}`;
                    statusElement.innerHTML = `<i class="bi bi-${isHealthy ? 'check-circle' : 'x-circle'}"></i> API ${isHealthy ? 'OK' : 'KO'}`;
                })
                .catch(() => {
                    statusElement.className = 'badge bg-danger';
                    statusElement.innerHTML = '<i class="bi bi-x-circle"></i> API KO';
                });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>