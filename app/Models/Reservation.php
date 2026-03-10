<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'duration_days',
        'base_price',
        'options_price',
        'insurance_price',
        'total_price',
        'deposit_amount',
        'child_seat',
        'gps',
        'additional_driver',
        'insurance_full',
        'customer_notes',
        'admin_notes',
        'status',
        'payment_status',
        'confirmation_code',
        'start_inspection_done',
        'end_inspection_done',
        'mileage_start',
        'mileage_end',
        'damage_cost',
        'late_penalty',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'base_price' => 'decimal:2',
            'options_price' => 'decimal:2',
            'insurance_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'damage_cost' => 'decimal:2',
            'late_penalty' => 'decimal:2',
            'child_seat' => 'boolean',
            'gps' => 'boolean',
            'additional_driver' => 'boolean',
            'insurance_full' => 'boolean',
            'start_inspection_done' => 'boolean',
            'end_inspection_done' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->confirmation_code)) {
                $reservation->confirmation_code = 'RES-'.strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function getIsLateAttribute(): bool
    {
        if (! in_array($this->status, ['active', 'late'])) {
            return false;
        }

        return Carbon::now()->greaterThan($this->end_date);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        $total = $this->total_price + $this->damage_cost + $this->late_penalty;

        return number_format($total, 2, ',', ' ').' €';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('En attente'),
            'confirmed' => __('Confirmée'),
            'active' => __('En cours'),
            'completed' => __('Terminée'),
            'cancelled' => __('Annulée'),
            'late' => __('En retard'),
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'active' => 'success',
            'completed' => 'primary',
            'cancelled' => 'danger',
            'late' => 'danger',
            default => 'secondary',
        };
    }

    public function calculateLatePenalty(): float
    {
        if (! $this->is_late) {
            return 0;
        }
        $lateDays = Carbon::now()->diffInDays($this->end_date);

        return $lateDays * ($this->vehicle->price_per_day * 1.5);
    }

    public static function calculateOfferBreakdown(Vehicle $vehicle, int $days): array
    {
        $remainingDays = max(1, $days);

        $months = 0;
        $weeks = 0;
        $singleDays = 0;

        if (! empty($vehicle->price_per_month) && $vehicle->price_per_month > 0) {
            $months = intdiv($remainingDays, 30);
            $remainingDays %= 30;
        }

        if (! empty($vehicle->price_per_week) && $vehicle->price_per_week > 0) {
            $weeks = intdiv($remainingDays, 7);
            $remainingDays %= 7;
        }

        $singleDays = $remainingDays;

        $monthAmount = $months * (float) ($vehicle->price_per_month ?? 0);
        $weekAmount = $weeks * (float) ($vehicle->price_per_week ?? 0);
        $dayAmount = $singleDays * (float) $vehicle->price_per_day;

        return [
            'months' => $months,
            'weeks' => $weeks,
            'days' => $singleDays,
            'month_amount' => $monthAmount,
            'week_amount' => $weekAmount,
            'day_amount' => $dayAmount,
            'total' => $monthAmount + $weekAmount + $dayAmount,
        ];
    }

    public static function calculateBasePriceForDays(Vehicle $vehicle, int $days): float
    {
        return (float) self::calculateOfferBreakdown($vehicle, $days)['total'];
    }

    public function complete(): void
    {
        $finalTotal = $this->total_price + $this->damage_cost + $this->late_penalty;
        $this->update([
            'status' => 'completed',
            'total_price' => $finalTotal,
        ]);
        $this->vehicle->update(['status' => 'available']);
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => __('En attente'),
            'confirmed' => __('Confirmée'),
            'active' => __('En cours'),
            'completed' => __('Terminée'),
            'cancelled' => __('Annulée'),
            'late' => __('En retard'),
        ];
    }
}
