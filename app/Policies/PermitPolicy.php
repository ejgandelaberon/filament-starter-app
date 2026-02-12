<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class PermitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-permit');
    }

    public function view(User $user): bool
    {
        return $user->can('view-permit');
    }

    public function create(User $user): bool
    {
        return $user->can('create-permit');
    }

    public function update(User $user): bool
    {
        return $user->can('update-permit');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-permit');
    }

    public function restore(User $user): bool
    {
        return $user->can('restore-permit');
    }

    public function forceDelete(User $user): bool
    {
        return $user->can('force-delete-permit');
    }
}
