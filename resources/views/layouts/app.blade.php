<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Machina - Votre solution de location de véhicules simple et rapide">
    <meta name="theme-color" content="#1a1a2e">
    
    <title>{{ config('app.name', 'Machina') }} - Location de Véhicules</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/style.css', 'resources/js/script.js'])
</head>
<body class="@if(Request::is('login') || Request::is('register') || Request::is('password/*') || Request::is('email/verify')) auth-page @endif">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Machina
                </a>
                
                <ul class="navbar-nav">
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
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                {{ Auth::user()->first_name }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Déconnexion') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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