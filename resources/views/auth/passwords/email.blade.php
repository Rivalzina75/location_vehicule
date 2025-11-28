@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        {{ __('Réinitialiser le mot de passe') }}
    </div>

    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <p style="text-align: center; margin-bottom: 1.5rem; color: var(--text-secondary); font-size: 0.95rem;">
            {{ __('Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.') }}
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">{{ __('Adresse Email') }}</label>
                <input id="email" type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="email" 
                       autofocus
                       placeholder="{{ __('Entrez votre adresse email') }}"
                       class="form-control @error('email') is-invalid @enderror">

                @error('email')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    {{ __('Envoyer le lien de réinitialisation') }}
                </button>
            </div>

            <div class="auth-links">
                <a href="{{ route('login') }}">
                    ← {{ __('Retour à la connexion') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection