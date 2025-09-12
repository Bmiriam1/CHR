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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            // Leave year
            $table->unsignedSmallInteger('leave_year')
                ->comment('Leave year (e.g., 2025)');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SOUTH AFRICAN STANDARD LEAVE TYPES
             * ─────────────────────────────────────────────────────────────────────────────
             */

            // Annual Leave (21 days per year)
            $table->decimal('annual_leave_entitled', 5, 2)->default(21)
                ->comment('Annual leave entitlement');
            $table->decimal('annual_leave_taken', 5, 2)->default(0)
                ->comment('Annual leave taken');
            $table->decimal('annual_leave_balance', 5, 2)->default(21)
                ->comment('Annual leave balance');
            $table->decimal('annual_leave_carried_over', 5, 2)->default(0)
                ->comment('Annual leave carried over from previous year');

            // Sick Leave (30 days per 36-month cycle)
            $table->decimal('sick_leave_cycle_entitled', 5, 2)->default(30)
                ->comment('Sick leave entitlement per 36-month cycle');
            $table->decimal('sick_leave_cycle_taken', 5, 2)->default(0)
                ->comment('Sick leave taken in current cycle');
            $table->decimal('sick_leave_cycle_balance', 5, 2)->default(30)
                ->comment('Sick leave balance in current cycle');
            $table->date('sick_leave_cycle_start')
                ->comment('Start date of current sick leave cycle');
            $table->date('sick_leave_cycle_end')
                ->comment('End date of current sick leave cycle');

            // Maternity Leave (4 months)
            $table->decimal('maternity_leave_entitled', 5, 2)->default(120)
                ->comment('Maternity leave entitlement (4 months)');
            $table->decimal('maternity_leave_taken', 5, 2)->default(0)
                ->comment('Maternity leave taken');
            $table->decimal('maternity_leave_balance', 5, 2)->default(120)
                ->comment('Maternity leave balance');

            // Paternity Leave (10 consecutive days)
            $table->decimal('paternity_leave_entitled', 5, 2)->default(10)
                ->comment('Paternity leave entitlement');
            $table->decimal('paternity_leave_taken', 5, 2)->default(0)
                ->comment('Paternity leave taken');
            $table->decimal('paternity_leave_balance', 5, 2)->default(10)
                ->comment('Paternity leave balance');

            // Family Responsibility Leave (3 days per year)
            $table->decimal('family_responsibility_leave_entitled', 5, 2)->default(3)
                ->comment('Family responsibility leave entitlement');
            $table->decimal('family_responsibility_leave_taken', 5, 2)->default(0)
                ->comment('Family responsibility leave taken');
            $table->decimal('family_responsibility_leave_balance', 5, 2)->default(3)
                ->comment('Family responsibility leave balance');

            // Study Leave (Company policy - 5 days)
            $table->decimal('study_leave_entitled', 5, 2)->default(5)
                ->comment('Study leave entitlement');
            $table->decimal('study_leave_taken', 5, 2)->default(0)
                ->comment('Study leave taken');
            $table->decimal('study_leave_balance', 5, 2)->default(5)
                ->comment('Study leave balance');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SERVICE AND EMPLOYMENT INFORMATION
             * ─────────────────────────────────────────────────────────────────────────────
             */

            // Leave year period
            $table->date('leave_year_start')
                ->comment('Start date of leave year');
            $table->date('leave_year_end')
                ->comment('End date of leave year');

            // Employment information
            $table->date('employment_start_date')
                ->comment('Employment start date for leave accrual');
            $table->boolean('is_probationary')->default(false)
                ->comment('Whether employee is still in probation');
            $table->date('probation_end_date')->nullable()
                ->comment('End date of probation period');

            // Accrual settings
            $table->decimal('accrual_rate_per_month', 5, 2)->default(1.75)
                ->comment('Monthly accrual rate (21/12 for annual leave)');

            // Status
            $table->boolean('is_active')->default(true)
                ->comment('Whether this balance record is active');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'program_id', 'leave_year']);
            $table->index(['user_id', 'leave_year']);
            $table->index(['company_id', 'leave_year']);
            $table->index(['is_active', 'leave_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
