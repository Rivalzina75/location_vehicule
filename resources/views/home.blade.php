@extends('layouts.app')

@section('content')
<div class="welcome-container">
    {{-- MODIFIÉ --}}
    <h1>{{ __('Bienvenue chez Machina') }}</h1>
    <p>{{ __('Votre solution de location de véhicules, simple et rapide.') }}</p>

    @auth
        <div class="welcome-buttons" style="margin-top: 2rem;">
            {{-- MODIFIÉ --}}
            <a href="{{ route('dashboard') }}" class="btn-welcome btn-connexion">
                {{ __('Aller à mon tableau de bord') }}
            </a>
        </div>
    @endauth
</div>
@endsection