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
        Schema::create('payment_schedules', function (Blueprint $table) {
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
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SCHEDULE IDENTIFICATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('schedule_number', 50)->unique()
                ->comment('Unique schedule reference number');
            $table->string('title')
                ->comment('Schedule title/description');
            $table->enum('frequency', ['weekly', 'bi_weekly', 'monthly'])
                ->comment('Payment schedule frequency');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PERIOD DEFINITION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->date('period_start_date')
                ->comment('Start date of payment period');
            $table->date('period_end_date')
                ->comment('End date of payment period');
            $table->date('payment_due_date')
                ->comment('When payment is due');
            $table->date('attendance_cutoff_date')
                ->comment('Last date to include attendance records');
            
            // Period numbering
            $table->unsignedSmallInteger('year')
                ->comment('Year this schedule is for');
            $table->unsignedTinyInteger('period_number')
                ->comment('Period number within the year');
            $table->unsignedTinyInteger('week_number')->nullable()
                ->comment('Week number for weekly schedules');
            $table->unsignedTinyInteger('month_number')->nullable()
                ->comment('Month number for monthly schedules');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ATTENDANCE SUMMARY
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('learner_summary')
                ->comment('Summary of learners and their attendance');
            $table->unsignedInteger('total_learners')
                ->comment('Total number of learners included');
            $table->decimal('total_days_worked', 8, 2)->default(0)
                ->comment('Total days worked by all learners');
            $table->decimal('total_hours_worked', 10, 2)->default(0)
                ->comment('Total hours worked by all learners');
            $table->decimal('total_overtime_hours', 10, 2)->default(0)
                ->comment('Total overtime hours by all learners');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PAYMENT CALCULATIONS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('total_basic_earnings', 12, 2)->default(0)
                ->comment('Total basic earnings for all learners');
            $table->decimal('total_overtime_pay', 12, 2)->default(0)
                ->comment('Total overtime pay for all learners');
            $table->decimal('total_allowances', 12, 2)->default(0)
                ->comment('Total allowances for all learners');
            $table->decimal('total_gross_pay', 12, 2)->default(0)
                ->comment('Total gross pay before deductions');
            
            // Deductions totals
            $table->decimal('total_paye_tax', 12, 2)->default(0)
                ->comment('Total PAYE tax deductions');
            $table->decimal('total_uif_employee', 10, 2)->default(0)
                ->comment('Total UIF employee contributions');
            $table->decimal('total_uif_employer', 10, 2)->default(0)
                ->comment('Total UIF employer contributions');
            $table->decimal('total_sdl_contribution', 10, 2)->default(0)
                ->comment('Total SDL contributions (employer)');
            $table->decimal('total_eti_benefit', 10, 2)->default(0)
                ->comment('Total ETI benefits for employer');
            $table->decimal('total_deductions', 12, 2)->default(0)
                ->comment('Total deductions from gross pay');
            $table->decimal('total_net_pay', 12, 2)->default(0)
                ->comment('Total net pay to all learners');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * EMPLOYER COSTS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('employer_total_cost', 12, 2)->default(0)
                ->comment('Total cost to employer (gross + employer UIF + SDL - ETI)');
            $table->decimal('employer_uif_cost', 10, 2)->default(0)
                ->comment('Employer UIF contribution cost');
            $table->decimal('employer_sdl_cost', 10, 2)->default(0)
                ->comment('Employer SDL contribution cost');
            $table->decimal('employer_eti_saving', 10, 2)->default(0)
                ->comment('Total ETI savings for employer');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * STATUS & PROCESSING
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('status', [
                'draft',
                'calculated', 
                'approved',
                'exported',
                'processed',
                'cancelled'
            ])->default('draft');
            
            $table->boolean('is_final')->default(false)
                ->comment('Schedule is finalized and cannot be modified');
            $table->boolean('includes_overtime')->default(false)
                ->comment('Schedule includes overtime calculations');
            $table->boolean('includes_allowances')->default(false)
                ->comment('Schedule includes allowance payments');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * EXPORT FUNCTIONALITY
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('export_formats')->nullable()
                ->comment('Available export formats (pdf, excel, csv, xml)');
            $table->timestamp('last_exported_at')->nullable()
                ->comment('When schedule was last exported');
            $table->string('export_file_path')->nullable()
                ->comment('Path to last exported file');
            $table->json('export_settings')->nullable()
                ->comment('Export format preferences and settings');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * APPROVAL WORKFLOW
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('created_by')
                ->constrained('users')
                ->comment('User who created this schedule');
            
            $table->foreignId('calculated_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who calculated this schedule');
            $table->timestamp('calculated_at')->nullable()
                ->comment('When schedule was calculated');
            
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who approved this schedule');
            $table->timestamp('approved_at')->nullable()
                ->comment('When schedule was approved');
            $table->text('approval_notes')->nullable()
                ->comment('Notes from approver');
            
            $table->foreignId('exported_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who exported this schedule');
            $table->timestamp('exported_at')->nullable()
                ->comment('When schedule was exported');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * COMPLIANCE & AUDIT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('calculation_metadata')->nullable()
                ->comment('Metadata about calculation process');
            $table->json('attendance_filters')->nullable()
                ->comment('Filters applied to attendance records');
            $table->string('calculation_hash', 64)->nullable()
                ->comment('Hash of calculation inputs for integrity');
            $table->unsignedInteger('attendance_records_count')->default(0)
                ->comment('Number of attendance records included');
            $table->text('notes')->nullable()
                ->comment('Additional notes about this schedule');
            
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
            $table->unique(['company_id', 'program_id', 'frequency', 'period_start_date'], 'unique_payment_schedule');
            $table->index(['company_id', 'status']);
            $table->index(['program_id', 'period_start_date']);
            $table->index(['frequency', 'year', 'period_number']);
            $table->index(['payment_due_date']);
            $table->index(['status', 'is_final']);
            $table->index(['last_exported_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};