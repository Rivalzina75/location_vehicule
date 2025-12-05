<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'price_per_day',
        'description',
        'image_path',
        'status',
    ];

    /**
     * Get all available vehicle types
     */
    public static function types()
    {
        return [
            'car' => 'Voiture',
            'suv' => 'SUV',
            'van' => 'Van',
            'motorcycle' => 'Moto',
            'scooter' => 'Scooter',
        ];
    }

    /**
     * Get all available transmissions
     */
    public static function transmissions()
    {
        return [
            'manual' => 'Manuelle',
            'automatic' => 'Automatique',
        ];
    }

    /**
     * Get all available fuel types
     */
    public static function fuelTypes()
    {
        return [
            'gasoline' => 'Essence',
            'diesel' => 'Diesel',
            'electric' => 'Électrique',
            'hybrid' => 'Hybride',
        ];
    }

    /**
     * Check if vehicle is available
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }
}
