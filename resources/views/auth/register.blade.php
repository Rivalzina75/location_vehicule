@extends('layouts.app')

@section('content')
<div class="auth-container auth-container-wide">
    <div class="auth-header">
        Inscription
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input id="first_name" type="text" 
                           name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" autofocus
                           class="form-control @error('first_name') is-invalid @enderror">
                    @error('first_name')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="last_name">Nom de famille</label>
                    <input id="last_name" type="text" 
                           name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name"
                           class="form-control @error('last_name') is-invalid @enderror">
                    @error('last_name')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input id="email" type="email" 
                       name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date de naissance</label>
                    <input id="date_of_birth" type="text" 
                           name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="bday"
                           class="form-control @error('date_of_birth') is-invalid @enderror"
                           placeholder="JJ/MM/AAAA">
                    @error('date_of_birth')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">Numéro de téléphone</label>
                    <input id="phone_number" type="text" 
                           name="phone_number" value="{{ old('phone_number') }}" required autocomplete="tel"
                           class="form-control @error('phone_number') is-invalid @enderror">
                    @error('phone_number')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address_line1">Adresse</label>
                <input id="address_line1" type="text" 
                       name="address_line1" value="{{ old('address_line1') }}" required autocomplete="address-line1"
                       class="form-control @error('address_line1') is-invalid @enderror">
                @error('address_line1')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="postal_code">Code postal</label>
                    <input id="postal_code" type="text" 
                           name="postal_code" value="{{ old('postal_code') }}" required autocomplete="postal-code"
                           class="form-control @error('postal_code') is-invalid @enderror">
                    @error('postal_code')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="city">Ville</label>
                    <input id="city" type="text" 
                           name="city" value="{{ old('city') }}" required autocomplete="address-level2"
                           class="form-control @error('city') is-invalid @enderror">
                    @error('city')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>
            
            <hr style="margin: 25px 0 10px 0; border: 0; border-top: 1px solid #eee;">

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" 
                           name="password" required autocomplete="new-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Confirmer le mot de passe</label>
                    <input id="password-confirm" type="password" 
                           name="password_confirmation" required autocomplete="new-password"
                           class="form-control">
                </div>
            </div>

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    S'inscrire
                </button>
            </div>
            
            <div class="auth-links">
                <a href="{{ route('login') }}">
                    Déjà un compte ? Se connecter
                </a>
            </div>
        </form>
    </div>
</div>
@endsection