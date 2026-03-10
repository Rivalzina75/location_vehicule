<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $date_of_birth
 * @property string $phone_number
 * @property string $address_line1
 * @property string $postal_code
 * @property string $city
 * @property string $role
 * @property int $login_attempts
 * @property string|null $blocked_until
 * @property string|null $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|Reservation[] $reservations
 * @property Collection|Document[] $documents
 * @property string $full_name
 * @property int $lockout_seconds
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method bool update(array $attributes = [], array $options = [])
 * @method bool delete()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reservations()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany documents()
 * @method bool isAdmin()
 * @method bool isClient()
 */
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
        'email_verified_at',
        'password',
        'date_of_birth',
        'phone_number',
        'address_line1',
        'postal_code',
        'city',
        'role',
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
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is client.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Relation: User has many reservations.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relation: User has many documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relation: User has many activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Relation: User has many payment methods.
     */
    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * Get user's default payment method.
     *
     * @return PaymentMethod|null
     */
    public function defaultPaymentMethod()
    {
        return $this->paymentMethods()->where('is_default', true)->first();
    }

    /**
     * Check if user has at least one valid (non-expired) payment method.
     */
    public function hasValidPaymentMethod(): bool
    {
        $now = now();
        $currentYear = (int) $now->format('Y');
        $currentMonth = (int) $now->format('m');

        return $this->paymentMethods()
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('expiry_year', '>', (string) $currentYear)
                    ->orWhere(function ($q) use ($currentYear, $currentMonth) {
                        $q->where('expiry_year', (string) $currentYear)
                            ->where('expiry_month', '>=', str_pad((string) $currentMonth, 2, '0', STR_PAD_LEFT));
                    });
            })
            ->exists();
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
        if (! $this->isLockedOut()) {
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
