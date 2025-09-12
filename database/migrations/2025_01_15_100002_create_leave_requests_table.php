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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();

            // Leave details
            $table->string('leave_type', 20)
                ->comment('Leave type code (for backward compatibility)');
            $table->date('start_date')
                ->comment('Start date of leave');
            $table->date('end_date')
                ->comment('End date of leave');

            // Request information
            $table->text('reason')
                ->comment('Reason for leave request');
            $table->text('notes')->nullable()
                ->comment('Additional notes');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('pending')
                ->comment('Status of leave request');

            // Approval information
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable()
                ->comment('Approval timestamp');
            $table->text('rejection_reason')->nullable()
                ->comment('Reason for rejection');

            // Emergency leave
            $table->boolean('is_emergency')->default(false)
                ->comment('Whether this is an emergency leave request');

            // Medical certificate
            $table->boolean('requires_medical_certificate')->default(false)
                ->comment('Whether medical certificate is required');
            $table->string('attachment_path')->nullable()
                ->comment('Path to attached medical certificate');
            $table->string('medical_certificate_path')->nullable()
                ->comment('Path to medical certificate file');
            $table->date('medical_certificate_date')->nullable()
                ->comment('Date on medical certificate');
            $table->string('medical_practitioner_name')->nullable()
                ->comment('Name of medical practitioner');
            $table->string('medical_practitioner_practice_number')->nullable()
                ->comment('Practice number of medical practitioner');

            // Payroll integration
            $table->boolean('is_paid_leave')->default(true)
                ->comment('Whether this leave is paid');
            $table->decimal('daily_rate_at_time', 8, 2)->default(0)
                ->comment('Daily rate at time of leave');
            $table->decimal('total_leave_pay', 10, 2)->default(0)
                ->comment('Total leave pay amount');
            $table->boolean('affects_tax_calculation')->default(false)
                ->comment('Whether this affects tax calculation');
            $table->decimal('tax_deduction_amount', 10, 2)->default(0)
                ->comment('Tax deduction amount for this leave');

            // Advanced leave
            $table->boolean('is_advance_leave')->default(false)
                ->comment('Whether this is advance leave (before accrual)');

            // Return to work
            $table->date('return_to_work_date')->nullable()
                ->comment('Actual return to work date');
            $table->text('return_to_work_notes')->nullable()
                ->comment('Notes about return to work');

            // Workflow
            $table->json('approval_workflow')->nullable()
                ->comment('JSON array of approval workflow steps');
            $table->unsignedTinyInteger('current_approval_step')->default(1)
                ->comment('Current step in approval workflow');

            // Submission
            $table->timestamp('submitted_at')
                ->comment('When the request was submitted');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['leave_type_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'submitted_at']);
            $table->index(['approved_by', 'approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
