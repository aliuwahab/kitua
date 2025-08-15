<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAccount extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'account_type',
        'account_number',
        'account_name',
        'provider',
        'provider_code',
        'is_primary',
        'is_verified',
        'verified_at',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the user that owns the payment account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a mobile money account
     */
    public function isMomoAccount(): bool
    {
        return $this->account_type === 'momo';
    }

    /**
     * Check if this is a bank account
     */
    public function isBankAccount(): bool
    {
        return $this->account_type === 'bank';
    }

    /**
     * Get formatted account number for display
     */
    public function getFormattedAccountNumberAttribute(): string
    {
        if ($this->isMomoAccount()) {
            // Format mobile number for display
            return substr($this->account_number, 0, 3) . '****' . substr($this->account_number, -3);
        }

        return $this->account_number;
    }

    /**
     * Scope for primary accounts only
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for verified accounts only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
