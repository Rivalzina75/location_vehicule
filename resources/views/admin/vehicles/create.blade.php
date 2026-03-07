@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="8">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Nouveau Vehicule') }}</h1>
            <p class="page-subtitle">{{ __('Ajouter un vehicule au parc') }}</p>
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

    <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" class="form-card">
        @csrf

        @include('admin.vehicles.form', ['vehicle' => null])

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ __('Enregistrer') }}</button>
            <a class="btn btn-secondary" href="{{ route('admin.vehicles.index') }}">{{ __('Annuler') }}</a>
        </div>
    </form>
</div>
@endsection
