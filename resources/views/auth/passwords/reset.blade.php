@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        Changer le mot de passe
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input id="email" type="email" 
                       name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                       class="form-control @error('email') is-invalid @enderror">

                @error('email')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input id="password" type="password" 
                       name="password" required autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror">

                @error('password')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm">Confirmer le nouveau mot de passe</label>
                <input id="password-confirm" type="password" 
                       name="password_confirmation" required autocomplete="new-password"
                       class="form-control">
            </div>

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    Réinitialiser le mot de passe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection