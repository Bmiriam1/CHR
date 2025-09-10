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
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();

            // Tenant & Relationships
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Host Information
            $table->string('name');
            $table->string('code')->unique()->comment('Unique code for QR generation');
            $table->text('description')->nullable();

            // Location Information
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country')->default('South Africa');

            // GPS Coordinates for validation
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('radius_meters', 8, 2)->default(100)
                ->comment('Acceptable radius in meters for check-in validation');

            // Contact Information
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            // QR Code Information
            $table->string('qr_code')->unique()->comment('Generated QR code string');
            $table->text('qr_code_data')->nullable()->comment('QR code payload data');
            $table->timestamp('qr_code_generated_at')->nullable();

            // Status & Configuration
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_gps_validation')->default(true);
            $table->boolean('requires_time_validation')->default(true);
            $table->time('check_in_start_time')->nullable()
                ->comment('Earliest allowed check-in time');
            $table->time('check_in_end_time')->nullable()
                ->comment('Latest allowed check-in time');
            $table->time('check_out_start_time')->nullable()
                ->comment('Earliest allowed check-out time');
            $table->time('check_out_end_time')->nullable()
                ->comment('Latest allowed check-out time');

            // Validation Settings
            $table->integer('max_daily_check_ins')->default(1)
                ->comment('Maximum check-ins allowed per day per user');
            $table->boolean('allow_multiple_check_ins')->default(false);
            $table->boolean('require_supervisor_approval')->default(false);

            // System Information
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id']);
            $table->index(['is_active', 'requires_gps_validation']);
            $table->index('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
