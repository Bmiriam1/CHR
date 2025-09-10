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
        Schema::create('attendance_records', function (Blueprint $table) {
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
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()
                ->constrained('schedules')
                ->onDelete('set null');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * DATE & TIME TRACKING
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->time('break_start_time')->nullable();
            $table->time('break_end_time')->nullable();
            
            // Calculated fields
            $table->decimal('hours_worked', 4, 2)->default(0)
                ->comment('Total hours worked (excluding breaks)');
            $table->decimal('break_duration', 4, 2)->default(0)
                ->comment('Break duration in hours');
            $table->decimal('overtime_hours', 4, 2)->default(0)
                ->comment('Overtime hours (>8 hours per day)');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * QR CODE & LOCATION TRACKING
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('check_in_qr_code', 255)->nullable()
                ->comment('QR code used for check-in');
            $table->string('check_out_qr_code', 255)->nullable()
                ->comment('QR code used for check-out');
            
            // Location data (optional geofencing)
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->string('check_in_location_name')->nullable();
            $table->string('check_out_location_name')->nullable();
            
            // Device information
            $table->string('check_in_device_id')->nullable();
            $table->string('check_out_device_id')->nullable();
            $table->string('check_in_ip_address', 45)->nullable();
            $table->string('check_out_ip_address', 45)->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ATTENDANCE STATUS & VALIDATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('status', [
                'present', 
                'late', 
                'absent', 
                'excused', 
                'on_leave', 
                'sick', 
                'half_day',
                'pending_approval'
            ])->default('present');
            
            $table->enum('attendance_type', [
                'regular', 
                'makeup', 
                'overtime', 
                'weekend', 
                'holiday'
            ])->default('regular');
            
            // Validation flags
            $table->boolean('is_validated')->default(false)
                ->comment('Attendance validated by supervisor');
            $table->boolean('has_anomaly')->default(false)
                ->comment('Flagged for anomalies (missing check-out, etc.)');
            $table->boolean('requires_approval')->default(false)
                ->comment('Requires supervisor approval');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PAYROLL IMPACT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('is_payable')->default(true)
                ->comment('Should be included in payroll calculations');
            $table->decimal('daily_rate_applied', 8, 2)->nullable()
                ->comment('Daily rate used for this attendance record');
            $table->decimal('calculated_pay', 8, 2)->default(0)
                ->comment('Calculated pay for this day');
            
            // Pro-rata calculations for partial days
            $table->boolean('is_partial_day')->default(false);
            $table->decimal('partial_day_percentage', 5, 2)->default(100.00)
                ->comment('Percentage of day worked (for pro-rata calculations)');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * EXCEPTIONS & NOTES
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->text('notes')->nullable()
                ->comment('Additional notes or comments');
            $table->text('exception_reason')->nullable()
                ->comment('Reason for attendance exception');
            $table->json('anomaly_details')->nullable()
                ->comment('Details of any attendance anomalies');
            
            // Weather or external factors
            $table->string('weather_condition')->nullable();
            $table->boolean('transport_issue')->default(false);
            $table->boolean('venue_issue')->default(false);
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * APPROVAL WORKFLOW
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('validated_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who validated this attendance');
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('Supervisor who approved this attendance');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SYSTEM TIMESTAMPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->timestamps();
            $table->timestamp('processed_for_payroll_at')->nullable()
                ->comment('When this record was included in payroll processing');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * INDEXES FOR PERFORMANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->unique(['company_id', 'user_id', 'program_id', 'attendance_date'], 'unique_attendance_record');
            $table->index(['company_id', 'attendance_date']);
            $table->index(['program_id', 'attendance_date']);
            $table->index(['user_id', 'attendance_date']);
            $table->index(['status', 'is_payable']);
            $table->index(['is_validated', 'requires_approval']);
            $table->index(['processed_for_payroll_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};