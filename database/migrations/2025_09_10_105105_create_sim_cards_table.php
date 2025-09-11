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
        Schema::create('sim_cards', function (Blueprint $table) {
            $table->id();

            // Company relationship
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // SIM card details
            $table->string('phone_number')->unique();
            $table->string('serial_number')->unique();
            $table->string('service_provider')->default('Telkom');
            $table->decimal('cost_price', 8, 2);
            $table->decimal('selling_price', 8, 2);

            // Status tracking
            $table->enum('status', ['available', 'allocated', 'deactivated'])->default('available');
            $table->boolean('is_active')->default(true);

            // Metadata
            $table->text('notes')->nullable();
            $table->datetime('purchased_at')->nullable();
            $table->datetime('activated_at')->nullable();
            $table->datetime('deactivated_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['service_provider', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_cards');
    }
};
