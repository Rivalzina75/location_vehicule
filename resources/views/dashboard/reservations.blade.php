@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="3">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Mes Réservations') }}</h1>
            <p class="page-subtitle">{{ __('Gérez et suivez vos réservations de véhicules') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Tabs -->
    <div class="tabs-container">
        <button class="tab-btn active" data-tab="all">{{ __('Toutes') }} ({{ $reservations->count() }})</button>
        <button class="tab-btn" data-tab="active">{{ __('En cours') }} ({{ $active->count() }})</button>
        <button class="tab-btn" data-tab="upcoming">{{ __('À venir') }} ({{ $upcoming->count() }})</button>
        <button class="tab-btn" data-tab="past">{{ __('Passées') }} ({{ $past->count() }})</button>
    </div>

    <!-- Reservations List -->
    <div class="reservations-list">
        @if($reservations->count() > 0)
            @foreach($reservations as $reservation)
                <div class="reservation-card" data-status="{{ $reservation->list_category ?? 'upcoming' }}">
                    <div class="reservation-card-header">
                        <div class="reservation-vehicle-info">
                            <div class="reservation-vehicle-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:32px;height:32px">
                                    <path d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1" stroke-width="2"/>
                                    <circle cx="8.5" cy="19" r="1.5"/>
                                    <circle cx="15.5" cy="19" r="1.5"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="reservation-vehicle-name">{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</h3>
                                <p class="reservation-code">{{ $reservation->confirmation_code }}</p>
                            </div>
                        </div>
                        <span class="status-badge status-{{ $reservation->status_color }}">{{ $reservation->status_label }}</span>
                    </div>

                    <div class="reservation-card-body">
                        <div class="reservation-detail">
                            <span class="detail-label">📅 {{ __('Dates') }}</span>
                            <span class="detail-value">{{ $reservation->start_date->format('d/m/Y') }} → {{ $reservation->end_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="reservation-detail">
                            <span class="detail-label">⏱️ {{ __('Durée') }}</span>
                            <span class="detail-value">{{ $reservation->duration_days }} {{ __('jours') }}</span>
                        </div>
                        <div class="reservation-detail">
                            <span class="detail-label">💰 {{ __('Total') }}</span>
                            <span class="detail-value price-accent">{{ number_format($reservation->total_price, 2, ',', ' ') }}€</span>
                        </div>
                        @if($reservation->child_seat || $reservation->gps || $reservation->insurance_full)
                            <div class="reservation-detail">
                                <span class="detail-label">📦 {{ __('Options') }}</span>
                                <span class="detail-value">
                                    @if($reservation->child_seat) 🪑 @endif
                                    @if($reservation->gps) 📍 @endif
                                    @if($reservation->insurance_full) 🛡️ @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="reservation-card-footer">
                        <a href="{{ route('dashboard.reservation.show', $reservation->id) }}" class="btn-outline-sm">
                            {{ __('Voir détails') }}
                        </a>
                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                            <form action="{{ route('dashboard.reservation.destroy', $reservation->id) }}" method="POST" 
                                  onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir annuler cette réservation ?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger-sm">{{ __('Annuler') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:64px;height:64px;opacity:0.5;margin-bottom:1rem">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <h3>{{ __('Aucune réservation') }}</h3>
                <p>{{ __('Parcourez notre catalogue pour réserver votre premier véhicule.') }}</p>
                <a href="{{ route('dashboard.catalogue') }}" class="btn-primary" style="text-decoration:none;margin-top:1rem">{{ __('Voir le catalogue') }}</a>
            </div>
        @endif
    </div>
</div>

<style>
.tabs-container { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.tab-btn { padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-full); background: var(--bg-secondary); color: var(--text-primary); cursor: pointer; font-size: 0.875rem; transition: all 0.2s; }
.tab-btn.active, .tab-btn:hover { background: var(--accent); color: white; border-color: var(--accent); }
.reservations-list { display: flex; flex-direction: column; gap: 1rem; }
.reservation-card { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; transition: all 0.3s; }
.reservation-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); border-color: var(--accent); }
.reservation-card-header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color); }
.reservation-vehicle-info { display: flex; align-items: center; gap: 1rem; }
.reservation-vehicle-icon { width: 48px; height: 48px; border-radius: var(--radius-md); background: rgba(var(--accent-rgb, 233, 69, 96), 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent); }
.reservation-vehicle-name { font-weight: 600; font-size: 1.1rem; color: var(--text-primary); }
.reservation-code { font-size: 0.8rem; color: var(--text-secondary); font-family: monospace; }
.status-badge { padding: 0.35rem 0.75rem; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 600; }
.status-success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.status-warning { background: rgba(251, 191, 36, 0.15); color: #f59e0b; }
.status-info { background: rgba(52, 152, 219, 0.15); color: #3498db; }
.status-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
.status-primary { background: rgba(var(--accent-rgb, 233, 69, 96), 0.15); color: var(--accent); }
.status-secondary { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
.reservation-card-body { padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
.reservation-detail { display: flex; flex-direction: column; gap: 0.25rem; }
.detail-label { font-size: 0.8rem; color: var(--text-secondary); }
.detail-value { font-weight: 500; color: var(--text-primary); }
.price-accent { color: var(--accent); font-weight: 700; }
.reservation-card-footer { display: flex; gap: 0.75rem; padding: 1rem 1.25rem; border-top: 1px solid var(--border-color); }
.btn-outline-sm { padding: 0.5rem 1rem; border: 1px solid var(--accent); color: var(--accent); border-radius: var(--radius-sm); text-decoration: none; font-size: 0.875rem; transition: all 0.2s; background: transparent; cursor: pointer; }
.btn-outline-sm:hover { background: var(--accent); color: white; }
.btn-danger-sm { padding: 0.5rem 1rem; border: 1px solid var(--danger); color: var(--danger); border-radius: var(--radius-sm); font-size: 0.875rem; background: transparent; cursor: pointer; transition: all 0.2s; }
.btn-danger-sm:hover { background: var(--danger); color: white; }
.empty-state { text-align: center; padding: 4rem 2rem; background: var(--bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--border-color); }
.empty-state h3 { font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-primary); }
.empty-state p { color: var(--text-secondary); }
.empty-state-small { display: flex; align-items: center; justify-content: center; min-height: 100px; color: var(--text-secondary); font-style: italic; }
.alert { padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; }
.alert-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const cards = document.querySelectorAll('.reservation-card');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.tab;
            cards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection
