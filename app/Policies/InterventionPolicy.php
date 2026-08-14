<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterventionPolicy
{
    use HandlesAuthorization;

    protected function userHasRole(User $user, $role): bool
    {
        // Flexible role check to support various implementations
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }
        if (isset($user->role)) {
            return strtolower($user->role) === strtolower($role);
        }
        if (isset($user->roles) && is_array($user->roles)) {
            return in_array($role, $user->roles, true);
        }
        return false;
    }

    public function view(User $user, Intervention $intervention): bool
    {
        if ($this->userHasRole($user, 'ADMIN') || $this->userHasRole($user, 'COMMERCIAL')) {
            return true;
        }
        if ($this->userHasRole($user, 'TECHNICIEN') && $intervention->technicien_id === $user->id) {
            return true;
        }
        if ($this->userHasRole($user, 'CLIENT') && method_exists($user, 'isClientOf')) {
            try {
                return $user->isClientOf($intervention->chantier);
            } catch (\Throwable $e) {
                return false;
            }
        }
        return false;
    }

    public function accept(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'TECHNICIEN') && $intervention->technicien_id === $user->id && $intervention->peutEtreAcceptee();
    }

    public function refuse(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'TECHNICIEN') && $intervention->technicien_id === $user->id && in_array($intervention->statut, [
            Intervention::STATUT_AFFECTEE,
            Intervention::STATUT_PLANIFIEE
        ], true);
    }

    public function start(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'TECHNICIEN') && $intervention->technicien_id === $user->id && $intervention->peutEtreDemarree();
    }

    public function suspend(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'TECHNICIEN') && $intervention->peutEtreSuspendue();
    }

    public function resume(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'TECHNICIEN') && $intervention->peutEtreReprise();
    }

    public function reschedule(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN') || $this->userHasRole($user, 'PLANIFICATEUR');
    }

    public function submitForm(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'TECHNICIEN') && in_array($intervention->statut, [Intervention::STATUT_EN_COURS, Intervention::STATUT_SUSPENDUE, Intervention::STATUT_ACCEPTEE], true);
    }

    public function validate(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN') || $this->userHasRole($user, 'PLANIFICATEUR');
    }

    public function reject(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN');
    }

    public function close(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN') && $intervention->statut === Intervention::STATUT_TERMINEE;
    }

    public function reopen(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN') && in_array($intervention->statut, [Intervention::STATUT_VALIDEE, Intervention::STATUT_TERMINEE], true);
    }

    public function cancel(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN') || $this->userHasRole($user, 'PLANIFICATEUR');
    }

    public function reassign(User $user, Intervention $intervention): bool
    {
        return $this->userHasRole($user, 'ADMIN') || $this->userHasRole($user, 'PLANIFICATEUR');
    }

    public function manageClients(User $user): bool
    {
        return $this->userHasRole($user, 'ADMIN');
    }
}
