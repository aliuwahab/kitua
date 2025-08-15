<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DeviceSession extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'device_fingerprint',
        'app_version',
        'os_version',
        'ip_address',
        'user_agent',
        'push_token',
        'first_login_at',
        'last_activity_at',
        'is_trusted',
        'is_active',
        'revoked_at',
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
            'first_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'revoked_at' => 'datetime',
            'is_trusted' => 'boolean',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the user that owns the device session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique device fingerprint from device characteristics
     */
    public static function generateFingerprint(array $deviceInfo): string
    {
        $components = [
            $deviceInfo['device_type'] ?? '',
            $deviceInfo['os_version'] ?? '',
            $deviceInfo['app_version'] ?? '',
            $deviceInfo['device_model'] ?? '',
            $deviceInfo['screen_resolution'] ?? '',
        ];
        
        return hash('sha256', implode('|', array_filter($components)));
    }

    /**
     * Create or update device session
     */
    public static function createOrUpdateSession(string $userId, array $deviceInfo): self
    {
        $fingerprint = self::generateFingerprint($deviceInfo);
        $deviceId = $deviceInfo['device_id'] ?? Str::uuid();
        
        // Prepare metadata from device info
        $metadata = $deviceInfo['metadata'] ?? [];
        
        // Add device_model to metadata if provided
        if (isset($deviceInfo['device_model'])) {
            $metadata['device_model'] = $deviceInfo['device_model'];
        }
        
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'device_fingerprint' => $fingerprint,
            ],
            [
                'device_id' => $deviceId,
                'device_name' => $deviceInfo['device_name'] ?? null,
                'device_type' => $deviceInfo['device_type'] ?? null,
                'app_version' => $deviceInfo['app_version'] ?? null,
                'os_version' => $deviceInfo['os_version'] ?? null,
                'ip_address' => $deviceInfo['ip_address'] ?? null,
                'user_agent' => $deviceInfo['user_agent'] ?? null,
                'push_token' => $deviceInfo['push_token'] ?? null,
                'first_login_at' => now(),
                'last_activity_at' => now(),
                'is_active' => true,
                'metadata' => !empty($metadata) ? $metadata : null,
            ]
        );
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Mark device as trusted
     */
    public function markAsTrusted(): void
    {
        $this->update(['is_trusted' => true]);
    }

    /**
     * Revoke device session
     */
    public function revoke(): void
    {
        $this->update([
            'is_active' => false,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Check if device session is expired
     */
    public function isExpired(int $daysThreshold = 30): bool
    {
        return $this->last_activity_at->diffInDays() > $daysThreshold;
    }

    /**
     * Scope for active sessions only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('revoked_at');
    }

    /**
     * Scope for trusted devices only
     */
    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }
}
