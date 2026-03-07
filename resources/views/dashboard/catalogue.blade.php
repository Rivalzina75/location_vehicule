@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="2">
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('Catalogue de Véhicules') }}</h1>
        <p class="page-subtitle">{{ __('Trouvez le véhicule parfait pour votre location') }}</p>
    </div>
</div>

<div class="catalogue-wrapper">
    <!-- BARRE LATÉRALE - FILTRES -->
    <aside class="filters-sidebar">
        <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;vertical-align:middle;margin-right:4px"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>{{ __('Filtrer') }}</h2>

        <form method="GET" action="{{ route('dashboard.catalogue') }}" id="filterForm" class="filters-form" data-price-bounds='@json($priceBounds)'>
            <!-- Active tags display -->
            @if(count($activeFeatures) > 0 || request('type') || request('transmission') || request('fuel_type'))
                <div class="active-filters-display">
                    <label class="filter-section-label">{{ __('Filtres actifs') }}</label>
                    <div class="active-tags-list">
                        @if(request('type'))
                            @php $typeArr = is_array(request('type')) ? request('type') : [request('type')]; @endphp
                            @foreach($typeArr as $t)
                                <span class="active-tag cat-type" data-filter="type" data-value="{{ $t }}">
                                    {{ $types[$t] ?? $t }} ✕
                                </span>
                            @endforeach
                        @endif
                        @if(request('transmission'))
                            @php $transArr = is_array(request('transmission')) ? request('transmission') : [request('transmission')]; @endphp
                            @foreach($transArr as $tr)
                                <span class="active-tag cat-spec" data-filter="transmission" data-value="{{ $tr }}">
                                    {{ $tr === 'manual' ? __('Manuelle') : __('Automatique') }} ✕
                                </span>
                            @endforeach
                        @endif
                        @if(request('fuel_type'))
                            @php $fuelArr = is_array(request('fuel_type')) ? request('fuel_type') : [request('fuel_type')]; @endphp
                            @foreach($fuelArr as $ft)
                                <span class="active-tag cat-fuel" data-filter="fuel_type" data-value="{{ $ft }}">
                                    {{ \App\Models\Vehicle::fuelTypes()[$ft] ?? $ft }} ✕
                                </span>
                            @endforeach
                        @endif
                        @foreach($activeFeatures as $feat)
                            <span class="active-tag cat-feature" data-filter="features" data-value="{{ $feat }}">
                                {{ $allFeatures[$feat]['label'] ?? $feat }} ✕
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Prix min/max -->
            <div class="filter-group">
                <label for="max_price">{{ __('Prix') }}</label>
                <div class="price-unit-toggle">
                    <label class="unit-option">
                        <input type="radio" name="price_unit" value="day" {{ $priceUnit === 'day' ? 'checked' : '' }}>
                        <span>{{ __('Jour') }}</span>
                    </label>
                    <label class="unit-option">
                        <input type="radio" name="price_unit" value="week" {{ $priceUnit === 'week' ? 'checked' : '' }}>
                        <span>{{ __('Semaine') }}</span>
                    </label>
                    <label class="unit-option">
                        <input type="radio" name="price_unit" value="month" {{ $priceUnit === 'month' ? 'checked' : '' }}>
                        <span>{{ __('Mois') }}</span>
                    </label>
                </div>
                <label for="min_price" class="price-slider-label">{{ __('Min') }}</label>
                <input 
                    type="range" 
                    id="min_price" 
                    name="min_price" 
                    min="{{ $minPriceDb }}" 
                    max="{{ $maxPriceDb }}" 
                    value="{{ $selectedMinPrice }}"
                    class="range-slider"
                    oninput="updatePriceRange('min')"
                >
                <label for="max_price" class="price-slider-label">{{ __('Max') }}</label>
                <input 
                    type="range" 
                    id="max_price" 
                    name="max_price" 
                    min="{{ $minPriceDb }}" 
                    max="{{ $maxPriceDb }}" 
                    value="{{ $selectedMaxPrice }}"
                    class="range-slider"
                    oninput="updatePriceRange('max')"
                >
                <div class="price-display">
                    <span id="priceMinValue">{{ $selectedMinPrice }}€</span>
                    <span class="price-separator">—</span>
                    <span id="priceMaxValue">{{ $selectedMaxPrice }}€</span>
                </div>
            </div>

            <!-- Type de véhicule (tags) -->
            <div class="filter-group">
                <label class="filter-section-label">{{ __('Type de véhicule') }}</label>
                <div class="filter-tags-grid">
                    @foreach($types as $typeKey => $typeName)
                        @php
                            $currentTypes = is_array(request('type')) ? request('type') : (request('type') ? [request('type')] : []);
                            $isActive = in_array($typeKey, $currentTypes);
                        @endphp
                        <label class="filter-tag cat-type {{ $isActive ? 'active' : '' }}">
                            <input type="checkbox" name="type[]" value="{{ $typeKey }}" {{ $isActive ? 'checked' : '' }} hidden>
                            <span>{{ $typeName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Transmission (tags) -->
            <div class="filter-group">
                <label class="filter-section-label">{{ __('Transmission') }}</label>
                <div class="filter-tags-grid">
                    @php
                        $currentTrans = is_array(request('transmission')) ? request('transmission') : (request('transmission') ? [request('transmission')] : []);
                    @endphp
                    <label class="filter-tag cat-spec {{ in_array('manual', $currentTrans) ? 'active' : '' }}">
                        <input type="checkbox" name="transmission[]" value="manual" {{ in_array('manual', $currentTrans) ? 'checked' : '' }} hidden>
                        <span>{{ __('Manuelle') }}</span>
                    </label>
                    <label class="filter-tag cat-spec {{ in_array('automatic', $currentTrans) ? 'active' : '' }}">
                        <input type="checkbox" name="transmission[]" value="automatic" {{ in_array('automatic', $currentTrans) ? 'checked' : '' }} hidden>
                        <span>{{ __('Automatique') }}</span>
                    </label>
                </div>
            </div>

            <!-- Carburant (tags) -->
            <div class="filter-group">
                <label class="filter-section-label">{{ __('Carburant') }}</label>
                <div class="filter-tags-grid">
                    @php
                        $currentFuel = is_array(request('fuel_type')) ? request('fuel_type') : (request('fuel_type') ? [request('fuel_type')] : []);
                    @endphp
                    @foreach(\App\Models\Vehicle::fuelTypes() as $fKey => $fName)
                        <label class="filter-tag cat-fuel {{ in_array($fKey, $currentFuel) ? 'active' : '' }}">
                            <input type="checkbox" name="fuel_type[]" value="{{ $fKey }}" {{ in_array($fKey, $currentFuel) ? 'checked' : '' }} hidden>
                            <span>{{ $fName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Équipements (tags) -->
            <div class="filter-group">
                <label class="filter-section-label">{{ __('Équipements') }}</label>
                <div class="filter-tags-grid">
                    @foreach($allFeatures as $featKey => $featData)
                        <label class="filter-tag cat-feature {{ in_array($featKey, $activeFeatures) ? 'active' : '' }}">
                            <input type="checkbox" name="features[]" value="{{ $featKey }}" {{ in_array($featKey, $activeFeatures) ? 'checked' : '' }} hidden>
                            <span>{{ $featData['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Trier par -->
            <div class="filter-group">
                <label for="sort">{{ __('Trier par') }}</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>{{ __('Prix croissant') }}</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('Prix décroissant') }}</option>
                    <option value="name" @selected(request('sort') === 'name')>{{ __('Nom (A-Z)') }}</option>
                    <option value="year" @selected(request('sort') === 'year')>{{ __('Année (Plus récent)') }}</option>
                </select>
            </div>

            <!-- Boutons -->
            <div class="filter-buttons">
                <button type="submit" class="btn-primary btn-full">
                    {{ __('Appliquer les filtres') }}
                </button>
                <a href="{{ route('dashboard.catalogue') }}" class="btn-secondary btn-full">
                    {{ __('Réinitialiser') }}
                </a>
            </div>
        </form>
    </aside>

    <!-- GRILLE DE VÉHICULES -->
    <main class="vehicles-grid-wrapper">
        <div class="vehicles-info">
            <p class="vehicles-count">
                {{ __('Résultats') }}: <strong>{{ $vehicles->total() }}</strong> {{ __('véhicule(s) trouvé(s)') }}
            </p>
        </div>

        @if($vehicles->count() > 0)
            <div class="vehicles-grid">
                @foreach($vehicles as $vehicle)
                    <div class="vehicle-card" data-vehicle-id="{{ $vehicle->id }}">
                        <!-- Image du véhicule -->
                        <div class="vehicle-image">
                            @if($vehicle->resolved_image_url)
                                <img 
                                    src="{{ $vehicle->resolved_image_url }}" 
                                    alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                                    class="vehicle-img"
                                >
                            @else
                                <div class="vehicle-img-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;opacity:0.5">
                                        <path d="M3 11l2-4h14l2 4"/><rect x="1" y="11" width="22" height="6" rx="1"/>
                                        <path d="M5 11l2.5-4h9L19 11"/><circle cx="6.5" cy="17" r="2"/><circle cx="17.5" cy="17" r="2"/>
                                        <circle cx="6.5" cy="17" r="0.75" fill="currentColor"/><circle cx="17.5" cy="17" r="0.75" fill="currentColor"/>
                                        <line x1="1" y1="13" x2="1" y2="15"/><line x1="23" y1="13" x2="23" y2="15"/>
                                    </svg>
                                    <span>{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                                </div>
                            @endif
                            
                            @if($vehicle->fuel_type)
                                <div class="vehicle-badge fuel-{{ $vehicle->fuel_type }}">
                                    {{ $vehicle->fuel_type_label }}
                                </div>
                            @endif
                        </div>

                        <!-- Informations du véhicule -->
                        <div class="vehicle-info">
                            <h3 class="vehicle-title">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </h3>
                            
                            <div class="vehicle-specs">
                                <span class="spec"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spec-icon"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> {{ $vehicle->year }}</span>
                                <span class="spec"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spec-icon"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg> {{ $vehicle->seats }} {{ __('places') }}</span>
                                @if($vehicle->doors)
                                    <span class="spec"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spec-icon"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/><path d="M14 9h1"/></svg> {{ $vehicle->doors }} {{ __('portes') }}</span>
                                @endif
                                <span class="spec"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spec-icon"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg> {{ $vehicle->transmission_label }}</span>
                                @if($vehicle->rating)
                                    <span class="spec"><svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" class="spec-icon"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> {{ $vehicle->rating }}/5</span>
                                @endif
                            </div>

                            <!-- Caractéristiques cliquables -->
                            @php
                                $vehicleFeatures = [];
                                if($vehicle->child_seat_available) $vehicleFeatures['child_seat_available'] = ['label' => __('Siège enfant')];
                                if($vehicle->gps_available) $vehicleFeatures['gps_available'] = ['label' => __('GPS')];
                                if($vehicle->bluetooth) $vehicleFeatures['bluetooth'] = ['label' => __('Bluetooth')];
                                if($vehicle->air_conditioning) $vehicleFeatures['air_conditioning'] = ['label' => __('Climatisation')];
                                if($vehicle->cruise_control) $vehicleFeatures['cruise_control'] = ['label' => __('Régulateur')];
                                if($vehicle->parking_sensors) $vehicleFeatures['parking_sensors'] = ['label' => __('Capteurs parking')];
                                if($vehicle->backup_camera) $vehicleFeatures['backup_camera'] = ['label' => __('Caméra de recul')];
                            @endphp
                            @if(count($vehicleFeatures) > 0)
                                <div class="vehicle-features">
                                    @foreach($vehicleFeatures as $fKey => $fData)
                                        <a href="{{ route('dashboard.catalogue', array_merge(request()->except('features', 'page'), ['features' => array_unique(array_merge($activeFeatures, [$fKey]))])) }}" 
                                           class="feature-badge feature-clickable {{ in_array($fKey, $activeFeatures) ? 'feature-active' : '' }}">
                                            {{ $fData['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Tarification -->
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">{{ __('Jour') }}</span>
                                <span class="price-value">{{ number_format($vehicle->price_per_day, 2, ',', ' ') }}€</span>
                            </div>
                            @if($vehicle->price_per_week)
                                <div class="price-item">
                                    <span class="price-label">{{ __('Semaine') }}</span>
                                    <span class="price-value">{{ number_format($vehicle->price_per_week, 2, ',', ' ') }}€</span>
                                </div>
                            @endif
                            @if($vehicle->price_per_month)
                                <div class="price-item">
                                    <span class="price-label">{{ __('Mois') }}</span>
                                    <span class="price-value">{{ number_format($vehicle->price_per_month, 2, ',', ' ') }}€</span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="vehicle-actions">
                            <a 
                                href="{{ route('dashboard.catalogue.show', $vehicle->id) }}" 
                                class="btn-primary btn-full"
                            >
                                {{ __('Voir les détails') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION -->
            @if($vehicles->hasPages())
                <div class="pagination-wrapper">
                    <a href="{{ $vehicles->onFirstPage() ? '#' : $vehicles->appends(request()->query())->previousPageUrl() }}" class="pagination-btn {{ $vehicles->onFirstPage() ? 'disabled' : '' }}" aria-label="{{ __('Page précédente') }}">
                        ‹
                    </a>

                    @foreach($vehicles->getUrlRange(max(1, $vehicles->currentPage() - 2), min($vehicles->lastPage(), $vehicles->currentPage() + 2)) as $page => $url)
                        @if($page == $vehicles->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $vehicles->appends(request()->query())->url($page) }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    <a href="{{ $vehicles->hasMorePages() ? $vehicles->appends(request()->query())->nextPageUrl() : '#' }}" class="pagination-btn {{ $vehicles->hasMorePages() ? '' : 'disabled' }}" aria-label="{{ __('Page suivante') }}">
                        ›
                    </a>
                </div>
            @endif
        @else
            <!-- Aucun résultat -->
            <div class="no-results">
                <div class="no-results-content">
                    <h3>{{ __('Aucun véhicule trouvé') }}</h3>
                    <p>{{ __('Veuillez essayer avec d\'autres critères de filtre.') }}</p>
                    <a href="{{ route('dashboard.catalogue') }}" class="btn-primary">
                        {{ __('Réinitialiser les filtres') }}
                    </a>
                </div>
            </div>
        @endif
    </main>
</div>
</div>

<script>
function updatePriceRange(changed = null) {
    const minSlider = document.getElementById('min_price');
    const maxSlider = document.getElementById('max_price');
    if (!minSlider || !maxSlider) {
        return;
    }

    let minValue = parseFloat(minSlider.value || 0);
    let maxValue = parseFloat(maxSlider.value || 0);

    if (minValue > maxValue) {
        if (changed === 'min') {
            maxSlider.value = minValue;
            maxValue = minValue;
        } else {
            minSlider.value = maxValue;
            minValue = maxValue;
        }
    }

    const min = parseFloat(minSlider.min || 0);
    const max = parseFloat(maxSlider.max || 1);

    const minPercent = ((minValue - min) * 100) / Math.max(max - min, 1);
    const maxPercent = ((maxValue - min) * 100) / Math.max(max - min, 1);

    minSlider.style.setProperty('--range-progress', minPercent + '%');
    maxSlider.style.setProperty('--range-progress', maxPercent + '%');

    const minLabel = document.getElementById('priceMinValue');
    const maxLabel = document.getElementById('priceMaxValue');
    if (minLabel) {
        minLabel.textContent = Math.round(minValue) + '€';
    }
    if (maxLabel) {
        maxLabel.textContent = Math.round(maxValue) + '€';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updatePriceRange();

    const filterForm = document.getElementById('filterForm');
    const bounds = filterForm ? JSON.parse(filterForm.dataset.priceBounds || '{}') : {};

    function applyPriceUnitBounds(unit) {
        const minSlider = document.getElementById('min_price');
        const maxSlider = document.getElementById('max_price');
        const unitBounds = bounds[unit];

        if (!minSlider || !maxSlider || !unitBounds) {
            return;
        }

        minSlider.min = unitBounds.min;
        minSlider.max = unitBounds.max;
        maxSlider.min = unitBounds.min;
        maxSlider.max = unitBounds.max;

        minSlider.value = unitBounds.min;
        maxSlider.value = unitBounds.max;
        updatePriceRange();
    }

    document.querySelectorAll('input[name="price_unit"]').forEach(radio => {
        radio.addEventListener('change', () => {
            applyPriceUnitBounds(radio.value);
        });
    });

    // Toggle filter tags
    document.querySelectorAll('.filter-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('active', checkbox.checked);
        });
    });

    // Remove active filter tags
    document.querySelectorAll('.active-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const filterName = this.dataset.filter;
            const filterValue = this.dataset.value;
            // Find matching checkbox and uncheck it
            const form = document.getElementById('filterForm');
            const checkboxes = form.querySelectorAll(`input[name="${filterName}[]"][value="${filterValue}"], input[name="${filterName}"][value="${filterValue}"]`);
            checkboxes.forEach(cb => { cb.checked = false; });
            // Auto submit
            form.submit();
        });
    });
});
</script>

</div>
@endsection
