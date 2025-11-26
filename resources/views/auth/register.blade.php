@extends('layouts.app')

@section('content')
<div class="auth-container auth-container-wide">
    <div class="auth-header">
        {{ __('Inscription') }}
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <!-- Nom & Prénom -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">{{ __('Prénom') }} *</label>
                    <input id="first_name" type="text" 
                           name="first_name" 
                           value="{{ old('first_name') }}" 
                           required 
                           autocomplete="given-name" 
                           autofocus
                           placeholder="Jean"
                           class="form-control @error('first_name') is-invalid @enderror">
                    @error('first_name')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="last_name">{{ __('Nom de famille') }} *</label>
                    <input id="last_name" type="text" 
                           name="last_name" 
                           value="{{ old('last_name') }}" 
                           required 
                           autocomplete="family-name"
                           placeholder="Dupont"
                           class="form-control @error('last_name') is-invalid @enderror">
                    @error('last_name')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">{{ __('Adresse Email') }} *</label>
                <input id="email" type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="email"
                       placeholder="jean.dupont@email.com"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <!-- Date de naissance & Téléphone -->
            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">{{ __('Date de naissance') }} *</label>
                    <input id="date_of_birth" type="text" 
                           name="date_of_birth" 
                           value="{{ old('date_of_birth') }}" 
                           autocomplete="bday"
                           class="form-control @error('date_of_birth') is-invalid @enderror"
                           placeholder="JJ/MM/AAAA" 
                           maxlength="10">
                    @error('date_of_birth')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">{{ __('Numéro de téléphone') }} *</label>
                    <input id="phone_number" type="text" 
                           name="phone_number" 
                           value="{{ old('phone_number') }}" 
                           autocomplete="tel"
                           placeholder="06 12 34 56 78"
                           class="form-control @error('phone_number') is-invalid @enderror">
                    @error('phone_number')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <!-- Adresse -->
            <div class="form-group">
                <label for="address_line1">{{ __('Adresse') }} *</label>
                <input id="address_line1" type="text" 
                       name="address_line1" 
                       value="{{ old('address_line1') }}" 
                       autocomplete="address-line1"
                       placeholder="123 Rue de la République"
                       class="form-control @error('address_line1') is-invalid @enderror">
                @error('address_line1')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <!-- Code postal & Ville -->
            <div class="form-row">
                <div class="form-group">
                    <label for="postal_code">{{ __('Code postal') }} *</label>
                    <input id="postal_code" type="text" 
                           name="postal_code" 
                           value="{{ old('postal_code') }}" 
                           autocomplete="postal-code"
                           placeholder="75001"
                           maxlength="5"
                           class="form-control @error('postal_code') is-invalid @enderror">
                    @error('postal_code')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="city">{{ __('Ville') }} *</label>
                    <input id="city" type="text" 
                           name="city" 
                           value="{{ old('city') }}" 
                           autocomplete="address-level2"
                           placeholder="Paris"
                           class="form-control @error('city') is-invalid @enderror">
                    @error('city')
                        <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>
            
            <hr>

            <!-- Mots de passe -->
            <div class="form-row">
                <div class="form-group">
                    <label for="password">{{ __('Mot de passe') }} *</label>
                    <div class="password-wrapper">
                        <input id="password" type="password" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               placeholder="{{ __('Min. 14 caractères') }}"
                               class="form-control @error('password') is-invalid @enderror">
                    </div>
                    @error('password')
                        <div class="error-message" role="alert"><strong>{!! $message !!}</strong></div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">{{ __('Confirmer le mot de passe') }} *</label>
                    <div class="password-wrapper">
                        <input id="password-confirm" type="password" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               placeholder="{{ __('Confirmez votre mot de passe') }}"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    {{ __('S\'inscrire') }}
                </button>
            </div>
            
            <div class="auth-links">
                <a href="{{ route('login') }}">
                    {{ __('Déjà un compte ? Se connecter') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection