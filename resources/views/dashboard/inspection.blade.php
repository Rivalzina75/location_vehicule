@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="5">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Inspection des véhicules') }}</h1>
            <p class="page-subtitle">{{ __('États des lieux de départ et retour pour vos réservations') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Start Inspections Needed -->
    <div class="card-section">
        <h2 class="section-title">🚗 {{ __('Inspections de départ') }}</h2>
        <p class="section-desc">{{ __('Réservations confirmées nécessitant un état des lieux de départ') }}</p>

        @if($needingStartInspection->count() > 0)
            @foreach($needingStartInspection as $reservation)
                <div class="inspection-card">
                    <div class="inspection-card-header">
                        <div class="inspection-vehicle">
                            <h3>{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</h3>
                            <span class="inspection-code">{{ $reservation->confirmation_code }}</span>
                        </div>
                        <span class="inspection-dates">
                            📅 {{ $reservation->start_date->format('d/m/Y') }} → {{ $reservation->end_date->format('d/m/Y') }}
                        </span>
                    </div>

                    <form action="{{ route('dashboard.inspection.start', $reservation->id) }}" method="POST" enctype="multipart/form-data" class="inspection-form">
                        @csrf
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">{{ __('Kilométrage') }} *</label>
                                <input type="number" name="mileage" class="form-control" min="0" required placeholder="ex: 45230">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Niveau de carburant') }} *</label>
                                <select name="fuel_level" class="form-control" required>
                                    <option value="full">{{ __('Plein') }}</option>
                                    <option value="three_quarters">{{ __('3/4') }}</option>
                                    <option value="half">{{ __('1/2') }}</option>
                                    <option value="quarter">{{ __('1/4') }}</option>
                                    <option value="empty">{{ __('Vide') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:1rem">
                            <label class="form-label">{{ __('Propreté') }} *</label>
                            <select name="cleanliness" class="form-control" required>
                                <option value="very_clean">{{ __('Très propre') }}</option>
                                <option value="clean">{{ __('Propre') }}</option>
                                <option value="acceptable">{{ __('Acceptable') }}</option>
                                <option value="dirty">{{ __('Sale') }}</option>
                            </select>
                        </div>

                        <div class="checklist-section">
                            <h4>{{ __('Points de contrôle') }}</h4>
                            <div class="checklist-grid">
                                <label class="check-item"><input type="checkbox" name="exterior_ok" value="1" checked> {{ __('Extérieur OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="interior_ok" value="1" checked> {{ __('Intérieur OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="tires_ok" value="1" checked> {{ __('Pneus OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="lights_ok" value="1" checked> {{ __('Éclairage OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="documents_ok" value="1" checked> {{ __('Documents OK') }}</label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:1rem">
                            <label class="form-label">{{ __('Photos') }}</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                            <small class="form-hint">{{ __('JPG/PNG, max 5Mo chacune') }}</small>
                        </div>

                        <div class="form-group" style="margin-top:1rem">
                            <label class="form-label">{{ __('Notes générales') }}</label>
                            <textarea name="general_notes" class="form-control" rows="3" placeholder="{{ __('Observations éventuelles...') }}"></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top:1rem">
                            ✅ {{ __('Valider l\'inspection de départ') }}
                        </button>
                    </form>
                </div>
            @endforeach
        @else
            <div class="empty-state-small">{{ __('Aucune inspection de départ en attente') }}</div>
        @endif
    </div>

    <!-- End Inspections Needed -->
    <div class="card-section">
        <h2 class="section-title">🔄 {{ __('Inspections de retour') }}</h2>
        <p class="section-desc">{{ __('Réservations actives nécessitant un état des lieux de retour') }}</p>

        @if($needingEndInspection->count() > 0)
            @foreach($needingEndInspection as $reservation)
                <div class="inspection-card">
                    <div class="inspection-card-header">
                        <div class="inspection-vehicle">
                            <h3>{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</h3>
                            <span class="inspection-code">{{ $reservation->confirmation_code }}</span>
                        </div>
                        <div>
                            <span class="inspection-dates">📅 {{ $reservation->start_date->format('d/m/Y') }} → {{ $reservation->end_date->format('d/m/Y') }}</span>
                            @if($reservation->mileage_start)
                                <span class="inspection-mileage">🔢 {{ __('Départ') }}: {{ number_format($reservation->mileage_start, 0, ',', ' ') }} km</span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('dashboard.inspection.end', $reservation->id) }}" method="POST" enctype="multipart/form-data" class="inspection-form">
                        @csrf
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">{{ __('Kilométrage') }} *</label>
                                <input type="number" name="mileage" class="form-control" min="{{ $reservation->mileage_start ?? 0 }}" required placeholder="ex: 45780">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Niveau de carburant') }} *</label>
                                <select name="fuel_level" class="form-control" required>
                                    <option value="full">{{ __('Plein') }}</option>
                                    <option value="three_quarters">{{ __('3/4') }}</option>
                                    <option value="half">{{ __('1/2') }}</option>
                                    <option value="quarter">{{ __('1/4') }}</option>
                                    <option value="empty">{{ __('Vide') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:1rem">
                            <label class="form-label">{{ __('Propreté') }} *</label>
                            <select name="cleanliness" class="form-control" required>
                                <option value="very_clean">{{ __('Très propre') }}</option>
                                <option value="clean">{{ __('Propre') }}</option>
                                <option value="acceptable">{{ __('Acceptable') }}</option>
                                <option value="dirty">{{ __('Sale') }}</option>
                            </select>
                        </div>

                        <div class="checklist-section">
                            <h4>{{ __('Points de contrôle') }}</h4>
                            <div class="checklist-grid">
                                <label class="check-item"><input type="checkbox" name="exterior_ok" value="1" checked> {{ __('Extérieur OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="interior_ok" value="1" checked> {{ __('Intérieur OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="tires_ok" value="1" checked> {{ __('Pneus OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="lights_ok" value="1" checked> {{ __('Éclairage OK') }}</label>
                                <label class="check-item"><input type="checkbox" name="documents_ok" value="1" checked> {{ __('Documents OK') }}</label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:1rem">
                            <label class="form-label">{{ __('Dommages constatés') }}</label>
                            <textarea name="damage_notes" class="form-control" rows="3" placeholder="{{ __('Décrivez les dommages éventuels...') }}"></textarea>
                        </div>

                        <div class="form-group" style="margin-top:1rem">
                            <label class="form-label">{{ __('Photos') }}</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                            <small class="form-hint">{{ __('JPG/PNG, max 5Mo chacune') }}</small>
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top:1rem">
                            ✅ {{ __('Valider l\'inspection de retour') }}
                        </button>
                    </form>
                </div>
            @endforeach
        @else
            <div class="empty-state-small">{{ __('Aucune inspection de retour en attente') }}</div>
        @endif
    </div>
</div>

<style>
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; }
.section-desc { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.25rem; }
.inspection-card { background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1rem; }
.inspection-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem; }
.inspection-vehicle h3 { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; }
.inspection-code { font-size: 0.8rem; font-family: monospace; color: var(--text-secondary); }
.inspection-dates { font-size: 0.85rem; color: var(--text-secondary); display: block; }
.inspection-mileage { font-size: 0.85rem; color: var(--accent); display: block; margin-top: 0.25rem; }
.inspection-form { border-top: 1px solid var(--border-color); padding-top: 1.25rem; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.375rem; }
.form-label { font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); }
.form-control { padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.9rem; transition: border-color 0.2s; }
.form-control:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(var(--accent-rgb, 233, 69, 96), 0.15); }
.form-hint { font-size: 0.75rem; color: var(--text-secondary); opacity: 0.7; }
.checklist-section { margin-top: 1rem; }
.checklist-section h4 { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; }
.checklist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 0.5rem; }
.check-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-primary); cursor: pointer; padding: 0.375rem 0.5rem; border-radius: var(--radius-sm); transition: background 0.15s; }
.check-item:hover { background: rgba(var(--accent-rgb, 233, 69, 96), 0.05); }
.check-item input[type="checkbox"] { accent-color: var(--accent); width: 16px; height: 16px; }
.empty-state-small { display: flex; align-items: center; justify-content: center; min-height: 80px; color: var(--text-secondary); font-style: italic; }
.alert { padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; }
.alert-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
@media (max-width: 640px) { .form-grid-2 { grid-template-columns: 1fr; } }
</style>
@endsection
