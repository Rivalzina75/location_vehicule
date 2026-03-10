<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspection extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'type',
        'inspection_date',
        'mileage',
        'fuel_level',
        'cleanliness',
        'exterior_ok',
        'interior_ok',
        'tires_ok',
        'lights_ok',
        'documents_ok',
        'photos',
        'damages',
        'general_notes',
        'damage_notes',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'datetime',
            'photos' => 'array',
            'damages' => 'array',
            'exterior_ok' => 'boolean',
            'interior_ok' => 'boolean',
            'tires_ok' => 'boolean',
            'lights_ok' => 'boolean',
            'documents_ok' => 'boolean',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFuelLevelLabelAttribute(): string
    {
        return match ($this->fuel_level) {
            'empty' => __('Vide'),
            'quarter' => __('1/4'),
            'half' => __('1/2'),
            'three_quarters' => __('3/4'),
            'full' => __('Plein'),
            default => $this->fuel_level,
        };
    }

    public function getCleanlinessLabelAttribute(): string
    {
        return match ($this->cleanliness) {
            'dirty' => __('Sale'),
            'acceptable' => __('Acceptable'),
            'clean' => __('Propre'),
            'very_clean' => __('Tr\u00e8s propre'),
            default => $this->cleanliness,
        };
    }

    public function hasIssues(): bool
    {
        return ! $this->exterior_ok || ! $this->interior_ok || ! $this->tires_ok || ! $this->lights_ok || ! $this->documents_ok;
    }
}
