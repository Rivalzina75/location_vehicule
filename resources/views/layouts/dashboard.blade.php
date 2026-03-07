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
    
    <title>{{ config('app.name', 'Machina') }} - Dashboard</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/style.css', 'resources/js/script.js'])
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-container">
                <a class="navbar-brand" href="{{ url('/') }}" aria-label="Accueil" data-page-index="0">
                    <img src="{{ asset('images/logo-dark.png') }}" alt="Logo" class="logo-img" width="128" height="32">
                </a>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link nav-home-btn" href="{{ route('dashboard') }}" title="{{ __('Accueil') }}" data-page-index="1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <span>{{ __('Accueil') }}</span>
                            </a>
                        </li>
                    @endauth
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
                </ul>
            </div>
        </nav>
        
        <!-- Dashboard Wrapper -->
        <div class="dashboard-wrapper-pro">
            <!-- Sidebar Premium -->
            <aside class="sidebar-pro">
                <div class="sidebar-brand">
                    <div class="logo-container">
                        <div class="logo-text">
                            <span class="brand-name">Machina</span>
                            <span class="brand-tagline">Location Premium</span>
                        </div>
                    </div>
                </div>

                <nav class="sidebar-menu">
                    <a href="{{ route('dashboard') }}" class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}" data-page-index="1">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <path d="M9 22V12h6v10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>{{ __('Accueil') }}</span>
                    </a>
                    
                    <a href="{{ route('dashboard.catalogue') }}" class="menu-item {{ Request::routeIs('dashboard.catalogue*') ? 'active' : '' }}" data-page-index="2">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <path d="M3 9h18M9 21V9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>{{ __('Catalogue') }}</span>
                    </a>
                    
                    <a href="{{ route('dashboard.reservations') }}" class="menu-item {{ Request::routeIs('dashboard.reservation*') ? 'active' : '' }}" data-page-index="3">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>{{ __('Mes Réservations') }}</span>
                    </a>
                    
                    <a href="{{ route('dashboard.documents') }}" class="menu-item {{ Request::routeIs('dashboard.documents') ? 'active' : '' }}" data-page-index="4">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>{{ __('Documents') }}</span>
                    </a>
                    
                    <a href="{{ route('dashboard.inspection') }}" class="menu-item {{ Request::routeIs('dashboard.inspection*') ? 'active' : '' }}" data-page-index="5">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <circle cx="12" cy="13" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>{{ __('Inspection') }}</span>
                    </a>
                    
                    <a href="{{ route('dashboard.profile.show') }}" class="menu-item {{ Request::routeIs('dashboard.profile.*') ? 'active' : '' }}" data-page-index="6">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>{{ __('Profil') }}</span>
                    </a>

                    <a href="{{ route('dashboard.payment-methods') }}" class="menu-item {{ Request::routeIs('dashboard.payment-methods*') ? 'active' : '' }}" data-page-index="7">
                        <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1 10h22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ __('Paiement') }}</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                        <div class="menu-divider"></div>
                        <div class="menu-section-title">{{ __('Administration') }}</div>
                        
                        <a href="{{ route('admin.dashboard') }}" class="menu-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" data-page-index="8">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="7" height="7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <rect x="14" y="3" width="7" height="7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <rect x="14" y="14" width="7" height="7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <rect x="3" y="14" width="7" height="7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            <span>{{ __('Admin Dashboard') }}</span>
                        </a>
                        
                        <a href="{{ route('admin.vehicles.index') }}" class="menu-item {{ Request::routeIs('admin.vehicles.*') ? 'active' : '' }}" data-page-index="9">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <circle cx="8.5" cy="19" r="1.5"/>
                                <circle cx="15.5" cy="19" r="1.5"/>
                            </svg>
                            <span>{{ __('Gestion Véhicules') }}</span>
                        </a>
                        
                        <a href="{{ route('admin.reservations.index') }}" class="menu-item {{ Request::routeIs('admin.reservations.*') ? 'active' : '' }}" data-page-index="10">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            <span>{{ __('Gestion Réservations') }}</span>
                        </a>
                    @endif
                </nav>

                <div class="sidebar-footer">
                    <div class="user-card">
                        <div class="user-avatar">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                            <div class="user-email">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="main-content-pro">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
