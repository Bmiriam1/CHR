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
        // Create leave types table for SA compliance
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // e.g., 'ANNUAL', 'SICK', 'MATERNITY'
            $table->string('name');
            $table->text('description')->nullable();
            
            // SA Legal Requirements
            $table->decimal('annual_entitlement_days', 5, 2)->default(0); // Days per year
            $table->decimal('accrual_rate_per_month', 5, 2)->default(0); // Monthly accrual
            $table->decimal('max_carry_over_days', 5, 2)->default(0); // Max days to carry over
            $table->integer('min_service_months')->default(0); // Minimum service required
            $table->boolean('requires_medical_certificate')->default(false);
            $table->integer('medical_cert_required_after_days')->default(2); // After how many days
            
            // SARS Compliance
            $table->boolean('is_taxable')->default(false);
            $table->string('sars_code', 10)->nullable(); // SARS reporting code
            $table->boolean('affects_etv')->default(false); // Employment Tax Value
            
            // Business Rules
            $table->boolean('allows_partial_days')->default(false);
            $table->boolean('allows_advance_request')->default(false); // Can request future leave
            $table->integer('min_notice_days')->default(0); // Minimum notice required
            $table->integer('max_consecutive_days')->default(0); // Max consecutive days (0 = unlimited)
            $table->json('applicable_to_roles')->nullable(); // Which roles can use this leave type
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Enhance leave_balances table
        Schema::table('leave_balances', function (Blueprint $table) {
            // Add SA-specific leave types
            $table->decimal('annual_leave_entitled', 5, 2)->default(21)->after('accrual_rate_per_month'); // 21 days = 3 weeks
            $table->decimal('annual_leave_taken', 5, 2)->default(0)->after('annual_leave_entitled');
            $table->decimal('annual_leave_balance', 5, 2)->default(21)->after('annual_leave_taken');
            $table->decimal('annual_leave_carried_over', 5, 2)->default(0)->after('annual_leave_balance');

            // Sick leave (30 days per 36-month cycle as per BCEA)
            $table->decimal('sick_leave_cycle_entitled', 5, 2)->default(30)->after('annual_leave_carried_over');
            $table->decimal('sick_leave_cycle_taken', 5, 2)->default(0)->after('sick_leave_cycle_entitled');
            $table->decimal('sick_leave_cycle_balance', 5, 2)->default(30)->after('sick_leave_cycle_taken');
            $table->date('sick_leave_cycle_start')->nullable()->after('sick_leave_cycle_balance');
            $table->date('sick_leave_cycle_end')->nullable()->after('sick_leave_cycle_start');

            // Maternity leave (4 months as per BCEA)
            $table->decimal('maternity_leave_entitled', 5, 2)->default(120)->after('sick_leave_cycle_end'); // 4 months
            $table->decimal('maternity_leave_taken', 5, 2)->default(0)->after('maternity_leave_entitled');
            $table->decimal('maternity_leave_balance', 5, 2)->default(120)->after('maternity_leave_taken');

            // Paternity leave (10 consecutive days)
            $table->decimal('paternity_leave_entitled', 5, 2)->default(10)->after('maternity_leave_balance');
            $table->decimal('paternity_leave_taken', 5, 2)->default(0)->after('paternity_leave_entitled');
            $table->decimal('paternity_leave_balance', 5, 2)->default(10)->after('paternity_leave_taken');

            // Family responsibility leave (3 days per year)
            $table->decimal('family_responsibility_leave_entitled', 5, 2)->default(3)->after('paternity_leave_balance');
            $table->decimal('family_responsibility_leave_taken', 5, 2)->default(0)->after('family_responsibility_leave_entitled');
            $table->decimal('family_responsibility_leave_balance', 5, 2)->default(3)->after('family_responsibility_leave_taken');

            // Study leave (as per company policy)
            $table->decimal('study_leave_entitled', 5, 2)->default(0)->after('family_responsibility_leave_balance');
            $table->decimal('study_leave_taken', 5, 2)->default(0)->after('study_leave_entitled');
            $table->decimal('study_leave_balance', 5, 2)->default(0)->after('study_leave_taken');

            // Leave cycle dates
            $table->date('leave_year_start')->nullable()->after('study_leave_balance');
            $table->date('leave_year_end')->nullable()->after('leave_year_start');
            
            // Accrual tracking
            $table->date('employment_start_date')->nullable()->after('leave_year_end');
            $table->boolean('is_probationary')->default(true)->after('employment_start_date');
            $table->date('probation_end_date')->nullable()->after('is_probationary');
            
            // SARS compliance fields
            $table->decimal('leave_encashment_amount', 10, 2)->default(0)->after('probation_end_date');
            $table->boolean('leave_paid_in_advance')->default(false)->after('leave_encashment_amount');
        });

        // Enhance leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) {
            // Change leave_type to reference leave_types table
            $table->foreignId('leave_type_id')->nullable()->after('leave_type')->constrained('leave_types')->onDelete('restrict');
            
            // SA-specific fields
            $table->boolean('requires_medical_certificate')->default(false)->after('is_emergency');
            $table->string('medical_certificate_path')->nullable()->after('requires_medical_certificate');
            $table->date('medical_certificate_date')->nullable()->after('medical_certificate_path');
            $table->string('medical_practitioner_name')->nullable()->after('medical_certificate_date');
            $table->string('medical_practitioner_practice_number')->nullable()->after('medical_practitioner_name');
            
            // Leave payment details
            $table->boolean('is_paid_leave')->default(true)->after('medical_practitioner_practice_number');
            $table->decimal('daily_rate_at_time', 10, 2)->nullable()->after('is_paid_leave');
            $table->decimal('total_leave_pay', 10, 2)->nullable()->after('daily_rate_at_time');
            
            // SARS compliance
            $table->boolean('affects_tax_calculation')->default(false)->after('total_leave_pay');
            $table->decimal('tax_deduction_amount', 10, 2)->default(0)->after('affects_tax_calculation');
            
            // Business rules compliance
            $table->boolean('is_advance_leave')->default(false)->after('tax_deduction_amount'); // Leave taken in advance
            $table->date('return_to_work_date')->nullable()->after('is_advance_leave');
            $table->text('return_to_work_notes')->nullable()->after('return_to_work_date');
            
            // Workflow
            $table->json('approval_workflow')->nullable()->after('return_to_work_notes'); // Multi-step approval
            $table->string('current_approval_step')->nullable()->after('approval_workflow');
            $table->timestamp('submitted_at')->nullable()->after('current_approval_step');
        });

        // Create leave accruals tracking table
        Schema::create('leave_accruals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('restrict');
            
            $table->date('accrual_date'); // When the leave was accrued
            $table->decimal('days_accrued', 5, 2); // How many days accrued
            $table->decimal('running_balance', 5, 2); // Balance after this accrual
            $table->string('accrual_reason')->default('monthly'); // monthly, bonus, carry_over, etc.
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'leave_type_id', 'accrual_date']);
        });

        // Create leave carry overs table for year-end processing
        Schema::create('leave_carry_overs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('restrict');
            
            $table->year('from_year');
            $table->year('to_year');
            $table->decimal('balance_at_year_end', 5, 2);
            $table->decimal('carried_over_days', 5, 2);
            $table->decimal('forfeited_days', 5, 2)->default(0);
            $table->date('carry_over_expiry_date')->nullable(); // When carried over days expire
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'leave_type_id', 'from_year', 'to_year']);
        });

        // Create leave certificates table for medical certificates
        Schema::create('leave_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained()->onDelete('cascade');
            $table->string('certificate_type')->default('medical'); // medical, death, etc.
            $table->string('file_path');
            $table->string('original_filename');
            $table->date('certificate_date');
            $table->string('issued_by'); // Doctor name, etc.
            $table->string('practitioner_number')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('restrictions')->nullable(); // Work restrictions
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_certificates');
        Schema::dropIfExists('leave_carry_overs');
        Schema::dropIfExists('leave_accruals');
        
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropColumn([
                'leave_type_id', 'requires_medical_certificate', 'medical_certificate_path',
                'medical_certificate_date', 'medical_practitioner_name', 'medical_practitioner_practice_number',
                'is_paid_leave', 'daily_rate_at_time', 'total_leave_pay', 'affects_tax_calculation',
                'tax_deduction_amount', 'is_advance_leave', 'return_to_work_date', 'return_to_work_notes',
                'approval_workflow', 'current_approval_step', 'submitted_at'
            ]);
        });
        
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn([
                'annual_leave_entitled', 'annual_leave_taken', 'annual_leave_balance', 'annual_leave_carried_over',
                'sick_leave_cycle_entitled', 'sick_leave_cycle_taken', 'sick_leave_cycle_balance',
                'sick_leave_cycle_start', 'sick_leave_cycle_end', 'maternity_leave_entitled',
                'maternity_leave_taken', 'maternity_leave_balance', 'paternity_leave_entitled',
                'paternity_leave_taken', 'paternity_leave_balance', 'family_responsibility_leave_entitled',
                'family_responsibility_leave_taken', 'family_responsibility_leave_balance',
                'study_leave_entitled', 'study_leave_taken', 'study_leave_balance',
                'leave_year_start', 'leave_year_end', 'employment_start_date', 'is_probationary',
                'probation_end_date', 'leave_encashment_amount', 'leave_paid_in_advance'
            ]);
        });
        
        Schema::dropIfExists('leave_types');
    }
};