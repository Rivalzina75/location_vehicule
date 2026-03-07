<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'filename',
        'path',
        'mime_type',
        'size',
        'status',
        'expiry_date',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'driving_license' => __('Permis de conduire'),
            'identity_card' => __('Carte d\'identit\u00e9'),
            'passport' => __('Passeport'),
            'credit_card_proof' => __('Justificatif CB'),
            'address_proof' => __('Justificatif de domicile'),
            'insurance' => __('Assurance'),
            'other' => __('Autre'),
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('En attente'),
            'approved' => __('Approuv\u00e9'),
            'rejected' => __('Rejet\u00e9'),
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' Mo';
        }
        return number_format($bytes / 1024, 0) . ' Ko';
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public static function typeOptions(): array
    {
        return [
            'driving_license' => __('Permis de conduire'),
            'identity_card' => __('Carte d\'identit\u00e9'),
            'passport' => __('Passeport'),
            'credit_card_proof' => __('Justificatif CB'),
            'address_proof' => __('Justificatif de domicile'),
            'insurance' => __('Assurance'),
            'other' => __('Autre'),
        ];
    }
}
