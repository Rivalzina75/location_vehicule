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
                {{-- La phrase entière est maintenant gérée par Laravel --}}
                    {{ __('countdown_message', ['seconds' => session('lockout_time')]) }}
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

    {{-- SCRIPT DU COMPTEUR (À placer à la fin de login.blade.php) --}}
@if (session('lockout_time') && session('lockout_time') > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // On récupère le temps initial
            let seconds = {{ session('lockout_time') }};
            
            const countdownElement = document.getElementById('countdown-timer');
            const submitButton = document.querySelector('form button[type="submit"]');

            // --- C'EST ICI QUE ÇA CHANGE ---
            // On prépare les phrases traduites (Laravel remplace les clés par le texte)
            // 'XX' est notre repère pour savoir où mettre le chiffre
            let messageTemplate = "{{ __('countdown_message', ['seconds' => 'XX']) }}";
            let completeMessage = "{{ __('lockout_complete') }}";

            // Désactivation du bouton
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.style.opacity = '0.5';
                submitButton.style.cursor = 'not-allowed';
            }

            const interval = setInterval(() => {
                seconds--; 
                
                // Mise à jour du texte pendant le décompte
                if (countdownElement && seconds > 0) {
                    // On remplace 'XX' par le vrai nombre de secondes
                    countdownElement.textContent = messageTemplate.replace('XX', seconds);
                }

                // Quand c'est fini
                if (seconds <= 0) {
                    clearInterval(interval); 
                    
                    if (countdownElement) {
                        // On affiche le message de fin traduit
                        countdownElement.textContent = completeMessage;
                        
                        // Style vert (succès)
                        countdownElement.style.color = '#28a745'; 
                        countdownElement.style.backgroundColor = '#e9f7ea';
                    }
                    
                    // Réactivation du bouton
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.style.opacity = '1';
                        submitButton.style.cursor = 'pointer';
                    }
                    
                }
            }, 1000);
        });
    </script>
@endif
@endsection