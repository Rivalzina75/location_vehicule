@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="3">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Réservation') }} #{{ $reservation->confirmation_code }}</h1>
            <p class="page-subtitle">{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</p>
        </div>
        <a href="{{ route('dashboard.reservations') }}" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.45rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex:0 0 auto;">
                <path d="M15 18l-6-6 6-6"/>
                <path d="M21 12H9"/>
            </svg>
            <span>{{ __('Mes réservations') }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="detail-grid">
        <!-- Left: Main Info -->
        <div class="detail-main">
            <!-- Status -->
            <div class="card-section">
                <div class="status-header">
                    <span class="status-badge-lg status-{{ $reservation->status_color }}">{{ $reservation->status_label }}</span>
                    @if(in_array($reservation->status, ['pending', 'confirmed']))
                        <form action="{{ route('dashboard.reservation.destroy', $reservation->id) }}" method="POST" 
                              onsubmit="return confirm('{{ __('Annuler cette réservation ?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-sm">{{ __('Annuler la réservation') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Vehicle Info -->
            <div class="card-section">
                <h2 class="section-title">🚗 {{ __('Véhicule') }}</h2>
                <div class="vehicle-summary">
                    <div class="vehicle-summary-image">
                        @if($reservation->vehicle->image_path)
                            <img src="{{ asset('storage/' . $reservation->vehicle->image_path) }}" alt="{{ $reservation->vehicle->brand }}">
                        @else
                            <div class="vehicle-placeholder-sm">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:40px;height:40px;opacity:0.3">
                                    <path d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1" stroke-width="2"/>
                                    <circle cx="8.5" cy="19" r="1.5"/><circle cx="15.5" cy="19" r="1.5"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3>{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</h3>
                        <p class="text-muted">{{ $reservation->vehicle->year }} · {{ $reservation->vehicle->type_label }} · {{ $reservation->vehicle->transmission_label }}</p>
                        <p class="text-muted">{{ $reservation->vehicle->fuel_type_label }} · {{ $reservation->vehicle->seats }} {{ __('places') }}</p>
                    </div>
                </div>
            </div>

            <!-- Dates & Duration -->
            <div class="card-section">
                <h2 class="section-title">📅 {{ __('Dates') }}</h2>
                <div class="dates-timeline">
                    <div class="date-point">
                        <div class="date-dot start"></div>
                        <div>
                            <span class="date-label">{{ __('Début') }}</span>
                            <span class="date-value">{{ $reservation->start_date->translatedFormat('l d F Y') }}</span>
                        </div>
                    </div>
                    <div class="date-line">
                        <span class="date-duration">{{ $reservation->duration_days }} {{ __('jours') }}</span>
                    </div>
                    <div class="date-point">
                        <div class="date-dot end"></div>
                        <div>
                            <span class="date-label">{{ __('Fin') }}</span>
                            <span class="date-value">{{ $reservation->end_date->translatedFormat('l d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options -->
            @if($reservation->child_seat || $reservation->gps || $reservation->additional_driver || $reservation->insurance_full)
            <div class="card-section">
                <h2 class="section-title">📦 {{ __('Options') }}</h2>
                <div class="options-list">
                    @if($reservation->gps) <span class="option-badge">📍 {{ __('GPS') }}</span> @endif
                    @if($reservation->child_seat) <span class="option-badge">🪑 {{ __('Siège enfant') }}</span> @endif
                    @if($reservation->additional_driver) <span class="option-badge">👤 {{ __('Conducteur additionnel') }}</span> @endif
                    @if($reservation->insurance_full) <span class="option-badge">🛡️ {{ __('Assurance tous risques') }}</span> @endif
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($reservation->customer_notes)
            <div class="card-section">
                <h2 class="section-title">📝 {{ __('Notes') }}</h2>
                <p>{{ $reservation->customer_notes }}</p>
            </div>
            @endif

            <!-- Inspections -->
            @if($reservation->inspections->count() > 0)
            <div class="card-section">
                <h2 class="section-title">🔍 {{ __('Inspections') }}</h2>
                @foreach($reservation->inspections as $inspection)
                    <div class="inspection-summary">
                        <h4>{{ $inspection->type === 'start' ? __('Inspection de départ') : __('Inspection de retour') }}</h4>
                        <p class="text-muted">{{ $inspection->inspection_date->format('d/m/Y H:i') }} · {{ number_format($inspection->mileage, 0, ',', ' ') }} km</p>
                        <div class="check-icons">
                            <span class="{{ $inspection->exterior_ok ? 'check-ok' : 'check-fail' }}">{{ $inspection->exterior_ok ? '✅' : '❌' }} {{ __('Extérieur') }}</span>
                            <span class="{{ $inspection->interior_ok ? 'check-ok' : 'check-fail' }}">{{ $inspection->interior_ok ? '✅' : '❌' }} {{ __('Intérieur') }}</span>
                            <span class="{{ $inspection->tires_ok ? 'check-ok' : 'check-fail' }}">{{ $inspection->tires_ok ? '✅' : '❌' }} {{ __('Pneus') }}</span>
                        </div>
                        @if($inspection->general_notes)
                            <p class="inspection-notes">{{ $inspection->general_notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Right: Pricing Summary -->
        <div class="detail-sidebar">
            <div class="card-section sticky-summary">
                <h2 class="section-title">💳 {{ __('Détail des coûts') }}</h2>
                <div class="summary-lines">
                    <div class="summary-line">
                        <span>{{ __('Prix de base') }}</span>
                        <span>{{ number_format($reservation->base_price, 2, ',', ' ') }}€</span>
                    </div>
                    @if($reservation->options_price > 0)
                    <div class="summary-line">
                        <span>{{ __('Options') }}</span>
                        <span>{{ number_format($reservation->options_price, 2, ',', ' ') }}€</span>
                    </div>
                    @endif
                    @if($reservation->insurance_price > 0)
                    <div class="summary-line">
                        <span>{{ __('Assurance') }}</span>
                        <span>{{ number_format($reservation->insurance_price, 2, ',', ' ') }}€</span>
                    </div>
                    @endif
                    @if($reservation->damage_cost > 0)
                    <div class="summary-line warning">
                        <span>{{ __('Dommages') }}</span>
                        <span>{{ number_format($reservation->damage_cost, 2, ',', ' ') }}€</span>
                    </div>
                    @endif
                    @if($reservation->late_penalty > 0)
                    <div class="summary-line warning">
                        <span>{{ __('Pénalité de retard') }}</span>
                        <span>{{ number_format($reservation->late_penalty, 2, ',', ' ') }}€</span>
                    </div>
                    @endif
                    <div class="summary-line total">
                        <span>{{ __('Total') }}</span>
                        <span>{{ number_format($reservation->total_price + ($reservation->damage_cost ?? 0) + ($reservation->late_penalty ?? 0), 2, ',', ' ') }}€</span>
                    </div>
                </div>

                @if($reservation->deposit_amount > 0)
                <div class="deposit-box">
                    🔒 {{ __('Caution') }}: {{ number_format($reservation->deposit_amount, 0, ',', ' ') }}€
                </div>
                @endif

                <div class="payment-status">
                    <span class="payment-label">{{ __('Paiement') }}:</span>
                    @if($reservation->payment_status === 'completed')
                        <span class="badge-success">✅ {{ __('Payé') }}</span>
                    @elseif($reservation->payment_status === 'refunded')
                        <span class="badge-info">↩️ {{ __('Remboursé') }}</span>
                    @else
                        <span class="badge-warning">⏳ {{ __('En attente') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-grid { display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem; align-items: start; }
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; }
.status-header { display: flex; justify-content: space-between; align-items: center; }
.status-badge-lg { padding: 0.5rem 1rem; border-radius: var(--radius-full); font-weight: 600; }
.status-success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.status-warning { background: rgba(251, 191, 36, 0.15); color: #f59e0b; }
.status-info { background: rgba(52, 152, 219, 0.15); color: #3498db; }
.status-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
.status-primary { background: rgba(var(--accent-rgb, 233, 69, 96), 0.15); color: var(--accent); }
.status-secondary { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
.vehicle-summary { display: flex; gap: 1rem; align-items: center; }
.vehicle-summary-image { width: 100px; height: 70px; border-radius: var(--radius-sm); overflow: hidden; background: var(--bg-primary); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.vehicle-summary-image img { width: 100%; height: 100%; object-fit: cover; }
.vehicle-summary h3 { font-weight: 600; color: var(--text-primary); }
.text-muted { font-size: 0.85rem; color: var(--text-secondary); }
.dates-timeline { display: flex; flex-direction: column; gap: 0; }
.date-point { display: flex; align-items: center; gap: 1rem; }
.date-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.date-dot.start { background: var(--accent); }
.date-dot.end { background: #10b981; }
.date-label { display: block; font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; }
.date-value { font-weight: 500; color: var(--text-primary); }
.date-line { margin-left: 5px; border-left: 2px dashed var(--border-color); height: 40px; display: flex; align-items: center; padding-left: 1.5rem; }
.date-duration { font-size: 0.85rem; color: var(--accent); font-weight: 600; }
.options-list { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.option-badge { padding: 0.375rem 0.75rem; border-radius: var(--radius-full); background: rgba(var(--accent-rgb, 233, 69, 96), 0.1); color: var(--accent); font-size: 0.85rem; }
.inspection-summary { padding: 1rem; background: var(--bg-primary); border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 0.75rem; }
.inspection-summary h4 { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; }
.check-icons { display: flex; gap: 1rem; margin-top: 0.5rem; flex-wrap: wrap; }
.check-icons span { font-size: 0.8rem; }
.inspection-notes { margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-secondary); font-style: italic; }
.sticky-summary { position: sticky; top: 6rem; }
.summary-lines { display: flex; flex-direction: column; gap: 0.75rem; }
.summary-line { display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-primary); padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); }
.summary-line.total { font-weight: 700; font-size: 1.1rem; color: var(--accent); border-top: 2px solid var(--border-color); padding-top: 0.75rem; }
.summary-line.warning span:last-child { color: #f59e0b; }
.deposit-box { margin-top: 1rem; padding: 0.75rem; background: rgba(var(--accent-rgb, 233, 69, 96), 0.05); border: 1px solid rgba(var(--accent-rgb, 233, 69, 96), 0.1); border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--text-secondary); text-align: center; }
.payment-status { margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; }
.payment-label { font-size: 0.875rem; color: var(--text-secondary); }
.badge-success { background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-size: 0.8rem; }
.badge-warning { background: rgba(251, 191, 36, 0.1); color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-size: 0.8rem; }
.badge-info { background: rgba(52, 152, 219, 0.1); color: #3498db; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-size: 0.8rem; }
.btn-outline { padding: 0.625rem 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: var(--text-primary); background: transparent; }
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }
.btn-danger-sm { padding: 0.5rem 1rem; border: 1px solid var(--danger, #ef4444); color: var(--danger, #ef4444); border-radius: var(--radius-sm); font-size: 0.875rem; background: transparent; cursor: pointer; }
.btn-danger-sm:hover { background: var(--danger, #ef4444); color: white; }
.alert { padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; }
.alert-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
@media (max-width: 768px) { .detail-grid { grid-template-columns: 1fr; } .sticky-summary { position: static; } }
</style>
@endsection
