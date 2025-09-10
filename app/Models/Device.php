<?php

namespace App\Models;

use App\Traits\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Relationships
        'user_id',
        
        // Device identification
        'device_id',
        'device_name',
        'device_type',
        'platform',
        'platform_version',
        'app_version',
        'device_model',
        'manufacturer',
        'browser',
        'browser_version',
        
        // Push notifications
        'expo_push_token',
        'fcm_token',
        'apns_token',
        'push_notifications_enabled',
        'notification_preferences',
        'push_token_updated_at',
        'push_token_expires_at',
        'push_token_valid',
        
        // Device activity
        'first_seen_at',
        'last_seen_at',
        'last_login_at',
        'is_active',
        'is_trusted',
        'is_blocked',
        
        // Location
        'last_latitude',
        'last_longitude',
        'last_location_name',
        'location_updated_at',
        
        // Biometric & security
        'biometric_enabled',
        'biometric_type',
        'pin_enabled',
        'screen_lock_enabled',
        'device_encrypted',
        'failed_login_attempts',
        'locked_until',
        
        // Attendance capabilities
        'supports_qr_scanning',
        'supports_gps',
        'supports_camera',
        'supports_offline_mode',
        'require_location_for_checkin',
        'auto_checkout_enabled',
        'auto_checkout_hours',
        
        // Network & connection
        'ip_address',
        'user_agent',
        'network_type',
        'carrier',
        'timezone',
        'locale',
        
        // Offline capabilities
        'offline_data',
        'offline_data_synced_at',
        'has_pending_sync',
        
        // Audit & compliance
        'device_fingerprint',
        'app_permissions',
        'registration_notes',
        'approved_by',
        'approved_at',
        'blocked_by',
        'blocked_at',
        'block_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'push_notifications_enabled' => 'boolean',
        'notification_preferences' => 'array',
        'push_token_updated_at' => 'datetime',
        'push_token_expires_at' => 'datetime',
        'push_token_valid' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'is_trusted' => 'boolean',
        'is_blocked' => 'boolean',
        'last_latitude' => 'decimal:8',
        'last_longitude' => 'decimal:8',
        'location_updated_at' => 'datetime',
        'biometric_enabled' => 'boolean',
        'pin_enabled' => 'boolean',
        'screen_lock_enabled' => 'boolean',
        'device_encrypted' => 'boolean',
        'failed_login_attempts' => 'integer',
        'locked_until' => 'datetime',
        'supports_qr_scanning' => 'boolean',
        'supports_gps' => 'boolean',
        'supports_camera' => 'boolean',
        'supports_offline_mode' => 'boolean',
        'require_location_for_checkin' => 'boolean',
        'auto_checkout_enabled' => 'boolean',
        'auto_checkout_hours' => 'integer',
        'offline_data' => 'array',
        'offline_data_synced_at' => 'datetime',
        'has_pending_sync' => 'boolean',
        'device_fingerprint' => 'array',
        'app_permissions' => 'array',
        'approved_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Device $device) {
            if (!$device->device_id) {
                $device->device_id = $device->generateDeviceId();
            }
            
            if (!$device->first_seen_at) {
                $device->first_seen_at = now();
            }
            
            $device->last_seen_at = now();
        });

        static::updating(function (Device $device) {
            if ($device->isDirty(['expo_push_token', 'fcm_token', 'apns_token'])) {
                $device->push_token_updated_at = now();
            }
        });
    }

    /**
     * Get the user this device belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the user who approved this device.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who blocked this device.
     */
    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    /**
     * Get attendance records created from this device.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'check_in_device_id');
    }

    /**
     * Generate a unique device ID.
     */
    protected function generateDeviceId(): string
    {
        do {
            $deviceId = 'DEV-' . strtoupper(Str::random(8));
        } while (static::where('device_id', $deviceId)->exists());
        
        return $deviceId;
    }

    /**
     * Update device activity.
     */
    public function updateActivity(?string $ipAddress = null, ?array $location = null): void
    {
        $this->update([
            'last_seen_at' => now(),
            'ip_address' => $ipAddress ?: $this->ip_address,
            'last_latitude' => $location['latitude'] ?? $this->last_latitude,
            'last_longitude' => $location['longitude'] ?? $this->last_longitude,
            'last_location_name' => $location['name'] ?? $this->last_location_name,
            'location_updated_at' => $location ? now() : $this->location_updated_at,
        ]);
    }

    /**
     * Register or update push token.
     */
    public function registerPushToken(string $token, string $type = 'expo'): bool
    {
        $field = match($type) {
            'expo' => 'expo_push_token',
            'fcm' => 'fcm_token',
            'apns' => 'apns_token',
            default => 'expo_push_token',
        };

        return $this->update([
            $field => $token,
            'push_token_valid' => true,
            'push_notifications_enabled' => true,
        ]);
    }

    /**
     * Mark push token as invalid.
     */
    public function invalidatePushToken(string $type = 'expo'): void
    {
        $field = match($type) {
            'expo' => 'expo_push_token',
            'fcm' => 'fcm_token',
            'apns' => 'apns_token',
            default => 'expo_push_token',
        };

        $this->update([
            $field => null,
            'push_token_valid' => false,
        ]);
    }

    /**
     * Check if device can receive push notifications.
     */
    public function canReceivePushNotifications(): bool
    {
        if (!$this->push_notifications_enabled || !$this->push_token_valid) {
            return false;
        }

        if ($this->is_blocked || !$this->is_active) {
            return false;
        }

        return $this->expo_push_token || $this->fcm_token || $this->apns_token;
    }

    /**
     * Get active push tokens.
     */
    public function getActivePushTokens(): array
    {
        if (!$this->canReceivePushNotifications()) {
            return [];
        }

        $tokens = [];
        
        if ($this->expo_push_token) {
            $tokens['expo'] = $this->expo_push_token;
        }
        
        if ($this->fcm_token) {
            $tokens['fcm'] = $this->fcm_token;
        }
        
        if ($this->apns_token) {
            $tokens['apns'] = $this->apns_token;
        }

        return $tokens;
    }

    /**
     * Check if device supports attendance features.
     */
    public function supportsAttendance(): bool
    {
        return $this->supports_qr_scanning || $this->supports_gps;
    }

    /**
     * Check if device is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && now()->lt($this->locked_until);
    }

    /**
     * Lock device for failed login attempts.
     */
    public function lockDevice(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
            'failed_login_attempts' => $this->failed_login_attempts + 1,
        ]);
    }

    /**
     * Unlock device and reset failed attempts.
     */
    public function unlockDevice(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Block device.
     */
    public function block(User $admin, string $reason): void
    {
        $this->update([
            'is_blocked' => true,
            'is_active' => false,
            'blocked_by' => $admin->id,
            'blocked_at' => now(),
            'block_reason' => $reason,
        ]);
    }

    /**
     * Unblock device.
     */
    public function unblock(): void
    {
        $this->update([
            'is_blocked' => false,
            'is_active' => true,
            'blocked_by' => null,
            'blocked_at' => null,
            'block_reason' => null,
        ]);
    }

    /**
     * Approve device.
     */
    public function approve(User $admin): void
    {
        $this->update([
            'is_active' => true,
            'is_trusted' => true,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Update offline sync status.
     */
    public function markSyncPending(): void
    {
        $this->update(['has_pending_sync' => true]);
    }

    /**
     * Mark offline data as synced.
     */
    public function markSynced(): void
    {
        $this->update([
            'has_pending_sync' => false,
            'offline_data_synced_at' => now(),
        ]);
    }

    /**
     * Get device status color for UI.
     */
    public function getStatusColor(): string
    {
        if ($this->is_blocked) return 'red';
        if ($this->isLocked()) return 'orange';
        if (!$this->is_active) return 'gray';
        if ($this->is_trusted) return 'green';
        return 'blue';
    }

    /**
     * Get device platform icon.
     */
    public function getPlatformIcon(): string
    {
        return match(strtolower($this->platform)) {
            'ios' => 'phone-portrait',
            'android' => 'phone-portrait',
            'web' => 'desktop',
            'windows' => 'laptop',
            'macos' => 'laptop',
            'linux' => 'laptop',
            default => 'device-mobile',
        };
    }

    /**
     * Scope to get active devices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_blocked', false);
    }

    /**
     * Scope to get devices with valid push tokens.
     */
    public function scopeWithValidPushTokens($query)
    {
        return $query->where('push_notifications_enabled', true)
                     ->where('push_token_valid', true)
                     ->where(function ($q) {
                         $q->whereNotNull('expo_push_token')
                           ->orWhereNotNull('fcm_token')
                           ->orWhereNotNull('apns_token');
                     });
    }

    /**
     * Scope to get devices by platform.
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get devices needing approval.
     */
    public function scopeNeedingApproval($query)
    {
        return $query->whereNull('approved_at')->where('is_active', false);
    }

    /**
     * Scope to get blocked devices.
     */
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    /**
     * Scope to get devices with pending sync.
     */
    public function scopePendingSync($query)
    {
        return $query->where('has_pending_sync', true);
    }
}