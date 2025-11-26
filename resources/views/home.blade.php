@extends('layouts.app')

@section('content')
<div class="welcome-container">
    <div style="font-size: 4rem; margin-bottom: 1rem;">🚗</div>
    <h1>{{ __('Bienvenue chez Machina') }}</h1>
    <p>{{ __('Votre solution de location de véhicules, simple et rapide.') }}</p>

    @auth
        <div class="welcome-buttons">
            <a href="{{ route('dashboard') }}" class="btn-welcome btn-connexion">
                {{ __('Aller à mon tableau de bord') }} →
            </a>
        </div>
    @else
        <div class="welcome-buttons" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('login') }}" class="btn-welcome btn-connexion">
                {{ __('Se connecter') }}
            </a>
            <a href="{{ route('register') }}" class="btn-welcome" style="background: transparent; border: 2px solid var(--accent); color: var(--accent);">
                {{ __('S\'inscrire') }}
            </a>
        </div>
    @endauth
</div>
@endsection