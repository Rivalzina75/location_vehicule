@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="9">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Gestion Reservations') }}</h1>
            <p class="page-subtitle">{{ __('Suivi et pilotage des reservations') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">{{ __('En attente') }}</div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ __('Confirmees') }}</div>
                <div class="stat-value">{{ $stats['confirmed'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ __('Actives') }}</div>
                <div class="stat-value">{{ $stats['active'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ __('En retard') }}</div>
                <div class="stat-value">{{ $stats['late'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ __('Revenu total') }}</div>
                <div class="stat-value">{{ number_format($stats['total_revenue'], 2, ',', ' ') }} €</div>
            </div>
        </div>
    </div>

    <div class="card-section">
        <h2 class="section-title">{{ __('Filtres') }}</h2>
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="filters-form">
            <div class="filter-group">
                <label for="status">{{ __('Statut') }}</label>
                <select id="status" name="status">
                    <option value="">{{ __('Tous') }}</option>
                    <option value="pending" @selected(request('status') === 'pending')>pending</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>confirmed</option>
                    <option value="active" @selected(request('status') === 'active')>active</option>
                    <option value="late" @selected(request('status') === 'late')>late</option>
                    <option value="completed" @selected(request('status') === 'completed')>completed</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>cancelled</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="date_from">{{ __('Du') }}</label>
                <input id="date_from" name="date_from" type="date" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label for="date_to">{{ __('Au') }}</label>
                <input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}">
            </div>
            <div class="filter-group">
                <label for="search">{{ __('Recherche') }}</label>
                <input id="search" name="search" type="text" placeholder="Nom, email, vehicule" value="{{ request('search') }}">
            </div>
            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">{{ __('Filtrer') }}</button>
                <a class="btn btn-secondary" href="{{ route('admin.reservations.index') }}">{{ __('Reinitialiser') }}</a>
            </div>
        </form>
    </div>

    <div class="card-section">
        <h2 class="section-title">{{ __('Reservations') }}</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Vehicule') }}</th>
                        <th>{{ __('Dates') }}</th>
                        <th>{{ __('Statut') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->confirmation_code }}</td>
                            <td>
                                <div>{{ $reservation->user->first_name }} {{ $reservation->user->last_name }}</div>
                                <div class="text-muted">{{ $reservation->user->email }}</div>
                            </td>
                            <td>{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</td>
                            <td>{{ $reservation->start_date->format('d/m/Y') }} - {{ $reservation->end_date->format('d/m/Y') }}</td>
                            <td>{{ $reservation->status_label }}</td>
                            <td>{{ number_format($reservation->total_price, 2, ',', ' ') }} €</td>
                            <td>
                                <div class="btn-group">
                                    @if($reservation->status === 'pending')
                                        <form method="POST" action="{{ route('admin.reservations.confirm', $reservation->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-primary" type="submit">{{ __('Confirmer') }}</button>
                                        </form>
                                    @endif

                                    @if($reservation->status === 'confirmed')
                                        <form method="POST" action="{{ route('admin.reservations.start', $reservation->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-secondary" type="submit">{{ __('Demarrer') }}</button>
                                        </form>
                                    @endif

                                    @if(in_array($reservation->status, ['active', 'late']))
                                        <form method="POST" action="{{ route('admin.reservations.complete', $reservation->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="mileage_end" min="{{ $reservation->mileage_start ?? 0 }}" placeholder="Km fin" required>
                                            <input type="number" step="0.01" name="damage_cost" placeholder="Degats">
                                            <button class="btn btn-secondary" type="submit">{{ __('Terminer') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">{{ __('Aucune reservation trouvee.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection
