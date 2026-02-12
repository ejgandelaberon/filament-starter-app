<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-role');
    }

    public function view(User $user): bool
    {
        return $user->can('view-role');
    }

    public function create(User $user): bool
    {
        return $user->can('create-role');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('update-role') && ! $role->isSystem();
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('delete-role') && ! $role->isSystem();
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->can('restore-role') && ! $role->isSystem();
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->can('force-delete-role') && ! $role->isSystem();
    }
}
