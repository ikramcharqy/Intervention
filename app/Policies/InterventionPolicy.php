<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;

class InterventionPolicy
{
    /**
     * Super Admin et Admin ont un accès complet par défaut.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasAnyRole(['Super Admin', 'superadmin', 'Admin', 'admin'])) {
            return true;
        }

        return null;
    }

    /**
     * Détermine si l'utilisateur peut voir la liste des interventions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur', 'Commercial', 'Technicien', 'Client', 'technicien']);
    }

    /**
     * Détermine si l'utilisateur peut consulter une intervention spécifique.
     */
    public function view(User $user, Intervention $intervention): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur'])) {
            return true;
        }

        if ($user->hasRole('Commercial') && $intervention->cree_par === $user->id) {
            return true;
        }

        if ($user->hasAnyRole(['Technicien', 'technicien']) && $intervention->technicien_id === $user->id) {
            return true;
        }

        if ($user->hasRole('Client')) {
            $clientUser = $user->client;
            return $clientUser && $intervention->chantier && $intervention->chantier->client_id === $clientUser->id;
        }

        return false;
    }

    /**
     * Détermine si l'utilisateur peut créer une intervention.
     * Le Commercial crée la demande, l'Admin / Planificateur crée et planifie.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur', 'Commercial']);
    }

    /**
     * Détermine si l'utilisateur peut planifier ou affecter l'intervention.
     * Réservé à l'Admin / Planificateur.
     */
    public function planOrAssign(User $user, Intervention $intervention): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur']);
    }

    /**
     * Détermine si le technicien peut accepter l'intervention.
     */
    public function accept(User $user, Intervention $intervention): bool
    {
        return $user->id === $intervention->technicien_id && $intervention->peutEtreAcceptee();
    }

    /**
     * Détermine si le technicien peut refuser et demander une réaffectation.
     */
    public function refuse(User $user, Intervention $intervention): bool
    {
        return $user->id === $intervention->technicien_id && $intervention->peutEtreRefusee();
    }

    /**
     * Détermine si l'utilisateur peut démarrer l'intervention.
     */
    public function start(User $user, Intervention $intervention): bool
    {
        return $user->id === $intervention->technicien_id && $intervention->peutEtreDemarree();
    }

    /**
     * Détermine si l'utilisateur peut mettre en pause / suspendre l'intervention.
     */
    public function suspend(User $user, Intervention $intervention): bool
    {
        return $user->id === $intervention->technicien_id && $intervention->peutEtreSuspendue();
    }

    /**
     * Détermine si l'utilisateur peut reprendre l'intervention.
     */
    public function resume(User $user, Intervention $intervention): bool
    {
        return $user->id === $intervention->technicien_id && $intervention->peutEtreReprise();
    }

    /**
     * Détermine si l'utilisateur peut reporter l'intervention.
     */
    public function reschedule(User $user, Intervention $intervention): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur'])) {
            return $intervention->peutEtreReportee();
        }

        return $user->id === $intervention->technicien_id && $intervention->peutEtreReportee();
    }

    /**
     * Détermine si le technicien peut remplir le formulaire et soumettre le rapport.
     */
    public function submitForm(User $user, Intervention $intervention): bool
    {
        return $user->id === $intervention->technicien_id && $intervention->peutEtreSoumise();
    }

    /**
     * Détermine si l'Admin / Manager peut valider et clôturer l'intervention.
     */
    public function validate(User $user, Intervention $intervention): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur']) && $intervention->peutEtreValidee();
    }

    /**
     * Détermine si l'Admin / Manager peut rejeter le rapport (retour terrain).
     */
    public function reject(User $user, Intervention $intervention): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur']) && $intervention->statut === Intervention::STATUT_FORM_REMPLI;
    }

    /**
     * Détermine si l'Admin / Planificateur peut annuler l'intervention.
     */
    public function cancel(User $user, Intervention $intervention): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur']) && $intervention->peutEtreAnnulee();
    }

    /**
     * Détermine si l'Admin peut rouvrir une intervention clôturée.
     */
    public function reopen(User $user, Intervention $intervention): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur']) && $intervention->peutEtreRouverte();
    }

    /**
     * Détermine si l'Admin peut traiter / arbitrer les demandes de réaffectation.
     */
    public function arbitrateReassignment(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Planificateur']);
    }
}
