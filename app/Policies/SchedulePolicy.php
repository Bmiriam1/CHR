<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SchedulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin', 'instructor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Schedule $schedule): bool
    {
        return $schedule->company_id === $user->company_id;
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
    public function update(User $user, Schedule $schedule): bool
    {
        if ($schedule->company_id !== $user->company_id) {
            return false;
        }

        // Instructors can only edit their own sessions, admins can edit any
        if ($user->hasRole('instructor')) {
            return $schedule->instructor_id === $user->id && 
                   $schedule->status === 'scheduled';
        }

        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']) &&
               $schedule->status !== 'completed';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Schedule $schedule): bool
    {
        return $schedule->company_id === $user->company_id &&
               $user->hasAnyRole(['admin', 'company_admin']) &&
               $schedule->status === 'scheduled';
    }

    /**
     * Determine whether the user can manage schedule status.
     */
    public function manage(User $user, Schedule $schedule): bool
    {
        if ($schedule->company_id !== $user->company_id) {
            return false;
        }

        // Instructors can manage their own sessions
        if ($user->hasRole('instructor')) {
            return $schedule->instructor_id === $user->id;
        }

        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Schedule $schedule): bool
    {
        return $schedule->company_id === $user->company_id &&
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Schedule $schedule): bool
    {
        return $schedule->company_id === $user->company_id &&
               $user->hasRole('admin');
    }
}
