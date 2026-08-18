<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\InterventionHistorique;
use App\Models\InterventionTache;
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
            // Auto-génération d'un code unique si non spécifié
            if (empty($data['code_intervention'])) {
                $data['code_intervention'] = 'INT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }

            // Valeur par défaut pour date_prevue_debut & date_prevue_fin si non fournies
            if (empty($data['date_prevue_debut'])) {
                $data['date_prevue_debut'] = now();
            }
            if (empty($data['date_prevue_fin'])) {
                $data['date_prevue_fin'] = now()->addHour();
            }

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
     * Admin — Planifie et affecte une intervention à un technicien.
     * Le statut passe de 'Planifiee' (ou 'Demande') à 'Affectee'.
     *
     * @throws Exception
     */
    public function planifierEtAffecter(
        Intervention $intervention,
        int $technicienId,
        string $datePrevueDebut,
        ?string $datePrevueFin = null,
        ?string $priorite = null,
        ?string $commentaire = null
    ): void {
        if (!in_array($intervention->statut, [Intervention::STATUT_PLANIFIEE, Intervention::STATUT_DEMANDE])) {
            throw new Exception("Seules les interventions en statut 'Planifiee' ou 'Demande' peuvent être affectées via ce formulaire.");
        }

        DB::transaction(function () use ($intervention, $technicienId, $datePrevueDebut, $datePrevueFin, $priorite, $commentaire) {
            $ancienStatut = $intervention->statut;

            $updateData = [
                'technicien_id'     => $technicienId,
                'statut'            => Intervention::STATUT_AFFECTEE,
                'date_prevue_debut' => $datePrevueDebut,
            ];

            if ($datePrevueFin) {
                $updateData['date_prevue_fin'] = $datePrevueFin;
            }
            if ($priorite) {
                $updateData['priorite'] = $priorite;
            }

            $intervention->update($updateData);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_AFFECTEE,
                commentaire:  $commentaire ?: "Intervention planifiée et affectée au technicien #{$technicienId} par l'administrateur."
            );

            // Notifier le technicien assigné
            $technicien = \App\Models\User::find($technicienId);
            if ($technicien) {
                try {
                    $technicien->notify(new \App\Notifications\InterventionPlanifieeNotification($intervention));
                } catch (\Throwable $e) {
                    // Notification non bloquante
                }
            }
        });
    }

    /**
     * Terminer l'intervention définitivement par le technicien.
     *
     * @throws Exception
     */
    public function finishIntervention(Intervention $intervention, ?string $commentaire = null): Intervention
    {
        if (in_array($intervention->statut, [Intervention::STATUT_TERMINEE, Intervention::STATUT_VALIDEE, Intervention::STATUT_ANNULEE])) {
            throw new Exception("Cette intervention est déjà clôturée.");
        }

        $statutAvant = $intervention->statut;
        $intervention->statut = Intervention::STATUT_TERMINEE;
        $intervention->date_reelle_fin = now();
        if ($intervention->date_reelle_debut) {
            $intervention->duree_reelle = (int) round($intervention->date_reelle_debut->diffInMinutes(now()));
        }
        $intervention->save();

        $this->enregistrerHistorique(
            $intervention,
            statut_avant: $statutAvant,
            statut_apres: Intervention::STATUT_TERMINEE,
            commentaire:  $commentaire ?: "L'intervention a été terminée et transmise à l'administration par le technicien. Formulaire et rapport scellés."
        );

        return $intervention;
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

            // Notifier le technicien des modifications
            if ($intervention->technicien) {
                $changesDesc = array_filter([
                    isset($data['date_prevue_debut'])  ? 'Date début modifiée'    : null,
                    isset($data['date_prevue_fin'])    ? 'Date fin modifiée'      : null,
                    isset($data['priorite'])           ? 'Priorité modifiée'      : null,
                    isset($data['description'])        ? 'Description modifiée'   : null,
                ]);
                $intervention->technicien->notify(new \App\Notifications\InterventionModifieeNotification($intervention, array_values($changesDesc)));
            }

            return $intervention;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow — Transitions de Statut
    |--------------------------------------------------------------------------
    */

    /**
     * Le technicien accepte l'intervention planifiée ou affectée.
     * Le statut passe à 'Acceptee'.
     *
     * @throws Exception
     */
    public function accept(Intervention $intervention): void
    {
        if (!$intervention->peutEtreAcceptee()) {
            throw new Exception("L'intervention doit être planifiée ou affectée pour pouvoir être acceptée.");
        }

        DB::transaction(function () use ($intervention) {
            $ancienStatut = $intervention->statut;

            $intervention->update([
                'statut' => Intervention::STATUT_ACCEPTEE,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_ACCEPTEE,
                commentaire:  'Intervention acceptée par le technicien'
            );

            if ($intervention->technicien) {
                $intervention->technicien->notify(new \App\Notifications\InterventionAccepteeNotification($intervention));
            }
        });
    }

    /**
     * Le technicien refuse l'intervention affectée et soumet une demande de réaffectation à l'Admin.
     * Le statut de l'intervention passe à 'En attente reafectation'.
     *
     * @throws Exception
     */
    public function refuserEtDemanderReaffectation(Intervention $intervention, string $motif, ?\App\Models\User $technicien = null): \App\Models\DemandeReaffectation
    {
        if (!$intervention->peutEtreRefusee()) {
            throw new Exception("Seule une intervention planifiée ou affectée peut faire l'objet d'un refus.");
        }

        if (empty(trim($motif))) {
            throw new Exception("Le motif de réaffectation est obligatoire en cas de refus d'une intervention.");
        }

        return DB::transaction(function () use ($intervention, $motif, $technicien) {
            $ancienStatut = $intervention->statut;
            $techUser = $technicien ?? Auth::user();

            // Création de la demande de réaffectation
            $demande = \App\Models\DemandeReaffectation::create([
                'intervention_id' => $intervention->id,
                'technicien_id'   => $techUser?->id ?? $intervention->technicien_id,
                'motif'           => $motif,
                'statut'          => 'En attente',
            ]);

            // Mise à jour du statut de l'intervention
            $intervention->update([
                'statut' => Intervention::STATUT_EN_ATTENTE_REAFFECTATION,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_EN_ATTENTE_REAFFECTATION,
                commentaire:  "Refus technicien - Demande de réaffectation transmise à l'Admin. Motif: {$motif}"
            );

            // Notifier les admins du refus
            \App\Models\User::role('Admin')->get()->each(function ($admin) use ($intervention, $motif) {
                $admin->notify(new \App\Notifications\InterventionRefuseeNotification($intervention, $motif));
            });

            return $demande;
        });
    }

    /**
     * L'Admin traite la demande de réaffectation (Acceptation ou Refus).
     *
     * @throws Exception
     */
    public function traiterDemandeReaffectation(
        \App\Models\DemandeReaffectation $demande,
        bool $accepter,
        ?int $nouveauTechnicienId = null,
        string $commentaireAdmin = '',
        ?\App\Models\User $admin = null
    ): void {
        DB::transaction(function () use ($demande, $accepter, $nouveauTechnicienId, $commentaireAdmin, $admin) {
            $intervention = $demande->intervention;
            $adminUser = $admin ?? Auth::user();

            if ($accepter) {
                // Admin ACCEPTE la réaffectation
                $demande->update([
                    'statut'                => 'Acceptee',
                    'admin_id'              => $adminUser?->id,
                    'nouveau_technicien_id' => $nouveauTechnicienId,
                    'commentaire_admin'     => $commentaireAdmin ?: 'Demande de réaffectation acceptée par l\'administrateur.',
                    'date_traitement'       => now(),
                ]);

                if ($nouveauTechnicienId) {
                    $intervention->update([
                        'technicien_id' => $nouveauTechnicienId,
                        'statut'        => Intervention::STATUT_AFFECTEE,
                    ]);
                    $comm = "Demande acceptée par Admin. Intervention réaffectée au technicien #{$nouveauTechnicienId}.";
                    // Notifier le nouveau technicien de sa réaffectation
                    $nouveauTech = \App\Models\User::find($nouveauTechnicienId);
                    if ($nouveauTech) {
                        $nouveauTech->notify(new \App\Notifications\InterventionReaffecteeNotification($intervention, $commentaireAdmin));
                    }
                } else {
                    $intervention->update([
                        'technicien_id' => null,
                        'statut'        => Intervention::STATUT_PLANIFIEE,
                    ]);
                    $comm = "Demande acceptée par Admin. Intervention remise en planification (non affectée).";
                }

                $this->enregistrerHistorique(
                    $intervention,
                    statut_avant: Intervention::STATUT_EN_ATTENTE_REAFFECTATION,
                    statut_apres: $intervention->statut,
                    commentaire:  $comm
                );

            } else {
                // Admin REFUSE la réaffectation -> le technicien actuel DOIT effectuer la mission
                $demande->update([
                    'statut'            => 'Refusee',
                    'admin_id'          => $adminUser?->id,
                    'commentaire_admin' => $commentaireAdmin ?: 'Demande de réaffectation refusée. La mission reste attribuée au technicien.',
                    'date_traitement'   => now(),
                ]);

                $intervention->update([
                    'statut' => Intervention::STATUT_ACCEPTEE,
                ]);

                $this->enregistrerHistorique(
                    $intervention,
                    statut_avant: Intervention::STATUT_EN_ATTENTE_REAFFECTATION,
                    statut_apres: Intervention::STATUT_ACCEPTEE,
                    commentaire:  "Demande de réaffectation refusée par Admin. Mission maintenue chez le technicien."
                );
            }
        });
    }

    /**
     * Reporte l'intervention à une date ultérieure avec motif obligatoire.
     *
     * @throws Exception
     */
    public function reporter(
        Intervention $intervention,
        string $motif,
        ?string $nouvelleDateDebut = null,
        ?string $nouvelleDateFin = null
    ): void {
        if (!$intervention->peutEtreReportee()) {
            throw new Exception("L'intervention ne peut pas être reportée dans son statut actuel ({$intervention->statut}).");
        }

        if (empty(trim($motif))) {
            throw new Exception("Un motif est obligatoire pour reporter une intervention.");
        }

        DB::transaction(function () use ($intervention, $motif, $nouvelleDateDebut, $nouvelleDateFin) {
            $ancienStatut = $intervention->statut;

            $dataToUpdate = [
                'statut' => Intervention::STATUT_REPORTEE,
                'observations' => trim(($intervention->observations ?? '') . "\n[REPORT] " . $motif),
            ];

            if ($nouvelleDateDebut) {
                $dataToUpdate['date_prevue_debut'] = $nouvelleDateDebut;
            }
            if ($nouvelleDateFin) {
                $dataToUpdate['date_prevue_fin'] = $nouvelleDateFin;
            }

            $intervention->update($dataToUpdate);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_REPORTEE,
                commentaire:  "Intervention reportée. Motif: {$motif}"
            );
        });
    }

    /**
     * Réouvre une intervention précédemment clôturée ou terminée.
     *
     * @throws Exception
     */
    public function rouvrir(Intervention $intervention, string $motif = ''): void
    {
        if (!$intervention->peutEtreRouverte()) {
            throw new Exception("Seule une intervention terminée ou validée peut être rouverte.");
        }

        DB::transaction(function () use ($intervention, $motif) {
            $ancienStatut = $intervention->statut;

            $intervention->update([
                'statut'            => Intervention::STATUT_ROUVERTE,
                'date_validation'   => null,
                'date_reelle_fin'   => null,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_ROUVERTE,
                commentaire:  "Réouverture exceptionnelle. Motif: " . ($motif ?: 'Complément de travaux nécessaire')
            );
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
     * Valide et clôture définitivement une intervention (Admin).
     * Vérifie obligatoirement la complétude des preuves.
     */
    public function validateIntervention(Intervention $intervention): void
    {
        $completude = $this->verifierCompletudePreuves($intervention);

        if (!$completude['pret_validation']) {
            $listeManquants = implode(', ', $completude['manquants']);
            throw new Exception("Validation impossible : Preuves obligatoires manquantes [{$listeManquants}].");
        }

        DB::transaction(function () use ($intervention) {
            $ancienStatut = $intervention->statut;
            $now = now();

            $statutCible = Intervention::STATUT_VALIDEE ?? 'Validee';

            $intervention->update([
                'statut'          => $statutCible,
                'valide_par'      => Auth::id(),
                'date_validation' => $now,
                'date_reelle_fin' => $intervention->date_reelle_fin ?? $now,
                'pourcentage_global' => 100,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: $statutCible,
                commentaire:  'Validée et clôturée avec succès par l\'administrateur (Toutes les preuves obligatoires sont vérifiées).'
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
     * Vérifie la complétude de l'ensemble des 7 preuves terrain (obligatoires & optionnelles).
     */
    public function verifierCompletudePreuves(Intervention $intervention): array
    {
        $intervention->loadMissing([
            'rapport.photos',
            'rapport.videos',
            'rapport.documents',
            'rapport.reponses.question',
            'gpsTrackingSessions.points',
            'trackingSessions',
            'chantier',
            'typeIntervention',
        ]);

        $rapport = $intervention->rapport;

        // 1. Rapport complet
        $hasRapport = !is_null($rapport);
        $rapportComplet = $hasRapport && (
            !empty($rapport->travaux_effectues) ||
            !empty($rapport->commentaire) ||
            !empty($rapport->observations)
        );

        // 2. Formulaire rempli
        $nbReponses = $rapport ? $rapport->reponses->count() : 0;
        $formulaireRempli = $nbReponses > 0 || in_array($intervention->statut, [
            Intervention::STATUT_FORM_REMPLI,
            Intervention::STATUT_TERMINEE,
            Intervention::STATUT_VALIDEE,
        ]);

        // 3. Photos (Avant / Après / Problème ou photos de formulaires)
        $nbPhotos = $rapport ? $rapport->photos->count() : 0;
        $nbPhotosForm = $rapport ? $rapport->reponses->filter(fn($r) => $r->question && in_array($r->question->type_reponse, ['Photo', 'Image']) && !empty($r->reponse_fichier))->count() : 0;
        $hasPhotos = ($nbPhotos + $nbPhotosForm) > 0;

        // 4. Signature Technicien
        $hasSigTech = $rapport && !empty($rapport->signature_technicien);

        // 5. Signature Client
        $hasSigClient = $rapport && !empty($rapport->signature_client);

        // 6. GPS
        $hasGpsRapport = $rapport && (!is_null($rapport->gps_latitude) && !is_null($rapport->gps_longitude));
        $hasGpsSessions = $intervention->gpsTrackingSessions->flatMap->points->isNotEmpty() || $intervention->trackingSessions->whereNotNull('latitude')->isNotEmpty();
        $hasGps = $hasGpsRapport || $hasGpsSessions;

        // 7. Documents requis
        $nbDocs = $rapport ? $rapport->documents->count() : 0;
        $nbDocsForm = $rapport ? $rapport->reponses->filter(fn($r) => $r->question && in_array($r->question->type_reponse, ['Document', 'Fichier']) && !empty($r->reponse_fichier))->count() : 0;
        $hasDocs = ($nbDocs + $nbDocsForm) > 0;

        // Contrôle des éléments obligatoires (Rapport + Formulaire + Signature Technicien)
        $manquants = [];
        if (!$rapportComplet) {
            $manquants[] = "Rapport complet (travaux et observations)";
        }
        if (!$formulaireRempli) {
            $manquants[] = "Formulaire terrain rempli";
        }
        if (!$hasSigTech) {
            $manquants[] = "Signature du technicien";
        }

        $pretValidation = empty($manquants);

        $checklist = [
            'rapport_complet' => [
                'libelle'     => 'Rapport complet (travaux & observations)',
                'valide'      => $rapportComplet,
                'obligatoire' => true,
                'detail'      => $rapportComplet ? 'Renseigné' : 'Incomplet',
            ],
            'formulaire' => [
                'libelle'     => 'Formulaire terrain',
                'valide'      => $formulaireRempli,
                'obligatoire' => true,
                'detail'      => $formulaireRempli ? "{$nbReponses} réponse(s)" : 'Non rempli',
            ],
            'signature_technicien' => [
                'libelle'     => 'Signature technicien',
                'valide'      => $hasSigTech,
                'obligatoire' => true,
                'detail'      => $hasSigTech ? 'Signé' : 'Manquante',
            ],
            'photos' => [
                'libelle'     => 'Photos terrain (Avant/Après/Problèmes)',
                'valide'      => $hasPhotos,
                'obligatoire' => false,
                'detail'      => "Total : " . ($nbPhotos + $nbPhotosForm) . " photo(s)",
            ],
            'signature_client' => [
                'libelle'     => 'Signature client',
                'valide'      => $hasSigClient,
                'obligatoire' => false,
                'detail'      => $hasSigClient ? 'Signé' : 'Non signée',
            ],
            'gps' => [
                'libelle'     => 'Coordonnées GPS',
                'valide'      => $hasGps,
                'obligatoire' => false,
                'detail'      => $hasGps ? ($rapport?->gps_adresse ?: 'Position enregistrée') : 'Non disponible',
            ],
            'documents' => [
                'libelle'     => 'Documents requis / justificatifs',
                'valide'      => $hasDocs,
                'obligatoire' => false,
                'detail'      => "Total : " . ($nbDocs + $nbDocsForm) . " document(s)",
            ],
        ];

        return [
            'pret_validation' => $pretValidation,
            'manquants'       => $manquants,
            'checklist'       => $checklist,
            'stats'           => [
                'validees'    => count(array_filter($checklist, fn($i) => $i['valide'])),
                'totales'     => count($checklist),
            ]
        ];
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
     * Annulation contrôlée avec motif obligatoire.
     * Conserve toujours l'historique (pas de suppression physique).
     *
     * @throws Exception
     */
    public function cancelOrDelete(Intervention $intervention, string $motif = ''): void
    {
        if (!$intervention->peutEtreAnnulee()) {
            throw new Exception("Cette intervention ne peut pas être annulée dans son statut actuel.");
        }

        if (empty(trim($motif))) {
            throw new Exception("Le motif d'annulation est obligatoire.");
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
                'motif_annulation'  => trim($motif),
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_ANNULEE,
                commentaire:  "Annulation d'intervention : {$motif}"
            );
        });
    }

    /**
     * Marque une intervention comme "Client absent" et permet de planifier un 2nd passage.
     */
    public function marquerClientAbsent(Intervention $intervention, string $motif, ?string $dateRevisite = null, ?int $technicienId = null): Intervention
    {
        return DB::transaction(function () use ($intervention, $motif, $dateRevisite, $technicienId) {
            $ancienStatut = $intervention->statut;

            TrackingSession::where('intervention_id', $intervention->id)
                ->whereNull('ended_at')
                ->update(['ended_at' => now()]);

            $this->arreterSuiviGps($intervention);

            $intervention->update([
                'statut'                => Intervention::STATUT_CLIENT_ABSENT,
                'resultat_intervention' => Intervention::STATUT_CLIENT_ABSENT,
                'motif_report'          => $motif,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_CLIENT_ABSENT,
                commentaire:  "Client absent sur le site. Motif / Détails: {$motif}"
            );

            // Si une date de revisite est spécifiée, créer automatiquement la 2ème visite
            if ($dateRevisite) {
                return $this->creerDeuxiemeVisite($intervention, [
                    'date_prevue_debut' => $dateRevisite,
                    'technicien_id'     => $technicienId ?? $intervention->technicien_id,
                    'motif'             => "Seconde visite suite à l'absence du client lors du premier passage (#{$intervention->code_intervention})",
                ]);
            }

            return $intervention;
        });
    }

    /**
     * Marque une intervention comme "Matériel manquant".
     */
    public function marquerMaterielManquant(Intervention $intervention, string $motif, ?string $dateRevisite = null, ?int $technicienId = null): Intervention
    {
        return DB::transaction(function () use ($intervention, $motif, $dateRevisite, $technicienId) {
            $ancienStatut = $intervention->statut;

            TrackingSession::where('intervention_id', $intervention->id)
                ->whereNull('ended_at')
                ->update(['ended_at' => now()]);

            $this->arreterSuiviGps($intervention);

            $intervention->update([
                'statut'                => Intervention::STATUT_MATERIEL_MANQUANT,
                'resultat_intervention' => Intervention::STATUT_MATERIEL_MANQUANT,
                'motif_report'          => $motif,
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_MATERIEL_MANQUANT,
                commentaire:  "Matériel manquant sur le site. Matériel nécessaire: {$motif}"
            );

            if ($dateRevisite) {
                return $this->creerDeuxiemeVisite($intervention, [
                    'date_prevue_debut' => $dateRevisite,
                    'technicien_id'     => $technicienId ?? $intervention->technicien_id,
                    'motif'             => "Seconde visite suite au matériel manquant lors de la première intervention (#{$intervention->code_intervention})",
                ]);
            }

            return $intervention;
        });
    }

    /**
     * Marque une intervention comme "Partiellement réalisée".
     */
    public function marquerPartiellementRealisee(Intervention $intervention, string $observations, ?string $dateRevisite = null, ?int $technicienId = null): Intervention
    {
        return DB::transaction(function () use ($intervention, $observations, $dateRevisite, $technicienId) {
            $ancienStatut = $intervention->statut;

            TrackingSession::where('intervention_id', $intervention->id)
                ->whereNull('ended_at')
                ->update(['ended_at' => now()]);

            $this->arreterSuiviGps($intervention);

            $intervention->update([
                'statut'                => Intervention::STATUT_PARTIELLEMENT_REALISEE,
                'resultat_intervention' => Intervention::STATUT_PARTIELLEMENT_REALISEE,
                'observations'          => trim(($intervention->observations ?? '') . "\n[PARTIEL] " . $observations),
            ]);

            $this->enregistrerHistorique(
                $intervention,
                statut_avant: $ancienStatut,
                statut_apres: Intervention::STATUT_PARTIELLEMENT_REALISEE,
                commentaire:  "Intervention partiellement réalisée. Travaux restants: {$observations}"
            );

            if ($dateRevisite) {
                return $this->creerDeuxiemeVisite($intervention, [
                    'date_prevue_debut' => $dateRevisite,
                    'technicien_id'     => $technicienId ?? $intervention->technicien_id,
                    'motif'             => "Deuxième visite pour finaliser les travaux partiellement réalisés (#{$intervention->code_intervention})",
                ]);
            }

            return $intervention;
        });
    }

    /**
     * Crée une deuxième visite (revisite) liée à l'intervention initiale.
     */
    public function creerDeuxiemeVisite(Intervention $interventionOriginale, array $dataRevisite): Intervention
    {
        return DB::transaction(function () use ($interventionOriginale, $dataRevisite) {
            $dateDebut = $dataRevisite['date_prevue_debut'] ?? now()->addDay()->format('Y-m-d H:i:s');
            $technicienId = $dataRevisite['technicien_id'] ?? $interventionOriginale->technicien_id;
            $motif = $dataRevisite['motif'] ?? 'Deuxième visite programmée';

            $codeRevisite = 'INT-' . date('Ymd') . '-REV' . rand(100, 999);

            $nouvelleIntervention = Intervention::create([
                'code_intervention'       => $codeRevisite,
                'intervention_parente_id' => $interventionOriginale->id,
                'chantier_id'             => $interventionOriginale->chantier_id,
                'emplacement_id'          => $interventionOriginale->emplacement_id,
                'technicien_id'           => $technicienId,
                'type_intervention_id'    => $interventionOriginale->type_intervention_id,
                'priorite'                => $interventionOriginale->priorite,
                'mode_suivi'              => $interventionOriginale->mode_suivi ?? Intervention::MODE_MANUEL,
                'cree_par'                => Auth::id() ?? $interventionOriginale->cree_par,
                'statut'                  => $technicienId ? Intervention::STATUT_AFFECTEE : Intervention::STATUT_PLANIFIEE,
                'date_prevue_debut'       => $dateDebut,
                'date_prevue_fin'         => \Carbon\Carbon::parse($dateDebut)->addHours(2),
                'description'             => "Revisite / 2ème passage pour l'intervention #{$interventionOriginale->code_intervention}. Motif: {$motif}",
            ]);

            // Mettre à jour le statut de l'intervention initiale
            $interventionOriginale->update([
                'statut' => Intervention::STATUT_DEUXIEME_VISITE,
            ]);

            // Historique sur l'ancienne intervention
            $this->enregistrerHistorique(
                $interventionOriginale,
                statut_avant: $interventionOriginale->statut,
                statut_apres: Intervention::STATUT_DEUXIEME_VISITE,
                commentaire:  "Deuxième visite programmée (#{$nouvelleIntervention->code_intervention} le {$dateDebut})."
            );

            // Historique sur la nouvelle intervention
            $this->enregistrerHistorique(
                $nouvelleIntervention,
                statut_avant: null,
                statut_apres: $nouvelleIntervention->statut,
                commentaire:  "Deuxième visite créée suite au passage #{$interventionOriginale->code_intervention}."
            );

            return $nouvelleIntervention;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Calculs & Utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Recalcule automatiquement le pourcentage global d'avancement de l'intervention
     * à partir de l'avancement réel des tâches (0% à 100% par tâche/sous-tâche).
     * Met à jour la colonne `pourcentage_global` en BDD.
     */
    public function recalculerPourcentageGlobal(Intervention $intervention): int
    {
        $taches = $intervention->taches()->get();

        if ($taches->isEmpty()) {
            return $intervention->pourcentage_global ?? 0;
        }

        // Somme des pourcentages de chaque tâche / sous-tâche assignée
        $totalPourcentage = $taches->sum('pourcentage');
        $count = $taches->count();

        $pourcentageGlobal = (int) round($totalPourcentage / $count);
        $pourcentageGlobal = min(100, max(0, $pourcentageGlobal));

        $intervention->update([
            'pourcentage_global' => $pourcentageGlobal
        ]);

        return $pourcentageGlobal;
    }

    /**
     * Alias de rétrocompatibilité.
     */
    public function calculerPourcentage(Intervention $intervention): int
    {
        return $this->recalculerPourcentageGlobal($intervention);
    }

    /**
     * Met à jour la progression d'une tâche d'intervention (statut, %, dates, commentaire)
     * et recalcule automatiquement le pourcentage global de l'intervention.
     */
    public function updateTacheProgress(
        Intervention $intervention,
        int $tachePivotId,
        int $pourcentage,
        ?string $statut = null,
        ?string $commentaire = null
    ): InterventionTache {
        $tachePivot = InterventionTache::where('intervention_id', $intervention->id)
            ->findOrFail($tachePivotId);

        $pourcentage = min(100, max(0, $pourcentage));

        if (is_null($statut)) {
            if ($pourcentage == 0) {
                $statut = 'Non commencee';
            } elseif ($pourcentage == 100) {
                $statut = 'Terminee';
            } else {
                $statut = 'En cours';
            }
        }

        $updates = [
            'pourcentage' => $pourcentage,
            'statut'      => $statut,
        ];

        if ($statut === 'En cours' && is_null($tachePivot->date_debut)) {
            $updates['date_debut'] = now();
        }

        if ($statut === 'Terminee') {
            if (is_null($tachePivot->date_debut)) {
                $updates['date_debut'] = now();
            }
            $updates['date_fin'] = now();
            $updates['pourcentage'] = 100;
        }

        if (!is_null($commentaire)) {
            $updates['commentaire'] = $commentaire;
        }

        $tachePivot->update($updates);

        // Recalculer automatiquement le pourcentage global de l'intervention
        $this->recalculerPourcentageGlobal($intervention);

        return $tachePivot;
    }

    /**
     * Démarre une tâche d'intervention (statut = En cours).
     */
    public function demarrerTache(Intervention $intervention, int $tachePivotId, ?string $commentaire = null): InterventionTache
    {
        $tachePivot = InterventionTache::where('intervention_id', $intervention->id)->findOrFail($tachePivotId);
        $progressionActuelle = $tachePivot->pourcentage > 0 ? $tachePivot->pourcentage : 15;

        return $this->updateTacheProgress($intervention, $tachePivotId, $progressionActuelle, 'En cours', $commentaire);
    }

    /**
     * Termine une tâche d'intervention (statut = Terminee, pourcentage = 100%).
     */
    public function terminerTache(Intervention $intervention, int $tachePivotId, ?string $commentaire = null): InterventionTache
    {
        return $this->updateTacheProgress($intervention, $tachePivotId, 100, 'Terminee', $commentaire);
    }

    /*
    |--------------------------------------------------------------------------
    | Historique
    |--------------------------------------------------------------------------
    */

    /**
     * Log d'historique de statut d'intervention.
     */
    public function enregistrerHistorique(
        Intervention $intervention,
        ?string $statut_avant,
        string $statut_apres,
        string $commentaire = '',
        ?string $source = null
    ): void {
        if (is_null($source)) {
            if (request()->wantsJson() || request()->is('api/*')) {
                $source = 'API';
            } elseif (request()->is('mobile/*')) {
                $source = 'Mobile';
            } else {
                $source = 'Web';
            }
        }

        InterventionHistorique::create([
            'intervention_id' => $intervention->id,
            'user_id'         => Auth::id(),
            'statut_avant'    => $statut_avant ?? '—',
            'statut_apres'    => $statut_apres,
            'commentaire'     => $commentaire,
            'source'          => $source,
        ]);
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
