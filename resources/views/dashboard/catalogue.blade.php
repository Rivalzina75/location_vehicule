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
            <h2>{{ __('Filtrer') }}</h2>

            <form method="GET" action="{{ route('dashboard.catalogue') }}" id="filterForm" class="filters-form">
                <!-- Recherche par prix -->
                <div class="filter-group">
                    <label for="max_price">{{ __('Prix max (€/jour)') }}</label>
                    <input 
                        type="range" 
                        id="max_price" 
                        name="max_price" 
                        min="0" 
                        max="500" 
                        value="{{ request('max_price', 500) }}"
                        class="range-slider"
                        oninput="updateMaxPrice(this)"
                    >
                    <div class="price-display">
                        <span id="priceValue">{{ request('max_price', 500) }}€</span>
                    </div>
                </div>

                <!-- Filtrer par type de véhicule -->
                <div class="filter-group">
                    <label for="type">{{ __('Type de véhicule') }}</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">{{ __('Tous les types') }}</option>
                        @foreach($types as $typeKey => $typeName)
                            <option value="{{ $typeKey }}" @selected(request('type') === $typeKey)>
                                {{ $typeName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtrer par transmission -->
                <div class="filter-group">
                    <label for="transmission">{{ __('Transmission') }}</label>
                    <select name="transmission" id="transmission" class="form-control">
                        <option value="">{{ __('Toutes les transmissions') }}</option>
                        <option value="manual" @selected(request('transmission') === 'manual')>{{ __('Manuelle') }}</option>
                        <option value="automatic" @selected(request('transmission') === 'automatic')>{{ __('Automatique') }}</option>
                    </select>
                </div>

                <!-- Filtrer par carburant -->
                <div class="filter-group">
                    <label for="fuel_type">{{ __('Carburant') }}</label>
                    <select name="fuel_type" id="fuel_type" class="form-control">
                        <option value="">{{ __('Tous les carburants') }}</option>
                        <option value="gasoline" @selected(request('fuel_type') === 'gasoline')>{{ __('Essence') }}</option>
                        <option value="diesel" @selected(request('fuel_type') === 'diesel')>{{ __('Diesel') }}</option>
                        <option value="electric" @selected(request('fuel_type') === 'electric')>{{ __('Électrique') }}</option>
                        <option value="hybrid" @selected(request('fuel_type') === 'hybrid')>{{ __('Hybride') }}</option>
                    </select>
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
                                @if($vehicle->images && count($vehicle->images) > 0)
                                    <img 
                                        src="{{ asset('storage/' . $vehicle->images[0]) }}" 
                                        alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                                        class="vehicle-img"
                                    >
                                @else
                                    <div class="vehicle-img-placeholder">
                                        <span>{{ __('Pas d\'image') }}</span>
                                    </div>
                                @endif
                                
                                @if($vehicle->fuel_type)
                                    <div class="vehicle-badge fuel-{{ $vehicle->fuel_type }}">
                                        {{ ucfirst($vehicle->fuel_type) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Informations du véhicule -->
                            <div class="vehicle-info">
                                <h3 class="vehicle-title">
                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                </h3>
                                
                                <div class="vehicle-specs">
                                    <span class="spec">📅 {{ $vehicle->year }}</span>
                                    <span class="spec">👥 {{ $vehicle->seats }} places</span>
                                    <span class="spec">🚪 {{ $vehicle->doors }} portes</span>
                                    <span class="spec">⚙️ {{ $vehicle->transmission === 'manual' ? __('Manuelle') : __('Automatique') }}</span>
                                </div>

                                <!-- Caractéristiques optionnelles -->
                                @if($vehicle->child_seat_available || $vehicle->gps_available || $vehicle->bluetooth || $vehicle->air_conditioning)
                                    <div class="vehicle-features">
                                        @if($vehicle->child_seat_available)
                                            <span class="feature-badge">🪑 {{ __('Siège enfant') }}</span>
                                        @endif
                                        @if($vehicle->gps_available)
                                            <span class="feature-badge">📍 {{ __('GPS') }}</span>
                                        @endif
                                        @if($vehicle->bluetooth)
                                            <span class="feature-badge">🎵 {{ __('Bluetooth') }}</span>
                                        @endif
                                        @if($vehicle->air_conditioning)
                                            <span class="feature-badge">❄️ {{ __('Climatisation') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Tarification -->
                            <div class="vehicle-pricing">
                                <div class="price-item">
                                    <span class="price-label">Jour</span>
                                    <span class="price-value">{{ number_format($vehicle->price_per_day, 2, ',', ' ') }}€</span>
                                </div>
                                @if($vehicle->price_per_week)
                                    <div class="price-item">
                                        <span class="price-label">Semaine</span>
                                        <span class="price-value">{{ number_format($vehicle->price_per_week, 2, ',', ' ') }}€</span>
                                    </div>
                                @endif
                                @if($vehicle->price_per_month)
                                    <div class="price-item">
                                        <span class="price-label">Mois</span>
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
                        {{ $vehicles->appends(request()->query())->links() }}
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
function updateMaxPrice(input) {
    const value = parseFloat(input.value || 0);
    const min = parseFloat(input.min || 0);
    const max = parseFloat(input.max || 1);
    const percent = ((value - min) * 100) / Math.max(max - min, 1);

    const priceLabel = document.getElementById('priceValue');
    if (priceLabel) {
        priceLabel.textContent = value + '€';
    }

    input.style.setProperty('--range-progress', percent + '%');
}

document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('max_price');
    if (slider) {
        updateMaxPrice(slider);
    }
});
</script>

<style>
.catalogue-wrapper {
    padding: 0;
    max-width: 100%;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.filters-sidebar {
    background: var(--bg-secondary);
    padding: 1.5rem;
    border-radius: 0.5rem;
    height: fit-content;
    position: sticky;
    top: 80px;
}

.filters-sidebar h2 {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--accent);
    padding-bottom: 0.5rem;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
}

.filter-group select,
.filter-group input:not([type="range"]) {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

.filter-group select option {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
}

.filter-group select:focus,
.filter-group input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.1);
}

.range-slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 18px;
    border-radius: 999px;
    background: transparent;
    cursor: pointer;
    display: block;
}

.range-slider:focus {
    outline: none;
}

.range-slider::-webkit-slider-runnable-track {
    height: 6px;
    border-radius: 999px;
    background: linear-gradient(
        90deg,
        var(--accent) 0,
        var(--accent) var(--range-progress, 100%),
        #cbd5e1 var(--range-progress, 100%),
        #cbd5e1 100%
    );
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.15);
}

.range-slider::-moz-range-track {
    height: 6px;
    border-radius: 999px;
    background: linear-gradient(
        90deg,
        var(--accent) 0,
        var(--accent) var(--range-progress, 100%),
        #cbd5e1 var(--range-progress, 100%),
        #cbd5e1 100%
    );
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.15);
}

