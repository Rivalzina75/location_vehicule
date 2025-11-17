@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        {{ __('Vérifiez votre adresse e-mail') }}
    </div>

    <div class="auth-body">
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}
            </div>
        @endif

        <p style="text-align: center; margin-bottom: 1.5rem; color: var(--text-secondary);">
            {{ __('Avant de continuer, veuillez vérifier votre boîte de réception pour un lien de vérification.') }}
            <br><br>
            {{ __('Si vous n\'avez pas reçu l\'e-mail') }} :
        </p>
        
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <div class="form-button-container" style="margin-top: 0;">
                <button type="submit" class="btn-primary">
                    {{ __('Cliquez ici pour en recevoir un autre') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection