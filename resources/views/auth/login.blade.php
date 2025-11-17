@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        {{ __('Connexion') }}
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">{{ __('Adresse Email') }}</label>
                <input id="email" type="email" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('Mot de passe') }}</label>
                <input id="password" type="password" 
                       name="password" required autocomplete="current-password"
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

            @if (session('lockout_time') && session('lockout_time') > 0)
                <div id="countdown-timer" class="countdown-timer">
                    {{-- On garde le texte en dur ici car c'est un message technique --}}
                    Temps restant : <strong id="timer">{{ session('lockout_time') }}</strong> secondes.
                </div>
            @endif
            <div class="form-button-container">
                <button type="submit" class="btn-primary">
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

@if (session('lockout_time') && session('lockout_time') > 0)
    <script>
        // ... (votre script de compteur reste ici, il n'a pas besoin de traduction)
        let seconds = {{ session('lockout_time') }};
        const timerElement = document.getElementById('timer');
        const countdownElement = document.getElementById('countdown-timer');
        const submitButton = document.querySelector('form button[type="submit"]');

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.style.opacity = '0.5';
            submitButton.style.cursor = 'not-allowed';
        }

        const interval = setInterval(() => {
            seconds--; 
            if (timerElement) {
                timerElement.textContent = seconds; 
            }
            if (seconds <= 0) {
                clearInterval(interval); 
                if (countdownElement) {
                    countdownElement.textContent = "Vous pouvez réessayer de vous connecter.";
                    countdownElement.style.color = '#28a745'; 
                    countdownElement.style.backgroundColor = '#e9f7ea';
                }
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.style.opacity = '1';
                    submitButton.style.cursor = 'pointer';
                }
            }
        }, 1000);
    </script>
@endif
@endsection