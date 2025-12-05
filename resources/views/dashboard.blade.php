@extends('layouts.app')

@section('content')
<div class="dashboard-wrapper-pro" style="padding-top: 70px;">
    <!-- Sidebar Premium -->
    <aside class="sidebar-pro">
        <div class="sidebar-brand">
            <div class="logo-container">
                <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1M12 15l-3 3m0 0l-3-3m3 3V9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <circle cx="8.5" cy="19" r="1.5"/>
                    <circle cx="15.5" cy="19" r="1.5"/>
                </svg>
                <div class="logo-text">
                    <span class="brand-name">Machina</span>
                    <span class="brand-tagline">Location Premium</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}" data-page-index="0">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <path d="M9 22V12h6v10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <span>{{ __('Accueil') }}</span>
            </a>
            
            <a href="{{ route('dashboard.catalogue') }}" class="menu-item {{ Request::routeIs('dashboard.catalogue*') ? 'active' : '' }}" data-page-index="1">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <path d="M3 9h18M9 21V9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <span>{{ __('Catalogue') }}</span>
            </a>
            
            <a href="{{ route('dashboard.reservations') }}" class="menu-item {{ Request::routeIs('dashboard.reservation*') ? 'active' : '' }}" data-page-index="2">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <span>{{ __('Mes Réservations') }}</span>
                <span class="badge">3</span>
            </a>
            
            <a href="{{ route('dashboard.documents') }}" class="menu-item {{ Request::routeIs('dashboard.documents') ? 'active' : '' }}" data-page-index="3">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <span>{{ __('Documents') }}</span>
            </a>
            
            <a href="{{ route('dashboard.inspection') }}" class="menu-item {{ Request::routeIs('dashboard.inspection*') ? 'active' : '' }}" data-page-index="4">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <circle cx="12" cy="13" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <span>{{ __('Inspection') }}</span>
            </a>
            
            <a href="#" class="menu-item" data-page-index="5">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <span>{{ __('Profil') }}</span>
            </a>
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
    <main class="main-content-pro page-transition" data-page-index="0">
        
        <div class="page-header">
            <div>
                <h1 class="page-title">{{ __('Bienvenue') }}, {{ Auth::user()->first_name }} 👋</h1>
                <p class="page-subtitle">{{ __('Gérez vos locations de véhicules en toute simplicité') }}</p>
            </div>
            <a href="{{ route('dashboard.catalogue') }}" class="btn-primary" style="text-decoration: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <path d="M12 8v8M8 12h8" stroke-linecap="round" stroke-width="2"/>
                </svg>
                {{ __('Nouvelle réservation') }}
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid-pro">
            <div class="stat-card-pro stat-primary">
                <div class="stat-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1" stroke-width="2"/>
                        <circle cx="8.5" cy="19" r="1.5"/>
                        <circle cx="15.5" cy="19" r="1.5"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">15</div>
                    <div class="stat-label">{{ __('Véhicules disponibles') }}</div>
                    <div class="stat-trend positive">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M18 15l-6-6-6 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        +12% {{ __('ce mois') }}
                    </div>
                </div>
            </div>

            <div class="stat-card-pro stat-success">
                <div class="stat-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">3</div>
                    <div class="stat-label">{{ __('Réservations actives') }}</div>
                    <div class="stat-trend neutral">{{ __('En cours') }}</div>
                </div>
            </div>

            <div class="stat-card-pro stat-warning">
                <div class="stat-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <path d="M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">12</div>
                    <div class="stat-label">{{ __('Locations terminées') }}</div>
                    <div class="stat-trend positive">{{ __('100% complétées') }}</div>
                </div>
            </div>

            <div class="stat-card-pro stat-info">
                <div class="stat-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">4.9/5</div>
                    <div class="stat-label">{{ __('Note moyenne') }}</div>
                    <div class="stat-trend positive">⭐⭐⭐⭐⭐</div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column-layout">
            <!-- Left Column -->
            <div class="column-left">
                <!-- Quick Actions -->
                <div class="card-pro">
                    <div class="card-header">
                        <h3>{{ __('Actions rapides') }}</h3>
                    </div>
                    <div class="quick-actions-grid">
                        <a href="{{ route('dashboard.catalogue') }}" class="action-card-pro" style="text-decoration: none;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1" stroke-width="2"/>
                                    <circle cx="8.5" cy="19" r="1.5"/>
                                    <circle cx="15.5" cy="19" r="1.5"/>
                                </svg>
                            </div>
                            <div class="action-label">{{ __('Voir catalogue') }}</div>
                        </a>

                        <a href="{{ route('dashboard.reservations') }}" class="action-card-pro" style="text-decoration: none;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="action-label">{{ __('Mes réservations') }}</div>
                        </a>

                        <a href="{{ route('dashboard.documents') }}" class="action-card-pro" style="text-decoration: none;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-width="2"/>
                                    <path d="M14 2v6h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="action-label">{{ __('Documents') }}</div>
                        </a>

                        <a href="{{ route('dashboard.inspection') }}" class="action-card-pro" style="text-decoration: none;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke-width="2"/>
                                    <circle cx="12" cy="13" r="4" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="action-label">{{ __('Inspection') }}</div>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card-pro">
                    <div class="card-header">
                        <h3>{{ __('Activité récente') }}</h3>
                        <a href="{{ route('dashboard.reservations') }}" class="link-text">{{ __('Tout voir') }} →</a>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                    <path d="M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ __('Réservation confirmée') }}</div>
                                <div class="activity-desc">Peugeot 508 • {{ __('Du 10 au 17 Nov') }}</div>
                                <div class="activity-time">{{ __('Il y a 2 heures') }}</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-width="2"/>
                                    <path d="M14 2v6h6" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ __('Document vérifié') }}</div>
                                <div class="activity-desc">{{ __('Permis de conduire approuvé') }}</div>
                                <div class="activity-time">{{ __('Hier à 14:30') }}</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon warning">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                    <path d="M12 8v4M12 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ __('Rappel : Inspection') }}</div>
                                <div class="activity-desc">{{ __('Inspection retour dans 3 jours') }}</div>
                                <div class="activity-time">{{ __('Il y a 1 jour') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="column-right">
                <!-- Upcoming Reservations -->
                <div class="card-pro">
                    <div class="card-header">
                        <h3>{{ __('Prochaines réservations') }}</h3>
                    </div>
                    <div class="reservation-preview-list">
                        <div class="reservation-preview">
                            <div class="preview-date">
                                <div class="date-day">10</div>
                                <div class="date-month">NOV</div>
                            </div>
                            <div class="preview-content">
                                <div class="preview-vehicle">Peugeot 508</div>
                                <div class="preview-details">7 {{ __('jours') }} • 315€</div>
                                <div class="preview-status active">{{ __('En cours') }}</div>
                            </div>
                        </div>

                        <div class="reservation-preview">
                            <div class="preview-date">
                                <div class="date-day">20</div>
                                <div class="date-month">NOV</div>
                            </div>
                            <div class="preview-content">
                                <div class="preview-vehicle">Yamaha MT-07</div>
                                <div class="preview-details">2 {{ __('jours') }} • 100€</div>
                                <div class="preview-status upcoming">{{ __('À venir') }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('dashboard.reservations') }}" class="btn-secondary" style="text-decoration: none; display: flex; justify-content: center; align-items: center;">
                        {{ __('Voir toutes les réservations') }}
                    </a>
                </div>

                <!-- Tips Card -->
                <div class="card-pro card-gradient">
                    <div class="tip-icon">💡</div>
                    <h4>{{ __('Astuce du jour') }}</h4>
                    <p>{{ __('Pensez à vérifier le niveau de carburant avant chaque départ pour éviter des frais supplémentaires !') }}</p>
                </div>

                <!-- Support Card -->
                <div class="card-pro">
                    <div class="card-header">
                        <h3>{{ __('Besoin d\'aide ?') }}</h3>
                    </div>
                    <div class="support-buttons">
                        <button class="support-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            {{ __('Chat en ligne') }}
                        </button>
                        <button class="support-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            {{ __('Appeler') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" id="mobileMenuToggle">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path d="M3 12h18M3 6h18M3 18h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
    </svg>
</button>
@endsection