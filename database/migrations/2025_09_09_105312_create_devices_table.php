<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            // Primary Key
            $table->id();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * USER & TENANT RELATIONSHIPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * DEVICE IDENTIFICATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('device_id')->unique()
                ->comment('Unique device identifier');
            $table->string('device_name')->nullable()
                ->comment('User-friendly device name');
            $table->string('device_type', 50)
                ->comment('mobile, tablet, desktop, etc.');

            // Platform information
            $table->string('platform', 50)
                ->comment('iOS, Android, Web, etc.');
            $table->string('platform_version')->nullable()
                ->comment('OS version (e.g., iOS 17.1, Android 14)');
            $table->string('app_version')->nullable()
                ->comment('App version installed on device');

            // Hardware information
            $table->string('device_model')->nullable()
                ->comment('Device model (iPhone 15, Samsung Galaxy S24, etc.)');
            $table->string('manufacturer')->nullable()
                ->comment('Apple, Samsung, Google, etc.');
            $table->string('browser')->nullable()
                ->comment('Browser name for web devices');
            $table->string('browser_version')->nullable()
                ->comment('Browser version for web devices');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PUSH NOTIFICATIONS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->text('expo_push_token')->nullable()
                ->comment('Expo push notification token');
            $table->text('fcm_token')->nullable()
                ->comment('Firebase Cloud Messaging token');
            $table->text('apns_token')->nullable()
                ->comment('Apple Push Notification Service token');

            // Push notification preferences
            $table->boolean('push_notifications_enabled')->default(true)
                ->comment('User enabled push notifications');
            $table->json('notification_preferences')->nullable()
                ->comment('Specific notification type preferences');

            // Push token management
            $table->timestamp('push_token_updated_at')->nullable()
                ->comment('When push token was last updated');
            $table->timestamp('push_token_expires_at')->nullable()
                ->comment('When push token expires (if applicable)');
            $table->boolean('push_token_valid')->default(true)
                ->comment('Whether push token is currently valid');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * DEVICE ACTIVITY & STATUS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->timestamp('first_seen_at')->nullable()
                ->comment('When device was first registered');
            $table->timestamp('last_seen_at')->nullable()
                ->comment('Last activity timestamp');
            $table->timestamp('last_login_at')->nullable()
                ->comment('Last successful login from this device');

            // Device status
            $table->boolean('is_active')->default(true)
                ->comment('Device is active and can be used');
            $table->boolean('is_trusted')->default(false)
                ->comment('Device has been marked as trusted');
            $table->boolean('is_blocked')->default(false)
                ->comment('Device has been blocked from access');

            // Location information (last known)
            $table->decimal('last_latitude', 10, 8)->nullable()
                ->comment('Last known latitude');
            $table->decimal('last_longitude', 11, 8)->nullable()
                ->comment('Last known longitude');
            $table->string('last_location_name')->nullable()
                ->comment('Last known location name');
            $table->timestamp('location_updated_at')->nullable()
                ->comment('When location was last updated');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * BIOMETRIC & SECURITY
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('biometric_enabled')->default(false)
                ->comment('Biometric authentication enabled');
            $table->string('biometric_type', 50)->nullable()
                ->comment('fingerprint, face_id, voice, etc.');
            $table->boolean('pin_enabled')->default(false)
                ->comment('PIN authentication enabled');

            // Security features
            $table->boolean('screen_lock_enabled')->default(false)
                ->comment('Device has screen lock enabled');
            $table->boolean('device_encrypted')->default(false)
                ->comment('Device storage is encrypted');
            $table->integer('failed_login_attempts')->default(0)
                ->comment('Number of failed login attempts');
            $table->timestamp('locked_until')->nullable()
                ->comment('Device locked until this timestamp');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ATTENDANCE & CHECK-IN CAPABILITIES
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('supports_qr_scanning')->default(false)
                ->comment('Device can scan QR codes');
            $table->boolean('supports_gps')->default(false)
                ->comment('Device has GPS capabilities');
            $table->boolean('supports_camera')->default(false)
                ->comment('Device has camera access');
            $table->boolean('supports_offline_mode')->default(false)
                ->comment('Device can work offline');

            // Check-in preferences for this device
            $table->boolean('require_location_for_checkin')->default(true)
                ->comment('Require location verification for check-in');
            $table->boolean('auto_checkout_enabled')->default(false)
                ->comment('Enable automatic check-out');
            $table->unsignedSmallInteger('auto_checkout_hours')->default(8)
                ->comment('Hours after which to auto check-out');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * NETWORK & CONNECTION INFO
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('ip_address', 45)->nullable()
                ->comment('Last known IP address');
            $table->string('user_agent')->nullable()
                ->comment('User agent string');
            $table->string('network_type', 50)->nullable()
                ->comment('wifi, cellular, ethernet, etc.');
            $table->string('carrier')->nullable()
                ->comment('Mobile network carrier');
            $table->string('timezone')->nullable()
                ->comment('Device timezone');
            $table->string('locale', 10)->nullable()
                ->comment('Device locale (en-US, en-ZA, etc.)');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * OFFLINE CAPABILITIES
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('offline_data')->nullable()
                ->comment('Cached data for offline functionality');
            $table->timestamp('offline_data_synced_at')->nullable()
                ->comment('When offline data was last synced');
            $table->boolean('has_pending_sync')->default(false)
                ->comment('Device has data pending synchronization');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * AUDIT & COMPLIANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('device_fingerprint')->nullable()
                ->comment('Additional device identification data');
            $table->json('app_permissions')->nullable()
                ->comment('App permissions granted on device');
            $table->text('registration_notes')->nullable()
                ->comment('Notes about device registration');

            // Admin actions
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('Admin who approved this device');
            $table->timestamp('approved_at')->nullable()
                ->comment('When device was approved');
            $table->foreignId('blocked_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('Admin who blocked this device');
            $table->timestamp('blocked_at')->nullable()
                ->comment('When device was blocked');
            $table->text('block_reason')->nullable()
                ->comment('Reason for blocking device');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SYSTEM TIMESTAMPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->timestamps();
            $table->softDeletes();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * INDEXES FOR PERFORMANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->index(['user_id', 'is_active']);
            $table->index(['platform', 'is_active']);
            $table->index(['last_seen_at']);
            $table->index(['is_blocked', 'is_active']);
            // $table->index(['expo_push_token'], 'devices_expo_push_token_index'); // Disabled - TEXT field can't be indexed without length
            $table->index(['push_notifications_enabled', 'push_token_valid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
