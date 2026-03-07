{{-- Partial: Document Card --}}
<div class="doc-card">
    <div class="doc-icon {{ in_array($doc->mime_type, ['application/pdf']) ? 'pdf' : 'image' }}">
        @if(in_array($doc->mime_type, ['application/pdf']))
            📄
        @else
            🖼️
        @endif
    </div>
    <div class="doc-info">
        <div class="doc-name">{{ $doc->type_label }}</div>
        <div class="doc-meta">
            {{ $doc->filename }} · {{ $doc->formatted_size }}
            · {{ $doc->created_at->format('d/m/Y') }}
        </div>
        @if($doc->expiry_date)
            <span class="doc-expiry {{ $doc->isExpired() ? 'expired' : 'valid' }}">
                {{ $doc->isExpired() ? __('Expiré') : __('Expire le') }} {{ $doc->expiry_date->format('d/m/Y') }}
            </span>
        @endif
        @if($doc->status === 'rejected' && $doc->rejection_reason)
            <div class="rejection-reason">{{ $doc->rejection_reason }}</div>
        @endif
        <div class="doc-actions">
            <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="btn-sm">{{ __('Voir') }}</a>
            @if($doc->status !== 'approved')
                <form action="{{ route('dashboard.documents.destroy', $doc->id) }}" method="POST" 
                      onsubmit="return confirm('{{ __('Supprimer ce document ?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-sm danger">{{ __('Supprimer') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>
