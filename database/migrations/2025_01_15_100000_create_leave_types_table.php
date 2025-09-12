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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();

            // Basic leave type information
            $table->string('code', 20)->unique()
                ->comment('Unique code for leave type (e.g., ANNUAL, SICK, MATERNITY)');
            $table->string('name', 100)
                ->comment('Human-readable name of leave type');
            $table->text('description')->nullable()
                ->comment('Detailed description of leave type');

            // Entitlement and accrual settings
            $table->decimal('annual_entitlement_days', 5, 2)->default(0)
                ->comment('Annual entitlement in days');
            $table->decimal('accrual_rate_per_month', 5, 2)->default(0)
                ->comment('Monthly accrual rate');
            $table->decimal('max_carry_over_days', 5, 2)->default(0)
                ->comment('Maximum days that can be carried over to next year');

            // Service requirements
            $table->unsignedTinyInteger('min_service_months')->default(0)
                ->comment('Minimum months of service required');

            // Medical certificate requirements
            $table->boolean('requires_medical_certificate')->default(false)
                ->comment('Whether medical certificate is required');
            $table->unsignedTinyInteger('medical_cert_required_after_days')->default(2)
                ->comment('Medical certificate required after this many days');

            // Tax and compliance
            $table->boolean('is_taxable')->default(false)
                ->comment('Whether this leave type is taxable');
            $table->string('sars_code', 10)->nullable()
                ->comment('SARS code for reporting (e.g., AL, SL, ML)');
            $table->boolean('affects_etv')->default(false)
                ->comment('Whether this affects ETI calculations');

            // Usage rules
            $table->boolean('allows_partial_days')->default(false)
                ->comment('Whether partial days are allowed');
            $table->boolean('allows_advance_request')->default(false)
                ->comment('Whether advance requests are allowed');
            $table->unsignedTinyInteger('min_notice_days')->default(0)
                ->comment('Minimum notice period in days');
            $table->unsignedTinyInteger('max_consecutive_days')->default(0)
                ->comment('Maximum consecutive days allowed (0 = no limit)');

            // Role restrictions
            $table->json('applicable_to_roles')->nullable()
                ->comment('JSON array of roles this leave type applies to');

            // Status
            $table->boolean('is_active')->default(true)
                ->comment('Whether this leave type is active');

            $table->timestamps();

            // Indexes
            $table->index(['code', 'is_active']);
            $table->index(['is_active', 'applicable_to_roles']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
