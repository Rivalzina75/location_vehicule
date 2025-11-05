@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        Réinitialiser le mot de passe
    </div>

    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

            <div class="form-button-container">
                <button type="submit" class="btn-primary">
                    Envoyer le lien de réinitialisation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection