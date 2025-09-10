<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any clients.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can view the client.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id;
    }

    /**
     * Determine whether the user can create clients.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can update the client.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can delete the client.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can restore the client.
     */
    public function restore(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }

    /**
     * Determine whether the user can permanently delete the client.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id &&
            $user->hasAnyRole(['admin', 'hr_manager', 'company_admin']);
    }
}
