<?php

namespace App\Policies;

use App\Models\Rapport;
use App\Models\User;

class RapportPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasAnyRole(['Super Admin', 'superadmin', 'Admin', 'admin'])) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur', 'Commercial', 'Technicien', 'Client', 'technicien']);
    }

    public function view(User $user, Rapport $rapport): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur'])) {
            return true;
        }

        if ($user->hasRole('Commercial') && $rapport->intervention->cree_par === $user->id) {
            return true;
        }

        if ($user->hasAnyRole(['Technicien', 'technicien']) && $rapport->intervention->technicien_id === $user->id) {
            return true;
        }

        if ($user->hasRole('Client')) {
            $clientUser = $user->client;
            return $clientUser && $rapport->intervention->chantier && $rapport->intervention->chantier->client_id === $clientUser->id && $rapport->intervention->statut === 'Terminee';
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur', 'Technicien', 'technicien']);
    }

    public function update(User $user, Rapport $rapport): bool
    {
        return $this->view($user, $rapport); // Simplified logic
    }

    public function delete(User $user, Rapport $rapport): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
}
