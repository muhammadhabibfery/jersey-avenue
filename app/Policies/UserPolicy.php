<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return setPermissions(User::$roles[0], $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return setPermissions(User::$roles[0], $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return setPermissions(User::$roles[0], $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return setPermissions(User::$roles[0], $user, fn (): bool => $model->role == User::$roles[1]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return setPermissions(User::$roles[0], $user, fn (): bool => $model->role == User::$roles[1] && $model->status == User::$status[1]);
    }
}
