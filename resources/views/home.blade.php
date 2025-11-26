@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">🚗 {{ __('Location de véhicules') }}</div>
        <h1 class="hero-title">{{ __('Bienvenue chez') }} <span class="text-accent">Machina</span></h1>
        <p class="hero-subtitle">{{ __('Votre solution de location de véhicules, simple et rapide. Des voitures, motos, scooters et utilitaires pour tous vos besoins.') }}</p>
        
        @auth
            <div class="hero-buttons">
                <a href="{{ route('dashboard') }}" class="btn-hero btn-primary-hero">
                    {{ __('Aller à mon tableau de bord') }} →
                </a>
            </div>
        @else
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-hero btn-primary-hero">
                    {{ __('Commencer maintenant') }}
                </a>
                <a href="{{ route('login') }}" class="btn-hero btn-outline-hero">
                    {{ __('Se connecter') }}
                </a>
            </div>
        @endauth

        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-number">50+</span>
                <span class="stat-label">{{ __('Véhicules') }}</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">1000+</span>
                <span class="stat-label">{{ __('Clients satisfaits') }}</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">24/7</span>
                <span class="stat-label">{{ __('Support') }}</span>
            </div>
        </div>
    </div>
</section>
@endsection