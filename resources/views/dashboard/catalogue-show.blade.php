@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="2">
    <div class="page-header">
        <a href="{{ route('dashboard.catalogue') }}" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.45rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex:0 0 auto;">
                <path d="M15 18l-6-6 6-6"/>
                <path d="M21 12H9"/>
            </svg>
            <span>{{ __('Retour au catalogue') }}</span>
        </a>
    </div>

    <div class="vehicle-detail-layout">
        <!-- Main Image -->
        <div class="vehicle-detail-image">
            @if(!empty($galleryImages))
                <img
                    id="vehicleGalleryImage"
                    src="{{ $galleryImages[0] }}"
                    alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                    data-images='@json($galleryImages)'
                    data-index="0"
                >
                @if(count($galleryImages) > 1)
                    <button type="button" class="gallery-nav gallery-prev" onclick="changeGalleryImage(-1)" aria-label="{{ __('Image précédente') }}">‹</button>
                    <button type="button" class="gallery-nav gallery-next" onclick="changeGalleryImage(1)" aria-label="{{ __('Image suivante') }}">›</button>
                @endif
            @else
                <div class="vehicle-image-placeholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:80px;height:80px;opacity:0.3">
                        <path d="M3 11l2-4h14l2 4"/><rect x="1" y="11" width="22" height="6" rx="1"/>
                        <path d="M5 11l2.5-4h9L19 11"/><circle cx="6.5" cy="17" r="2"/><circle cx="17.5" cy="17" r="2"/>
                        <circle cx="6.5" cy="17" r="0.75" fill="currentColor"/><circle cx="17.5" cy="17" r="0.75" fill="currentColor"/>
                        <line x1="1" y1="13" x2="1" y2="15"/><line x1="23" y1="13" x2="23" y2="15"/>
                    </svg>
                    <span>{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                </div>
            @endif
        </div>

        <!-- Vehicle Info -->
        <div class="vehicle-detail-info">
            <div class="vehicle-detail-header">
                <div>
                    <span class="vehicle-type-badge">{{ $vehicle->type_label }}</span>
                    <h1 class="vehicle-detail-title">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                    <p class="vehicle-detail-year">{{ $vehicle->year }} · {{ $vehicle->registration_number }}</p>
                </div>
                @if($vehicle->rating)
                    <div class="vehicle-rating-large">
                        <span class="rating-star">⭐</span>
                        <span class="rating-value">{{ number_format($vehicle->rating, 1) }}</span>
                        <span class="rating-count">({{ $vehicle->reviews_count }} {{ __('avis') }})</span>
                    </div>
                @endif
            </div>

            @if($vehicle->description)
                <p class="vehicle-description">{{ $vehicle->description }}</p>
            @endif

            <!-- Specs Grid -->
            <div class="specs-grid">
                <div class="spec-item">
                    <span class="spec-icon">⛽</span>
                    <span class="spec-label">{{ __('Carburant') }}</span>
                    <span class="spec-value">{{ $vehicle->fuel_type_label }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-icon">⚙️</span>
                    <span class="spec-label">{{ __('Transmission') }}</span>
                    <span class="spec-value">{{ $vehicle->transmission_label }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-icon">👥</span>
                    <span class="spec-label">{{ __('Places') }}</span>
                    <span class="spec-value">{{ $vehicle->seats }}</span>
                </div>
                @if($vehicle->doors)
                <div class="spec-item">
                    <span class="spec-icon">🚪</span>
                    <span class="spec-label">{{ __('Portes') }}</span>
                    <span class="spec-value">{{ $vehicle->doors }}</span>
                </div>
                @endif
                @if($vehicle->engine_power)
                <div class="spec-item">
                    <span class="spec-icon">🏎️</span>
                    <span class="spec-label">{{ __('Puissance') }}</span>
                    <span class="spec-value">{{ $vehicle->engine_power }} {{ __('ch') }}</span>
                </div>
                @endif
                @if($vehicle->fuel_consumption)
                <div class="spec-item">
                    <span class="spec-icon">📊</span>
                    <span class="spec-label">{{ __('Consommation') }}</span>
                    <span class="spec-value">{{ $vehicle->fuel_consumption }} L/100km</span>
                </div>
                @endif
                @if($vehicle->trunk_capacity)
                <div class="spec-item">
                    <span class="spec-icon">🧳</span>
                    <span class="spec-label">{{ __('Coffre') }}</span>
                    <span class="spec-value">{{ $vehicle->trunk_capacity }} L</span>
                </div>
                @endif
                <div class="spec-item">
                    <span class="spec-icon">🔢</span>
                    <span class="spec-label">{{ __('Kilométrage') }}</span>
                    <span class="spec-value">{{ number_format($vehicle->mileage, 0, ',', ' ') }} km</span>
                </div>
            </div>

            <!-- Equipment -->
            <div class="equipment-section">
                <h3>{{ __('Équipements') }}</h3>
                <div class="equipment-tags">
                    @if($vehicle->air_conditioning) <span class="equip-tag available">❄️ {{ __('Climatisation') }}</span> @endif
                    @if($vehicle->gps_available) <span class="equip-tag available">📍 {{ __('GPS') }}</span> @endif
                    @if($vehicle->bluetooth) <span class="equip-tag available">🔵 {{ __('Bluetooth') }}</span> @endif
                    @if($vehicle->cruise_control) <span class="equip-tag available">🚀 {{ __('Régulateur') }}</span> @endif
                    @if($vehicle->parking_sensors) <span class="equip-tag available">📡 {{ __('Capteurs parking') }}</span> @endif
                    @if($vehicle->backup_camera) <span class="equip-tag available">📷 {{ __('Caméra de recul') }}</span> @endif
                    @if($vehicle->child_seat_available) <span class="equip-tag available">🪑 {{ __('Siège enfant') }}</span> @endif
                </div>
            </div>

            <!-- Pricing -->
            <div class="pricing-section">
                <h3>{{ __('Tarifs') }}</h3>
                <div class="pricing-grid">
                    <div class="price-card accent">
                        <span class="price-amount">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }}€</span>
                        <span class="price-period">/ {{ __('jour') }}</span>
                    </div>
                    @if($vehicle->price_per_week)
                    <div class="price-card">
                        <span class="price-amount">{{ number_format($vehicle->price_per_week, 0, ',', ' ') }}€</span>
                        <span class="price-period">/ {{ __('semaine') }}</span>
                    </div>
                    @endif
                    @if($vehicle->price_per_month)
                    <div class="price-card">
                        <span class="price-amount">{{ number_format($vehicle->price_per_month, 0, ',', ' ') }}€</span>
                        <span class="price-period">/ {{ __('mois') }}</span>
                    </div>
                    @endif
                </div>
                @if($vehicle->deposit > 0)
                    <p class="deposit-info">🔒 {{ __('Caution') }}: {{ number_format($vehicle->deposit, 0, ',', ' ') }}€</p>
                @endif
            </div>

            <!-- CTA -->
            <a href="{{ route('dashboard.reservation.create', ['vehicle_id' => $vehicle->id]) }}" class="btn-primary btn-lg" style="text-decoration:none;display:inline-flex;margin-top:1.5rem">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:20px;height:20px"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                {{ __('Réserver ce véhicule') }}
            </a>
        </div>
    </div>
</div>

<script>
function changeGalleryImage(direction) {
    const image = document.getElementById('vehicleGalleryImage');
    if (!image) {
        return;
    }

    const images = JSON.parse(image.dataset.images || '[]');
    if (images.length <= 1) {
        return;
    }

    const currentIndex = parseInt(image.dataset.index || '0', 10);
    const nextIndex = (currentIndex + direction + images.length) % images.length;

    image.src = images[nextIndex];
    image.dataset.index = String(nextIndex);
}
</script>
@endsection
