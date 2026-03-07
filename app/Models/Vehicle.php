<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'type',
        'year',
        'registration_number',
        'transmission',
        'fuel_type',
        'seats',
        'doors',
        'engine_power',
        'fuel_consumption',
        'trunk_capacity',
        'price_per_day',
        'price_per_week',
        'price_per_month',
        'deposit',
        'mileage',
        'gps_available',
        'child_seat_available',
        'bluetooth',
        'air_conditioning',
        'cruise_control',
        'parking_sensors',
        'backup_camera',
        'description',
        'image_path',
        'status',
        'rating',
        'reviews_count',
        'rental_count',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'price_per_week' => 'decimal:2',
            'price_per_month' => 'decimal:2',
            'deposit' => 'decimal:2',
            'fuel_consumption' => 'decimal:2',
            'rating' => 'decimal:1',
            'gps_available' => 'boolean',
            'child_seat_available' => 'boolean',
            'bluetooth' => 'boolean',
            'air_conditioning' => 'boolean',
            'cruise_control' => 'boolean',
            'parking_sensors' => 'boolean',
            'backup_camera' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get all available vehicle types
     */
    public static function types(): array
    {
        return [
            'car' => __('Berline'),
            'suv' => __('SUV'),
            'van' => __('Camionnette'),
            'motorcycle' => __('Moto'),
            'scooter' => __('Scooter'),
            'citycar' => __('Citadine'),
            'luxury' => __('Luxe'),
            'convertible' => __('Cabriolet'),
        ];
    }

    /**
     * Get all available transmissions
     */
    public static function transmissions(): array
    {
        return [
            'manual' => __('Manuelle'),
            'automatic' => __('Automatique'),
        ];
    }

    /**
     * Get all available fuel types
     */
    public static function fuelTypes(): array
    {
        return [
            'gasoline' => __('Essence'),
            'diesel' => __('Diesel'),
            'electric' => __('Électrique'),
            'hybrid' => __('Hybride'),
        ];
    }

    /**
     * Check if vehicle is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Calculate price based on duration and rate type
     */
    public function calculatePrice(int $days, string $rateType = 'daily'): float
    {
        return match ($rateType) {
            'weekly' => $this->price_per_week ? ceil($days / 7) * $this->price_per_week : $days * $this->price_per_day,
            'monthly' => $this->price_per_month ? ceil($days / 30) * $this->price_per_month : $days * $this->price_per_day,
            default => $days * $this->price_per_day,
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    /**
     * Get transmission label
     */
    public function getTransmissionLabelAttribute(): string
    {
        return self::transmissions()[$this->transmission] ?? $this->transmission;
    }

    /**
     * Get fuel type label
     */
    public function getFuelTypeLabelAttribute(): string
    {
        return self::fuelTypes()[$this->fuel_type] ?? $this->fuel_type;
    }
}
