<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\InterventionHistorique;
use App\Models\TrackingSession;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InterventionService
{
    public function __construct(protected GpsTrackingService $gpsTrackingService)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Création & Mise à jour
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre une nouvelle intervention dans une transaction DB.
     * Le statut est forcé à 'Planifiee' indépendamment des données reçues.
     * Le créateur est automatiquement renseigné depuis l'utilisateur authentifié.
     */
    public function createIntervention(array $data): Intervention
    {
        return DB::transaction(function () use ($data) {
            // Forçage du statut initial et du créateur
            $data['statut']    = Intervention::STATUT_PLANIFIEE;
            $data['cree_par']  = Auth::id();

            $intervention = Intervention::create($data);

            // Enregistrement de l'entrée initiale dans l'historique
            $this->enregistrerHistorique(
                $intervention,
                statut_avant: null,
                statut_apres: Intervention::STATUT_PLANIFIEE,
                commentaire:  'Intervention créée'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionPlanifieeNotification($intervention));
            }

            return $intervention;
        });
    }

    /**
     * Met à jour une intervention.
     * Protège les champs sensibles si le statut est avancé.
     */
    public function updateIntervention(Intervention $intervention, array $data): Intervention
    {
        return DB::transaction(function () use ($intervention, $data) {
            // Protection : chantier et technicien non modifiables si En cours ou après
            if (in_array($intervention->statut, [
                Intervention::STATUT_EN_COURS,
                Intervention::STATUT_EN_ATTENTE_VALID,
                Intervention::STATUT_TERMINEE,
            ])) {
                unset($data['chantier_id'], $data['emplacement_id'], $data['technicien_id']);
            }

            // Le statut ne peut pas être modifié via update() — uniquement via les actions dédiées
            unset($data['statut']);

            $intervention->update($data);

            return $intervention;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow — Transitions de Statut
    |--------------------------------------------------------------------------
    */

    /**
     * Le technicien accepte l'intervention planifiée.
     * Le statut passe de 'Planifiee' à 'Acceptee'.
     *
     * @throws Exception
     */
    public function accept(Intervention $intervention): void
    {
        if (!$intervention->peutEtreAcceptee()) {
            throw new Exception("L'intervention doit être planifiée pour pouvoir être acceptée.");
        }

        DB::transaction(function () use ($intervention) {
            $intervention->update([
                'statut' => Intervention::STATUT_ACCEPTEE,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: Intervention::STATUT_PLANIFIEE,
                statut_apres: Intervention::STATUT_ACCEPTEE,
                commentaire:  'Intervention acceptée par le technicien'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionAccepteeNotification($intervention));
            }
        });
    }

    /**
     * Démarre l'intervention selon le mode de présence choisi (GPS, QR, NFC, Manuel).
     * Crée une TrackingSession et passe le statut à 'En cours'.
     *
     * @throws Exception
     */
    public function startIntervention(Intervention $intervention, string $mode, array $params): void
    {
        if (!$intervention->peutEtreDemarree()) {
            throw new Exception("L'intervention doit être acceptée pour pouvoir être démarrée.");
        }

        DB::transaction(function () use ($intervention, $mode, $params) {

            // Validation de présence selon le mode
            switch ($mode) {
                case Intervention::MODE_GPS:
                    $this->validateGPS(
                        $intervention,
                        $params['latitude'] ?? null,
                        $params['longitude'] ?? null
                    );
                    break;

                case Intervention::MODE_QR:
                    $this->validateQRCode($intervention, $params['qr_code'] ?? null);
                    break;

                case Intervention::MODE_NFC:
                    $this->validateNFCTag($intervention, $params['nfc_tag'] ?? null);
                    break;

                case Intervention::MODE_MANUEL:
                    // Pas de validation de présence physique requise
                    break;

                default:
                    throw new Exception("Mode de suivi invalide : {$mode}.");
            }

            $ancienStatut = $intervention->statut;
            $now = now();

            // Mise à jour du statut et de la date réelle de début
            $intervention->update([
                'statut'             => Intervention::STATUT_EN_COURS,
                'mode_suivi'         => $mode,
                'date_reelle_debut'  => $now,
            ]);

            // Création de la TrackingSession
            TrackingSession::create([
                'intervention_id' => $intervention->id,
                'technicien_id'   => $intervention->technicien_id,
                'mode'            => $mode,
                'latitude'        => $params['latitude']  ?? null,
                'longitude'       => $params['longitude'] ?? null,
                'qr_code_scan'    => $params['qr_code']  ?? null,
                'nfc_uid_scan'    => $params['nfc_tag']  ?? null,
                'started_at'      => $now,
            ]);

            // Historique
            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_EN_COURS,
                commentaire:  "Démarrage en mode {$mode}"
            );

            $this->demarrerSuiviGps($intervention);

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionDemarreeNotification($intervention));
            }
        });
    }

    /**
     * Met en pause l'intervention (Suspendue).
     * Ferme la TrackingSession en cours si elle existe.
     *
     * @throws Exception
     */
    public function suspendreIntervention(Intervention $intervention, string $motif = ''): void
    {
        if (!$intervention->peutEtreSuspendue()) {
            throw new Exception("Seule une intervention en cours peut être suspendue.");
        }

        DB::transaction(function () use ($intervention, $motif) {
            $ancienStatut = $intervention->statut;

            // Fermeture de la session de tracking active
            TrackingSession::where('intervention_id', $intervention->id)
                ->whereNull('ended_at')
                ->update(['ended_at' => now()]);

            $this->arreterSuiviGps($intervention);

            $intervention->update([
                'statut' => Intervention::STATUT_SUSPENDUE,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_SUSPENDUE,
                commentaire:  $motif ?: 'Intervention suspendue'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionSuspenduNotification($intervention, $motif));
            }
        });
    }

    /**
     * Reprend une intervention suspendue (retour à En cours).
     *
     * @throws Exception
     */
    public function reprendreIntervention(Intervention $intervention): void
    {
        if (!$intervention->peutEtreReprise()) {
            throw new Exception("Seule une intervention suspendue peut être reprise.");
        }

        DB::transaction(function () use ($intervention) {
            $ancienStatut = $intervention->statut;

            // Nouvelle session de tracking pour la reprise
            TrackingSession::create([
                'intervention_id' => $intervention->id,
                'technicien_id'   => $intervention->technicien_id,
                'mode'            => $intervention->mode_suivi ?? Intervention::MODE_MANUEL,
                'started_at'      => now(),
            ]);

            $intervention->update([
                'statut' => Intervention::STATUT_EN_COURS,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_EN_COURS,
                commentaire:  'Reprise de l\'intervention'
            );

            $this->demarrerSuiviGps($intervention);
        });
    }

    /**
     * Soumission du formulaire par le technicien.
     * L'intervention passe à 'Formulaire rempli' (équivalent à attente de validation).
     * Vérifie que toutes les tâches obligatoires sont terminées.
     * Ferme la session de tracking active.
     *
     * @throws Exception
     */
    public function submitForm(Intervention $intervention): void
    {
        if (!$intervention->peutEtreSoumise()) {
            throw new Exception("L'intervention doit être en cours pour soumettre le formulaire.");
        }

        // Vérification des tâches obligatoires non terminées
        $tachesObligatoiresIncompletes = $intervention->taches()
            ->whereHas('tache', fn($q) => $q->where('is_obligatoire', true))
            ->where('statut', '!=', 'Terminee')
            ->count();

        if ($tachesObligatoiresIncompletes > 0) {
            throw new Exception(
                "Il reste {$tachesObligatoiresIncompletes} tâche(s) obligatoire(s) à compléter avant de soumettre."
            );
        }

        DB::transaction(function () use ($intervention) {
            $ancienStatut = $intervention->statut;
            $now = now();

            // Fermeture de la session de tracking active
            TrackingSession::where('intervention_id', $intervention->id)
                ->whereNull('ended_at')
                ->update(['ended_at' => $now]);

            $this->arreterSuiviGps($intervention);

            $intervention->update([
                'statut'                      => Intervention::STATUT_FORM_REMPLI,
                'date_soumission_validation'  => $now,
                'pourcentage_global'          => $this->calculerPourcentage($intervention),
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_FORM_REMPLI,
                commentaire:  'Formulaire rempli et soumis à validation'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionFormulaireSubmisNotification($intervention));
            }
        });
    }

    /**
     * Validation finale par l'administrateur.
     * L'intervention passe à 'Terminee' et la date de fin réelle est horodatée.
     * Nécessite qu'un rapport existe.
     *
     * @throws Exception
     */
    public function validateIntervention(Intervention $intervention): void
    {
        if (!$intervention->peutEtreValidee()) {
            throw new Exception("Seule une intervention dont le formulaire est rempli peut être validée.");
        }

        // Un rapport est obligatoire avant la validation
        if (!$intervention->rapport()->exists()) {
            throw new Exception("Un rapport d'intervention doit être rédigé avant de pouvoir valider.");
        }

        DB::transaction(function () use ($intervention) {
            $ancienStatut = $intervention->statut;
            $now = now();

            $intervention->update([
                'statut'          => Intervention::STATUT_TERMINEE,
                'valide_par'      => Auth::id(),
                'date_validation' => $now,
                'date_reelle_fin' => $now,
                'pourcentage_global' => 100,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_TERMINEE,
                commentaire:  'Validée et clôturée par l\'administrateur'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionValideeNotification($intervention));
            }

            \App\Models\AuditLog::create([
                'user_id'    => Auth::id(),
                'user_name'  => Auth::user()?->name ?? 'Admin',
                'action'     => 'Validation Intervention',
                'module'     => 'Interventions',
                'severity'   => 'success',
                'ip_address' => request()->ip(),
                'details'    => "Intervention {$intervention->code_intervention} validée et clôturée avec succès.",
            ]);
        });
    }

    /**
     * Rejet de la validation par l'admin — renvoie le technicien sur le terrain.
     * L'intervention repasse à 'En cours'.
     *
     * @throws Exception
     */
    public function rejeterValidation(Intervention $intervention, string $motif): void
    {
        if (!$intervention->peutEtreValidee()) {
            throw new Exception("L'intervention doit être au statut 'Formulaire rempli' pour être rejetée.");
        }

        DB::transaction(function () use ($intervention, $motif) {
            $ancienStatut = $intervention->statut;

            $intervention->update([
                'statut'                     => Intervention::STATUT_EN_COURS,
                'date_soumission_validation' => null,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_EN_COURS,
                commentaire:  "Rejet admin : {$motif}"
            );

            $this->demarrerSuiviGps($intervention);

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionRejeteeNotification($intervention, $motif));
            }
        });
    }

    /**
     * Annulation ou suppression logique.
     * - Planifiee  → suppression physique (jamais démarrée)
     * - En cours / Suspendue → passage à Annulee avec motif obligatoire
     *
     * @throws Exception
     */
    public function cancelOrDelete(Intervention $intervention, string $motif = ''): void
    {
        if (!$intervention->peutEtreAnnulee()) {
            throw new Exception("Cette intervention ne peut pas être annulée dans son statut actuel.");
        }

        if ($intervention->statut === Intervention::STATUT_PLANIFIEE) {
            $intervention->delete();
            return;
        }

        DB::transaction(function () use ($intervention, $motif) {
            $ancienStatut = $intervention->statut;

            // Fermeture de toute session de tracking ouverte
            TrackingSession::where('intervention_id', $intervention->id)
                ->whereNull('ended_at')
                ->update(['ended_at' => now()]);

            $this->arreterSuiviGps($intervention);

            $intervention->update([
                'statut'            => Intervention::STATUT_ANNULEE,
                'motif_annulation'  => $motif ?: 'Annulation sans motif précisé',
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_ANNULEE,
                commentaire:  $motif ?: 'Intervention annulée'
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Calculs & Utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le pourcentage global d'avancement basé sur les tâches complétées.
     * Si aucune tâche n'est associée, retourne le pourcentage actuel.
     */
    public function calculerPourcentage(Intervention $intervention): int
    {
        $total = $intervention->taches()->count();

        if ($total === 0) {
            return $intervention->pourcentage_global ?? 0;
        }

        $terminees = $intervention->taches()->where('statut', 'Terminee')->count();

        return (int) round(($terminees / $total) * 100);
    }

    /*
    |--------------------------------------------------------------------------
    | Historique
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre une entrée dans l'historique des transitions de statut.
     */
    protected function enregistrerHistorique(
        Intervention $intervention,
        ?string $statut_avant,
        string $statut_apres,
        string $commentaire = ''
    ): void {
        // Support both legacy and new migration schemas by checking columns
        $payload = [
            'intervention_id' => $intervention->id,
            'user_id'         => Auth::id(),
            'metadata'        => null,
            'ip_address'      => request()?->ip() ?? null,
            'device_id'       => request()->header('X-Device-Id') ?? null,
        ];

        // Prefer new column names if present
        if (\Illuminate\Support\Facades\Schema::hasColumn('intervention_historiques', 'ancien_statut')) {
            $payload['ancien_statut']  = $statut_avant ?? '—';
            $payload['nouveau_statut'] = $statut_apres;
            $payload['action']         = $commentaire ? substr($commentaire, 0, 255) : null;
            $payload['motif']          = $commentaire ?: null;
        } else {
            // Fallback to legacy column names
            $payload['statut_avant'] = $statut_avant ?? '—';
            $payload['statut_apres'] = $statut_apres;
            $payload['commentaire']  = $commentaire ?: null;
        }

        InterventionHistorique::create($payload);
    }

    /**
     * Le technicien refuse l'intervention (motif obligatoire) — passe à REFUSEE.
     */
    public function refuseIntervention(Intervention $intervention, string $motif): void
    {
        if (!in_array($intervention->statut, [Intervention::STATUT_AFFECTEE, Intervention::STATUT_PLANIFIEE], true)) {
            throw new \Exception('Impossible de refuser : statut invalide.');
        }

        DB::transaction(function () use ($intervention, $motif) {
            $ancien = $intervention->statut;

            $intervention->update([
                'statut' => Intervention::STATUT_REFUSEE,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancien,
                statut_apres: Intervention::STATUT_REFUSEE,
                commentaire: "Refus technicien : {$motif}"
            );

            // Notifier l'admin/planificateur
            if ($intervention->createur) {
                $intervention->createur->notify(new \App\Notifications\InterventionRefuseeNotification($intervention, $motif));
            }
        });
    }

    /**
     * Reporter / replanifier une intervention (Admin ou Planificateur).
     */
    public function reporterIntervention(Intervention $intervention, string $nouvelleDate, string $motif = ''): void
    {
        // Validation simple de date (format attendu: YYYY-MM-DD or parsable)
        $date = null;
        try { $date = \Illuminate\Support\Carbon::parse($nouvelleDate); } catch (\Throwable $e) { }
        if (!$date) {
            throw new \Exception('Date de report invalide');
        }

        DB::transaction(function () use ($intervention, $date, $motif) {
            $ancien = $intervention->statut;

            $intervention->update([
                'statut' => Intervention::STATUT_REPORTEE,
                'date_prevue_debut' => $date,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancien,
                statut_apres: Intervention::STATUT_REPORTEE,
                commentaire: "Reporté à {$date->toDateTimeString()} - {$motif}"
            );

            // Notifier technicien et planificateur
            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionReporteeNotification($intervention, $date, $motif));
            }
        });
    }

    /**
     * Réaffecter une intervention à un autre technicien (Admin / Planificateur).
     */
    public function reassignIntervention(Intervention $intervention, int $nouveauTechnicienId, string $motif = ''): void
    {
        DB::transaction(function () use ($intervention, $nouveauTechnicienId, $motif) {
            $ancien = $intervention->statut;
            $ancienTech = $intervention->technicien_id;

            $intervention->update([
                'technicien_id' => $nouveauTechnicienId,
                'statut' => Intervention::STATUT_AFFECTEE,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancien,
                statut_apres: Intervention::STATUT_AFFECTEE,
                commentaire: "Réaffectation de technicien ({$ancienTech} -> {$nouveauTechnicienId}) : {$motif}"
            );

            // Notifications basiques
            $nouveau = \App\Models\User::find($nouveauTechnicienId);
            if ($nouveau) {
                $nouveau->notify(new \App\Notifications\InterventionAffecteeNotification($intervention));
            }
        });
    }

    /**
     * Clôture administrative finale (après Terminee) → passe à VALIDEE
     */
    public function cloturerIntervention(Intervention $intervention): void
    {
        if ($intervention->statut !== Intervention::STATUT_TERMINEE) {
            throw new \Exception('Lintervention doit être au statut Terminee pour être clôturée.');
        }

        DB::transaction(function () use ($intervention) {
            $ancien = $intervention->statut;

            $intervention->update([
                'statut' => Intervention::STATUT_VALIDEE,
                'valide_par' => Auth::id(),
                'date_validation' => now(),
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancien,
                statut_apres: Intervention::STATUT_VALIDEE,
                commentaire: 'Clôture administrative'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionClotureeNotification($intervention));
            }
        });
    }

    /**
     * Rouvrir une intervention clôturée/validée pour complément ou litige.
     */
    public function rouvrirIntervention(Intervention $intervention, string $motif = ''): void
    {
        if (!in_array($intervention->statut, [Intervention::STATUT_VALIDEE, Intervention::STATUT_TERMINEE], true)) {
            throw new \Exception('Seules les interventions Validée/Terminée peuvent être rouvertes.');
        }

        DB::transaction(function () use ($intervention, $motif) {
            $ancien = $intervention->statut;

            $intervention->update([
                'statut' => Intervention::STATUT_ROUVERTE,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancien,
                statut_apres: Intervention::STATUT_ROUVERTE,
                commentaire: "Réouverture : {$motif}"
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionRouverteNotification($intervention, $motif));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Validation de Présence Physique
    |--------------------------------------------------------------------------
    */

    /**
     * Validation GPS : le technicien doit être à moins de 100m de l'emplacement.
     *
     * @throws Exception
     */
    protected function validateGPS(Intervention $intervention, ?float $latitude, ?float $longitude): void
    {
        $emplacement = $intervention->emplacement;

        if (!$emplacement || is_null($emplacement->latitude) || is_null($emplacement->longitude)) {
            // Fallback sur les coordonnées du chantier
            $chantier = $intervention->chantier;
            if (!$chantier || is_null($chantier->latitude) || is_null($chantier->longitude)) {
                throw new Exception("Les coordonnées GPS de l'emplacement et du chantier ne sont pas configurées.");
            }
            $targetLat = (float) $chantier->latitude;
            $targetLon = (float) $chantier->longitude;
        } else {
            $targetLat = (float) $emplacement->latitude;
            $targetLon = (float) $emplacement->longitude;
        }

        if (is_null($latitude) || is_null($longitude)) {
            throw new Exception("Les coordonnées GPS du technicien sont requises pour démarrer en mode GPS.");
        }

        $distance = $this->calculateDistance($latitude, $longitude, $targetLat, $targetLon);

        if ($distance > 0.1) { // 100 mètres
            throw new Exception("Vous êtes trop éloigné de l'emplacement d'intervention (distance : " . round($distance * 1000) . " m).");
        }
    }

    /**
     * Validation QR Code : le code scanné doit correspondre à l'emplacement.
     *
     * @throws Exception
     */
    protected function validateQRCode(Intervention $intervention, ?string $qrCode): void
    {
        $emplacement = $intervention->emplacement;

        if (!$emplacement || !$emplacement->qr_code) {
            throw new Exception("L'emplacement n'a pas de QR Code configuré.");
        }

        if (is_null($qrCode) || $qrCode !== $emplacement->qr_code) {
            throw new Exception("Le QR Code scanné est invalide ou ne correspond pas à cet emplacement.");
        }
    }

    /**
     * Validation NFC : l'UID scanné doit correspondre à l'emplacement.
     *
     * @throws Exception
     */
    protected function validateNFCTag(Intervention $intervention, ?string $nfcTag): void
    {
        if (is_null($nfcTag)) {
            throw new Exception("Le tag NFC est requis pour ce mode de démarrage.");
        }

        $emplacement = $intervention->emplacement;

        if (!$emplacement || !$emplacement->nfc_uid) {
            throw new Exception("L'emplacement n'a pas de tag NFC configuré.");
        }

        if ($nfcTag !== $emplacement->nfc_uid) {
            throw new Exception("Le tag NFC scanné est incorrect.");
        }
    }

    /**
     * Calcule la distance entre deux points GPS (formule Haversine, résultat en km).
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2))
               + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos(min(1.0, max(-1.0, $dist))); // Clamping pour éviter les erreurs flottantes
        $dist  = rad2deg($dist);

        return $dist * 60 * 1.1515 * 1.609344;
    }

    /*
    |--------------------------------------------------------------------------
    | Suivi GPS automatique (module GpsTrackingSession — indépendant de TrackingSession)
    |--------------------------------------------------------------------------
    */

    /**
     * Démarre automatiquement une session de suivi GPS si le technicien est assigné.
     * Silencieux si une session est déjà active (ne bloque jamais le workflow principal).
     */
    private function demarrerSuiviGps(Intervention $intervention): void
    {
        if (!$intervention->technicien_id || !$intervention->technicien) {
            return;
        }

        try {
            $this->gpsTrackingService->startSession($intervention, $intervention->technicien);
        } catch (\RuntimeException $e) {
            // Une session GPS est déjà en cours : rien à faire.
        }
    }

    /**
     * Arrête automatiquement la session de suivi GPS active, s'il y en a une.
     */
    private function arreterSuiviGps(Intervention $intervention): void
    {
        $sessionActive = $intervention->gpsTrackingSessions()->whereNull('ended_at')->first();

        if ($sessionActive) {
            $this->gpsTrackingService->stopSession($sessionActive);
        }
    }
}
