@extends('layouts.app')

@section('content')
<div class="catalogue-page" style="padding-top: 100px; min-height: 100vh; background: #f8fafc;">
    <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
        
        <!-- Header -->
        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 36px; font-weight: 800; color: #1a1a2e; margin-bottom: 12px;">
                🚗 {{ __('Catalogue de véhicules') }}
            </h1>
            <p style="font-size: 16px; color: #64748b;">
                Découvrez notre flotte de {{ $vehicles->total() }} véhicules disponibles
            </p>
        </div>

        <!-- Filtres -->
        <div style="background: white; border-radius: 16px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <form method="GET" action="{{ route('catalogue.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                
                <!-- Type de véhicule -->
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                        Type de véhicule
                    </label>
                    <select name="type" class="form-control" style="width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Transmission -->
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                        Transmission
                    </label>
                    <select name="transmission" class="form-control">
                        <option value="">Toutes</option>
                        <option value="automatique" {{ request('transmission') == 'automatique' ? 'selected' : '' }}>Automatique</option>
                        <option value="manuelle" {{ request('transmission') == 'manuelle' ? 'selected' : '' }}>Manuelle</option>
                    </select>
                </div>

                <!-- Prix maximum -->
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                        Prix max/jour
                    </label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           placeholder="Ex: 100" class="form-control" step="10">
                </div>

                <!-- Tri -->
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                        Trier par
                    </label>
                    <select name="sort" class="form-control">
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 12px 20px;">
                        🔍 Rechercher
                    </button>
                    <a href="{{ route('catalogue.index') }}" class="btn-secondary" style="flex: 1; padding: 12px 20px; text-align: center; text-decoration: none;">
                        ↻ Réinitialiser
                    </a>
                </div>

            </form>
        </div>

        <!-- Grille de véhicules -->
        @if($vehicles->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; margin-bottom: 40px;">
                @foreach($vehicles as $vehicle)
                    <div class="vehicle-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;" 
                         onclick="window.location='{{ route('catalogue.show', $vehicle->id) }}'">
                        
                        <!-- Image -->
                        <div style="height: 200px; background: linear-gradient(135deg, #f8fafc, #e2e8f0); display: flex; align-items: center; justify-content: center; font-size: 4rem; position: relative;">
                            @if($vehicle->main_image)
                                <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->full_name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <!-- Emoji par type -->
                                @switch($vehicle->type)
                                    @case('berline') 🚗 @break
                                    @case('suv') 🚙 @break
                                    @case('moto') 🏍️ @break
                                    @case('scooter') 🛵 @break
                                    @case('camionnette') 🚐 @break
                                    @case('camion') 🚚 @break
                                    @default 🚗
                                @endswitch
                            @endif
                            
                            <!-- Badge statut -->
                            <div style="position: absolute; top: 12px; right: 12px; background: #00d9a5; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                ✓ Disponible
                            </div>
                        </div>

                        <!-- Infos -->
                        <div style="padding: 20px;">
                            <!-- Titre -->
                            <h3 style="font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px;">
                                {{ $vehicle->full_name }}
                            </h3>

                            <!-- Type -->
                            <div style="display: inline-block; background: #f8fafc; color: #64748b; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-bottom: 16px;">
                                {{ $types[$vehicle->type] ?? $vehicle->type }}
                            </div>

                            <!-- Caractéristiques -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                                @if($vehicle->seats)
                                <div style="text-align: center;">
                                    <div style="font-size: 20px;">👥</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">{{ $vehicle->seats }} places</div>
                                </div>
                                @endif

                                <div style="text-align: center;">
                                    <div style="font-size: 20px;">⚙️</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">{{ ucfirst($vehicle->transmission) }}</div>
                                </div>

                                <div style="text-align: center;">
                                    <div style="font-size: 20px;">⛽</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">{{ ucfirst($vehicle->fuel_type) }}</div>
                                </div>
                            </div>

                            <!-- Prix -->
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 28px; font-weight: 800; color: #e94560;">
                                        {{ number_format($vehicle->price_per_day, 0) }}€
                                    </div>
                                    <div style="font-size: 12px; color: #94a3b8;">par jour</div>
                                </div>
                                
                                <button class="btn-primary" style="padding: 10px 20px; font-size: 14px;" 
                                        onclick="event.stopPropagation(); window.location='{{ route('catalogue.show', $vehicle->id) }}'">
                                    Réserver →
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="display: flex; justify-content: center; margin-top: 40px;">
                {{ $vehicles->links() }}
            </div>

        @else
            <!-- Aucun résultat -->
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🔍</div>
                <h2 style="font-size: 24px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px;">
                    Aucun véhicule trouvé
                </h2>
                <p style="color: #64748b; margin-bottom: 24px;">
                    Essayez de modifier vos filtres de recherche
                </p>
                <a href="{{ route('catalogue.index') }}" class="btn-primary" style="display: inline-block; padding: 12px 24px; text-decoration: none;">
                    Voir tous les véhicules
                </a>
            </div>
        @endif

    </div>
</div>

<style>
.vehicle-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.pagination {
    display: flex;
    gap: 8px;
    list-style: none;
    padding: 0;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    padding: 10px 16px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: #1a1a2e;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    background: #f8fafc;
    border-color: #e94560;
    color: #e94560;
}

.pagination .active .page-link {
    background: #e94560;
    border-color: #e94560;
    color: white;
}
</style>
@endsection