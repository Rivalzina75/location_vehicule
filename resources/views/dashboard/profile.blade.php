@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="6">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Mon Profil') }}</h1>
            <p class="page-subtitle">{{ __('Consultez vos informations personnelles') }}</p>
        </div>
        <a href="{{ route('dashboard.profile.edit') }}" class="btn-primary" style="text-decoration:none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:18px;height:18px"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            {{ __('Modifier') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="profile-layout">
        <!-- Profile Card -->
        <div class="card-section profile-card-main">
            <div class="profile-avatar-large">
                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
            </div>
            <h2 class="profile-name">{{ $user->first_name }} {{ $user->last_name }}</h2>
            <p class="profile-role">{{ $user->role === 'admin' ? __('Administrateur') : __('Client') }}</p>
            <p class="profile-member-since">{{ __('Membre depuis') }} {{ $user->created_at->translatedFormat('F Y') }}</p>
        </div>

        <!-- Info Sections -->
        <div class="profile-details">
            <div class="card-section">
                <h3 class="section-title">📧 {{ __('Informations de contact') }}</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">{{ __('Email') }}</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Téléphone') }}</span>
                        <span class="info-value">{{ $user->phone_number ?? __('Non renseigné') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Email vérifié') }}</span>
                        <span class="info-value">
                            @if($user->email_verified_at)
                                <span class="badge-success">✅ {{ __('Vérifié le') }} {{ $user->email_verified_at->format('d/m/Y') }}</span>
                            @else
                                <span class="badge-warning">⏳ {{ __('Non vérifié') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-section">
                <h3 class="section-title">👤 {{ __('Informations personnelles') }}</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">{{ __('Date de naissance') }}</span>
                        <span class="info-value">{{ $user->date_of_birth ?? __('Non renseigné') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Adresse') }}</span>
                        <span class="info-value">{{ $user->address_line1 ?? __('Non renseigné') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Code postal') }}</span>
                        <span class="info-value">{{ $user->postal_code ?? __('Non renseigné') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Ville') }}</span>
                        <span class="info-value">{{ $user->city ?? __('Non renseigné') }}</span>
                    </div>
                </div>
            </div>

            <div class="card-section">
                <h3 class="section-title">📊 {{ __('Statistiques') }}</h3>
                <div class="stats-mini-grid">
                    <div class="stat-mini">
                        <span class="stat-mini-value">{{ $user->reservations()->where('status', '!=', 'cancelled')->count() }}</span>
                        <span class="stat-mini-label">{{ __('Réservations') }}</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-mini-value">{{ $user->reservations()->where('status', 'completed')->count() }}</span>
                        <span class="stat-mini-label">{{ __('Terminées') }}</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-mini-value">{{ $user->documents()->count() }}</span>
                        <span class="stat-mini-label">{{ __('Documents') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 1.5rem; }
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; }
.profile-card-main { text-align: center; padding: 2rem 1.5rem; height: fit-content; min-height: 244px; }
.profile-avatar-large { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #ff7eb3); color: white; font-size: 1.75rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
.profile-name { font-size: 1.3rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
.profile-role { font-size: 0.875rem; color: var(--accent); font-weight: 500; }
.profile-member-since { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.5rem; }
.info-grid { display: flex; flex-direction: column; gap: 1rem; }
.info-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color); }
.info-item:last-child { border-bottom: none; }
.info-label { font-size: 0.875rem; color: var(--text-secondary); font-weight: 500; }
.info-value { font-size: 0.9rem; color: var(--text-primary); font-weight: 500; }
.badge-success { background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-size: 0.8rem; }
.badge-warning { background: rgba(251, 191, 36, 0.1); color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-size: 0.8rem; }
.stats-mini-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center; }
.stat-mini { display: flex; flex-direction: column; gap: 0.25rem; padding: 1rem; background: var(--bg-primary); border-radius: var(--radius-sm); border: 1px solid var(--border-color); }
.stat-mini-value { font-size: 1.5rem; font-weight: 700; color: var(--accent); }
.stat-mini-label { font-size: 0.8rem; color: var(--text-secondary); }
.alert { padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; }
.alert-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
@media (max-width: 768px) { .profile-layout { grid-template-columns: 1fr; } }
</style>
@endsection
