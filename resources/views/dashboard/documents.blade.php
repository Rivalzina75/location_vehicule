@extends('layouts.dashboard')

@section('content')
<div class="page-transition" data-page-index="4">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Mes Documents') }}</h1>
            <p class="page-subtitle">{{ __('Gérez vos documents nécessaires pour la location') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Upload Form -->
    <div class="card-section">
        <h2 class="section-title">📤 {{ __('Ajouter un document') }}</h2>
        <form action="{{ route('dashboard.documents.store') }}" method="POST" enctype="multipart/form-data" class="upload-form">
            @csrf
            <div class="form-grid-3">
                <div class="form-group">
                    <label for="type" class="form-label">{{ __('Type de document') }} *</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">{{ __('Sélectionner...') }}</option>
                        <option value="driving_license">{{ __('Permis de conduire') }}</option>
                        <option value="identity_card">{{ __('Carte d\'identité') }}</option>
                        <option value="passport">{{ __('Passeport') }}</option>
                        <option value="credit_card_proof">{{ __('Justificatif carte bancaire') }}</option>
                        <option value="address_proof">{{ __('Justificatif de domicile') }}</option>
                        <option value="insurance">{{ __('Attestation d\'assurance') }}</option>
                        <option value="other">{{ __('Autre') }}</option>
                    </select>
                    @error('type') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="file" class="form-label">{{ __('Fichier') }} * <small>(PDF, JPG, PNG - max 5Mo)</small></label>
                    <input type="file" name="file" id="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    @error('file') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="expiry_date" class="form-label">{{ __('Date d\'expiration') }}</label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('expiry_date') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
            <button type="submit" class="btn-primary" style="margin-top:1rem">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:18px;height:18px"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                {{ __('Uploader') }}
            </button>
        </form>
    </div>

    <!-- Documents List -->
    <div class="documents-sections">
        <!-- Approved -->
        <div class="card-section">
            <h2 class="section-title">✅ {{ __('Documents approuvés') }} ({{ $approved->count() }})</h2>
            @if($approved->count() > 0)
                <div class="documents-grid">
                    @foreach($approved as $doc)
                        @include('dashboard._document-card', ['doc' => $doc])
                    @endforeach
                </div>
            @else
                <div class="empty-state-small">{{ __('Aucun document approuvé') }}</div>
            @endif
        </div>

        <!-- Pending -->
        <div class="card-section">
            <h2 class="section-title">⏳ {{ __('En attente de validation') }} ({{ $pending->count() }})</h2>
            @if($pending->count() > 0)
                <div class="documents-grid">
                    @foreach($pending as $doc)
                        @include('dashboard._document-card', ['doc' => $doc])
                    @endforeach
                </div>
            @else
                <div class="empty-state-small">{{ __('Aucun document en attente') }}</div>
            @endif
        </div>

        <!-- Rejected -->
        @if($rejected->count() > 0)
        <div class="card-section">
            <h2 class="section-title">❌ {{ __('Documents refusés') }} ({{ $rejected->count() }})</h2>
            <div class="documents-grid">
                @foreach($rejected as $doc)
                    @include('dashboard._document-card', ['doc' => $doc])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.card-section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; }
.upload-form { display: flex; flex-direction: column; }
.form-grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.375rem; }
.form-label { font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); }
.form-label small { opacity: 0.7; }
.form-control { padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary); font-size: 0.9rem; transition: border-color 0.2s; }
.form-control:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(var(--accent-rgb, 233, 69, 96), 0.15); }
.form-error { color: var(--danger, #ef4444); font-size: 0.8rem; }
.documents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
.doc-card { background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; display: flex; gap: 1rem; align-items: flex-start; transition: all 0.2s; }
.doc-card:hover { border-color: var(--accent); }
.doc-icon { width: 44px; height: 44px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
.doc-icon.pdf { background: rgba(239, 68, 68, 0.1); }
.doc-icon.image { background: rgba(52, 152, 219, 0.1); }
.doc-info { flex: 1; min-width: 0; }
.doc-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.25rem; }
.doc-meta { font-size: 0.8rem; color: var(--text-secondary); }
.doc-actions { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
.btn-sm { padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: var(--radius-sm); cursor: pointer; transition: all 0.2s; border: 1px solid var(--border-color); background: transparent; color: var(--text-secondary); text-decoration: none; }
.btn-sm:hover { border-color: var(--accent); color: var(--accent); }
.btn-sm.danger { border-color: var(--danger, #ef4444); color: var(--danger, #ef4444); }
.btn-sm.danger:hover { background: var(--danger, #ef4444); color: white; }
.empty-state-small { display: flex; align-items: center; justify-content: center; min-height: 80px; color: var(--text-secondary); font-style: italic; }
.doc-expiry { font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: var(--radius-full); display: inline-block; margin-top: 0.25rem; }
.doc-expiry.valid { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.doc-expiry.expired { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.rejection-reason { margin-top: 0.5rem; padding: 0.5rem; background: rgba(239, 68, 68, 0.05); border-left: 3px solid var(--danger, #ef4444); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: 0.8rem; color: var(--danger, #ef4444); }
</style>
@endsection
