<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can create companies.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->company_id === $company->id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->company_id === $company->id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can restore the company.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->company_id === $company->id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can permanently delete the company.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->company_id === $company->id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }
}
