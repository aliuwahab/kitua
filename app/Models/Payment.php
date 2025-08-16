<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payable_type',
        'payable_id',
        'amount',
        'currency_code',
        'provider',
        'provider_reference',
        'provider_payment_method',
        'status',
        'payment_method',
        'phone_number',
        'account_number',
        'initiated_at',
        'completed_at',
        'failed_at',
        'provider_response',
        'metadata',
        'failure_reason',
        'failure_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'provider_response' => 'json',
        'metadata' => 'json',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = ['formatted_amount', 'is_completed', 'is_failed', 'is_pending'];

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payable entity (PaymentRequest, GroupPaymentRequest, etc.).
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency_code . ' ' . number_format($this->amount, 2);
    }

    /**
     * Check if the payment is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the payment has failed.
     */
    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the payment is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    /**
     * Scope a query by provider.
     */
    public function scopeByProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider', $provider);
    }

    /**
     * Mark the payment as initiated.
     */
    public function markAsInitiated(string $providerReference = null, array $metadata = null): bool
    {
        $data = [
            'status' => 'processing',
            'initiated_at' => now(),
        ];

        if ($providerReference) {
            $data['provider_reference'] = $providerReference;
        }

        if ($metadata) {
            $data['metadata'] = array_merge($this->metadata ?? [], $metadata);
        }

        return $this->update($data);
    }

    /**
     * Mark the payment as completed.
     */
    public function markAsCompleted(array $providerResponse = null, array $metadata = null): bool
    {
        $data = [
            'status' => 'completed',
            'completed_at' => now(),
        ];

        if ($providerResponse) {
            $data['provider_response'] = $providerResponse;
        }

        if ($metadata) {
            $data['metadata'] = array_merge($this->metadata ?? [], $metadata);
        }

        return $this->update($data);
    }

    /**
     * Mark the payment as failed.
     */
    public function markAsFailed(string $reason = null, string $message = null, array $providerResponse = null): bool
    {
        $data = [
            'status' => 'failed',
            'failed_at' => now(),
        ];

        if ($reason) {
            $data['failure_reason'] = $reason;
        }

        if ($message) {
            $data['failure_message'] = $message;
        }

        if ($providerResponse) {
            $data['provider_response'] = $providerResponse;
        }

        return $this->update($data);
    }

    /**
     * Mark the payment as cancelled.
     */
    public function markAsCancelled(string $reason = null): bool
    {
        $data = [
            'status' => 'cancelled',
            'failed_at' => now(),
        ];

        if ($reason) {
            $data['failure_reason'] = $reason;
        }

        return $this->update($data);
    }

    /**
     * Scope a query to apply filters using QueryFilter
     */
    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {
        return $filters->apply($builder);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
