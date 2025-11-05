@extends('layouts.app')

@section('content')
<div class="welcome-container">
    <h1>Bienvenue chez Machina</h1>
    <p>Votre solution de location de véhicules, simple et rapide.</p>

    @auth
        <div class_="welcome-buttons" style="margin-top: 2rem;">
            <a href="{{ route('dashboard') }}" class="btn-welcome btn-connexion">
                Aller à mon tableau de bord
            </a>
        </div>
    @endauth
</div>
@endsection