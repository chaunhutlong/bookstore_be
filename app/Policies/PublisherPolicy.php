<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Publisher;
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
    public function viewAny(User $user, $ability)
    {
        // 4 is the id of the admin role
        return $user->hasRole(UserRole::getKey(4));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Publisher  $publisher
     * @return mixed
     */
    public function view(User $user, Publisher $publisher)
    {
        switch ($user->role) {
            case UserRole::getKey(4):
                return true;
            case UserRole::getKey(3):
            default:
                return $user->id === $publisher->user_id;

                return false;
        }
    }
}
