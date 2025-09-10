<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Basic company info
        'parent_company_id',
        'tenant_key',
        'name',
        'display_name',
        'trading_name',
        'company_registration_number',
        'vat_number',
        'vat_vendor',
        'sic_code',
        'industry_sector',
        
        // SARS compliance
        'paye_reference_number',
        'uif_reference_number',
        'sdl_reference_number',
        'wsp_sdp_number',
        'paye_registration_date',
        'uif_registration_date',
        'sdl_registration_date',
        'tax_year_end',
        'first_paye_period',
        
        // Contact info
        'email',
        'phone',
        'fax',
        'website',
        
        // Physical address
        'physical_address_line1',
        'physical_address_line2',
        'physical_suburb',
        'physical_city',
        'physical_postal_code',
        'physical_country_code',
        
        // Postal address
        'postal_address_line1',
        'postal_address_line2',
        'postal_suburb',
        'postal_city',
        'postal_code',
        'postal_country_code',
        
        // Payroll settings
        'default_pay_frequency',
        'pay_day_of_month',
        
        // ETI settings
        'eti_registered',
        'eti_registration_date',
        'eti_certificate_number',
        
        // UIF & SDL
        'uif_exempt',
        'sdl_exempt',
        'sdl_rate_override',
        
        // Leave policies
        'annual_leave_days',
        'sick_leave_days',
        'family_leave_days',
        'allow_leave_carryover',
        'max_carryover_days',
        'allow_leave_encashment',
        'leave_notice_days',
        'max_consecutive_days',
        
        // Banking
        'bank_name',
        'bank_branch_code',
        'bank_account_number',
        'bank_account_type',
        'bank_account_holder',
        
        // System settings
        'is_active',
        'is_verified',
        'subscription_tier',
        'subscription_start_date',
        'subscription_end_date',
        'billing_active',
        'max_learners',
        'max_programs',
        
        // JSON fields
        'compliance_settings',
        'notification_preferences',
        
        // Timestamps
        'last_compliance_check',
        'last_backup_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'vat_vendor' => 'boolean',
        'paye_registration_date' => 'date',
        'uif_registration_date' => 'date',
        'sdl_registration_date' => 'date',
        'first_paye_period' => 'date',
        'eti_registered' => 'boolean',
        'eti_registration_date' => 'date',
        'uif_exempt' => 'boolean',
        'sdl_exempt' => 'boolean',
        'sdl_rate_override' => 'decimal:4',
        'allow_leave_carryover' => 'boolean',
        'allow_leave_encashment' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'billing_active' => 'boolean',
        'compliance_settings' => 'array',
        'notification_preferences' => 'array',
        'last_compliance_check' => 'datetime',
        'last_backup_date' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate tenant key when creating a new company
        static::creating(function (Company $company) {
            if (!$company->tenant_key) {
                $company->tenant_key = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the parent company.
     */
    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    /**
     * Get the child companies (branches).
     */
    public function childCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    /**
     * Get all programs for this company.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get all users (learners/employees) for this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the company branches.
     */
    public function branches(): HasMany
    {
        return $this->hasMany(CompanyBranch::class);
    }

    /**
     * Check if this is a parent company.
     */
    public function isParentCompany(): bool
    {
        return $this->parent_company_id === null;
    }

    /**
     * Check if this is a branch/subsidiary.
     */
    public function isBranch(): bool
    {
        return $this->parent_company_id !== null;
    }

    /**
     * Get the root parent company.
     */
    public function getRootParent(): Company
    {
        if ($this->isParentCompany()) {
            return $this;
        }

        return $this->parentCompany->getRootParent();
    }

    /**
     * Get all companies in the same group (parent + all children).
     */
    public function getCompanyGroup()
    {
        $root = $this->getRootParent();
        
        return Company::where('id', $root->id)
            ->orWhere('parent_company_id', $root->id)
            ->get();
    }

    /**
     * Check if ETI is enabled and configured.
     */
    public function hasEtiEnabled(): bool
    {
        return $this->eti_registered && 
               $this->eti_registration_date && 
               $this->eti_certificate_number;
    }

    /**
     * Check if company is UIF exempt.
     */
    public function isUifExempt(): bool
    {
        return $this->uif_exempt;
    }

    /**
     * Check if company is SDL exempt.
     */
    public function isSdlExempt(): bool
    {
        return $this->sdl_exempt;
    }

    /**
     * Get the SDL rate for this company.
     */
    public function getSdlRate(): float
    {
        return $this->sdl_rate_override ?? 0.01; // Default 1%
    }

    /**
     * Get formatted physical address.
     */
    public function getFormattedPhysicalAddress(): string
    {
        $parts = array_filter([
            $this->physical_address_line1,
            $this->physical_address_line2,
            $this->physical_suburb,
            $this->physical_city,
            $this->physical_postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get formatted postal address.
     */
    public function getFormattedPostalAddress(): string
    {
        $parts = array_filter([
            $this->postal_address_line1,
            $this->postal_address_line2,
            $this->postal_suburb,
            $this->postal_city,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if company subscription is active.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->billing_active && 
               $this->subscription_end_date && 
               $this->subscription_end_date->isFuture();
    }

    /**
     * Get remaining learner capacity.
     */
    public function getRemainingLearnerCapacity(): int
    {
        $currentCount = $this->users()->where('is_learner', true)->count();
        return max(0, $this->max_learners - $currentCount);
    }

    /**
     * Get remaining program capacity.
     */
    public function getRemainingProgramCapacity(): int
    {
        $currentCount = $this->programs()->where('status', '!=', 'completed')->count();
        return max(0, $this->max_programs - $currentCount);
    }
}