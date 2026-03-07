@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="3">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Nouvelle réservation') }}</h1>
            <p class="page-subtitle">{{ __('Réservez votre véhicule en quelques clics') }}</p>
        </div>
        <a href="{{ route('dashboard.catalogue') }}" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.45rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex:0 0 auto;">
                <path d="M15 18l-6-6 6-6"/>
                <path d="M21 12H9"/>
            </svg>
            <span>{{ __('Catalogue') }}</span>
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('dashboard.reservation.store') }}" method="POST" class="reservation-form" id="reservationForm" data-has-payment-method="{{ !empty($hasValidPaymentMethod) ? '1' : '0' }}">
        @csrf

        @if(empty($hasValidPaymentMethod))
            <div class="alert alert-warning reservation-payment-warning">
                {{ __('Vous devez ajouter un moyen de paiement valide pour effectuer une réservation.') }}
                <a href="{{ route('dashboard.payment-methods') }}" class="reservation-payment-link">{{ __('Ajouter un moyen de paiement') }}</a>
            </div>
        @endif

        <div class="reservation-grid">
            <!-- Left: Form -->
            <div class="reservation-form-section">
                <!-- Vehicle Selection -->
                <div class="card-section">
                    <h2 class="section-title">🚗 {{ __('Véhicule') }}</h2>
                    <div class="form-group">
                        <label class="form-label">{{ __('Véhicule sélectionné') }}</label>
                        @if($vehicle)
                            <div class="selected-vehicle-display">
                                <span>{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }}) — {{ number_format($vehicle->price_per_day, 0, ',', ' ') }}€/{{ __('jour') }}</span>
                            </div>
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        @else
                            <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                                <option value="">{{ __('Sélectionner un véhicule...') }}</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" 
                                            data-price="{{ $v->price_per_day }}" 
                                            data-deposit="{{ $v->deposit }}"
                                            data-gps="{{ $v->gps_available ? '1' : '0' }}"
                                            data-seat="{{ $v->child_seat_available ? '1' : '0' }}">
                                        {{ $v->brand }} {{ $v->model }} ({{ $v->year }}) — {{ number_format($v->price_per_day, 0, ',', ' ') }}€/{{ __('jour') }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('vehicle_id') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Dates -->
                <div class="card-section">
                    <h2 class="section-title">📅 {{ __('Dates') }}</h2>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="start_date" class="form-label">{{ __('Date de début') }} *</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                   min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" required>
                            @error('start_date') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="end_date" class="form-label">{{ __('Date de fin') }} *</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('end_date') }}" required>
                            @error('end_date') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="duration-display" id="durationDisplay" style="display:none">
                        <span class="duration-text">📆 <strong id="durationDays">0</strong> {{ __('jours') }}</span>
                    </div>
                </div>

                <!-- Options -->
                <div class="card-section">
                    <h2 class="section-title">📦 {{ __('Options') }}</h2>
                    <div class="options-grid">
                        <label class="option-item" id="optGps" style="display:none">
                            <input type="checkbox" name="gps" value="1" {{ old('gps') ? 'checked' : '' }}>
                            <div class="option-content">
                                <span class="option-icon">📍</span>
                                <div>
                                    <span class="option-name">{{ __('GPS') }}</span>
                                    <span class="option-price">+3€/{{ __('jour') }}</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item" id="optSeat" style="display:none">
                            <input type="checkbox" name="child_seat" value="1" {{ old('child_seat') ? 'checked' : '' }}>
                            <div class="option-content">
                                <span class="option-icon">🪑</span>
                                <div>
                                    <span class="option-name">{{ __('Siège enfant') }}</span>
                                    <span class="option-price">+5€/{{ __('jour') }}</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="checkbox" name="additional_driver" value="1" {{ old('additional_driver') ? 'checked' : '' }}>
                            <div class="option-content">
                                <span class="option-icon">👤</span>
                                <div>
                                    <span class="option-name">{{ __('Conducteur additionnel') }}</span>
                                    <span class="option-price">+10€/{{ __('jour') }}</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="checkbox" name="insurance_full" value="1" {{ old('insurance_full') ? 'checked' : '' }}>
                            <div class="option-content">
                                <span class="option-icon">🛡️</span>
                                <div>
                                    <span class="option-name">{{ __('Assurance tous risques') }}</span>
                                    <span class="option-price">+15%</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card-section">
                    <h2 class="section-title">📝 {{ __('Notes') }}</h2>
                    <div class="form-group">
                        <textarea name="customer_notes" class="form-control" rows="3" maxlength="500"
                                  style="resize: none; overflow-y: auto;"
                                  placeholder="{{ __('Informations ou demandes particulières...') }}">{{ old('customer_notes') }}</textarea>
                        <small class="text-muted" style="color: var(--text-secondary); font-size: 0.75rem;">{{ __('Maximum 500 caractères') }}</small>
                    </div>
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="reservation-summary-section">
                <div class="card-section sticky-summary">
                    <h2 class="section-title">💳 {{ __('Récapitulatif') }}</h2>
                    <div class="summary-lines" id="summaryLines">
                        <div class="summary-line">
                            <span>{{ __('Véhicule') }}</span>
                            <span id="summaryVehicle">—</span>
                        </div>
                        <div class="summary-line">
                            <span>{{ __('Durée') }}</span>
                            <span id="summaryDuration">—</span>
                        </div>
                        <div class="summary-line">
                            <span>{{ __('Prix de base') }}</span>
                            <span id="summaryBase">—</span>
                        </div>
                        <div class="summary-line" id="summaryOptionsLine" style="display:none">
                            <span>{{ __('Options') }}</span>
                            <span id="summaryOptions">0€</span>
                        </div>
                        <div class="summary-line" id="summaryInsuranceLine" style="display:none">
                            <span>{{ __('Assurance') }}</span>
                            <span id="summaryInsurance">0€</span>
                        </div>
                        <div class="summary-line total">
                            <span>{{ __('Total') }}</span>
                            <span id="summaryTotal">—</span>
                        </div>
                        <div class="summary-line deposit" id="summaryDepositLine" style="display:none">
                            <span>🔒 {{ __('Caution') }}</span>
                            <span id="summaryDeposit">0€</span>
                        </div>
                    </div>
                    <div id="paymentMethodNotice" class="summary-warning" style="display:none">
                        {{ __('Réservation du jour indisponible sans moyen de paiement valide.') }}
                    </div>
                    <button type="submit" id="reservationSubmitBtn" class="btn-primary btn-block" style="margin-top:1.5rem">
                        {{ __('Confirmer la réservation') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.reservation-grid { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; align-items: start; }
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.375rem; }
.form-label { font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); }
.form-control { padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary); font-size: 0.9rem; transition: border-color 0.2s; }
.form-control:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(var(--accent-rgb, 233, 69, 96), 0.15); }
.form-error { color: var(--danger, #ef4444); font-size: 0.8rem; }
.duration-display { margin-top: 0.75rem; padding: 0.5rem 1rem; background: rgba(var(--accent-rgb, 233, 69, 96), 0.05); border-radius: var(--radius-sm); border: 1px solid rgba(var(--accent-rgb, 233, 69, 96), 0.1); }
.duration-text { font-size: 0.9rem; color: var(--accent); }
.options-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.75rem; }
.option-item { cursor: pointer; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); transition: all 0.2s; }
.option-item:hover { border-color: var(--accent); }
.option-item input[type="checkbox"] { accent-color: var(--accent); width: 18px; height: 18px; flex-shrink: 0; }
.option-item input[type="checkbox"]:checked ~ .option-content { color: var(--accent); }
.option-content { display: flex; align-items: center; gap: 0.5rem; }
.option-icon { font-size: 1.25rem; }
.option-name { font-size: 0.875rem; font-weight: 500; color: var(--text-primary); display: block; }
.option-price { font-size: 0.8rem; color: var(--text-secondary); }
.sticky-summary { position: sticky; top: 6rem; }
.summary-lines { display: flex; flex-direction: column; gap: 0.75rem; }
.summary-line { display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; color: var(--text-primary); padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); }
.summary-line:last-child { border-bottom: none; }
.summary-line.total { font-weight: 700; font-size: 1.1rem; color: var(--accent); border-top: 2px solid var(--border-color); padding-top: 0.75rem; }
.summary-line.deposit { font-size: 0.85rem; color: var(--text-secondary); }
.btn-block { width: 100%; text-align: center; justify-content: center; }
.btn-outline { padding: 0.625rem 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: var(--text-primary); background: transparent; }
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }
.alert { padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; }
.alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
.alert-warning { background: rgba(251, 191, 36, 0.15); color: #f59e0b; border: 1px solid rgba(251, 191, 36, 0.3); }
.reservation-payment-warning { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
.reservation-payment-link { color: var(--accent); font-weight: 600; text-decoration: none; }
.reservation-payment-link:hover { text-decoration: underline; }
.summary-warning { margin-top: 1rem; padding: 0.625rem 0.75rem; border: 1px solid rgba(251, 191, 36, 0.35); border-radius: var(--radius-sm); background: rgba(251, 191, 36, 0.08); color: #f59e0b; font-size: 0.8rem; }
@media (max-width: 768px) { .reservation-grid { grid-template-columns: 1fr; } .sticky-summary { position: static; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const form = document.getElementById('reservationForm');
    const submitButton = document.getElementById('reservationSubmitBtn');
    const paymentMethodNotice = document.getElementById('paymentMethodNotice');
    const hasPaymentMethod = form?.dataset?.hasPaymentMethod === '1';

    // Pre-selected vehicle data (when coming from catalogue)
    const fixedVehicleData = {
        price: {{ $vehicle ? $vehicle->price_per_day : 0 }},
        deposit: {{ $vehicle ? $vehicle->deposit : 0 }},
        gps: {{ $vehicle && $vehicle->gps_available ? 'true' : 'false' }},
        seat: {{ $vehicle && $vehicle->child_seat_available ? 'true' : 'false' }},
        name: '{{ $vehicle ? addslashes($vehicle->brand . " " . $vehicle->model . " (" . $vehicle->year . ")") : "" }}'
    };

    function updateSummary() {
        let pricePerDay, deposit, hasGps, hasSeat, vehicleName, hasVehicle;

        if (vehicleSelect) {
            const selected = vehicleSelect.options[vehicleSelect.selectedIndex];
            pricePerDay = parseFloat(selected?.dataset?.price || 0);
            deposit = parseFloat(selected?.dataset?.deposit || 0);
            hasGps = selected?.dataset?.gps === '1';
            hasSeat = selected?.dataset?.seat === '1';
            vehicleName = vehicleSelect.value ? selected.text.split('—')[0].trim() : '—';
            hasVehicle = !!vehicleSelect.value;
        } else {
            pricePerDay = fixedVehicleData.price;
            deposit = fixedVehicleData.deposit;
            hasGps = fixedVehicleData.gps;
            hasSeat = fixedVehicleData.seat;
            vehicleName = fixedVehicleData.name;
            hasVehicle = true;
        }

        // Show/hide GPS and seat options
        document.getElementById('optGps').style.display = hasGps ? '' : 'none';
        document.getElementById('optSeat').style.display = hasSeat ? '' : 'none';

        // Vehicle name
        document.getElementById('summaryVehicle').textContent = vehicleName || '—';

        // Duration
        let days = 0;
        if (startDate.value && endDate.value) {
            const s = new Date(startDate.value);
            const e = new Date(endDate.value);
            days = Math.max(1, Math.ceil((e - s) / (1000 * 60 * 60 * 24)));
        }

        const durationDisplay = document.getElementById('durationDisplay');
        const daysSpan = document.getElementById('durationDays');
        if (days > 0) {
            durationDisplay.style.display = '';
            daysSpan.textContent = days;
            document.getElementById('summaryDuration').textContent = days + ' jour' + (days > 1 ? 's' : '');
        } else {
            durationDisplay.style.display = 'none';
            document.getElementById('summaryDuration').textContent = '—';
        }

        // Base price
        const basePrice = pricePerDay * days;
        document.getElementById('summaryBase').textContent = basePrice > 0 ? basePrice.toFixed(0) + '€' : '—';

        // Options
        let optionsPrice = 0;
        const gpsCheck = form.querySelector('[name="gps"]');
        const seatCheck = form.querySelector('[name="child_seat"]');
        const driverCheck = form.querySelector('[name="additional_driver"]');
        const insuranceCheck = form.querySelector('[name="insurance_full"]');

        if (gpsCheck?.checked && hasGps) optionsPrice += 3 * days;
        if (seatCheck?.checked && hasSeat) optionsPrice += 5 * days;
        if (driverCheck?.checked) optionsPrice += 10 * days;

        const optLine = document.getElementById('summaryOptionsLine');
        if (optionsPrice > 0) {
            optLine.style.display = '';
            document.getElementById('summaryOptions').textContent = optionsPrice.toFixed(0) + '€';
        } else {
            optLine.style.display = 'none';
        }

        // Insurance
        let insurancePrice = 0;
        const insLine = document.getElementById('summaryInsuranceLine');
        if (insuranceCheck?.checked && basePrice > 0) {
            insurancePrice = basePrice * 0.15;
            insLine.style.display = '';
            document.getElementById('summaryInsurance').textContent = insurancePrice.toFixed(0) + '€';
        } else {
            insLine.style.display = 'none';
        }

        // Total
        const total = basePrice + optionsPrice + insurancePrice;
        document.getElementById('summaryTotal').textContent = total > 0 ? total.toFixed(0) + '€' : '—';

        // Deposit
        const depLine = document.getElementById('summaryDepositLine');
        if (deposit > 0 && hasVehicle) {
            depLine.style.display = '';
            document.getElementById('summaryDeposit').textContent = deposit.toFixed(0) + '€';
        } else {
            depLine.style.display = 'none';
        }

        const selectedStartDate = startDate.value ? new Date(startDate.value + 'T00:00:00') : null;
        const shouldBlockByPayment = selectedStartDate && !hasPaymentMethod;

        if (submitButton) {
            submitButton.disabled = shouldBlockByPayment;
            submitButton.style.opacity = shouldBlockByPayment ? '0.65' : '1';
            submitButton.style.cursor = shouldBlockByPayment ? 'not-allowed' : 'pointer';
        }

        if (paymentMethodNotice) {
            paymentMethodNotice.style.display = shouldBlockByPayment ? '' : 'none';
        }
    }

    if (vehicleSelect) vehicleSelect.addEventListener('change', updateSummary);
    startDate.addEventListener('change', function() {
        if (startDate.value) {
            const min = new Date(startDate.value);
            min.setDate(min.getDate() + 1);
            endDate.min = min.toISOString().split('T')[0];
            if (endDate.value && new Date(endDate.value) <= new Date(startDate.value)) {
                endDate.value = min.toISOString().split('T')[0];
            }
        }
        updateSummary();
    });
    endDate.addEventListener('change', updateSummary);
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.addEventListener('change', updateSummary));

    updateSummary();
});
</script>
@endsection
