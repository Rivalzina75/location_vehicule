@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="8">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
            <p class="page-subtitle">{{ __('Details du vehicule') }}</p>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="{{ route('admin.vehicles.edit', $vehicle->id) }}">{{ __('Editer') }}</a>
            <a class="btn btn-secondary" href="{{ route('admin.vehicles.index') }}">{{ __('Retour') }}</a>
        </div>
    </div>

    <div class="card-section">
        <div class="details-grid">
            <div><strong>{{ __('Type') }}:</strong> {{ $vehicle->type_label }}</div>
            <div><strong>{{ __('Annee') }}:</strong> {{ $vehicle->year }}</div>
            <div><strong>{{ __('Transmission') }}:</strong> {{ $vehicle->transmission_label }}</div>
            <div><strong>{{ __('Carburant') }}:</strong> {{ $vehicle->fuel_type_label }}</div>
            <div><strong>{{ __('Places') }}:</strong> {{ $vehicle->seats }}</div>
            <div><strong>{{ __('Portes') }}:</strong> {{ $vehicle->doors ?? '-' }}</div>
            <div><strong>{{ __('Kilometrage') }}:</strong> {{ $vehicle->mileage ?? '-' }}</div>
            <div><strong>{{ __('Tarif') }}:</strong> {{ number_format($vehicle->price_per_day, 2, ',', ' ') }} € / j</div>
            <div><strong>{{ __('Statut') }}:</strong> {{ $vehicle->status }}</div>
        </div>
    </div>
</div>
@endsection
