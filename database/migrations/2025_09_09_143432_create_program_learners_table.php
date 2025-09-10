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
        Schema::create('program_learners', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Enrollment details
            $table->date('enrollment_date')->default(now());
            $table->date('completion_date')->nullable();
            $table->enum('status', ['enrolled', 'active', 'completed', 'withdrawn', 'terminated'])
                ->default('enrolled');

            // ETI specific fields
            $table->boolean('eti_eligible')->default(false);
            $table->decimal('eti_monthly_amount', 8, 2)->default(0);
            $table->unsignedTinyInteger('eti_months_claimed')->default(0);

            // Section 12H specific fields
            $table->boolean('section_12h_eligible')->default(false);
            $table->string('section_12h_contract_number')->nullable();
            $table->date('section_12h_start_date')->nullable();
            $table->date('section_12h_end_date')->nullable();
            $table->decimal('section_12h_allowance', 8, 2)->default(0);

            // Performance tracking
            $table->unsignedTinyInteger('attendance_percentage')->nullable();
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['program_id', 'user_id']);
            $table->index(['status', 'eti_eligible']);
            $table->index(['enrollment_date', 'completion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_learners');
    }
};
