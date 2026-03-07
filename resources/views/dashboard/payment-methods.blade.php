@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="7">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Moyens de paiement') }}</h1>
            <p class="page-subtitle">{{ __('Gérez vos cartes bancaires pour vos réservations') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="payment-layout">
        <!-- Existing Cards -->
        <div class="payment-cards-section">
            @if($paymentMethods->count() > 0)
                <div class="payment-cards-grid">
                    @foreach($paymentMethods as $card)
                        <div class="payment-card {{ $card->is_default ? 'payment-card-default' : '' }} {{ $card->is_expired ? 'payment-card-expired' : '' }}">
                            <div class="payment-card-header">
                                <div class="card-brand-info">
                                    <span class="card-brand-icon">{{ $card->brand_icon }}</span>
                                    <span class="card-brand-name">{{ ucfirst($card->card_brand) }}</span>
                                </div>
                                @if($card->is_default)
                                    <span class="default-badge">{{ __('Par défaut') }}</span>
                                @endif
                                @if($card->is_expired)
                                    <span class="expired-badge">{{ __('Expirée') }}</span>
                                @endif
                            </div>
                            
                            <div class="card-number-display">
                                •••• •••• •••• {{ $card->card_last_four }}
                            </div>

                            <div class="card-details-row">
                                <div>
                                    <span class="card-detail-label">{{ __('Titulaire') }}</span>
                                    <span class="card-detail-value">{{ $card->card_holder_name }}</span>
                                </div>
                                <div>
                                    <span class="card-detail-label">{{ __('Expiration') }}</span>
                                    <span class="card-detail-value">{{ $card->expiry_month }}/{{ $card->expiry_year }}</span>
                                </div>
                            </div>

                            <div class="card-actions">
                                @if(!$card->is_default)
                                    <form method="POST" action="{{ route('dashboard.payment-methods.default', $card->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-outline btn-sm">{{ __('Définir par défaut') }}</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('dashboard.payment-methods.destroy', $card->id) }}" 
                                      onsubmit="return confirm('{{ __('Voulez-vous vraiment supprimer ce moyen de paiement ?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger-sm">🗑️ {{ __('Supprimer') }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">💳</div>
                    <h3>{{ __('Aucun moyen de paiement') }}</h3>
                    <p>{{ __('Ajoutez une carte bancaire pour faciliter vos réservations.') }}</p>
                </div>
            @endif
        </div>

        <!-- Add Card Form -->
        <div class="card-section add-card-section">
            <h3 class="section-title">➕ {{ __('Ajouter une carte') }}</h3>
            
            <form method="POST" action="{{ route('dashboard.payment-methods.store') }}" id="addCardForm">
                @csrf

                <div class="form-group">
                    <label for="card_holder_name">{{ __('Nom du titulaire') }}</label>
                    <input type="text" name="card_holder_name" id="card_holder_name" class="form-control @error('card_holder_name') is-invalid @enderror" 
                           value="{{ old('card_holder_name') }}" placeholder="{{ __('Nom tel qu\'il apparaît sur la carte') }}" required>
                    @error('card_holder_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="card_number">{{ __('Numéro de carte') }}</label>
                    <input type="text" name="card_number" id="card_number" class="form-control @error('card_number') is-invalid @enderror" 
                           value="{{ old('card_number') }}" placeholder="1234 5678 9012 3456" maxlength="19" 
                           inputmode="numeric" autocomplete="cc-number" required>
                    @error('card_number') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="card_brand">{{ __('Type de carte') }}</label>
                    <select name="card_brand" id="card_brand" class="form-control @error('card_brand') is-invalid @enderror" required>
                        <option value="">{{ __('Sélectionner...') }}</option>
                        @foreach(\App\Models\PaymentMethod::cardBrands() as $bKey => $bName)
                            <option value="{{ $bKey }}" @selected(old('card_brand') === $bKey)>{{ $bName }}</option>
                        @endforeach
                    </select>
                    @error('card_brand') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry_month">{{ __('Mois d\'expiration') }}</label>
                        <select name="expiry_month" id="expiry_month" class="form-control @error('expiry_month') is-invalid @enderror" required>
                            <option value="">{{ __('MM') }}</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" @selected(old('expiry_month') === str_pad($m, 2, '0', STR_PAD_LEFT))>
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                        @error('expiry_month') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="expiry_year">{{ __('Année d\'expiration') }}</label>
                        <select name="expiry_year" id="expiry_year" class="form-control @error('expiry_year') is-invalid @enderror" required>
                            <option value="">{{ __('AAAA') }}</option>
                            @for($y = date('Y'); $y <= date('Y') + 15; $y++)
                                <option value="{{ $y }}" @selected(old('expiry_year') === (string) $y)>{{ $y }}</option>
                            @endfor
                        </select>
                        @error('expiry_year') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        {{ __('Définir comme moyen de paiement par défaut') }}
                    </label>
                </div>

                <div class="form-info">
                    <p>🔒 {{ __('Vos informations de paiement sont stockées de manière sécurisée. L\'intégration Stripe sera disponible prochainement.') }}</p>
                </div>

                <button type="submit" class="btn-primary btn-full">
                    💳 {{ __('Ajouter la carte') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cardInput = document.getElementById('card_number');
    const brandSelect = document.getElementById('card_brand');

    if (cardInput) {
        // Format card number with spaces
        cardInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            // Limit to 19 digits
            if (value.length > 19) value = value.substring(0, 19);
            
            // Auto-detect brand
            if (value.startsWith('4')) {
                brandSelect.value = 'visa';
            } else if (/^5[1-5]/.test(value) || /^2[2-7]/.test(value)) {
                brandSelect.value = 'mastercard';
            } else if (/^3[47]/.test(value)) {
                brandSelect.value = 'amex';
            }

            // Store raw value for form submission
            this.dataset.rawValue = value;
            // Display formatted value
            this.value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        });

        // Send raw number on form submit
        const form = document.getElementById('addCardForm');
        form.addEventListener('submit', function() {
            cardInput.value = cardInput.dataset.rawValue || cardInput.value.replace(/\s/g, '');
        });
    }
});
</script>

