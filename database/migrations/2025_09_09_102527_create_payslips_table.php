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
        Schema::create('payslips', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * TENANT & RELATIONSHIPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PAYROLL PERIOD & IDENTIFICATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->string('payslip_number', 50)->unique()
                ->comment('Unique payslip reference number');
            $table->date('payroll_period_start');
            $table->date('payroll_period_end');
            $table->date('pay_date');
            $table->unsignedSmallInteger('pay_year');
            $table->unsignedTinyInteger('pay_month');
            $table->unsignedTinyInteger('pay_period_number')
                ->comment('Pay period number within the year');
            
            // Tax year period (March to February in SA)
            $table->unsignedSmallInteger('tax_year')
                ->comment('Tax year (e.g., 2024 for 2024/2025 tax year)');
            $table->unsignedTinyInteger('tax_month_number')
                ->comment('Month number in tax year (1=March, 12=February)');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ATTENDANCE & WORKING TIME
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('days_worked', 5, 2)->default(0)
                ->comment('Actual days worked in this period');
            $table->decimal('days_on_leave', 5, 2)->default(0)
                ->comment('Days on paid leave');
            $table->decimal('days_absent', 5, 2)->default(0)
                ->comment('Days absent (unpaid)');
            $table->decimal('hours_worked', 7, 2)->default(0)
                ->comment('Total hours worked');
            $table->decimal('overtime_hours', 7, 2)->default(0)
                ->comment('Overtime hours worked');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * EARNINGS & INCOME (SARS CODES)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            // Basic earnings from attendance
            $table->decimal('basic_earnings', 10, 2)->default(0)
                ->comment('Basic earnings from attendance');
            $table->decimal('daily_rate_used', 8, 2)->default(0)
                ->comment('Daily rate applied for this payslip');
            
            // Leave pay
            $table->decimal('leave_pay', 10, 2)->default(0)
                ->comment('Payment for leave taken');
            $table->decimal('leave_days_paid', 5, 2)->default(0)
                ->comment('Number of leave days paid');
            
            // Allowances and other earnings
            $table->decimal('transport_allowance', 8, 2)->default(0);
            $table->decimal('meal_allowance', 8, 2)->default(0);
            $table->decimal('accommodation_allowance', 8, 2)->default(0);
            $table->decimal('other_allowances', 8, 2)->default(0);
            $table->text('other_allowances_description')->nullable();
            
            // Overtime and bonuses
            $table->decimal('overtime_pay', 8, 2)->default(0);
            $table->decimal('bonus_pay', 8, 2)->default(0);
            $table->decimal('commission', 8, 2)->default(0);
            
            // Section 12H allowances
            $table->decimal('section_12h_allowance', 8, 2)->default(0)
                ->comment('Section 12H skills development allowance');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SARS IRP5 SOURCE CODES (CRITICAL FOR COMPLIANCE)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('sars_3601', 10, 2)->default(0)
                ->comment('Basic salary/wages/leave pay/sick pay');
            $table->decimal('sars_3605', 10, 2)->default(0)
                ->comment('Lump sum payments (leave encashment, termination)');
            $table->decimal('sars_3615', 10, 2)->default(0)
                ->comment('Travel allowance (if applicable)');
            $table->decimal('sars_3617', 10, 2)->default(0)
                ->comment('Overtime payments');
            $table->decimal('sars_3627', 10, 2)->default(0)
                ->comment('Bonus/13th cheque');
            $table->decimal('sars_3699', 10, 2)->default(0)
                ->comment('Total remuneration for UIF/SDL purposes');
            
            // Additional SARS codes as needed
            $table->decimal('sars_other_codes', 10, 2)->default(0)
                ->comment('Other SARS codes not listed above');
            $table->json('sars_code_breakdown')->nullable()
                ->comment('Detailed breakdown of all SARS codes');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * TOTAL EARNINGS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('gross_earnings', 10, 2)->default(0)
                ->comment('Total earnings before deductions');
            $table->decimal('taxable_earnings', 10, 2)->default(0)
                ->comment('Earnings subject to PAYE');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * DEDUCTIONS & CONTRIBUTIONS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            // PAYE Tax
            $table->decimal('paye_tax', 10, 2)->default(0)
                ->comment('Pay As You Earn tax deducted');
            $table->decimal('paye_tax_rate', 5, 4)->default(0)
                ->comment('PAYE tax rate applied');
            $table->decimal('tax_rebate_primary', 8, 2)->default(0)
                ->comment('Primary tax rebate applied');
            $table->decimal('tax_rebate_secondary', 8, 2)->default(0)
                ->comment('Secondary tax rebate (age 65+)');
            
            // UIF Contributions
            $table->decimal('uif_employee', 8, 2)->default(0)
                ->comment('Employee UIF contribution (1%)');
            $table->decimal('uif_employer', 8, 2)->default(0)
                ->comment('Employer UIF contribution (1%)');
            $table->decimal('uif_contribution_base', 10, 2)->default(0)
                ->comment('Base amount for UIF calculation');
            $table->boolean('uif_exempt')->default(false)
                ->comment('Employee exempt from UIF');
            
            // SDL Contribution (Employer only)
            $table->decimal('sdl_contribution', 8, 2)->default(0)
                ->comment('Skills Development Levy (1% of payroll)');
            $table->decimal('sdl_contribution_base', 10, 2)->default(0)
                ->comment('Base amount for SDL calculation');
            
            // Other deductions
            $table->decimal('other_deductions', 8, 2)->default(0);
            $table->text('other_deductions_description')->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * ETI (EMPLOYMENT TAX INCENTIVE) CALCULATIONS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->boolean('eti_eligible')->default(false)
                ->comment('Eligible for Employment Tax Incentive');
            $table->decimal('eti_benefit', 8, 2)->default(0)
                ->comment('ETI benefit amount for employer');
            $table->decimal('eti_qualifying_remuneration', 10, 2)->default(0)
                ->comment('Remuneration qualifying for ETI');
            $table->decimal('eti_hours_worked', 7, 2)->default(0)
                ->comment('Hours worked for ETI calculation');
            $table->decimal('eti_hours_on_leave', 7, 2)->default(0)
                ->comment('Hours on leave (excluded from ETI hours)');
            $table->unsignedTinyInteger('eti_month_number')->default(0)
                ->comment('Month number for ETI progression (1-24)');
            $table->enum('eti_age_category', ['18-29', '30+', 'disabled'])->nullable()
                ->comment('ETI age category for rate calculation');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * NET PAY CALCULATION
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('total_deductions', 10, 2)->default(0)
                ->comment('Total deductions from gross earnings');
            $table->decimal('net_pay', 10, 2)->default(0)
                ->comment('Final amount payable to employee');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * YEAR-TO-DATE TOTALS (for IRP5 reporting)
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->decimal('ytd_gross_earnings', 12, 2)->default(0)
                ->comment('Year-to-date gross earnings');
            $table->decimal('ytd_taxable_earnings', 12, 2)->default(0)
                ->comment('Year-to-date taxable earnings');
            $table->decimal('ytd_paye_tax', 12, 2)->default(0)
                ->comment('Year-to-date PAYE tax');
            $table->decimal('ytd_uif_employee', 10, 2)->default(0)
                ->comment('Year-to-date UIF employee contributions');
            $table->decimal('ytd_uif_employer', 10, 2)->default(0)
                ->comment('Year-to-date UIF employer contributions');
            $table->decimal('ytd_sdl', 10, 2)->default(0)
                ->comment('Year-to-date SDL contributions');
            $table->decimal('ytd_eti_benefit', 10, 2)->default(0)
                ->comment('Year-to-date ETI benefits');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PAYMENT & BANKING
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('payment_method', ['eft', 'cash', 'cheque', 'card'])->default('eft');
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_code')->nullable();
            $table->string('payment_reference')->nullable()
                ->comment('Bank payment reference');
            $table->timestamp('payment_processed_at')->nullable();
            $table->boolean('payment_successful')->default(false);
            $table->text('payment_notes')->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * PAYSLIP STATUS & APPROVAL
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->enum('status', [
                'draft', 
                'calculated', 
                'approved', 
                'processed', 
                'paid', 
                'cancelled'
            ])->default('draft');
            
            $table->boolean('is_final')->default(false)
                ->comment('Finalized payslip (cannot be modified)');
            $table->boolean('is_corrected')->default(false)
                ->comment('This is a corrected payslip');
            $table->foreignId('corrects_payslip_id')->nullable()
                ->constrained('payslips')->onDelete('set null')
                ->comment('Original payslip this corrects');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * APPROVAL WORKFLOW
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->foreignId('calculated_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who calculated this payslip');
            $table->timestamp('calculated_at')->nullable();
            
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who approved this payslip');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            $table->foreignId('processed_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who processed this payslip');
            $table->timestamp('processed_at')->nullable();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * AUDIT & COMPLIANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->json('calculation_details')->nullable()
                ->comment('Detailed calculation breakdown for audit');
            $table->json('attendance_summary')->nullable()
                ->comment('Summary of attendance records used');
            $table->json('leave_summary')->nullable()
                ->comment('Summary of leave records used');
            
            $table->string('calculation_hash', 64)->nullable()
                ->comment('Hash of calculation inputs for integrity checking');
            $table->timestamp('exported_to_sars_at')->nullable()
                ->comment('When included in SARS export');
            $table->timestamp('exported_to_uif_at')->nullable()
                ->comment('When included in UIF export');
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * SYSTEM TIMESTAMPS
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->timestamps();
            $table->softDeletes();
            
            /**
             * ─────────────────────────────────────────────────────────────────────────────
             * INDEXES FOR PERFORMANCE
             * ─────────────────────────────────────────────────────────────────────────────
             */
            $table->unique(['company_id', 'user_id', 'payroll_period_start', 'payroll_period_end'], 'unique_payslip_period');
            $table->index(['company_id', 'pay_date']);
            $table->index(['user_id', 'tax_year']);
            $table->index(['program_id', 'pay_date']);
            $table->index(['status', 'is_final']);
            $table->index(['tax_year', 'tax_month_number']);
            $table->index(['exported_to_sars_at']);
            $table->index(['exported_to_uif_at']);
            $table->index(['eti_eligible', 'eti_month_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};