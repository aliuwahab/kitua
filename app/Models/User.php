<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mobile_number',
        'email',
        'first_name',
        'surname',
        'other_names',
        'pin',
        'password',
        'user_type',
        'is_active',
        'mobile_verified_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = ['full_name'];

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->surname . ' ' . ($this->other_names ?? ''));
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $names = array_filter([$this->first_name, $this->surname, $this->other_names]);
        return collect($names)
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is a mobile app user
     */
    public function isMobileUser(): bool
    {
        return $this->user_type === 'mobile';
    }

    /**
     * Check if user is an admin user
     */
    public function isAdminUser(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Get the user's payment accounts
     */
    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class);
    }

    /**
     * Get the user's primary payment account
     */
    public function primaryPaymentAccount()
    {
        return $this->paymentAccounts()->where('is_primary', true)->first();
    }

    /**
     * Get verified payment accounts
     */
    public function verifiedPaymentAccounts(): HasMany
    {
        return $this->paymentAccounts()->where('is_verified', true);
    }

    /**
     * Get the user's device sessions
     */
    public function deviceSessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class);
    }

    /**
     * Get active device sessions
     */
    public function activeDeviceSessions(): HasMany
    {
        return $this->deviceSessions()->active();
    }

    /**
     * Get trusted device sessions
     */
    public function trustedDeviceSessions(): HasMany
    {
        return $this->deviceSessions()->trusted()->active();
    }
}
