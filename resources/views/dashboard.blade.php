@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="1">
        
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

        <!-- Stats Cards - DYNAMIC -->
        <div class="stats-grid-pro">
            <div class="stat-card-pro stat-primary">
                <div class="stat-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
    <!-- Carrosserie principale -->
    <path d="M3 11l2-4h14l2 4"/>
    <rect x="1" y="11" width="22" height="6" rx="1"/>
    <!-- Toit / vitres -->
    <path d="M5 11l2.5-4h9L19 11"/>
    <path d="M7.5 7.5l.5-0.5h8l.5.5" fill="currentColor" fill-opacity="0.15"/>
    <!-- Roues -->
    <circle cx="6.5" cy="17" r="2"/>
    <circle cx="17.5" cy="17" r="2"/>
    <!-- Jantes -->
    <circle cx="6.5" cy="17" r="0.75" fill="currentColor"/>
    <circle cx="17.5" cy="17" r="0.75" fill="currentColor"/>
    <!-- Phares -->
    <line x1="1" y1="13" x2="1" y2="15"/>
    <line x1="23" y1="13" x2="23" y2="15"/>
</svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $availableVehicles }}</div>
                    <div class="stat-label">{{ __('Véhicules disponibles') }}</div>
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
                    <div class="stat-value">{{ $activeReservations }}</div>
                    <div class="stat-label">{{ __('Réservations actives') }}</div>
                    <div class="stat-trend neutral">{{ $activeReservations > 0 ? __('En cours') : __('Aucune') }}</div>
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
                    <div class="stat-value">{{ $completedReservations }}</div>
                    <div class="stat-label">{{ __('Locations terminées') }}</div>
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
                    <div class="stat-value">{{ $avgRating ? $avgRating . '/5' : '-' }}</div>
                    <div class="stat-label">{{ __('Note moyenne') }}</div>
                    @if($avgRating)
                        <div class="stat-trend positive">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= round($avgRating) ? '⭐' : '☆' }}
                            @endfor
                        </div>
                    @endif
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
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 11l2-4h14l2 4"/><rect x="1" y="11" width="22" height="6" rx="1"/>
                                    <path d="M5 11l2.5-4h9L19 11"/><circle cx="6.5" cy="17" r="2"/><circle cx="17.5" cy="17" r="2"/>
                                    <circle cx="6.5" cy="17" r="0.75" fill="currentColor"/><circle cx="17.5" cy="17" r="0.75" fill="currentColor"/>
                                    <line x1="1" y1="13" x2="1" y2="15"/><line x1="23" y1="13" x2="23" y2="15"/>
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

                <!-- Recent Activity - DYNAMIC -->
                <div class="card-pro">
                    <div class="card-header">
                        <h3>{{ __('Activité récente') }}</h3>
                        <a href="{{ route('dashboard.activity') }}" class="link-text">{{ __('Tout voir') }} →</a>
                    </div>
                    <div class="activity-list" style="min-height: 300px;">
                        @if($recentActivities->count() > 0)
                            @foreach($recentActivities as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon {{ in_array($activity->type, ['reservation_confirmed', 'reservation_completed']) ? 'success' : (in_array($activity->type, ['document_uploaded', 'inspection_start', 'inspection_end']) ? 'info' : 'warning') }}">
                                        @if(str_contains($activity->type, 'reservation'))
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                <path d="M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                            </svg>
                                        @elseif(str_contains($activity->type, 'document'))
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-width="2"/>
                                                <path d="M14 2v6h6" stroke-width="2"/>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                                <path d="M12 8v4M12 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">{{ $activity->title }}</div>
                                        @if($activity->description)
                                            <div class="activity-desc">{{ $activity->description }}</div>
                                        @endif
                                        <div class="activity-time">
                                            @if($activity->created_at->isToday())
                                                {{ __('Aujourd\'hui') }}, {{ $activity->created_at->format('H:i') }}
                                            @elseif($activity->created_at->isYesterday())
                                                {{ __('Hier à') }} {{ $activity->created_at->format('H:i') }}
                                            @else
                                                {{ $activity->created_at->format('d/m/Y') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state-small">
                                <p>{{ __('Aucune activité récente') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="column-right">
                <!-- Upcoming Reservations - DYNAMIC -->
                <div class="card-pro" style="min-height: 320px;">
                    <div class="card-header">
                        <h3>{{ __('Prochaines réservations') }}</h3>
                    </div>
                    <div class="reservation-preview-list" style="min-height: 220px;">
                        @if($upcomingReservations->count() > 0)
                            @foreach($upcomingReservations as $reservation)
                                <div class="reservation-preview">
                                    <div class="preview-date">
                                        <div class="date-day">{{ $reservation->start_date->format('d') }}</div>
                                        <div class="date-month">{{ strtoupper($reservation->start_date->locale('fr')->shortMonthName) }}</div>
                                    </div>
                                    <div class="preview-content">
                                        <div class="preview-vehicle">{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</div>
                                        <div class="preview-details">{{ $reservation->duration_days }} {{ __('jours') }} • {{ number_format($reservation->total_price, 0, ',', ' ') }}€</div>
                                        <div class="preview-status {{ $reservation->status === 'active' ? 'active' : 'upcoming' }}">{{ $reservation->status_label }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state-small">
                                <p>{{ __('Pas de réservation en cours') }}</p>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('dashboard.reservations') }}" class="btn-secondary" style="text-decoration: none; display: flex; justify-content: center; align-items: center;">
                        {{ __('Voir toutes les réservations') }}
                    </a>
                </div>

                <!-- Tips Card -->
                <div class="card-pro card-gradient">
                    <div class="tip-icon">💡</div>
                    <h4>{{ __('Astuce du jour') }}</h4>
                    <p>{{ $tipOfTheDay }}</p>
                </div>

                <!-- Support Card -->
                <div class="card-pro">
                    <div class="card-header">
                        <h3>{{ __('Besoin d\'aide ?') }}</h3>
                    </div>
                    <div class="support-buttons">
                        <a href="mailto:support@machina.fr" class="support-btn" style="text-decoration: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            {{ __('Email support') }}
                        </a>
                        <a href="tel:+33123456789" class="support-btn" style="text-decoration: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            {{ __('Appeler') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection