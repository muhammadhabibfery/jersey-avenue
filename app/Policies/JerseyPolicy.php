<?php

namespace App\Policies;

use App\Models\Jersey;
use App\Models\User;

class JerseyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return setPermissions(User::$roles[1], $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Jersey $jersey): bool
    {
        return setPermissions(User::$roles[1], $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return setPermissions(User::$roles[1], $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Jersey $jersey): bool
    {
        return setPermissions(User::$roles[1], $user, fn (): bool => $user->id == $jersey->created_by);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Jersey $jersey): bool
    {
        return setPermissions(User::$roles[1], $user, fn (): bool => $user->id == $jersey->created_by);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Jersey $jersey): bool
    {
        return setPermissions(User::$roles[0], $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Jersey $jersey): bool
    {
        return setPermissions(User::$roles[0], $user);
    }
}
