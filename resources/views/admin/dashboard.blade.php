@extends('layouts.dashboard')

@section('content')
    <div class="page-transition" data-page-index="7">
        <section class="dashboard-page">
        <div class="page-header">
            <h1>{{ __('Admin Dashboard') }}</h1>
            <p>{{ __('Vue d ensemble des operations administratives.') }}</p>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Acces rapide') }}</h3>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('admin.vehicles.index') }}">
                        {{ __('Gestion Vehicules') }}
                    </a>
                    <a class="btn btn-secondary" href="{{ route('admin.reservations.index') }}">
                        {{ __('Gestion Reservations') }}
                    </a>
                </div>
            </div>
        </div>
        </section>
    </div>
@endsection
