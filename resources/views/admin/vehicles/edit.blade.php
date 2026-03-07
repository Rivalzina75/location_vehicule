@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="8">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Modifier Vehicule') }}</h1>
            <p class="page-subtitle">{{ __('Mettre a jour les informations') }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')

        @include('admin.vehicles.form', ['vehicle' => $vehicle])

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ __('Mettre a jour') }}</button>
            <a class="btn btn-secondary" href="{{ route('admin.vehicles.index') }}">{{ __('Annuler') }}</a>
        </div>
    </form>
</div>
@endsection
