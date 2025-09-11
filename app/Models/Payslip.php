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
        'pay_period_start',
        'pay_period_end',
        'pay_date',
        'basic_pay',
        'daily_rate',
        'days_worked',
        'days_present',
        'days_absent',
        'days_authorized_absent',
        'days_unauthorized_absent',
        'transport_allowance',
        'meal_allowance',
        'accommodation_allowance',
        'other_allowance',
        'other_allowance_description',
        'gross_pay',
        'uif_deduction',
        'paye_deduction',
        'other_deductions',
        'other_deductions_description',
        'net_pay',
        'taxable_income',
        'annual_taxable_income',
        'tax_threshold',
        'tax_rebate',
        'uif_contribution',
        'uif_employer_contribution',
        'status',
        'is_irp5_generated',
        'irp5_reference',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'pay_date' => 'date',
        'basic_pay' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'accommodation_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'uif_deduction' => 'decimal:2',
        'paye_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'annual_taxable_income' => 'decimal:2',
        'tax_threshold' => 'decimal:2',
        'tax_rebate' => 'decimal:2',
        'uif_contribution' => 'decimal:2',
        'uif_employer_contribution' => 'decimal:2',
        'approved_at' => 'datetime',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getPayPeriodFormattedAttribute(): string
    {
        return $this->pay_period_start->format('d M Y') . ' - ' . $this->pay_period_end->format('d M Y');
    }

    public function getTotalAllowancesAttribute(): float
    {
        return $this->transport_allowance + $this->meal_allowance + $this->accommodation_allowance + $this->other_allowance;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->uif_deduction + $this->paye_deduction + $this->other_deductions;
    }

    // Methods
    public function calculateGrossPay(): float
    {
        $this->gross_pay = $this->basic_pay + $this->total_allowances;
        return $this->gross_pay;
    }

    public function calculateNetPay(): float
    {
        $this->net_pay = $this->gross_pay - $this->total_deductions;
        return $this->net_pay;
    }

    public function calculateUIF(): float
    {
        // UIF is 1% of gross pay, capped at R148.72 per month
        $this->uif_deduction = min($this->gross_pay * 0.01, 148.72);
        return $this->uif_deduction;
    }

    public function calculatePAYE(): float
    {
        // Simplified PAYE calculation based on 2024 tax tables
        $annualIncome = $this->annual_taxable_income;

        if ($annualIncome <= 237100) {
            // 18% of taxable income
            $this->paye_deduction = ($annualIncome * 0.18) / 12;
        } elseif ($annualIncome <= 370500) {
            // 26% of taxable income above 237100
            $this->paye_deduction = (42678 + (($annualIncome - 237100) * 0.26)) / 12;
        } elseif ($annualIncome <= 512800) {
            // 31% of taxable income above 370500
            $this->paye_deduction = (77362 + (($annualIncome - 370500) * 0.31)) / 12;
        } elseif ($annualIncome <= 673000) {
            // 36% of taxable income above 512800
            $this->paye_deduction = (121475 + (($annualIncome - 512800) * 0.36)) / 12;
        } elseif ($annualIncome <= 857900) {
            // 39% of taxable income above 673000
            $this->paye_deduction = (179147 + (($annualIncome - 673000) * 0.39)) / 12;
        } elseif ($annualIncome <= 1817000) {
            // 41% of taxable income above 857900
            $this->paye_deduction = (251258 + (($annualIncome - 857900) * 0.41)) / 12;
        } else {
            // 45% of taxable income above 1817000
            $this->paye_deduction = (644258 + (($annualIncome - 1817000) * 0.45)) / 12;
        }

        // Apply primary rebate (R17,235 for 2024)
        $this->paye_deduction = max(0, $this->paye_deduction - (17235 / 12));

        return $this->paye_deduction;
    }

    public function generateIRP5Reference(): string
    {
        $this->irp5_reference = 'IRP5-' . $this->company_id . '-' . $this->user_id . '-' . $this->pay_period_start->format('Ym');
        return $this->irp5_reference;
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
        return $query->where('pay_period_start', '>=', $startDate)
            ->where('pay_period_end', '<=', $endDate);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
