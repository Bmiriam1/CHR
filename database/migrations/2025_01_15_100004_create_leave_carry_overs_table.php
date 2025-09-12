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
        Schema::create('leave_carry_overs', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();

            // Carry over details
            $table->unsignedSmallInteger('from_year')
                ->comment('Year from which leave is carried over');
            $table->unsignedSmallInteger('to_year')
                ->comment('Year to which leave is carried over');

            // Balance information
            $table->decimal('balance_at_year_end', 5, 2)
                ->comment('Leave balance at end of from_year');
            $table->decimal('carried_over_days', 5, 2)
                ->comment('Days carried over to next year');
            $table->decimal('forfeited_days', 5, 2)->default(0)
                ->comment('Days forfeited due to carry over limits');

            // Expiry
            $table->date('carry_over_expiry_date')
                ->comment('Date when carried over leave expires');

            // Additional information
            $table->text('notes')->nullable()
                ->comment('Additional notes about carry over');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'leave_type_id', 'from_year', 'to_year']);
            $table->index(['user_id', 'to_year']);
            $table->index(['leave_type_id', 'from_year']);
            $table->index(['carry_over_expiry_date']);
            $table->index(['company_id', 'from_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_carry_overs');
    }
};
