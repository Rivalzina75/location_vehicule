@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="8">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Gestion Vehicules') }}</h1>
            <p class="page-subtitle">{{ __('Suivi du parc automobile') }}</p>
        </div>
        <a class="btn-primary" href="{{ route('admin.vehicles.create') }}">{{ __('Ajouter un vehicule') }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card-section">
        <h2 class="section-title">{{ __('Filtres') }}</h2>
        <form method="GET" action="{{ route('admin.vehicles.index') }}" class="filters-form">
            <div class="filter-group">
                <label for="status">{{ __('Statut') }}</label>
                <select id="status" name="status">
                    <option value="">{{ __('Tous') }}</option>
                    <option value="available" @selected(request('status') === 'available')>available</option>
                    <option value="rented" @selected(request('status') === 'rented')>rented</option>
                    <option value="maintenance" @selected(request('status') === 'maintenance')>maintenance</option>
                    <option value="unavailable" @selected(request('status') === 'unavailable')>unavailable</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="type">{{ __('Type') }}</label>
                <select id="type" name="type">
                    <option value="">{{ __('Tous') }}</option>
                    @foreach(\App\Models\Vehicle::types() as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}" @selected(request('type') === $typeKey)>{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="search">{{ __('Recherche') }}</label>
                <input id="search" name="search" type="text" placeholder="Marque, modele, immatriculation" value="{{ request('search') }}">
            </div>
            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">{{ __('Filtrer') }}</button>
                <a class="btn btn-secondary" href="{{ route('admin.vehicles.index') }}">{{ __('Reinitialiser') }}</a>
            </div>
        </form>
    </div>

    <div class="card-section">
        <h2 class="section-title">{{ __('Vehicules') }}</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Vehicule') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Transmission') }}</th>
                        <th>{{ __('Statut') }}</th>
                        <th>{{ __('Tarif') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_number }})</td>
                            <td>{{ $vehicle->type_label }}</td>
                            <td>{{ $vehicle->transmission_label }}</td>
                            <td>{{ $vehicle->status }}</td>
                            <td>{{ number_format($vehicle->price_per_day, 2, ',', ' ') }} € / j</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-secondary" href="{{ route('admin.vehicles.show', $vehicle->id) }}">{{ __('Voir') }}</a>
                                    <a class="btn btn-secondary" href="{{ route('admin.vehicles.edit', $vehicle->id) }}">{{ __('Editer') }}</a>
                                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" onsubmit="return confirm('Supprimer ce vehicule ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">{{ __('Supprimer') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('Aucun vehicule trouve.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $vehicles->links() }}
        </div>
    </div>
</div>
@endsection
