<?php

namespace App\Models;

use App\Traits\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Relationships
        'company_id',
        'user_id',
        'program_id',
        
        // Payroll period
        'payslip_number',
        'payroll_period_start',
        'payroll_period_end',
        'pay_date',
        'pay_year',
        'pay_month',
        'pay_period_number',
        'tax_year',
        'tax_month_number',
        
        // Attendance & working time
        'days_worked',
        'days_on_leave',
        'days_absent',
        'hours_worked',
        'overtime_hours',
        
        // Earnings
        'basic_earnings',
        'daily_rate_used',
        'leave_pay',
        'leave_days_paid',
        'transport_allowance',
        'meal_allowance',
        'accommodation_allowance',
        'other_allowances',
        'other_allowances_description',
        'overtime_pay',
        'bonus_pay',
        'commission',
        'section_12h_allowance',
        
        // SARS codes
        'sars_3601',
        'sars_3605',
        'sars_3615',
        'sars_3617',
        'sars_3627',
        'sars_3699',
        'sars_other_codes',
        'sars_code_breakdown',
        
        // Total earnings
        'gross_earnings',
        'taxable_earnings',
        
        // Deductions
        'paye_tax',
        'paye_tax_rate',
        'tax_rebate_primary',
        'tax_rebate_secondary',
        'uif_employee',
        'uif_employer',
        'uif_contribution_base',
        'uif_exempt',
        'sdl_contribution',
        'sdl_contribution_base',
        'other_deductions',
        'other_deductions_description',
        
        // ETI
        'eti_eligible',
        'eti_benefit',
        'eti_qualifying_remuneration',
        'eti_hours_worked',
        'eti_hours_on_leave',
        'eti_month_number',
        'eti_age_category',
        
        // Net pay
        'total_deductions',
        'net_pay',
        
        // Year-to-date totals
        'ytd_gross_earnings',
        'ytd_taxable_earnings',
        'ytd_paye_tax',
        'ytd_uif_employee',
        'ytd_uif_employer',
        'ytd_sdl',
        'ytd_eti_benefit',
        
        // Payment
        'payment_method',
        'bank_account_number',
        'bank_name',
        'bank_branch_code',
        'payment_reference',
        'payment_processed_at',
        'payment_successful',
        'payment_notes',
        
        // Status
        'status',
        'is_final',
        'is_corrected',
        'corrects_payslip_id',
        
        // Approval
        'calculated_by',
        'calculated_at',
        'approved_by',
        'approved_at',
        'approval_notes',
        'processed_by',
        'processed_at',
        
        // Audit
        'calculation_details',
        'attendance_summary',
        'leave_summary',
        'calculation_hash',
        'exported_to_sars_at',
        'exported_to_uif_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
        'pay_date' => 'date',
        'days_worked' => 'decimal:2',
        'days_on_leave' => 'decimal:2',
        'days_absent' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'basic_earnings' => 'decimal:2',
        'daily_rate_used' => 'decimal:2',
        'leave_pay' => 'decimal:2',
        'leave_days_paid' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'accommodation_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus_pay' => 'decimal:2',
        'commission' => 'decimal:2',
        'section_12h_allowance' => 'decimal:2',
        'sars_3601' => 'decimal:2',
        'sars_3605' => 'decimal:2',
        'sars_3615' => 'decimal:2',
        'sars_3617' => 'decimal:2',
        'sars_3627' => 'decimal:2',
        'sars_3699' => 'decimal:2',
        'sars_other_codes' => 'decimal:2',
        'sars_code_breakdown' => 'array',
        'gross_earnings' => 'decimal:2',
        'taxable_earnings' => 'decimal:2',
        'paye_tax' => 'decimal:2',
        'paye_tax_rate' => 'decimal:4',
        'tax_rebate_primary' => 'decimal:2',
        'tax_rebate_secondary' => 'decimal:2',
        'uif_employee' => 'decimal:2',
        'uif_employer' => 'decimal:2',
        'uif_contribution_base' => 'decimal:2',
        'uif_exempt' => 'boolean',
        'sdl_contribution' => 'decimal:2',
        'sdl_contribution_base' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'eti_eligible' => 'boolean',
        'eti_benefit' => 'decimal:2',
        'eti_qualifying_remuneration' => 'decimal:2',
        'eti_hours_worked' => 'decimal:2',
        'eti_hours_on_leave' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'ytd_gross_earnings' => 'decimal:2',
        'ytd_taxable_earnings' => 'decimal:2',
        'ytd_paye_tax' => 'decimal:2',
        'ytd_uif_employee' => 'decimal:2',
        'ytd_uif_employer' => 'decimal:2',
        'ytd_sdl' => 'decimal:2',
        'ytd_eti_benefit' => 'decimal:2',
        'payment_processed_at' => 'datetime',
        'payment_successful' => 'boolean',
        'is_final' => 'boolean',
        'is_corrected' => 'boolean',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'calculation_details' => 'array',
        'attendance_summary' => 'array',
        'leave_summary' => 'array',
        'exported_to_sars_at' => 'datetime',
        'exported_to_uif_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate payslip number on create
        static::creating(function (Payslip $payslip) {
            if (!$payslip->payslip_number) {
                $payslip->payslip_number = $payslip->generatePayslipNumber();
            }
            
            $payslip->setTaxYearFields();
        });
    }

    /**
     * Get the company that owns this payslip.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user (learner) this payslip belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program this payslip is for.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user who calculated this payslip.
     */
    public function calculator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    /**
     * Get the user who approved this payslip.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed this payslip.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the original payslip this corrects.
     */
    public function correctedPayslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class, 'corrects_payslip_id');
    }

    /**
     * Generate a unique payslip number.
     */
    protected function generatePayslipNumber(): string
    {
        $company = $this->company;
        $payDate = Carbon::parse($this->pay_date ?? now());
        
        $prefix = $company?->tenant_key ? strtoupper(substr($company->tenant_key, 0, 4)) : 'CHR';
        $yearMonth = $payDate->format('Ym');
        $sequence = str_pad($this->getNextSequenceNumber($yearMonth), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$yearMonth}-{$sequence}";
    }

    /**
     * Get the next sequence number for the month.
     */
    protected function getNextSequenceNumber(string $yearMonth): int
    {
        $lastPayslip = static::where('company_id', $this->company_id)
            ->where('payslip_number', 'like', "%-{$yearMonth}-%")
            ->orderBy('payslip_number', 'desc')
            ->first();

        if (!$lastPayslip) {
            return 1;
        }

        // Extract sequence number from last payslip
        $parts = explode('-', $lastPayslip->payslip_number);
        return isset($parts[2]) ? (int) $parts[2] + 1 : 1;
    }

    /**
     * Set tax year related fields.
     */
    protected function setTaxYearFields(): void
    {
        $payDate = Carbon::parse($this->pay_date ?? now());
        
        // SA tax year runs March to February
        if ($payDate->month >= 3) {
            $this->tax_year = $payDate->year;
            $this->tax_month_number = $payDate->month - 2; // March = 1, February = 12
        } else {
            $this->tax_year = $payDate->year - 1;
            $this->tax_month_number = $payDate->month + 10; // Jan = 11, Feb = 12
        }
    }

    /**
     * Calculate SARS code mappings.
     */
    public function calculateSarsCodes(): void
    {
        // SARS 3601: Basic salary/wages/leave pay/sick pay
        $this->sars_3601 = $this->basic_earnings + $this->leave_pay;
        
        // SARS 3605: Lump sum payments (bonuses, leave encashments)
        $this->sars_3605 = $this->bonus_pay;
        
        // SARS 3615: Travel allowance
        $this->sars_3615 = $this->transport_allowance;
        
        // SARS 3617: Overtime payments
        $this->sars_3617 = $this->overtime_pay;
        
        // SARS 3627: 13th cheque/bonus
        $this->sars_3627 = 0; // Will be set if applicable
        
        // SARS 3699: Total remuneration for UIF/SDL
        $this->sars_3699 = $this->gross_earnings;
    }

    /**
     * Calculate PAYE tax.
     */
    public function calculatePaye(): void
    {
        $taxableIncome = $this->taxable_earnings;
        
        if ($taxableIncome <= 0) {
            $this->paye_tax = 0;
            $this->paye_tax_rate = 0;
            return;
        }

        // Simplified PAYE calculation (should use actual SARS tax tables)
        $annualTaxableIncome = $taxableIncome * 12;
        $annualTax = $this->calculateAnnualTax($annualTaxableIncome);
        $monthlyTax = $annualTax / 12;
        
        // Apply tax rebates
        $this->tax_rebate_primary = $this->getTaxRebatePrimary() / 12;
        if ($this->user && $this->user->birth_date) {
            $age = Carbon::parse($this->user->birth_date)->age;
            if ($age >= 65) {
                $this->tax_rebate_secondary = $this->getTaxRebateSecondary() / 12;
            }
        }
        
        $this->paye_tax = max(0, $monthlyTax - $this->tax_rebate_primary - $this->tax_rebate_secondary);
        $this->paye_tax_rate = $taxableIncome > 0 ? ($this->paye_tax / $taxableIncome) : 0;
    }

    /**
     * Calculate annual tax (simplified - should use official SARS tax tables).
     */
    protected function calculateAnnualTax(float $annualIncome): float
    {
        // Simplified 2024/2025 tax brackets (for demonstration)
        if ($annualIncome <= 237100) {
            return $annualIncome * 0.18;
        } elseif ($annualIncome <= 370500) {
            return 42678 + ($annualIncome - 237100) * 0.26;
        } elseif ($annualIncome <= 512800) {
            return 77362 + ($annualIncome - 370500) * 0.31;
        } else {
            // Higher brackets would continue...
            return 121475 + ($annualIncome - 512800) * 0.36;
        }
    }

    /**
     * Get primary tax rebate amount.
     */
    protected function getTaxRebatePrimary(): float
    {
        return 17235; // 2024/2025 primary rebate
    }

    /**
     * Get secondary tax rebate amount (65+).
     */
    protected function getTaxRebateSecondary(): float
    {
        return 9444; // 2024/2025 secondary rebate
    }

    /**
     * Calculate UIF contributions.
     */
    public function calculateUif(): void
    {
        if ($this->uif_exempt || $this->user?->uif_exempt) {
            $this->uif_employee = 0;
            $this->uif_employer = 0;
            $this->uif_contribution_base = 0;
            return;
        }

        // UIF contribution base (capped amount)
        $uifCap = 17712; // Monthly UIF cap for 2024
        $this->uif_contribution_base = min($this->gross_earnings, $uifCap);
        
        // 1% employee, 1% employer
        $this->uif_employee = $this->uif_contribution_base * 0.01;
        $this->uif_employer = $this->uif_contribution_base * 0.01;
    }

    /**
     * Calculate SDL contribution.
     */
    public function calculateSdl(): void
    {
        if ($this->company?->isSdlExempt()) {
            $this->sdl_contribution = 0;
            $this->sdl_contribution_base = 0;
            return;
        }

        $this->sdl_contribution_base = $this->gross_earnings;
        $this->sdl_contribution = $this->sdl_contribution_base * $this->company->getSdlRate();
    }

    /**
     * Calculate ETI benefit.
     */
    public function calculateEti(): void
    {
        if (!$this->eti_eligible || !$this->program?->isEtiEligible() || !$this->user?->eti_eligible) {
            $this->eti_benefit = 0;
            $this->eti_qualifying_remuneration = 0;
            return;
        }

        // ETI calculation based on age and month number
        $ageCategory = $this->determineEtiAgeCategory();
        $this->eti_age_category = $ageCategory;
        
        // ETI hours exclude leave hours but include remuneration
        $this->eti_hours_worked = max(0, $this->hours_worked - $this->eti_hours_on_leave);
        
        // Qualifying remuneration includes leave pay
        $this->eti_qualifying_remuneration = $this->basic_earnings + $this->leave_pay;
        
        // Calculate ETI benefit based on category and month
        $this->eti_benefit = $this->calculateEtiBenefit($ageCategory, $this->eti_month_number);
    }

    /**
     * Determine ETI age category.
     */
    protected function determineEtiAgeCategory(): string
    {
        if (!$this->user?->birth_date) {
            return '18-29'; // Default
        }

        $age = Carbon::parse($this->user->birth_date)->age;
        
        if ($age < 18) {
            return '18-29'; // Treat under 18 as 18-29 category
        } elseif ($age <= 29) {
            return '18-29';
        } else {
            return '30+';
        }
    }

    /**
     * Calculate ETI benefit amount.
     */
    protected function calculateEtiBenefit(string $ageCategory, int $monthNumber): float
    {
        // Simplified ETI calculation (should use official rates)
        if ($monthNumber <= 0 || $monthNumber > 24) {
            return 0;
        }

        $baseAmount = $ageCategory === '18-29' ? 1000 : 500; // Simplified rates
        
        // First 12 months vs second 12 months
        if ($monthNumber <= 12) {
            return $baseAmount;
        } else {
            return $baseAmount * 0.5; // Reduced rate for months 13-24
        }
    }

    /**
     * Perform full payslip calculation.
     */
    public function calculate(): void
    {
        // Calculate gross earnings
        $this->gross_earnings = $this->basic_earnings + 
                               $this->leave_pay + 
                               $this->transport_allowance + 
                               $this->meal_allowance + 
                               $this->accommodation_allowance + 
                               $this->other_allowances + 
                               $this->overtime_pay + 
                               $this->bonus_pay + 
                               $this->commission + 
                               $this->section_12h_allowance;

        // Taxable earnings (some allowances may be exempt)
        $this->taxable_earnings = $this->gross_earnings;

        // Calculate all components
        $this->calculateSarsCodes();
        $this->calculatePaye();
        $this->calculateUif();
        $this->calculateSdl();
        $this->calculateEti();

        // Calculate total deductions
        $this->total_deductions = $this->paye_tax + 
                                 $this->uif_employee + 
                                 $this->other_deductions;

        // Calculate net pay
        $this->net_pay = $this->gross_earnings - $this->total_deductions;

        // Update calculation timestamp
        $this->calculated_at = now();
        
        // Generate calculation hash for integrity
        $this->calculation_hash = $this->generateCalculationHash();

        $this->status = 'calculated';
    }

    /**
     * Generate calculation hash for integrity checking.
     */
    protected function generateCalculationHash(): string
    {
        $data = [
            'gross_earnings' => $this->gross_earnings,
            'paye_tax' => $this->paye_tax,
            'uif_employee' => $this->uif_employee,
            'uif_employer' => $this->uif_employer,
            'sdl_contribution' => $this->sdl_contribution,
            'net_pay' => $this->net_pay,
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Check if payslip is editable.
     */
    public function isEditable(): bool
    {
        return !$this->is_final && 
               !in_array($this->status, ['paid', 'processed']) &&
               !$this->exported_to_sars_at &&
               !$this->exported_to_uif_at;
    }

    /**
     * Check if payslip is ready for approval.
     */
    public function isReadyForApproval(): bool
    {
        return $this->status === 'calculated' && !$this->approved_at;
    }

    /**
     * Approve the payslip.
     */
    public function approve(User $approver, ?string $notes = null): void
    {
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->status = 'approved';
        $this->save();
    }

    /**
     * Process the payslip for payment.
     */
    public function process(User $processor): void
    {
        $this->processed_by = $processor->id;
        $this->processed_at = now();
        $this->status = 'processed';
        $this->save();
    }

    /**
     * Mark payslip as paid.
     */
    public function markAsPaid(?string $paymentReference = null): void
    {
        $this->payment_processed_at = now();
        $this->payment_successful = true;
        $this->payment_reference = $paymentReference;
        $this->status = 'paid';
        $this->save();
    }

    /**
     * Finalize the payslip (cannot be edited after this).
     */
    public function finalize(): void
    {
        $this->is_final = true;
        $this->save();
    }

    /**
     * Get payslip status color for UI.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'calculated' => 'blue',
            'approved' => 'green',
            'processed' => 'purple',
            'paid' => 'emerald',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope to get payslips for a specific tax year.
     */
    public function scopeForTaxYear($query, int $taxYear)
    {
        return $query->where('tax_year', $taxYear);
    }

    /**
     * Scope to get payslips by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get finalized payslips.
     */
    public function scopeFinalized($query)
    {
        return $query->where('is_final', true);
    }

    /**
     * Scope to get payslips ready for SARS export.
     */
    public function scopeReadyForSarsExport($query)
    {
        return $query->where('status', 'paid')
                     ->whereNull('exported_to_sars_at')
                     ->where('is_final', true);
    }

    /**
     * Scope to get payslips ready for UIF export.
     */
    public function scopeReadyForUifExport($query)
    {
        return $query->where('status', 'paid')
                     ->whereNull('exported_to_uif_at')
                     ->where('is_final', true)
                     ->where('uif_exempt', false);
    }
}