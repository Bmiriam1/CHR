<?php

namespace App\Policies;

use App\Models\Payslip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayslipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any payslips
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view payslips
        // (they'll only see their own if they're learners)
        return true;
    }

    /**
     * Determine if the user can view the payslip
     */
    public function view(User $user, Payslip $payslip): bool
    {
        // Learners can only view their own payslips
        if ($user->hasRole('learner')) {
            return $payslip->user_id === $user->id;
        }
        
        // Admins/HR can view all payslips in their company
        if ($user->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            return $user->company_id === $payslip->company_id;
        }

        return false;
    }

    /**
     * Determine if the user can download the payslip
     */
    public function download(User $user, Payslip $payslip): bool
    {
        // Same rules as viewing
        return $this->view($user, $payslip);
    }

    /**
     * Determine if the user can create payslips
     */
    public function create(User $user): bool
    {
        // Only admins and HR managers can create payslips
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine if the user can update the payslip
     */
    public function update(User $user, Payslip $payslip): bool
    {
        // Only admins and HR managers can update payslips
        if (!$user->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            return false;
        }

        // Must be in the same company
        return $user->company_id === $payslip->company_id;
    }

    /**
     * Determine if the user can delete the payslip
     */
    public function delete(User $user, Payslip $payslip): bool
    {
        // Only admins can delete payslips
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Must be in the same company (or super admin)
        return $user->company_id === $payslip->company_id;
    }

    /**
     * Determine if the user can approve the payslip
     */
    public function approve(User $user, Payslip $payslip): bool
    {
        // Only HR managers and admins can approve
        if (!$user->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            return false;
        }

        // Must be in the same company
        if ($user->company_id !== $payslip->company_id) {
            return false;
        }

        // Can only approve if status is 'calculated' or 'generated'
        return in_array($payslip->status, ['calculated', 'generated']);
    }

    /**
     * Determine if the user can mark payslip as paid
     */
    public function markAsPaid(User $user, Payslip $payslip): bool
    {
        // Only HR managers and admins can mark as paid
        if (!$user->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            return false;
        }

        // Must be in the same company
        if ($user->company_id !== $payslip->company_id) {
            return false;
        }

        // Can only mark as paid if status is 'approved'
        return $payslip->status === 'approved';
    }

    /**
     * Determine if the user can generate payslips
     */
    public function generate(User $user): bool
    {
        // Only admins and HR managers can generate payslips
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }
}