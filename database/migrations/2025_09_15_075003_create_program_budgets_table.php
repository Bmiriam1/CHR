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
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('budget_start_date');
            $table->date('budget_end_date');
            $table->string('budget_name');
            $table->text('description')->nullable();
            $table->decimal('travel_daily_rate', 8, 2)->default(0);
            $table->decimal('online_daily_rate', 8, 2)->default(0);
            $table->decimal('equipment_daily_rate', 8, 2)->default(0);
            $table->decimal('onsite_daily_rate', 8, 2)->default(0);
            $table->decimal('travel_allowance', 8, 2)->default(0);
            $table->decimal('meal_allowance', 8, 2)->default(0);
            $table->decimal('accommodation_allowance', 8, 2)->default(0);
            $table->decimal('equipment_allowance', 8, 2)->default(0);
            $table->decimal('total_budget', 12, 2)->default(0);
            $table->decimal('used_budget', 12, 2)->default(0);
            $table->decimal('remaining_budget', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_calculate_rates')->default(true);
            $table->json('rate_calculation_rules')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'active', 'completed'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
