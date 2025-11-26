@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        {{ __('Changer le mot de passe') }}
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">{{ __('Adresse Email') }}</label>
                <input id="email" type="email" 
                       name="email" 
                       value="{{ $email ?? old('email') }}" 
                       required 
                       autocomplete="email" 
                       autofocus
                       class="form-control @error('email') is-invalid @enderror">

                @error('email')
                    <div class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('Nouveau mot de passe') }}</label>
                <div class="password-wrapper">
                    <input id="password" type="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="{{ __('Min. 14 caractères') }}"
                           class="form-control @error('password') is-invalid @enderror">
                </div>
                @error('password')
                    <div class="error-message" role="alert">
                        <strong>{!! $message !!}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm">{{ __('Confirmer le nouveau mot de passe') }}</label>
                <div class="password-wrapper">
                    <input id="password-confirm" type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="{{ __('Confirmez votre mot de passe') }}"
                           class="form-control">
                </div>
            </div>

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    {{ __('Réinitialiser le mot de passe') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection