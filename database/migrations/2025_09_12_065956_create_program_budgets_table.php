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
        Schema::create('program_budgets', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Budget period
            $table->date('budget_start_date');
            $table->date('budget_end_date');
            $table->string('budget_name');
            $table->text('description')->nullable();

            // Daily rates for different attendance types
            $table->decimal('travel_daily_rate', 8, 2)->default(0)
                ->comment('Daily rate for travel-based attendance');
            $table->decimal('online_daily_rate', 8, 2)->default(0)
                ->comment('Daily rate for online attendance');
            $table->decimal('equipment_daily_rate', 8, 2)->default(0)
                ->comment('Daily rate for equipment hire attendance');
            $table->decimal('onsite_daily_rate', 8, 2)->default(0)
                ->comment('Daily rate for onsite attendance');

            // Allowances
            $table->decimal('travel_allowance', 8, 2)->default(0)
                ->comment('Daily travel allowance');
            $table->decimal('meal_allowance', 8, 2)->default(0)
                ->comment('Daily meal allowance');
            $table->decimal('accommodation_allowance', 8, 2)->default(0)
                ->comment('Daily accommodation allowance');
            $table->decimal('equipment_allowance', 8, 2)->default(0)
                ->comment('Daily equipment allowance');

            // Budget totals
            $table->decimal('total_budget', 12, 2)->default(0)
                ->comment('Total budget allocated for this period');
            $table->decimal('used_budget', 12, 2)->default(0)
                ->comment('Budget used so far');
            $table->decimal('remaining_budget', 12, 2)->default(0)
                ->comment('Remaining budget');

            // Budget settings
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_calculate_rates')->default(true)
                ->comment('Automatically calculate rates based on attendance type');
            $table->json('rate_calculation_rules')->nullable()
                ->comment('JSON rules for rate calculations');

            // Approval workflow
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'active', 'completed'])
                ->default('draft');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // System
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['program_id', 'is_active']);
            $table->index(['company_id', 'status']);
            $table->index(['budget_start_date', 'budget_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_budgets');
    }
};
