<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Intervention extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constantes métier
    |--------------------------------------------------------------------------
    */

    // Statuts possibles (Ordre complet du workflow)
    const STATUT_DEMANDE                = 'Demande';
    const STATUT_PLANIFIEE              = 'Planifiee';
    const STATUT_AFFECTEE               = 'Affectee';
    const STATUT_EN_ATTENTE_REAFFECTATION = 'En attente reafectation';
    const STATUT_ACCEPTEE               = 'Acceptee';
    const STATUT_REFUSEE                = 'Refusee';
    const STATUT_EN_COURS               = 'En cours';
    const STATUT_SUSPENDUE              = 'Suspendue';
    const STATUT_REPORTEE               = 'Reportee';
    const STATUT_PARTIELLEMENT_REALISEE = 'Partiellement realisee';
    const STATUT_CLIENT_ABSENT          = 'Client absent';
    const STATUT_MATERIEL_MANQUANT      = 'Materiel manquant';
    const STATUT_DEUXIEME_VISITE        = 'Deuxieme visite requise';
    const STATUT_FORM_REMPLI            = 'Formulaire rempli';
    const STATUT_EN_ATTENTE_VALID       = 'En attente validation'; // Rétrocompatibilité
    const STATUT_REJETEE                = 'Rejetee';
    const STATUT_TERMINEE               = 'Terminee';
    const STATUT_VALIDEE                = 'Validee';
    const STATUT_ANNULEE                = 'Annulee';
    const STATUT_ROUVERTE               = 'Rouverte';

    // Priorités (ordre croissant d'urgence)
    const PRIORITE_FAIBLE   = 'Faible';
    const PRIORITE_NORMALE  = 'Normale';
    const PRIORITE_HAUTE    = 'Haute';
    const PRIORITE_URGENTE  = 'Urgente';

    // Modes de suivi de présence
    const MODE_GPS    = 'GPS';
    const MODE_QR     = 'QR';
    const MODE_NFC    = 'NFC';
    const MODE_MANUEL = 'Manuel';

    protected $fillable = [
        'code_intervention',

        'chantier_id',
        'emplacement_id',

        'technicien_id',
        'type_intervention_id',

        'mode_suivi',

        'cree_par',
        'valide_par',

        'priorite',
        'statut',

        'date_prevue_debut',
        'date_prevue_fin',

        'date_reelle_debut',
        'date_reelle_fin',

        'date_soumission_validation',
        'date_validation',

        'duree_prevue',
        'duree_reelle',

        'pourcentage_global',

        'description',
        'observations',
        'motif_annulation',
        'motif_suspension',
        'motif_report',
        'resultat_intervention',
        'intervention_parente_id',
    ];

    protected $casts = [
        'date_prevue_debut'           => 'datetime',
        'date_prevue_fin'             => 'datetime',
        'date_reelle_debut'           => 'datetime',
        'date_reelle_fin'             => 'datetime',
        'date_soumission_validation'  => 'datetime',
        'date_validation'             => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Client propriétaire du chantier.
     * Correction : utilisation d'un chemin de relation explicite
     * (hasManyThrough n'est pas adapté ici — on accède via chantier).
     */
    public function client()
    {
        return $this->hasOneThrough(
            Client::class,    // Model final
            Chantier::class,  // Model intermédiaire
            'id',             // Clé locale sur chantiers (jointure depuis interventions.chantier_id)
            'id',             // Clé locale sur clients
            'chantier_id',    // Clé étrangère sur interventions → chantiers
            'client_id'       // Clé étrangère sur chantiers → clients
        );
    }

    // Chantier de l'intervention
    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }

    // Emplacement précis (sous-partie du chantier)
    public function emplacement()
    {
        return $this->belongsTo(Emplacement::class);
    }

    // Technicien assigné
    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    // Utilisateur ayant créé l'intervention (admin/planificateur)
    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    // Utilisateur ayant validé la clôture (admin)
    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Type d'intervention
    public function typeIntervention()
    {
        return $this->belongsTo(TypeIntervention::class);
    }

    // Tâches de l'intervention (table pivot intervention_taches)
    public function taches()
    {
        return $this->hasMany(InterventionTache::class);
    }

    // Matériaux utilisés (table pivot intervention_materiaus)
    public function materiaux()
    {
        return $this->hasMany(InterventionMateriau::class);
    }

    // Rapport d'intervention (1 seul par intervention)
    public function rapport()
    {
        return $this->hasOne(Rapport::class);
    }

    // Sessions de tracking (GPS/QR/NFC/Manuel) — pointage de présence
    public function trackingSessions()
    {
        return $this->hasMany(TrackingSession::class);
    }

    // Sessions de suivi GPS continu du déplacement (module indépendant)
    public function gpsTrackingSessions()
    {
        return $this->hasMany(GpsTrackingSession::class);
    }

    // Demandes de réaffectation du technicien
    public function demandesReaffectation()
    {
        return $this->hasMany(DemandeReaffectation::class);
    }

    // Historique des transitions de statut
    public function historiques()
    {
        return $this->hasMany(InterventionHistorique::class);
    }

    // Intervention parente (dans le cas d'une 2ème visite / revisite)
    public function interventionParente()
    {
        return $this->belongsTo(Intervention::class, 'intervention_parente_id');
    }

    // Interventions enfants (revisites générées)
    public function interventionsEnfants()
    {
        return $this->hasMany(Intervention::class, 'intervention_parente_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de filtrage
    |--------------------------------------------------------------------------
    */

    // Interventions planifiées uniquement
    public function scopePlanifiees(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PLANIFIEE);
    }

    // Interventions en cours
    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_EN_COURS);
    }

    // Interventions en attente de validation admin
    public function scopeEnAttenteValidation(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE_VALID);
    }

    // Interventions terminées
    public function scopeTerminees(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_TERMINEE);
    }

    // Interventions urgentes ou haute priorité
    public function scopeUrgentes(Builder $query): Builder
    {
        return $query->whereIn('priorite', [self::PRIORITE_URGENTE, self::PRIORITE_HAUTE]);
    }

    // Interventions actives (non terminées, non annulées)
    public function scopeActives(Builder $query): Builder
    {
        return $query->whereNotIn('statut', [self::STATUT_TERMINEE, self::STATUT_ANNULEE]);
    }

    // Filtre par technicien
    public function scopePourTechnicien(Builder $query, int $technicienId): Builder
    {
        return $query->where('technicien_id', $technicienId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accesseurs utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Matrice des transitions de statut autorisées dans le workflow.
     */
    public const ALLOWED_TRANSITIONS = [
        self::STATUT_DEMANDE => [
            self::STATUT_PLANIFIEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_PLANIFIEE => [
            self::STATUT_AFFECTEE,
            self::STATUT_ACCEPTEE,
            self::STATUT_REPORTEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_AFFECTEE => [
            self::STATUT_ACCEPTEE,
            self::STATUT_EN_ATTENTE_REAFFECTATION,
            self::STATUT_REFUSEE,
            self::STATUT_REPORTEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_EN_ATTENTE_REAFFECTATION => [
            self::STATUT_PLANIFIEE, // Demande acceptée par Admin -> remise en planification
            self::STATUT_ACCEPTEE,  // Demande refusée par Admin -> technicien forcé à l'effectuer
            self::STATUT_AFFECTEE,  // Réaffectation directe
            self::STATUT_ANNULEE,
        ],
        self::STATUT_REFUSEE => [
            self::STATUT_PLANIFIEE,
            self::STATUT_AFFECTEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_ACCEPTEE => [
            self::STATUT_EN_COURS,
            self::STATUT_REPORTEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_EN_COURS => [
            self::STATUT_SUSPENDUE,
            self::STATUT_REPORTEE,
            self::STATUT_PARTIELLEMENT_REALISEE,
            self::STATUT_CLIENT_ABSENT,
            self::STATUT_MATERIEL_MANQUANT,
            self::STATUT_DEUXIEME_VISITE,
            self::STATUT_FORM_REMPLI,
            self::STATUT_TERMINEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_SUSPENDUE => [
            self::STATUT_EN_COURS,
            self::STATUT_REPORTEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_REPORTEE => [
            self::STATUT_PLANIFIEE,
            self::STATUT_AFFECTEE,
            self::STATUT_EN_COURS,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_PARTIELLEMENT_REALISEE => [
            self::STATUT_DEUXIEME_VISITE,
            self::STATUT_PLANIFIEE,
            self::STATUT_REPORTEE,
            self::STATUT_FORM_REMPLI,
            self::STATUT_TERMINEE,
            self::STATUT_VALIDEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_CLIENT_ABSENT => [
            self::STATUT_DEUXIEME_VISITE,
            self::STATUT_REPORTEE,
            self::STATUT_PLANIFIEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_MATERIEL_MANQUANT => [
            self::STATUT_DEUXIEME_VISITE,
            self::STATUT_REPORTEE,
            self::STATUT_PLANIFIEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_DEUXIEME_VISITE => [
            self::STATUT_PLANIFIEE,
            self::STATUT_AFFECTEE,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_FORM_REMPLI => [
            self::STATUT_REJETEE,
            self::STATUT_TERMINEE,
            self::STATUT_VALIDEE,
        ],
        self::STATUT_EN_ATTENTE_VALID => [ // Rétrocompatibilité
            self::STATUT_REJETEE,
            self::STATUT_TERMINEE,
            self::STATUT_VALIDEE,
        ],
        self::STATUT_REJETEE => [
            self::STATUT_EN_COURS,
            self::STATUT_FORM_REMPLI,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_TERMINEE => [
            self::STATUT_VALIDEE,
            self::STATUT_ROUVERTE,
        ],
        self::STATUT_VALIDEE => [
            self::STATUT_ROUVERTE,
        ],
        self::STATUT_ROUVERTE => [
            self::STATUT_EN_COURS,
            self::STATUT_ANNULEE,
        ],
        self::STATUT_ANNULEE => [],
    ];

    /**
     * Vérifie si le passage du statut actuel vers le nouveau statut est valide.
     */
    public function transitionPossibleVers(string $nouveauStatut): bool
    {
        $statutActuel = $this->statut;

        if ($statutActuel === $nouveauStatut) {
            return true;
        }

        return in_array($nouveauStatut, self::ALLOWED_TRANSITIONS[$statutActuel] ?? [], true);
    }

    public function peutEtreAcceptee(): bool
    {
        return in_array($this->statut, [self::STATUT_PLANIFIEE, self::STATUT_AFFECTEE], true);
    }

    public function peutEtreRefusee(): bool
    {
        return in_array($this->statut, [self::STATUT_PLANIFIEE, self::STATUT_AFFECTEE], true);
    }

    public function peutEtreDemarree(): bool
    {
        return in_array($this->statut, [
            self::STATUT_ACCEPTEE,
            self::STATUT_SUSPENDUE,
            self::STATUT_REPORTEE,
            self::STATUT_REJETEE,
            self::STATUT_ROUVERTE,
        ], true);
    }

    public function peutEtreSoumise(): bool
    {
        return in_array($this->statut, [
            self::STATUT_EN_COURS,
            self::STATUT_FORM_REMPLI,
            self::STATUT_REJETEE,
        ], true);
    }

    public function peutEtreValidee(): bool
    {
        return in_array($this->statut, [
            self::STATUT_FORM_REMPLI,
            self::STATUT_EN_ATTENTE_VALID,
            self::STATUT_TERMINEE,
        ], true);
    }

    public function peutEtreSuspendue(): bool
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    public function peutEtreReprise(): bool
    {
        return $this->statut === self::STATUT_SUSPENDUE;
    }

    public function peutEtreReportee(): bool
    {
        return in_array($this->statut, [
            self::STATUT_PLANIFIEE,
            self::STATUT_AFFECTEE,
            self::STATUT_ACCEPTEE,
            self::STATUT_EN_COURS,
            self::STATUT_SUSPENDUE,
        ], true);
    }

    public function peutEtreRouverte(): bool
    {
        return in_array($this->statut, [self::STATUT_TERMINEE, self::STATUT_VALIDEE], true);
    }

    public function peutEtreAnnulee(): bool
    {
        return !in_array($this->statut, [self::STATUT_VALIDEE, self::STATUT_ANNULEE], true);
    }
}