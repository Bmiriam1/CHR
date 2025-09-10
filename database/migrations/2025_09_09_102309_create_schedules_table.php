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
        Schema::create('schedules', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * TENANT & RELATIONSHIPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SCHEDULE DETAILS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('session_code', 50)->nullable()
                ->comment('Unique identifier for this session');
            
            // Date and time
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('break_start_time')->nullable();
            $table->time('break_end_time')->nullable();
            
            // Duration calculations
            $table->decimal('planned_duration_hours', 4, 2)
                ->comment('Planned session duration in hours');
            $table->decimal('break_duration_hours', 4, 2)->default(0)
                ->comment('Break duration in hours');
            $table->decimal('net_training_hours', 4, 2)
                ->comment('Net training hours (excluding breaks)');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * RECURRENCE PATTERN
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('recurrence_type', ['none', 'daily', 'weekly', 'monthly', 'custom'])
                ->default('none');
            $table->json('recurrence_pattern')->nullable()
                ->comment('JSON object with recurrence rules');
            $table->date('recurrence_end_date')->nullable();
            
            // Days of week for weekly recurrence
            $table->boolean('monday')->default(false);
            $table->boolean('tuesday')->default(false);
            $table->boolean('wednesday')->default(false);
            $table->boolean('thursday')->default(false);
            $table->boolean('friday')->default(false);
            $table->boolean('saturday')->default(false);
            $table->boolean('sunday')->default(false);
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * LOCATION & VENUE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('room_number')->nullable();
            $table->string('building')->nullable();
            $table->string('campus')->nullable();
            
            // Online session details
            $table->boolean('is_online')->default(false);
            $table->string('meeting_url')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->string('platform', 50)->nullable()
                ->comment('Zoom, Teams, Google Meet, etc.');
            
            // Geofencing for mobile check-in
            $table->decimal('venue_latitude', 10, 8)->nullable();
            $table->decimal('venue_longitude', 11, 8)->nullable();
            $table->unsignedSmallInteger('geofence_radius')->default(100)
                ->comment('Geofence radius in meters');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * QR CODE GENERATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('qr_code_content')->unique()
                ->comment('QR code content for attendance');
            $table->string('qr_code_path')->nullable()
                ->comment('Path to generated QR code image');
            $table->boolean('qr_code_active')->default(true)
                ->comment('Whether QR code is active for check-in');
            $table->timestamp('qr_code_valid_from')->nullable();
            $table->timestamp('qr_code_valid_until')->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * CURRICULUM & CONTENT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('module_name')->nullable();
            $table->string('unit_standard')->nullable()
                ->comment('SAQA unit standard reference');
            $table->text('learning_outcomes')->nullable()
                ->comment('Expected learning outcomes');
            $table->text('assessment_criteria')->nullable();
            $table->json('required_materials')->nullable()
                ->comment('Required materials/equipment');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ATTENDANCE TRACKING
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->unsignedSmallInteger('expected_attendees')->default(0)
                ->comment('Expected number of attendees');
            $table->unsignedSmallInteger('actual_attendees')->default(0)
                ->comment('Actual number of attendees');
            $table->decimal('attendance_rate', 5, 2)->default(0)
                ->comment('Attendance rate percentage');
            
            // Check-in settings
            $table->time('check_in_opens_at')->nullable()
                ->comment('When check-in becomes available');
            $table->time('check_in_closes_at')->nullable()
                ->comment('When check-in closes');
            $table->boolean('allow_late_check_in')->default(true);
            $table->unsignedTinyInteger('late_threshold_minutes')->default(15)
                ->comment('Minutes after start time considered late');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SESSION STATUS & MANAGEMENT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('status', [
                'scheduled', 
                'in_progress', 
                'completed', 
                'cancelled', 
                'postponed', 
                'rescheduled'
            ])->default('scheduled');
            
            $table->enum('session_type', [
                'lecture', 
                'practical', 
                'assessment', 
                'workshop', 
                'seminar', 
                'field_work', 
                'orientation',
                'other'
            ])->default('lecture');
            
            // Cancellation/rescheduling
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()
                ->constrained('users')->onDelete('set null');
            
            $table->foreignId('rescheduled_from_id')->nullable()
                ->constrained('schedules')->onDelete('set null')
                ->comment('Original schedule this was rescheduled from');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * NOTIFICATIONS & REMINDERS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('send_reminders')->default(true);
            $table->json('reminder_settings')->nullable()
                ->comment('Reminder notification settings');
            $table->timestamp('reminder_sent_at')->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SYSTEM FIELDS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->onDelete('set null');
            $table->json('additional_settings')->nullable();
            $table->timestamps();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * INDEXES FOR PERFORMANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->index(['company_id', 'session_date']);
            $table->index(['program_id', 'session_date']);
            $table->index(['session_date', 'start_time']);
            $table->index(['status', 'session_date']);
            $table->index(['instructor_id', 'session_date']);
            $table->index(['qr_code_content']);
            $table->index(['is_online']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};