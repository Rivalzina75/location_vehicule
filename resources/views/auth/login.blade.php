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
@endsection