<style>
.payment-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    align-items: start;
}

.payment-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.payment-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s ease;
}

.payment-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}

.payment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.2);
}

.payment-card-default { border: 2px solid var(--accent); }
.payment-card-expired { opacity: 0.7; }

.payment-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-brand-info { display: flex; align-items: center; gap: 0.5rem; }
.card-brand-icon { font-size: 1.5rem; }
.card-brand-name { font-weight: 600; font-size: 0.95rem; }
.default-badge { background: var(--accent); padding: 0.2rem 0.5rem; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 700; }
.expired-badge { background: var(--danger); padding: 0.2rem 0.5rem; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 700; }

.card-number-display {
    font-size: 1.25rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    font-family: 'Courier New', monospace;
    text-align: center;
    padding: 0.75rem 0;
}

.card-details-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.card-detail-label {
    display: block;
    font-size: 0.65rem;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.card-detail-value {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255,255,255,0.15);
}

.card-actions .btn-outline {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.35rem 0.75rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.2s;
}

.card-actions .btn-outline:hover { background: rgba(255,255,255,0.15); }

.card-actions .btn-danger-sm {
    background: transparent;
    border: 1px solid rgba(251, 113, 133, 0.5);
    color: #fb7185;
    padding: 0.35rem 0.75rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.2s;
}

.card-actions .btn-danger-sm:hover { background: rgba(251, 113, 133, 0.2); }

.add-card-section {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--border);
    position: sticky;
    top: 80px;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--text-secondary);
}

.empty-icon { font-size: 3rem; margin-bottom: 1rem; }
.empty-state h3 { color: var(--text-primary); margin-bottom: 0.5rem; }

.form-info {
    background: rgba(var(--accent-rgb, 233, 69, 96), 0.05);
    border: 1px solid rgba(var(--accent-rgb, 233, 69, 96), 0.1);
    border-radius: var(--radius-sm);
    padding: 0.75rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: var(--text-secondary);
}

@media (max-width: 1024px) {
    .payment-layout { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .payment-cards-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
