<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublisherPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        foreach ($user->roles as $role) {
            if ($role->name == UserRole::getKey(UserRole::Admin)) {
                return true;
            }
        }
    }
}
