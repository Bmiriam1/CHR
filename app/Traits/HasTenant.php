<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Scopes\TenantScope;

trait HasTenant
{
    /**
     * Boot the HasTenant trait for a model.
     */
    protected static function bootHasTenant(): void
    {
        // Add global scope for tenant isolation
        static::addGlobalScope(new TenantScope);
        
        // Automatically set company_id when creating new records
        static::creating(function (Model $model) {
            if (!$model->company_id && session()->has('current_company_id')) {
                $model->company_id = session('current_company_id');
            }
        });
    }

    /**
     * Get the company that owns this model.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to only include models for a specific company.
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include models for the current tenant.
     */
    public function scopeForCurrentTenant(Builder $query): Builder
    {
        $companyId = session('current_company_id');
        
        if (!$companyId) {
            // If no tenant is set, return empty results for security
            return $query->whereRaw('1 = 0');
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Check if this model belongs to the current tenant.
     */
    public function belongsToCurrentTenant(): bool
    {
        $currentCompanyId = session('current_company_id');
        
        return $this->company_id === $currentCompanyId;
    }

    /**
     * Check if this model belongs to the specified tenant.
     */
    public function belongsToTenant(int $companyId): bool
    {
        return $this->company_id === $companyId;
    }

    /**
     * Get the tenant key for this model.
     */
    public function getTenantKey(): ?string
    {
        return $this->company?->tenant_key;
    }
}