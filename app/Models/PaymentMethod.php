<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $fillable = [
        'user_id',
        'card_brand',
        'card_last_four',
        'card_holder_name',
        'expiry_month',
        'expiry_year',
        'is_default',
        'stripe_payment_method_id',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the payment method
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get card brand icon
     */
    public function getBrandIconAttribute(): string
    {
        return match (strtolower($this->card_brand)) {
            'visa' => '💳',
            'mastercard' => '🟠',
            'amex', 'american express' => '💎',
            'discover' => '🔶',
            default => '💳',
        };
    }

    /**
     * Get formatted display string
     */
    public function getDisplayNameAttribute(): string
    {
        return ucfirst($this->card_brand).' •••• '.$this->card_last_four;
    }

    /**
     * Check if card is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        $now = now();
        $expiryDate = Carbon::createFromFormat('m/Y', $this->expiry_month.'/'.$this->expiry_year)->endOfMonth();

        return $now->greaterThan($expiryDate);
    }

    /**
     * Available card brands
     */
    public static function cardBrands(): array
    {
        return [
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            'amex' => 'American Express',
        ];
    }
}
