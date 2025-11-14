@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        Connexion
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input id="email" type="email" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                       class="form-control @error('email') is-invalid @enderror">

                {{-- Le message d'erreur standard (ex: "Compte bloqué 30s") s'affiche ici --}}
                @error('email')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
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
                    Se souvenir de moi
                </label>
            </div>

            @if (session('lockout_time') && session('lockout_time') > 0)
                <div id="countdown-timer" class="countdown-timer">
                    Temps restant : <strong id="timer">{{ session('lockout_time') }}</strong> secondes.
                </div>
            @endif
            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    Se connecter
                </button>
            </div>

            <div class="auth-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
                <br>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" style="margin-top: 10px; display: inline-block;">
                        Créer un compte
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if (session('lockout_time') && session('lockout_time') > 0)
    <script>
        let seconds = {{ session('lockout_time') }};
        const timerElement = document.getElementById('timer');
        const countdownElement = document.getElementById('countdown-timer');
        const submitButton = document.querySelector('form button[type="submit"]');

        // Désactiver le bouton et le griser (en utilisant votre style)
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
                
                // Changer le message de blocage en message de succès
                if (countdownElement) {
                    countdownElement.textContent = "Vous pouvez réessayer de vous connecter.";
                    // Utilise la couleur verte de votre classe .btn-inscription
                    countdownElement.style.color = '#28a745'; 
                    countdownElement.style.backgroundColor = '#e9f7ea';
                }
                
                // Réactiver le bouton
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.style.opacity = '1';
                    submitButton.style.cursor = 'pointer';
                }
            }
        }, 1000); // 1 seconde
    </script>
@endif
@endsection