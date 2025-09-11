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
        Schema::create('sim_card_allocations', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('sim_card_id')
                ->constrained('sim_cards')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Allocation details
            $table->date('allocated_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'lost', 'damaged'])->default('active');

            // Financial tracking
            $table->decimal('charge_amount', 8, 2)->default(0); // Amount charged to learner
            $table->boolean('payment_required')->default(false);
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'waived'])->default('paid');

            // Audit trail
            $table->foreignId('allocated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('returned_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Metadata
            $table->text('notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->json('conditions_on_allocation')->nullable(); // Device condition when allocated
            $table->json('conditions_on_return')->nullable(); // Device condition when returned

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['program_id', 'status']);
            $table->index(['company_id', 'allocated_date']);
            $table->index(['sim_card_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_card_allocations');
    }
};
