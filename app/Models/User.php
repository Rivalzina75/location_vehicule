<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmail;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'phone_number',
        'address_line1',
        'postal_code',
        'city',
        'login_attempts',
        'blocked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'blocked_until' => 'datetime',
        ];
    }

    /**
     * Mutator for date_of_birth (French Format → MySQL).
     * Handles both d/m/Y format and already formatted dates.
     */
    protected function setDateOfBirthAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['date_of_birth'] = null;
            return;
        }

        // If already in Y-m-d format, keep it
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->attributes['date_of_birth'] = $value;
            return;
        }

        // Convert from French format (d/m/Y) to MySQL format
        try {
            $this->attributes['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, try to parse as any date format
            try {
                $this->attributes['date_of_birth'] = Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->attributes['date_of_birth'] = null;
            }
        }
    }

    /**
     * Accessor for date_of_birth (MySQL → French Format).
     */
    protected function getDateOfBirthAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if user is currently locked out.
     */
    public function isLockedOut(): bool
    {
        return $this->blocked_until && Carbon::now()->lessThan($this->blocked_until);
    }

    /**
     * Get remaining lockout seconds.
     */
    public function getLockoutSecondsAttribute(): int
    {
        if (!$this->isLockedOut()) {
            return 0;
        }

        return (int) Carbon::now()->diffInSeconds($this->blocked_until, false);
    }

    /**
     * Send email verification notification using custom template.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send password reset notification using custom template.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
