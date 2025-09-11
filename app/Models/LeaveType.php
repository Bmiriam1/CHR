<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'annual_entitlement_days',
        'accrual_rate_per_month',
        'max_carry_over_days',
        'min_service_months',
        'requires_medical_certificate',
        'medical_cert_required_after_days',
        'is_taxable',
        'sars_code',
        'affects_etv',
        'allows_partial_days',
        'allows_advance_request',
        'min_notice_days',
        'max_consecutive_days',
        'applicable_to_roles',
        'is_active',
    ];

    protected $casts = [
        'annual_entitlement_days' => 'decimal:2',
        'accrual_rate_per_month' => 'decimal:2',
        'max_carry_over_days' => 'decimal:2',
        'min_service_months' => 'integer',
        'requires_medical_certificate' => 'boolean',
        'medical_cert_required_after_days' => 'integer',
        'is_taxable' => 'boolean',
        'affects_etv' => 'boolean',
        'allows_partial_days' => 'boolean',
        'allows_advance_request' => 'boolean',
        'min_notice_days' => 'integer',
        'max_consecutive_days' => 'integer',
        'applicable_to_roles' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get leave requests for this leave type.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get leave accruals for this leave type.
     */
    public function leaveAccruals(): HasMany
    {
        return $this->hasMany(LeaveAccrual::class);
    }

    /**
     * Get leave carry overs for this leave type.
     */
    public function leaveCarryOvers(): HasMany
    {
        return $this->hasMany(LeaveCarryOver::class);
    }

    /**
     * Check if this leave type is applicable to a specific role.
     */
    public function isApplicableToRole(string $role): bool
    {
        if (empty($this->applicable_to_roles)) {
            return true; // If no roles specified, applicable to all
        }

        return in_array($role, $this->applicable_to_roles);
    }

    /**
     * Check if medical certificate is required for given duration.
     */
    public function requiresMedicalCertificate(int $days): bool
    {
        if (!$this->requires_medical_certificate) {
            return false;
        }

        return $days >= $this->medical_cert_required_after_days;
    }

    /**
     * Calculate entitlement for a given service period.
     */
    public function calculateEntitlement(int $serviceMonths): float
    {
        if ($serviceMonths < $this->min_service_months) {
            return 0;
        }

        // For most leave types, full entitlement after minimum service
        // Special cases can be handled in specific implementations
        return $this->annual_entitlement_days;
    }

    /**
     * Calculate accrual for a given month.
     */
    public function calculateMonthlyAccrual(int $serviceMonths): float
    {
        if ($serviceMonths < $this->min_service_months) {
            return 0;
        }

        return $this->accrual_rate_per_month;
    }

    /**
     * Get SA-standard leave types.
     */
    public static function getSAStandardTypes(): array
    {
        return [
            'ANNUAL' => [
                'code' => 'ANNUAL',
                'name' => 'Annual Leave',
                'description' => 'Annual vacation leave as per Basic Conditions of Employment Act',
                'annual_entitlement_days' => 21, // 3 weeks per year
                'accrual_rate_per_month' => 1.75, // 21/12 months
                'max_carry_over_days' => 6, // Usually 6 days max carry over
                'min_service_months' => 12, // Full entitlement after 12 months
                'requires_medical_certificate' => false,
                'is_taxable' => false,
                'sars_code' => 'AL',
                'affects_etv' => false,
                'allows_partial_days' => true,
                'allows_advance_request' => true,
                'min_notice_days' => 14,
                'max_consecutive_days' => 0,
            ],
            'SICK' => [
                'code' => 'SICK',
                'name' => 'Sick Leave',
                'description' => 'Sick leave as per Basic Conditions of Employment Act - 30 days per 36-month cycle',
                'annual_entitlement_days' => 10, // 30 days over 3 years = 10 per year average
                'accrual_rate_per_month' => 0.83, // 30 days over 36 months
                'max_carry_over_days' => 30, // Full cycle amount
                'min_service_months' => 6, // After 6 months of service
                'requires_medical_certificate' => true,
                'medical_cert_required_after_days' => 2,
                'is_taxable' => false,
                'sars_code' => 'SL',
                'affects_etv' => false,
                'allows_partial_days' => true,
                'allows_advance_request' => false,
                'min_notice_days' => 0,
                'max_consecutive_days' => 0,
            ],
            'MATERNITY' => [
                'code' => 'MATERNITY',
                'name' => 'Maternity Leave',
                'description' => 'Maternity leave as per Basic Conditions of Employment Act - 4 consecutive months',
                'annual_entitlement_days' => 120, // 4 months
                'accrual_rate_per_month' => 0, // Not accrued monthly
                'max_carry_over_days' => 0,
                'min_service_months' => 0,
                'requires_medical_certificate' => true,
                'medical_cert_required_after_days' => 1,
                'is_taxable' => false,
                'sars_code' => 'ML',
                'affects_etv' => false,
                'allows_partial_days' => false,
                'allows_advance_request' => true,
                'min_notice_days' => 60,
                'max_consecutive_days' => 120,
                'applicable_to_roles' => ['learner'], // Gender-specific in practice
            ],
            'PATERNITY' => [
                'code' => 'PATERNITY',
                'name' => 'Paternity Leave',
                'description' => 'Paternity leave as per Basic Conditions of Employment Act - 10 consecutive days',
                'annual_entitlement_days' => 10,
                'accrual_rate_per_month' => 0,
                'max_carry_over_days' => 0,
                'min_service_months' => 0,
                'requires_medical_certificate' => false,
                'is_taxable' => false,
                'sars_code' => 'PL',
                'affects_etv' => false,
                'allows_partial_days' => false,
                'allows_advance_request' => true,
                'min_notice_days' => 30,
                'max_consecutive_days' => 10,
                'applicable_to_roles' => ['learner'], // Gender-specific in practice
            ],
            'FAMILY' => [
                'code' => 'FAMILY',
                'name' => 'Family Responsibility Leave',
                'description' => 'Family responsibility leave as per Basic Conditions of Employment Act - 3 days per year',
                'annual_entitlement_days' => 3,
                'accrual_rate_per_month' => 0.25,
                'max_carry_over_days' => 0,
                'min_service_months' => 6,
                'requires_medical_certificate' => false,
                'is_taxable' => false,
                'sars_code' => 'FL',
                'affects_etv' => false,
                'allows_partial_days' => false,
                'allows_advance_request' => false,
                'min_notice_days' => 0,
                'max_consecutive_days' => 3,
            ],
            'STUDY' => [
                'code' => 'STUDY',
                'name' => 'Study Leave',
                'description' => 'Study leave for learner development programs',
                'annual_entitlement_days' => 5, // Company policy
                'accrual_rate_per_month' => 0.42,
                'max_carry_over_days' => 0,
                'min_service_months' => 12,
                'requires_medical_certificate' => false,
                'is_taxable' => false,
                'sars_code' => 'ST',
                'affects_etv' => false,
                'allows_partial_days' => true,
                'allows_advance_request' => true,
                'min_notice_days' => 30,
                'max_consecutive_days' => 5,
            ],
        ];
    }

    /**
     * Create standard SA leave types.
     */
    public static function createSAStandardTypes(): void
    {
        $types = self::getSAStandardTypes();
        
        foreach ($types as $typeData) {
            self::updateOrCreate(
                ['code' => $typeData['code']],
                $typeData
            );
        }
    }

    /**
     * Get leave type by code.
     */
    public static function getByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    /**
     * Get active leave types.
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Get leave types applicable to a role.
     */
    public static function getForRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
            ->where(function ($query) use ($role) {
                $query->whereNull('applicable_to_roles')
                      ->orWhereJsonContains('applicable_to_roles', $role);
            })
            ->orderBy('name')
            ->get();
    }
}