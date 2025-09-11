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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->year('leave_year');

            // Leave types and balances
            $table->decimal('sick_leave_entitled', 5, 2)->default(0);
            $table->decimal('sick_leave_taken', 5, 2)->default(0);
            $table->decimal('sick_leave_balance', 5, 2)->default(0);

            $table->decimal('personal_leave_entitled', 5, 2)->default(0);
            $table->decimal('personal_leave_taken', 5, 2)->default(0);
            $table->decimal('personal_leave_balance', 5, 2)->default(0);

            $table->decimal('emergency_leave_entitled', 5, 2)->default(0);
            $table->decimal('emergency_leave_taken', 5, 2)->default(0);
            $table->decimal('emergency_leave_balance', 5, 2)->default(0);

            $table->decimal('other_leave_entitled', 5, 2)->default(0);
            $table->decimal('other_leave_taken', 5, 2)->default(0);
            $table->decimal('other_leave_balance', 5, 2)->default(0);

            // Total calculations
            $table->decimal('total_entitled', 5, 2)->default(0);
            $table->decimal('total_taken', 5, 2)->default(0);
            $table->decimal('total_balance', 5, 2)->default(0);

            // Accrual settings
            $table->decimal('accrual_rate_per_month', 5, 2)->default(1.25); // 15 days per year = 1.25 per month
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['user_id', 'program_id', 'leave_year']);
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
