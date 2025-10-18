<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Car Guesser') }} - Connexion</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: #0a0a0f;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Background animé avec particules */
        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at top, #1a1a2e 0%, #0a0a0f 100%);
            z-index: 0;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .particle-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);
            top: -150px;
            right: -150px;
            animation: float 20s ease-in-out infinite;
        }

        .particle-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.1) 0%, transparent 70%);
            bottom: -200px;
            left: -200px;
            animation: float 25s ease-in-out infinite reverse;
        }

        .particle-3 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(50px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-30px, 30px) scale(0.9);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.5;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0.2;
            }
        }

        /* Grid animé */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(139, 92, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            z-index: 1;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(17, 17, 27, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 24px;
            padding: 3.5rem;
            box-shadow: 
                0 0 60px rgba(139, 92, 246, 0.15),
                0 20px 40px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 480px;
            animation: cardEntry 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes cardEntry {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Bordure animée */
        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                #8b5cf6, 
                #ec4899, 
                #3b82f6, 
                #8b5cf6);
            border-radius: 24px;
            z-index: -1;
            opacity: 0;
            animation: borderGlow 3s ease-in-out infinite;
            background-size: 300% 300%;
            animation: borderGlow 3s ease-in-out infinite, gradientShift 6s ease infinite;
        }

        @keyframes borderGlow {
            0%, 100% {
                opacity: 0.3;
            }
            50% {
                opacity: 0.6;
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Effet de brillance qui traverse */
        .login-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(139, 92, 246, 0.1),
                transparent
            );
            animation: shine 8s ease-in-out infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
            50% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
            100% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInDown 0.8s ease-out 0.2s both;
        }

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

        .app-logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border-radius: 22px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: white;
            box-shadow: 
                0 20px 40px rgba(139, 92, 246, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            animation: logoFloat 3s ease-in-out infinite;
            position: relative;
        }

        .app-logo::before {
            content: '🚗';
            animation: logoBounce 2s ease-in-out infinite;
        }

        .app-logo::after {
            content: '';
            position: absolute;
            inset: -3px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border-radius: 22px;
            z-index: -1;
            filter: blur(20px);
            opacity: 0.5;
            animation: logoGlow 2s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes logoBounce {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @keyframes logoGlow {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 0.8;
            }
        }

        .login-title {
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #fff, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.95rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1.75rem;
            position: relative;
            animation: fadeInUp 0.8s ease-out both;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.4s;
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

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1.125rem 3.5rem 1.125rem 1.25rem;
            border: 1.5px solid rgba(139, 92, 246, 0.2);
            border-radius: 14px;
            font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(17, 17, 27, 0.6);
            color: #fff;
            box-sizing: border-box;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-input:focus {
            outline: none;
            border-color: #8b5cf6;
            background: rgba(17, 17, 27, 0.9);
            box-shadow: 
                0 0 0 3px rgba(139, 92, 246, 0.15),
                0 10px 30px rgba(139, 92, 246, 0.2);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.25rem;
            transition: all 0.3s ease;
            filter: grayscale(100%);
        }

        .form-input:focus ~ .input-icon {
            filter: grayscale(0%);
            transform: translateY(-50%) scale(1.1);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.875rem;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remember-me:hover {
            color: #8b5cf6;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #8b5cf6;
            cursor: pointer;
        }

        .login-button {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(139, 92, 246, 0.5);
        }

        .login-button:active {
            transform: translateY(-1px);
        }

        .login-button.loading {
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
            animation: spin 0.8s linear infinite;
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
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            animation: shake 0.5s ease, fadeInUp 0.8s ease-out 0.2s both;
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-10px);
            }
            75% {
                transform: translateX(10px);
            }
        }

        .success-enhanced {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        /* Stars dans le fond */
        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s ease-in-out infinite;
        }

        @keyframes twinkle {
            0%, 100% {
                opacity: 0.2;
            }
            50% {
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .login-container {
                padding: 1rem;
            }

            .login-card {
                padding: 2.5rem 1.75rem;
            }

            .login-title {
                font-size: 1.625rem;
            }

            .app-logo {
                width: 75px;
                height: 75px;
                font-size: 36px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        /* Effet de particules au survol des inputs */
        @keyframes inputParticle {
            0% {
                transform: translateY(0) scale(0);
                opacity: 1;
            }
            100% {
                transform: translateY(-30px) scale(1);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Background animé -->
    <div class="animated-background">
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
    </div>
    <div class="grid-overlay"></div>

    <div class="login-container">
        <div class="login-card">
            <!-- Logo et titre -->
            <div class="logo-section">
                <div class="app-logo"></div>
                <h1 class="login-title">GuessTheCar - Admin Panel</h1>
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

                <!-- Remember Me -->
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
            const background = document.querySelector('.animated-background');

            // Créer des étoiles aléatoires
            for (let i = 0; i < 50; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = Math.random() * 3 + 's';
                background.appendChild(star);
            }

            // Animation des champs au focus
            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.closest('.form-group').style.transform = 'translateX(5px)';
                });

                input.addEventListener('blur', function () {
                    this.closest('.form-group').style.transform = 'translateX(0)';
                });

                // Animation de typing
                input.addEventListener('input', function () {
                    this.style.transform = 'scale(1.01)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });

            // Gestion de la soumission du formulaire
            form.addEventListener('submit', function (e) {
                submitBtn.classList.add('loading');
                submitBtn.textContent = '';
                submitBtn.disabled = true;
            });

            // Effet de parallaxe avec la souris
            document.addEventListener('mousemove', function (e) {
                const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
                const moveY = (e.clientY - window.innerHeight / 2) * 0.01;
                
                document.querySelector('.particle-1').style.transform = 
                    `translate(${moveX}px, ${moveY}px)`;
                document.querySelector('.particle-2').style.transform = 
                    `translate(${-moveX}px, ${-moveY}px)`;
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