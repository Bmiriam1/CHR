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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id();
            $table->string('firebase_uid')->nullable()->unique();


            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PERSONAL & IDENTITY (SARS/ETI depend on accurate identity)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('first_name');
            $table->string('last_name');
            $table->string('initials')->nullable();                   // IRP5 often includes initials
            $table->string('preferred_name')->nullable();

            // National ID / Passport
            $table->string('id_number')->nullable()->index();         // SA ID (13 digits) OR leave null if passport used
            $table->enum('id_type', ['sa_id', 'passport', 'asylum', 'other'])->default('sa_id');
            $table->string('passport_number')->nullable();
            $table->string('passport_country_code', 3)->nullable();   // ISO3166-1 alpha-3 (e.g., ZAF)
            $table->date('passport_expiry')->nullable();

            // Tax reference
            $table->string('tax_number')->nullable()->index();        // SARS tax ref (not always available for IT3(a))
            $table->boolean('identity_verified')->default(false);

            // Demographics (for ETI age checks etc.)
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();

            // Citizenship / Residency (IRP5 Nature of person and residency influence)
            $table->enum('citizenship_status', ['citizen', 'permanent_resident', 'work_permit', 'other'])->nullable();
            $table->enum('tax_residency_status', ['resident', 'non_resident'])->default('resident'); // SARS residency
            $table->string('work_permit_number')->nullable();
            $table->date('work_permit_expiry')->nullable();

            // Employment Equity & Disability (you already had these)
            $table->foreignId('race_id')->nullable()->constrained();
            $table->boolean('disability')->default(false);            // Impacts certain tax allowances

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * CONTACT
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->unique()->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * AUTH & STATUS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('password');
            $table->boolean('is_active')->default(true);

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ADDRESSES (IRP5 requires physical & postal; keep normalized but ok inline)
             * ─────────────────────────────────────────────────────────────────────────────
             * Consider normalizing into an addresses table if you prefer. For now:
             */
            // Physical (Residential) Address
            $table->string('res_addr_line1')->nullable();
            $table->string('res_addr_line2')->nullable();
            $table->string('res_suburb')->nullable();
            $table->string('res_city')->nullable();
            $table->string('res_postcode', 10)->nullable();
            $table->string('res_country_code', 3)->nullable()->default('ZAF');

            // Postal Address
            $table->string('post_addr_line1')->nullable();
            $table->string('post_addr_line2')->nullable();
            $table->string('post_suburb')->nullable();
            $table->string('post_city')->nullable();
            $table->string('post_postcode', 10)->nullable();
            $table->string('post_country_code', 3)->nullable()->default('ZAF');

            // (Legacy simple address fields retained for compatibility; can be removed later)
            $table->string('street_address')->nullable();
            $table->string('town')->nullable();
            $table->foreignId('province_id')->nullable()
                ->constrained('provinces')->onDelete('set null');

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * EMPLOYMENT BASICS (needed for IRP5/UIF/ETI calculations)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('occupation')->nullable();
            $table->boolean('is_learner')->default(false);
            $table->boolean('is_employee')->default(false);

            $table->string('employee_number')->nullable()->unique();  // Payroll/HR number (IRP5 certificate reference)
            $table->string('payroll_number')->nullable()->unique();   // If different from employee_number

            $table->date('employment_start_date')->nullable();        // ETI qualifying period & leave accrual start
            $table->date('employment_end_date')->nullable();          // IRP5 period-to, UIF termination
            $table->enum('employment_status', ['active', 'terminated', 'suspended', 'on_leave'])
                ->default('active')->index();
            $table->enum('employment_basis', ['full_time', 'part_time', 'fixed_term', 'learner', 'intern', 'other'])
                ->default('learner');

            // Termination reason (UIF & IT3(a)/IRP5 nuances)
            $table->string('termination_reason_code', 4)->nullable(); // map to UIF reason codes; keep dictionary-driven
            $table->date('last_working_day')->nullable();

            // Pay frequency can influence tax calc method & reporting periods
            $table->enum('pay_frequency', ['monthly', 'weekly', 'biweekly'])->default('monthly');

            // SARS directive (occasionally needed for special tax situations)
            $table->string('tax_directive_number')->nullable();
            $table->date('tax_directive_date')->nullable();

            // ETI flags (permanent user attributes; month-by-month values live in payroll tables)
            $table->boolean('eti_eligible')->default(false);          // eligibility baseline (age/work status)
            $table->unsignedSmallInteger('eti_months_claimed')->default(0); // running total (1–24), also tracked per-month elsewhere

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * STATUTORY CONTRIBUTIONS FLAGS (company defaults might override at assignment)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('uif_exempt')->default(false);            // Rare; defaults to false
            $table->boolean('sdl_exempt')->default(false);            // Typically company-level, but allow per-user override

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * BANKING (for stipend payments; consider separate table if multiple accounts)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('bank_account_holder')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_code', 20)->nullable();
            $table->string('bank_account_number')->nullable();
            $table->enum('bank_account_type', ['cheque', 'savings', 'transmission', 'other'])->nullable();

            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * LEGACY / EDUCATION LINKS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('student_number')->nullable()->unique();
            // Learnership/12H contract details should live on the enrollment/program pivot,
            // not on the user (since they vary per program & year). We'll keep user-level flags only:
            $table->boolean('twelve_h_candidate')->default(false);    // qualifies for Section 12H via an active contract

            $table->rememberToken();
            $table->timestamps();

            // Useful compound indexes for reporting/perf
            $table->index(['employment_status', 'is_active']);
            $table->index(['tax_residency_status', 'citizenship_status']);
            $table->index(['employment_start_date', 'employment_end_date']);
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
