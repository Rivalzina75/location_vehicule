@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        {{ __('Confirmer le mot de passe') }}
    </div>

    <div class="auth-body">
        <p style="text-align: center; margin-bottom: 1.5rem; color: var(--text-secondary);">
            {{ __('Veuillez confirmer votre mot de passe avant de continuer.') }}
        </p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

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

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    {{ __('Confirmer le mot de passe') }}
                </button>
            </div>

            <div class="auth-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection