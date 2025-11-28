@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        {{ __('Connexion') }}
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Hidden data for JavaScript lockout countdown --}}
            <input type="hidden" id="lockout-until" value="{{ session('lockout_until', 0) }}">
            <input type="hidden" id="lockout-translations" 
                   data-countdown="{{ __('countdown_message') }}" 
                   data-complete="{{ __('lockout_complete') }}">

            <div class="form-group">
                <label for="email">{{ __('Adresse Email') }}</label>
                <input id="email" type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="email" 
                       autofocus
                       placeholder="{{ __('Entrez votre adresse email') }}"
                       class="form-control @if(session('lockout_until') || $errors->has('email')) is-invalid @endif">
                {{-- Only show error if NOT in lockout mode --}}
                @if(!session('lockout_until') && $errors->has('email'))
                    @error('email')
                        <div class="error-message" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                @endif
            </div>

            <div class="form-group">
                <label for="password">{{ __('Mot de passe') }}</label>
                <input id="password" type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="{{ __('Entrez votre mot de passe') }}"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">
                    {{ __('Se souvenir de moi') }}
                </label>
            </div>

            {{-- Countdown timer - managed by JavaScript --}}
            <div id="countdown-timer" class="countdown-timer" style="display: none;"></div>

            <div class="form-button-container">
                <button type="submit" class="btn-primary" id="login-btn">
                    {{ __('Se connecter') }}
                </button>
            </div>

            <div class="auth-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
                <br>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" style="margin-top: 10px; display: inline-block;">
                        {{ __('Créer un compte') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection