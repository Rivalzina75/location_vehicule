<div class="form-grid">
    <div class="form-group">
        <label for="type">{{ __('Type') }}</label>
        <select id="type" name="type" required>
            @foreach($types ?? \App\Models\Vehicle::types() as $typeKey => $typeLabel)
                <option value="{{ $typeKey }}" @selected(old('type', $vehicle->type ?? '') === $typeKey)>{{ $typeLabel }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="brand">{{ __('Marque') }}</label>
        <input id="brand" name="brand" type="text" value="{{ old('brand', $vehicle->brand ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="model">{{ __('Modele') }}</label>
        <input id="model" name="model" type="text" value="{{ old('model', $vehicle->model ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="year">{{ __('Annee') }}</label>
        <input id="year" name="year" type="number" value="{{ old('year', $vehicle->year ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="registration_number">{{ __('Immatriculation') }}</label>
        <input id="registration_number" name="registration_number" type="text" value="{{ old('registration_number', $vehicle->registration_number ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="transmission">{{ __('Transmission') }}</label>
        <select id="transmission" name="transmission" required>
            <option value="manual" @selected(old('transmission', $vehicle->transmission ?? '') === 'manual')>manual</option>
            <option value="automatic" @selected(old('transmission', $vehicle->transmission ?? '') === 'automatic')>automatic</option>
        </select>
    </div>

    <div class="form-group">
        <label for="fuel_type">{{ __('Carburant') }}</label>
        <select id="fuel_type" name="fuel_type" required>
            <option value="gasoline" @selected(old('fuel_type', $vehicle->fuel_type ?? '') === 'gasoline')>gasoline</option>
            <option value="diesel" @selected(old('fuel_type', $vehicle->fuel_type ?? '') === 'diesel')>diesel</option>
            <option value="electric" @selected(old('fuel_type', $vehicle->fuel_type ?? '') === 'electric')>electric</option>
            <option value="hybrid" @selected(old('fuel_type', $vehicle->fuel_type ?? '') === 'hybrid')>hybrid</option>
        </select>
    </div>

    <div class="form-group">
        <label for="seats">{{ __('Places') }}</label>
        <input id="seats" name="seats" type="number" value="{{ old('seats', $vehicle->seats ?? 5) }}" required>
    </div>

    <div class="form-group">
        <label for="doors">{{ __('Portes') }}</label>
        <input id="doors" name="doors" type="number" value="{{ old('doors', $vehicle->doors ?? '') }}">
    </div>

    <div class="form-group">
        <label for="mileage">{{ __('Kilometrage') }}</label>
        <input id="mileage" name="mileage" type="number" value="{{ old('mileage', $vehicle->mileage ?? '') }}">
    </div>

    <div class="form-group">
        <label for="price_per_day">{{ __('Prix par jour') }}</label>
        <input id="price_per_day" name="price_per_day" type="number" step="0.01" value="{{ old('price_per_day', $vehicle->price_per_day ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="price_per_week">{{ __('Prix par semaine') }}</label>
        <input id="price_per_week" name="price_per_week" type="number" step="0.01" value="{{ old('price_per_week', $vehicle->price_per_week ?? '') }}">
    </div>

    <div class="form-group">
        <label for="price_per_month">{{ __('Prix par mois') }}</label>
        <input id="price_per_month" name="price_per_month" type="number" step="0.01" value="{{ old('price_per_month', $vehicle->price_per_month ?? '') }}">
    </div>

    <div class="form-group">
        <label for="deposit">{{ __('Caution') }}</label>
        <input id="deposit" name="deposit" type="number" step="0.01" value="{{ old('deposit', $vehicle->deposit ?? '') }}">
    </div>

    <div class="form-group">
        <label for="status">{{ __('Statut') }}</label>
        <select id="status" name="status" required>
            <option value="available" @selected(old('status', $vehicle->status ?? '') === 'available')>available</option>
            <option value="rented" @selected(old('status', $vehicle->status ?? '') === 'rented')>rented</option>
            <option value="maintenance" @selected(old('status', $vehicle->status ?? '') === 'maintenance')>maintenance</option>
        </select>
    </div>

    <div class="form-group">
        <label for="image">{{ __('Image') }}</label>
        <input id="image" name="image" type="file" accept="image/*">
    </div>

    <div class="form-group">
        <label for="description">{{ __('Description') }}</label>
        <textarea id="description" name="description" rows="4">{{ old('description', $vehicle->description ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="child_seat_available" value="1" @checked(old('child_seat_available', $vehicle->child_seat_available ?? false))>
            {{ __('Siege enfant disponible') }}
        </label>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="gps_available" value="1" @checked(old('gps_available', $vehicle->gps_available ?? false))>
            {{ __('GPS disponible') }}
        </label>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="bluetooth" value="1" @checked(old('bluetooth', $vehicle->bluetooth ?? false))>
            {{ __('Bluetooth') }}
        </label>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="air_conditioning" value="1" @checked(old('air_conditioning', $vehicle->air_conditioning ?? false))>
            {{ __('Climatisation') }}
        </label>
    </div>
</div>
