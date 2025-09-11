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
        Schema::create('programs', function (Blueprint $table) {
            // Primary Key
            $table->id();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * BASIC PROGRAM INFORMATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('title');
            $table->string('program_code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * TENANT & COMPANY RELATIONSHIPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable();
            $table->foreignId('program_type_id')
                ->constrained('program_types')
                ->cascadeOnDelete();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PROGRAM DATES & DURATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->date('start_date');
            $table->date('end_date');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * FINANCIAL & DAILY RATES (CRITICAL FOR PAYROLL)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('daily_rate', 8, 2);

            // Additional allowances
            $table->decimal('transport_allowance', 8, 2)->default(0);


            // Payment settings
            $table->enum('payment_frequency', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->unsignedTinyInteger('payment_day_of_month')->default(25);

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SARS & COMPLIANCE SETTINGS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            // Section 12H (Skills Development) settings
            $table->boolean('section_12h_eligible')->default(false);
            $table->string('section_12h_contract_number', 50)->nullable();
            $table->date('section_12h_start_date')->nullable();
            $table->date('section_12h_end_date')->nullable();
            $table->decimal('section_12h_allowance', 8, 2)->default(0);

            // ETI (Employment Tax Incentive) settings
            $table->boolean('eti_eligible_program')->default(false);
            $table->enum('eti_category', ['youth', 'disabled', 'other'])->nullable();

            // NQF and SAQA details
            $table->unsignedTinyInteger('nqf_level')->nullable();
            $table->string('saqa_id', 20)->nullable();
            $table->string('qualification_title')->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PROGRAM LOGISTICS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('location_type', ['onsite', 'online', 'hybrid'])->default('onsite');
            $table->string('venue')->nullable();
            $table->text('venue_address')->nullable();

            // Capacity and limits
            $table->unsignedSmallInteger('max_learners')->default(25);
            $table->unsignedSmallInteger('min_learners')->default(5);
            $table->unsignedSmallInteger('enrolled_count')->default(0);

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * BBBEE & CLIENT REQUIREMENTS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('bbbee_category', ['Cat A', 'Cat B, C, D', 'Cat E'])->nullable();
            $table->enum('is_client_hosting', ['Yes', 'No', 'Maybe'])->nullable();
            $table->text('specific_requirements')->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PERFORMANCE METRICS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->unsignedTinyInteger('learner_retention_rate')
                ->nullable()
                ->comment('Retention % 0–100');
            $table->unsignedTinyInteger('completion_rate')
                ->nullable()
                ->comment('Completion % 0–100');
            $table->unsignedTinyInteger('placement_rate')
                ->nullable()
                ->comment('Placement % 0–100');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * STAFF & MANAGEMENT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('coordinator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * STATUS & APPROVAL
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'active', 'completed', 'cancelled'])
                ->default('draft');
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SYSTEM FIELDS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->integer('sort_order')->default(0);
            $table->json('additional_settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * INDEXES FOR PERFORMANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->index(['company_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['section_12h_eligible', 'eti_eligible_program']);
            $table->index(['is_approved', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
