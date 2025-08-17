<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Car Guesser') }} - Connexion</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .app-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .app-logo::before {
            content: '🚗';
        }

        .login-title {
            color: #333;
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 3rem;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: #333;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1.125rem 1.5rem;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.125rem;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            font-size: 0.875rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #764ba2;
        }

        .login-button {
            width: 100%;
            padding: 1.125rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button:disabled {
            opacity: 0.7;
            pointer-events: none;
        }

        .login-button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
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

        .error-enhanced {
            background: #fee;
            border: 1px solid #fcc;
            color: #c66;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }

        .success-enhanced {
            background: #efe;
            border: 1px solid #cfc;
            color: #6c6;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }

        /* Animation des champs */
        .form-group.focused {
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .login-container {
                padding: 1rem;
            }

            .login-card {
                padding: 2.5rem 1.5rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .app-logo {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.25rem;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo et titre -->
            <div class="logo-section">
                <div class="app-logo"></div>
                <h1 class="login-title">GuessTheCar - Admin Pannel</h1>
                <p class="login-subtitle">Connectez-vous à votre tableau de bord administrateur</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="success-enhanced">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Erreurs de validation -->
            @if ($errors->any())
                <div class="error-enhanced">
                    @if ($errors->has('email'))
                        {{ $errors->first('email') }}
                    @elseif ($errors->has('password'))
                        {{ $errors->first('password') }}
                    @else
                        {{ $errors->first() }}
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <div class="input-wrapper">
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}"
                            required autofocus autocomplete="username" placeholder="votre.email@exemple.com">
                        <span class="input-icon">📧</span>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <input id="password" class="form-input" type="password" name="password" required
                            autocomplete="current-password" placeholder="Votre mot de passe">
                        <span class="input-icon">🔒</span>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="remember-forgot">
                    <label for="remember_me" class="remember-me">
                        <input id="remember_me" type="checkbox" class="remember-checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-button" id="submitBtn">
                    {{ __('Log in') }}
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const inputs = document.querySelectorAll('.form-input');

            // Animation des champs au focus
            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.closest('.form-group').classList.add('focused');
                });

                input.addEventListener('blur', function () {
                    this.closest('.form-group').classList.remove('focused');
                });
            });

            // Gestion de la soumission du formulaire
            form.addEventListener('submit', function (e) {
                submitBtn.classList.add('loading');
                submitBtn.textContent = 'Connexion...';
                submitBtn.disabled = true;
            });

            // Réactiver le bouton si erreur côté client
            setTimeout(() => {
                if (submitBtn.classList.contains('loading')) {
                    submitBtn.classList.remove('loading');
                    submitBtn.textContent = 'Se connecter';
                    submitBtn.disabled = false;
                }
            }, 5000);
        });
    </script>
</body>

</html>