.range-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--accent);
    border: 2px solid var(--bg-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    margin-top: -5px; /* center thumb on 6px track */
}

.range-slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--accent);
    border: 2px solid var(--bg-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    margin-top: -5px; /* center thumb on 6px track */
}

.price-display {
    text-align: center;
    margin-top: 0.5rem;
    font-weight: 600;
    color: var(--accent);
}

.filter-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 2rem;
}

.btn-full {
    width: 100%;
    padding: 0.75rem;
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary.btn-full {
    background: var(--accent);
    color: white;
}

.btn-primary.btn-full:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
}

.btn-secondary.btn-full {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary.btn-full:hover {
    background: var(--bg-secondary);
}

.vehicles-grid-wrapper {
    display: flex;
    flex-direction: column;
}

.vehicles-info {
    margin-bottom: 1.5rem;
}

.vehicles-count {
    color: var(--text-secondary);
}

.vehicles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.vehicle-card {
    background: var(--bg-secondary);
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.vehicle-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: var(--accent);
}

.vehicle-image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: var(--bg-tertiary);
}

.vehicle-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vehicle-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
}

.vehicle-badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: var(--accent);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.vehicle-info {
    padding: 1rem;
    flex-grow: 1;
}

.vehicle-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
}

.vehicle-specs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
}

.spec {
    display: inline-flex;
    align-items: center;
    background: var(--bg-tertiary);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.vehicle-features {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.feature-badge {
    font-size: 0.8rem;
    background: rgba(var(--accent-rgb), 0.15);
    color: var(--accent);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    white-space: nowrap;
}

.vehicle-pricing {
    padding: 0 1rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}

.price-item {
    text-align: center;
}

.price-label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}

.price-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--accent);
}

.vehicle-actions {
    padding: 1rem;
    padding-top: 0;
}

.no-results {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
}

.no-results-content {
    text-align: center;
    color: var(--text-secondary);
}

.no-results-content h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

@media (max-width: 1024px) {
    .catalogue-wrapper {
        grid-template-columns: 1fr;
    }
    .filters-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .catalogue-container {
        padding: 1rem;
    }
    .page-header h1 {
        font-size: 1.8rem;
    }
    .vehicles-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .vehicles-grid {
        grid-template-columns: 1fr;
    }
}

/* Pagination Laravel */
.pagination-wrapper nav {
    display: flex;
    justify-content: center;
    align-items: center;
}

.pagination-wrapper .flex {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.pagination-wrapper a,
.pagination-wrapper span {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    color: var(--text-primary);
    text-decoration: none;
    transition: all 0.2s;
    background: var(--bg-secondary);
}

.pagination-wrapper a:hover {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

.pagination-wrapper .relative {
    display: flex;
    gap: 0.5rem;
}

.pagination-wrapper [aria-current="page"] {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
    font-weight: 600;
}

.pagination-wrapper [aria-disabled="true"] {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
</div>
@endsection
