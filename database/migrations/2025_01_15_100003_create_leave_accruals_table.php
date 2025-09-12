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
        Schema::create('leave_accruals', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();

            // Accrual details
            $table->date('accrual_date')
                ->comment('Date when accrual occurred');
            $table->decimal('days_accrued', 5, 2)
                ->comment('Days accrued (positive for accrual, negative for deduction)');
            $table->decimal('running_balance', 5, 2)
                ->comment('Running balance after this accrual');

            // Accrual reason
            $table->enum('accrual_reason', [
                'initial_entitlement',
                'monthly',
                'bonus',
                'carry_over',
                'leave_taken',
                'leave_cancelled',
                'manual_adjustment',
                'year_end_balance'
            ])->comment('Reason for accrual or deduction');

            // Additional information
            $table->text('notes')->nullable()
                ->comment('Additional notes about this accrual');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'leave_type_id']);
            $table->index(['user_id', 'accrual_date']);
            $table->index(['leave_type_id', 'accrual_reason']);
            $table->index(['accrual_date', 'accrual_reason']);
            $table->index(['company_id', 'accrual_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_accruals');
    }
};
