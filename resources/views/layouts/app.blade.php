<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Machina - Votre solution de location de véhicules simple et rapide">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="color-scheme" content="light dark">
    
    <!-- Appliquer le thème IMMÉDIATEMENT pour éviter le flash -->
    <script>
        (function() {
            const THEME_KEY = 'theme';
            let theme = 'light';
            try {
                const stored = localStorage.getItem(THEME_KEY);
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                theme = stored || (prefersDark ? 'dark' : 'light');
            } catch (e) {}
            
            const className = theme === 'dark' ? 'theme-dark' : 'theme-light';
            document.documentElement.classList.add(className);
            document.documentElement.style.background = theme === 'dark' ? '#0e0b1a' : '#f8fafc';
        })();
    </script>
    
    <title>{{ config('app.name', 'Machina') }} - Location de Véhicules</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/style.css', 'resources/js/script.js'])
</head>
@php
    $isAuthPage = request()->routeIs(
        'login',
        'register',
        'password.*',
        'verification.*',
        'email.*'
    );
@endphp
<body class="{{ $isAuthPage ? 'auth-page' : '' }}">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-container">
                <a class="navbar-brand" href="{{ url('/') }}" aria-label="Accueil" data-page-index="0">
                    <img src="{{ asset('images/logo-dark.png') }}" alt="Logo" class="logo-img" width="128" height="32">
                </a>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button type="button" class="nav-link theme-toggle" id="theme-toggle" aria-label="Basculer le thème" onclick="window.toggleTheme && window.toggleTheme();">
                            <span class="theme-icon" aria-hidden="true">🌙</span>
                        </button>
                    </li>
                    <!-- Language Switcher -->
                    <li class="nav-item lang-switcher">
                        <button type="button" class="nav-link lang-toggle" id="lang-toggle-btn">
                            @if(app()->getLocale() == 'fr')
                                🇫🇷 FR
                            @else
                                🇬🇧 EN
                            @endif
                            <span class="arrow-down">▼</span>
                        </button>
                        <ul class="lang-dropdown" id="lang-dropdown-menu">
                            <li><a class="nav-link" href="{{ route('lang.switch', 'fr') }}">🇫🇷 Français</a></li>
                            <li><a class="nav-link" href="{{ route('lang.switch', 'en') }}">🇬🇧 English</a></li>
                        </ul>
                    </li>

                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('S\'inscrire') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <button type="button" class="nav-link logout-nav" aria-label="Se déconnecter" title="Se déconnecter">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                                </svg>
                            </button>

                            <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>