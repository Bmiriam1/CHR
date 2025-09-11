<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payslip extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'user_id',
        'company_id',
        'program_id',
        'payroll_period_start',
        'payroll_period_end',
        'pay_date',
        'pay_year',
        'pay_month',
        'pay_period_number',
        'tax_year',
        'tax_month_number',
        'days_worked',
        'days_on_leave',
        'days_absent',
        'hours_worked',
        'overtime_hours',
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
        'sars_3601',
        'sars_3605',
        'sars_3615',
        'sars_3617',
        'sars_3627',
        'sars_3699',
        'sars_other_codes',
        'sars_code_breakdown',
        'gross_earnings',
        'taxable_earnings',
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
        'eti_eligible',
        'eti_benefit',
        'eti_qualifying_remuneration',
        'eti_hours_worked',
        'eti_hours_on_leave',
        'eti_month_number',
        'eti_age_category',
        'total_deductions',
        'net_pay',
        'ytd_gross_earnings',
        'ytd_taxable_earnings',
        'ytd_paye_tax',
        'ytd_uif_employee',
        'ytd_uif_employer',
        'ytd_sdl',
        'ytd_eti_benefit',
        'payment_method',
        'bank_account_number',
        'bank_name',
        'bank_branch_code',
        'payment_reference',
        'payment_processed_at',
        'payment_successful',
        'payment_notes',
        'status',
        'is_final',
        'is_corrected',
        'corrects_payslip_id',
        'calculated_by',
        'calculated_at',
        'approved_by',
        'approved_at',
        'approval_notes',
        'processed_by',
        'processed_at',
        'calculation_details',
        'attendance_summary',
        'leave_summary',
        'calculation_hash',
        'exported_to_sars_at',
        'exported_to_uif_at',
    ];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
        'pay_date' => 'date',
        'basic_earnings' => 'decimal:2',
        'daily_rate_used' => 'decimal:2',
        'leave_pay' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'accommodation_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus_pay' => 'decimal:2',
        'commission' => 'decimal:2',
        'section_12h_allowance' => 'decimal:2',
        'gross_earnings' => 'decimal:2',
        'taxable_earnings' => 'decimal:2',
        'paye_tax' => 'decimal:2',
        'uif_employee' => 'decimal:2',
        'uif_employer' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'eti_benefit' => 'decimal:2',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'payment_processed_at' => 'datetime',
        'exported_to_sars_at' => 'datetime',
        'exported_to_uif_at' => 'datetime',
        'sars_code_breakdown' => 'array',
        'calculation_details' => 'array',
        'attendance_summary' => 'array',
        'leave_summary' => 'array',
        'payment_successful' => 'boolean',
        'uif_exempt' => 'boolean',
        'eti_eligible' => 'boolean',
        'is_final' => 'boolean',
        'is_corrected' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getPayPeriodFormattedAttribute(): string
    {
        if (!$this->payroll_period_start || !$this->payroll_period_end) {
            return 'N/A';
        }
        return $this->payroll_period_start->format('d M Y') . ' - ' . $this->payroll_period_end->format('d M Y');
    }

    public function getTotalAllowancesAttribute(): float
    {
        return $this->transport_allowance + $this->meal_allowance + $this->accommodation_allowance + $this->other_allowances;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->paye_tax + $this->uif_employee + $this->other_deductions;
    }

    // Methods
    public function calculateGrossPay(): float
    {
        $this->gross_earnings = $this->basic_earnings + $this->total_allowances;
        return $this->gross_earnings;
    }

    public function calculateNetPay(): float
    {
        $this->net_pay = $this->gross_earnings - $this->total_deductions;
        return $this->net_pay;
    }

    public function calculateUIF(): float
    {
        // UIF is 1% of gross pay, capped at R148.72 per month
        $this->uif_employee = min($this->gross_earnings * 0.01, 148.72);
        return $this->uif_employee;
    }

    public function calculatePAYE(): float
    {
        // Simplified PAYE calculation based on 2024 tax tables
        $annualIncome = $this->taxable_earnings * 12;

        if ($annualIncome <= 237100) {
            // 18% of taxable income
            $this->paye_tax = ($annualIncome * 0.18) / 12;
        } elseif ($annualIncome <= 370500) {
            // 26% of taxable income above 237100
            $this->paye_tax = (42678 + (($annualIncome - 237100) * 0.26)) / 12;
        } elseif ($annualIncome <= 512800) {
            // 31% of taxable income above 370500
            $this->paye_tax = (77362 + (($annualIncome - 370500) * 0.31)) / 12;
        } elseif ($annualIncome <= 673000) {
            // 36% of taxable income above 512800
            $this->paye_tax = (121475 + (($annualIncome - 512800) * 0.36)) / 12;
        } elseif ($annualIncome <= 857900) {
            // 39% of taxable income above 673000
            $this->paye_tax = (179147 + (($annualIncome - 673000) * 0.39)) / 12;
        } elseif ($annualIncome <= 1817000) {
            // 41% of taxable income above 857900
            $this->paye_tax = (251258 + (($annualIncome - 857900) * 0.41)) / 12;
        } else {
            // 45% of taxable income above 1817000
            $this->paye_tax = (644258 + (($annualIncome - 1817000) * 0.45)) / 12;
        }

        // Apply primary rebate (R17,235 for 2024)
        $this->paye_tax = max(0, $this->paye_tax - (17235 / 12));

        return $this->paye_tax;
    }

    public function generateIRP5Reference(): string
    {
        $irp5_reference = 'IRP5-' . $this->company_id . '-' . $this->user_id . '-' . $this->payroll_period_start->format('Ym');
        return $irp5_reference;
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->where('payroll_period_start', '>=', $startDate)
            ->where('payroll_period_end', '<=', $endDate);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}