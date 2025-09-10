<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProgramPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Program $program): bool
    {
        return $program->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Program $program): bool
    {
        if ($program->company_id !== $user->company_id) {
            return false;
        }

        // Only allow updates if not yet active or by admins
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']) &&
               ($program->status !== 'active' || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Program $program): bool
    {
        return $program->company_id === $user->company_id &&
               $user->hasAnyRole(['admin', 'company_admin']) &&
               $program->status === 'draft';
    }

    /**
     * Determine whether the user can manage program status.
     */
    public function manage(User $user, Program $program): bool
    {
        return $program->company_id === $user->company_id &&
               $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can approve programs.
     */
    public function approve(User $user, Program $program): bool
    {
        return $program->company_id === $user->company_id &&
               $user->hasAnyRole(['admin', 'company_admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Program $program): bool
    {
        return $program->company_id === $user->company_id &&
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Program $program): bool
    {
        return $program->company_id === $user->company_id &&
               $user->hasRole('admin');
    }
}
