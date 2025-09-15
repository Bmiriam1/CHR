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
        Schema::create('companies', function (Blueprint $table) {
            // Primary Key & Tenant Identifier
            $table->id();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * TENANT & HIERARCHY
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('parent_company_id')->nullable()
                ->constrained('companies')->onDelete('set null')
                ->comment('Parent company for multi-branch organizations');

            $table->string('tenant_key')->unique()
                ->comment('Unique identifier for multi-tenant isolation');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * COMPANY DETAILS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('company_registration_number')->nullable()->unique();
            $table->string('vat_number', 20)->nullable();
            $table->boolean('vat_vendor')->default(false);

            // Industry classification
            $table->string('sic_code', 10)->nullable()->comment('Standard Industrial Classification');
            $table->string('industry_sector')->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SARS COMPLIANCE REFERENCES (CRITICAL)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('paye_reference_number', 20)->nullable()->unique()
                ->comment('SARS PAYE reference for EMP201/EMP501');
            $table->string('uif_reference_number', 20)->nullable()
                ->comment('UIF reference for monthly declarations');
            $table->string('sdl_reference_number', 20)->nullable()
                ->comment('SDL reference number');
            $table->string('wsp_sdp_number', 20)->nullable()
                ->comment('WSP/SDF number for skills development');

            // SARS registration details
            $table->date('paye_registration_date')->nullable();
            $table->date('uif_registration_date')->nullable();
            $table->date('sdl_registration_date')->nullable();

            // Tax year and periods
            $table->enum('tax_year_end', ['February', 'March'])->default('February')
                ->comment('Company tax year end month');
            $table->date('first_paye_period')->nullable()
                ->comment('First PAYE period for this company');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * CONTACT INFORMATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('website')->nullable();

            // Physical Address (for SARS correspondence)
            $table->string('physical_address_line1')->nullable();
            $table->string('physical_address_line2')->nullable();
            $table->string('physical_suburb')->nullable();
            $table->string('physical_city')->nullable();
            $table->string('physical_postal_code', 10)->nullable();
            $table->foreignId('province_id')->nullable()
                ->constrained('provinces')->onDelete('set null');

            // Postal Address (for SARS correspondence)
            $table->string('postal_address_line1')->nullable();
            $table->string('postal_address_line2')->nullable();
            $table->string('postal_suburb')->nullable();
            $table->string('postal_city')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('postal_country_code', 3)->default('ZAF');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PAYROLL & COMPLIANCE SETTINGS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            // Default payroll settings
            $table->enum('default_pay_frequency', ['monthly', 'weekly', 'biweekly'])->default('monthly');
            $table->unsignedTinyInteger('pay_day_of_month')->default(25)
                ->comment('Default pay day (1-31)');

            // ETI Settings
            $table->boolean('eti_registered')->default(false)
                ->comment('Registered for Employment Tax Incentive');
            $table->date('eti_registration_date')->nullable();
            $table->string('eti_certificate_number', 20)->nullable();

            // UIF & SDL settings
            $table->boolean('uif_exempt')->default(false);
            $table->boolean('sdl_exempt')->default(false);
            $table->decimal('sdl_rate_override', 5, 4)->nullable()
                ->comment('Override SDL rate if different from standard 1%');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * LEAVE POLICY DEFAULTS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->unsignedTinyInteger('annual_leave_days')->default(21)
                ->comment('Annual leave entitlement in days');
            $table->unsignedTinyInteger('sick_leave_days')->default(30)
                ->comment('Sick leave days per 3-year cycle');
            $table->unsignedTinyInteger('family_leave_days')->default(3)
                ->comment('Family responsibility leave days per year');

            $table->boolean('allow_leave_carryover')->default(true);
            $table->unsignedTinyInteger('max_carryover_days')->default(21);
            $table->boolean('allow_leave_encashment')->default(false);

            // Leave approval settings
            $table->unsignedTinyInteger('leave_notice_days')->default(2)
                ->comment('Minimum notice days for leave requests');
            $table->unsignedTinyInteger('max_consecutive_days')->default(10)
                ->comment('Max consecutive leave days without special approval');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * BANKING & PAYMENT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_code', 10)->nullable();
            $table->string('bank_account_number', 20)->nullable();
            $table->enum('bank_account_type', ['current', 'cheque', 'transmission'])->nullable();
            $table->string('bank_account_holder')->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SYSTEM SETTINGS & STATUS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false)
                ->comment('Company details verified by admin');

            // Subscription/billing
            $table->enum('subscription_tier', ['basic', 'professional', 'enterprise'])->default('basic');
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->boolean('billing_active')->default(true);

            // System limits
            $table->unsignedInteger('max_learners')->default(100)
                ->comment('Maximum learners allowed for this company');
            $table->unsignedInteger('max_programs')->default(10)
                ->comment('Maximum active programs allowed');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * AUDIT & COMPLIANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('compliance_settings')->nullable()
                ->comment('JSON object storing additional compliance settings');
            $table->json('notification_preferences')->nullable()
                ->comment('Email/SMS notification preferences');

            $table->timestamp('last_compliance_check')->nullable();
            $table->timestamp('last_backup_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * INDEXES FOR PERFORMANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->index(['tenant_key', 'is_active']);
            $table->index(['parent_company_id', 'is_active']);
            $table->index(['paye_reference_number']);
            $table->index(['uif_reference_number']);
            $table->index(['subscription_tier', 'billing_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
