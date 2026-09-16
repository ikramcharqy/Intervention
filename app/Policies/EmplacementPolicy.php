<?php

namespace App\Policies;

use App\Models\Emplacement;
use App\Models\User;

class EmplacementPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasAnyRole(['Super Admin', 'admin', 'Admin'])) {
            return true;
        }

        return null;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Emplacement $emplacement): bool
    {
        return false;
    }

    public function delete(User $user, Emplacement $emplacement): bool
    {
        return false;
    }
}
