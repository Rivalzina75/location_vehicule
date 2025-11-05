@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        Inscription
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input id="first_name" type="text" 
                       name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" autofocus
                       class="form-control @error('first_name') is-invalid @enderror">
                @error('first_name')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-group">
                <label for="last_name">Nom de famille</label>
                <input id="last_name" type="text" 
                       name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name"
                       class="form-control @error('last_name') is-invalid @enderror">
                @error('last_name')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input id="email" type="email" 
                       name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date de naissance</label>
                <input id="date_of_birth" type="text" 
                       name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="bday"
                       class="form-control @error('date_of_birth') is-invalid @enderror"
                       placeholder="JJ/MM/AAAA">
                @error('date_of_birth')
                    <div class="error-message" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone_number">Numéro de téléphone</label>
                <input id="phone_number" type="text" 
                       name="phone_number" value="{{ old('phone_number') }}" required autocomplete="tel"
                       class="form-control @error('phone_number') is-invalid @enderror