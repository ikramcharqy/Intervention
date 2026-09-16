<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasAnyRole(['Super Admin', 'admin', 'Admin'])) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('Commercial');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasRole('Commercial') && $client->commercial_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Commercial');
    }

    /**
     * CIN/date de naissance : donnée personnelle sensible (loi 09-08), visible
     * uniquement par Admin/Super Admin (via before()) et le Commercial assigné
     * à ce client précis — pas par les autres rôles ni les autres commerciaux.
     */
    public function viewIdentity(User $user, Client $client): bool
    {
        return $user->hasRole('Commercial') && $client->commercial_id === $user->id;
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasRole('Commercial') && $client->commercial_id === $user->id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->update($user, $client);
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }

    public function reassign(User $user, Client $client): bool
    {
        return false;
    }

    public function manageAssignment(User $user): bool
    {
        return false;
    }
}
