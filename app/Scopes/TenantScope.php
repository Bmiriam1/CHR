<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $companyId = session('current_company_id');
        
        if ($companyId) {
            $builder->where($model->getTable() . '.company_id', $companyId);
        } else {
            // If no tenant is set, return empty results for security
            // This prevents accidental data leakage between tenants
            $builder->whereRaw('1 = 0');
        }
    }
}