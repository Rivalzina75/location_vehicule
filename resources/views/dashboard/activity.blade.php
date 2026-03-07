@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="1">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Historique d\'activité') }}</h1>
            <p class="page-subtitle">{{ __('Toutes vos actions et modifications') }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.45rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex:0 0 auto;">
                <path d="M15 18l-6-6 6-6"/>
                <path d="M21 12H9"/>
            </svg>
            <span>{{ __('Accueil') }}</span>
        </a>
    </div>

    <div class="card-section">
        @if($activities->count() > 0)
            <div class="activity-timeline">
                @php $lastDate = null; @endphp
                @foreach($activities as $activity)
                    @php 
                        $currentDate = $activity->created_at->format('Y-m-d');
                    @endphp
                    @if($lastDate !== $currentDate)
                        <div class="activity-date-separator">
                            <span>
                                @if($activity->created_at->isToday())
                                    {{ __('Aujourd\'hui') }}
                                @elseif($activity->created_at->isYesterday())
                                    {{ __('Hier') }}
                                @else
                                    {{ $activity->created_at->translatedFormat('l d F Y') }}
                                @endif
                            </span>
                        </div>
                        @php $lastDate = $currentDate; @endphp
                    @endif
                    <div class="activity-item">
                        <div class="activity-icon {{ in_array($activity->type, ['reservation_confirmed', 'reservation_completed']) ? 'success' : (in_array($activity->type, ['document_uploaded', 'inspection_start', 'inspection_end']) ? 'info' : (str_contains($activity->type, 'cancelled') ? 'danger' : 'warning')) }}">
                            @if(str_contains($activity->type, 'reservation'))
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            @elseif(str_contains($activity->type, 'document'))
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-width="2"/>
                                    <path d="M14 2v6h6" stroke-width="2"/>
                                </svg>
                            @elseif(str_contains($activity->type, 'inspection'))
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke-width="2"/>
                                    <circle cx="12" cy="13" r="4" stroke-width="2"/>
                                </svg>
                            @elseif(str_contains($activity->type, 'profile'))
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke-width="2"/>
                                    <circle cx="12" cy="7" r="4" stroke-width="2"/>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                    <path d="M12 8v4M12 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity->title }}</div>
                            @if($activity->description)
                                <div class="activity-desc">{{ $activity->description }}</div>
                            @endif
                            <div class="activity-time">{{ $activity->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($activities->hasPages())
                <div class="pagination-wrapper" style="margin-top: 1.5rem;">
                    {{ $activities->links() }}
                </div>
            @endif
        @else
            <div class="empty-state" style="padding: 3rem; text-align: center;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:64px;height:64px;opacity:0.3;margin:0 auto 1rem">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <path d="M12 6v6l4 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">{{ __('Aucune activité') }}</h3>
                <p style="color: var(--text-secondary);">{{ __('Votre historique d\'activité apparaîtra ici.') }}</p>
            </div>
        @endif
    </div>
</div>

<style>
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; }
.activity-timeline { display: flex; flex-direction: column; gap: 0.5rem; }
.activity-date-separator { padding: 0.75rem 0; margin-top: 0.5rem; }
.activity-date-separator:first-child { margin-top: 0; }
.activity-date-separator span { font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; padding: 0.25rem 0.75rem; background: var(--bg-primary); border-radius: var(--radius-full); }
.activity-item { display: flex; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: var(--radius-sm); transition: all 0.2s; }
.activity-item:hover { transform: translateX(4px); }
.activity-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.activity-icon svg { width: 20px; height: 20px; stroke-width: 2; }
.activity-icon.success { background: rgba(16, 185, 129, 0.15); }
.activity-icon.success svg { stroke: #10b981; }
.activity-icon.info { background: rgba(52, 152, 219, 0.15); }
.activity-icon.info svg { stroke: #3498db; }
.activity-icon.warning { background: rgba(251, 191, 36, 0.15); }
.activity-icon.warning svg { stroke: var(--accent); }
.activity-icon.danger { background: rgba(239, 68, 68, 0.15); }
.activity-icon.danger svg { stroke: #ef4444; }
.activity-content { flex: 1; }
.activity-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; }
.activity-desc { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.25rem; }
.activity-time { font-size: 0.75rem; color: var(--text-light); }
.btn-outline { padding: 0.625rem 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: var(--text-primary); background: transparent; }
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }
</style>
@endsection
