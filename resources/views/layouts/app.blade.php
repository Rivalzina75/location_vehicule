<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Machina</title>
    @vite(['resources/css/style.css', 'resources/js/script.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar">
            <div class="navbar-container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Machina
                </a>
                <ul class="navbar-nav">
                    
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
                                {{-- MODIFIÉ : On utilise une clé --}}
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                {{-- MODIFIÉ : On utilise une clé --}}
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
                                {{-- MODIFIÉ : On utilise une clé --}}
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
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>