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
            // If no tenant is set, allow access to user's company group
            // This prevents accidental data leakage while allowing proper access
            if (auth()->check() && auth()->user()->company_id) {
                try {
                    $userCompany = auth()->user()->company;
                    if ($userCompany && method_exists($userCompany, 'getCompanyGroup')) {
                        $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');
                        $builder->whereIn($model->getTable() . '.company_id', $allowedCompanyIds);
                    } else {
                        $builder->where($model->getTable() . '.company_id', auth()->user()->company_id);
                    }
                } catch (\Exception $e) {
                    // Fallback to user's company only
                    $builder->where($model->getTable() . '.company_id', auth()->user()->company_id);
                }
            } else {
                // If no user is authenticated, return empty results for security
                $builder->whereRaw('1 = 0');
            }
        }
    }
}
