@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="6">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Modifier mon profil') }}</h1>
            <p class="page-subtitle">{{ __('Modifiez vos informations personnelles') }}</p>
        </div>
        <a href="{{ route('dashboard.profile.show') }}" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.45rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex:0 0 auto;">
                <path d="M15 18l-6-6 6-6"/>
                <path d="M21 12H9"/>
            </svg>
            <span>{{ __('Retour') }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Personal Info Form -->
    <div class="card-section">
        <h2 class="section-title">👤 {{ __('Informations personnelles') }}</h2>
        <form action="{{ route('dashboard.profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="first_name" class="form-label">{{ __('Prénom') }} *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') form-error-border @enderror" 
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">{{ __('Nom') }} *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') form-error-border @enderror" 
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">{{ __('Email') }} *</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') form-error-border @enderror" 
                           value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="date_of_birth" class="form-label">{{ __('Date de naissance') }} *</label>
                    <input type="text" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') form-error-border @enderror" 
                           value="{{ old('date_of_birth', $user->date_of_birth) }}" placeholder="JJ/MM/AAAA" required>
                    @error('date_of_birth') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="phone_number" class="form-label">{{ __('Téléphone') }} *</label>
                    <input type="tel" name="phone_number" id="phone_number" class="form-control @error('phone_number') form-error-border @enderror" 
                           value="{{ old('phone_number', $user->phone_number) }}" placeholder="06 12 34 56 78" required>
                    @error('phone_number') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="address_line1" class="form-label">{{ __('Adresse') }} *</label>
                    <input type="text" name="address_line1" id="address_line1" class="form-control @error('address_line1') form-error-border @enderror" 
                           value="{{ old('address_line1', $user->address_line1) }}" required>
                    @error('address_line1') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="postal_code" class="form-label">{{ __('Code postal') }} *</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') form-error-border @enderror" 
                           value="{{ old('postal_code', $user->postal_code) }}" pattern="[0-9]{5}" required>
                    @error('postal_code') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="city" class="form-label">{{ __('Ville') }} *</label>
                    <input type="text" name="city" id="city" class="form-control @error('city') form-error-border @enderror" 
                           value="{{ old('city', $user->city) }}" required>
                    @error('city') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
            <button type="submit" class="btn-primary" style="margin-top:1.5rem">{{ __('Enregistrer les modifications') }}</button>
        </form>
    </div>

    <!-- Password Change Form -->
    <div class="card-section">
        <h2 class="section-title">🔒 {{ __('Changer le mot de passe') }}</h2>
        <form action="{{ route('dashboard.profile.password.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')
            <div class="form-grid-2" style="max-width:600px">
                <div class="form-group" style="grid-column: 1 / -1">
                    <label for="current_password" class="form-label">{{ __('Mot de passe actuel') }} *</label>
                    <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') form-error-border @enderror" required>
                    @error('current_password') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">{{ __('Nouveau mot de passe') }} *</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') form-error-border @enderror" required>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">{{ __('Confirmer') }} *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="margin-top:1.5rem">{{ __('Mettre à jour le mot de passe') }}</button>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="card-section danger-zone">
        <h2 class="section-title">⚠️ {{ __('Zone de danger') }}</h2>
        <p class="danger-desc">{{ __('La suppression de votre compte est irréversible. Toutes vos données seront perdues.') }}</p>
        <form action="{{ route('dashboard.profile.destroy') }}" method="POST" 
              onsubmit="return confirm('{{ __('Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est irréversible.') }}')">
            @csrf
            @method('DELETE')
            <div class="form-group" style="max-width:300px;margin-bottom:1rem">
                <label for="delete_password" class="form-label">{{ __('Confirmez votre mot de passe') }} *</label>
                <input type="password" name="password" id="delete_password" class="form-control" required>
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn-danger">{{ __('Supprimer mon compte') }}</button>
        </form>
    </div>
</div>

<style>
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.375rem; }
.form-label { font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); }
.form-control { padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary); font-size: 0.9rem; transition: border-color 0.2s; }
.form-control:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(var(--accent-rgb, 233, 69, 96), 0.15); }
.form-error { color: var(--danger, #ef4444); font-size: 0.8rem; }
.form-error-border { border-color: var(--danger, #ef4444) !important; }
.btn-outline { padding: 0.625rem 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: var(--text-primary); background: transparent; cursor: pointer; transition: all 0.2s; }
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }
.danger-zone { border-color: rgba(239, 68, 68, 0.3); }
.danger-desc { color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem; }
.btn-danger { padding: 0.625rem 1.25rem; background: var(--danger, #ef4444); color: white; border: none; border-radius: var(--radius-sm); cursor: pointer; font-weight: 600; transition: all 0.2s; }
.btn-danger:hover { opacity: 0.9; transform: translateY(-1px); }
.alert { padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; }
.alert-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
@media (max-width: 640px) { .form-grid-2 { grid-template-columns: 1fr; } }
</style>
@endsection
