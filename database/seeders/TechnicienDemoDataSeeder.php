<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Emplacement;
use App\Models\Intervention;
use App\Models\Rapport;
use App\Models\TypeIntervention;
use App\Models\User;
use App\Notifications\ClientPortalNotification;
use App\Services\ReferenceGeneratorService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Jeu de données de test pour l'app Flutter Technicien (compte "Amine" —
 * yassine.tech@test.com), couvrant tous les statuts du workflow interventions
 * (Planifiée → Acceptée → En cours → Terminée) pour valider visuellement
 * l'écran d'accueil (carte "Prochaine mission", compteurs KPI, "Missions
 * récentes", badge de notification).
 *
 * Volontairement une classe séparée de TechnicienSeeder (qui ne fait que
 * créer les 2 comptes techniciens et tourne tôt dans DatabaseSeeder) plutôt
 * que d'y ajouter cette logique : elle dépend de Chantier/Emplacement/
 * TypeIntervention, seedés plus tard — d'où son enregistrement en toute fin
 * de DatabaseSeeder, après ClientSeeder/TechnicienSeeder/ChantierSeeder/
 * EmplacementSeeder/DemoDataSeeder.
 *
 * Idempotent via un rapprochement (technicien, chantier, date_prevue_debut) —
 * unique pour chacune des 8 interventions de démo ci-dessous — plutôt que sur
 * `code_intervention` : la référence de chaque intervention est désormais
 * générée par ReferenceGeneratorService::generateInterventionReference()
 * (format INT-[INITIALES_CLIENT]-[SEQ], cohérent avec le reste de l'app) au
 * lieu d'un préfixe "INT-DEMO-AM-xxx" codé en dur, qui n'existe nulle part
 * ailleurs dans le générateur de références centralisé.
 */
class TechnicienDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $technicien = User::where('email', 'yassine.tech@test.com')->first();
        if (! $technicien) {
            return;
        }

        $chantiers = Chantier::whereIn('code_chantier', ['CHT001', 'CHT002'])->get()->keyBy('code_chantier');
        $cht1 = $chantiers->get('CHT001');
        $cht2 = $chantiers->get('CHT002');
        if (! $cht1 || ! $cht2) {
            return;
        }

        $emplacement1 = Emplacement::where('chantier_id', $cht1->id)->first();
        $emplacement2 = Emplacement::where('chantier_id', $cht2->id)->first();

        $typeCamera = TypeIntervention::where('nom', 'Installation Caméra')->first() ?? TypeIntervention::first();
        $typeMaintenance = TypeIntervention::where('nom', 'Maintenance')->first() ?? TypeIntervention::first();
        $typeWifi = TypeIntervention::where('nom', 'Installation WiFi')->first() ?? TypeIntervention::first();
        $typeReparation = TypeIntervention::where('nom', 'Réparation')->first() ?? TypeIntervention::first();

        $now = Carbon::now();

        // --- 2 x Planifiée (futures proches, priorités différentes) ---
        $this->creerIntervention($technicien, $cht1, $emplacement1, $typeCamera, [
            'priorite' => Intervention::PRIORITE_URGENTE,
            'statut' => Intervention::STATUT_PLANIFIEE,
            'date_prevue_debut' => $now->copy()->addDay()->setTime(9, 0),
            'date_prevue_fin' => $now->copy()->addDay()->setTime(11, 0),
            'duree_prevue' => 120,
            'description' => 'Installation de 4 caméras extérieures sur le site.',
        ]);

        $this->creerIntervention($technicien, $cht2, $emplacement2, $typeMaintenance, [
            'priorite' => Intervention::PRIORITE_NORMALE,
            'statut' => Intervention::STATUT_PLANIFIEE,
            'date_prevue_debut' => $now->copy()->addDays(2)->setTime(14, 0),
            'date_prevue_fin' => $now->copy()->addDays(2)->setTime(15, 30),
            'duree_prevue' => 90,
            'description' => 'Maintenance préventive annuelle des équipements bureau.',
        ]);

        // --- 1 x Acceptée, datée le plus proche : c'est elle qui doit apparaître
        // comme "Prochaine mission" avec le bouton "Démarrer" actionnable
        // (Intervention::peutEtreDemarree() n'autorise le démarrage que depuis
        // Acceptée/Suspendue/Reportee/Rejetee/Rouverte, pas depuis Planifiée). ---
        $this->creerIntervention($technicien, $cht1, $emplacement1, $typeWifi, [
            'priorite' => Intervention::PRIORITE_HAUTE,
            'statut' => Intervention::STATUT_ACCEPTEE,
            'date_prevue_debut' => $now->copy()->addHours(3),
            'date_prevue_fin' => $now->copy()->addHours(4),
            'duree_prevue' => 60,
            'description' => 'Extension du réseau Wi-Fi vers le local technique.',
        ]);

        // --- 2 x En cours ---
        $this->creerIntervention($technicien, $cht2, $emplacement2, $typeReparation, [
            'priorite' => Intervention::PRIORITE_URGENTE,
            'statut' => Intervention::STATUT_EN_COURS,
            'date_prevue_debut' => $now->copy()->subHours(1),
            'date_prevue_fin' => $now->copy()->addHours(1),
            'date_reelle_debut' => $now->copy()->subMinutes(50),
            'duree_prevue' => 120,
            'pourcentage_global' => 40,
            'description' => 'Réparation panne réseau signalée ce matin.',
        ]);

        $this->creerIntervention($technicien, $cht1, $emplacement1, $typeCamera, [
            'priorite' => Intervention::PRIORITE_NORMALE,
            'statut' => Intervention::STATUT_EN_COURS,
            'date_prevue_debut' => $now->copy()->subMinutes(30),
            'date_prevue_fin' => $now->copy()->addHours(2),
            'date_reelle_debut' => $now->copy()->subMinutes(20),
            'duree_prevue' => 150,
            'pourcentage_global' => 15,
            'description' => 'Remplacement d\'une caméra défectueuse.',
        ]);

        // --- 3 x Terminée (2 avec rapport rempli) ---
        $terminee1 = $this->creerIntervention($technicien, $cht1, $emplacement1, $typeMaintenance, [
            'priorite' => Intervention::PRIORITE_NORMALE,
            'statut' => Intervention::STATUT_TERMINEE,
            'date_prevue_debut' => $now->copy()->subDays(1)->setTime(9, 0),
            'date_prevue_fin' => $now->copy()->subDays(1)->setTime(10, 30),
            'date_reelle_debut' => $now->copy()->subDays(1)->setTime(9, 5),
            'date_reelle_fin' => $now->copy()->subDays(1)->setTime(10, 20),
            'duree_prevue' => 90,
            'duree_reelle' => 75,
            'pourcentage_global' => 100,
            'description' => 'Maintenance préventive trimestrielle.',
            'observations' => 'RAS, tout est conforme.',
        ]);

        $terminee2 = $this->creerIntervention($technicien, $cht2, $emplacement2, $typeWifi, [
            'priorite' => Intervention::PRIORITE_HAUTE,
            'statut' => Intervention::STATUT_TERMINEE,
            'date_prevue_debut' => $now->copy()->subDays(3)->setTime(11, 0),
            'date_prevue_fin' => $now->copy()->subDays(3)->setTime(12, 30),
            'date_reelle_debut' => $now->copy()->subDays(3)->setTime(11, 10),
            'date_reelle_fin' => $now->copy()->subDays(3)->setTime(12, 45),
            'duree_prevue' => 90,
            'duree_reelle' => 95,
            'pourcentage_global' => 100,
            'description' => 'Installation Wi-Fi complémentaire, étage 2.',
            'observations' => 'Client satisfait, débit vérifié.',
        ]);

        $this->creerIntervention($technicien, $cht1, $emplacement1, $typeReparation, [
            'priorite' => Intervention::PRIORITE_FAIBLE,
            'statut' => Intervention::STATUT_TERMINEE,
            'date_prevue_debut' => $now->copy()->subDays(6)->setTime(15, 0),
            'date_prevue_fin' => $now->copy()->subDays(6)->setTime(16, 0),
            'date_reelle_debut' => $now->copy()->subDays(6)->setTime(15, 5),
            'date_reelle_fin' => $now->copy()->subDays(6)->setTime(15, 55),
            'duree_prevue' => 60,
            'duree_reelle' => 50,
            'pourcentage_global' => 100,
            'description' => 'Remplacement d\'un boîtier réseau hors service.',
            'observations' => 'Intervention rapide, sans incident.',
        ]);

        Rapport::updateOrCreate(
            ['intervention_id' => $terminee1->id],
            [
                'date_debut' => $terminee1->date_reelle_debut,
                'date_fin' => $terminee1->date_reelle_fin,
                'travaux_effectues' => 'Vérification et nettoyage des équipements, tests de fonctionnement.',
                'observations' => 'RAS.',
                'statut_equipement' => 'Conforme',
                'commentaire' => 'Intervention réalisée sans incident.',
                'duree_reelle' => $terminee1->duree_reelle,
                'pourcentage_global' => 100,
                'statut_validation' => 'Validé',
                'signature_client' => $cht1->responsable,
                'signature_technicien' => $technicien->name,
            ]
        );

        Rapport::updateOrCreate(
            ['intervention_id' => $terminee2->id],
            [
                'date_debut' => $terminee2->date_reelle_debut,
                'date_fin' => $terminee2->date_reelle_fin,
                'travaux_effectues' => 'Installation de 2 points d\'accès Wi-Fi supplémentaires.',
                'observations' => 'Débit testé à 300 Mbps, conforme aux attentes.',
                'statut_equipement' => 'Conforme',
                'commentaire' => 'Client présent, a validé sur place.',
                'duree_reelle' => $terminee2->duree_reelle,
                'pourcentage_global' => 100,
                'statut_validation' => 'Validé',
                'signature_client' => $cht2->responsable,
                'signature_technicien' => $technicien->name,
            ]
        );

        // --- Notifications (3 min., dont non lues pour le badge cloche) ---
        if (! $technicien->notifications()->exists()) {
            $this->creerNotification(
                $technicien,
                'intervention_planifiee',
                'Nouvelle intervention planifiée',
                "L'intervention {$terminee1->code_intervention} vous a été planifiée.",
                $now->copy()->subDays(4),
                lu: true,
            );

            $this->creerNotification(
                $technicien,
                'intervention_urgente',
                'Intervention urgente à traiter',
                'Une intervention de priorité Urgente vous attend sur le chantier Installation système sécurité.',
                $now->copy()->subHours(2),
                lu: false,
            );

            $this->creerNotification(
                $technicien,
                'rapport_a_faire',
                'Rapport en attente',
                "Pensez à compléter le rapport de l'intervention {$terminee2->code_intervention}.",
                $now->copy()->subMinutes(45),
                lu: false,
            );

            $this->creerNotification(
                $technicien,
                'reaffectation',
                'Mission réaffectée',
                'Une mission vous a été réaffectée suite à une indisponibilité.',
                $now->copy()->subMinutes(10),
                lu: false,
            );
        }
    }

    private function creerIntervention(
        User $technicien,
        Chantier $chantier,
        ?Emplacement $emplacement,
        ?TypeIntervention $type,
        array $attributes,
    ): Intervention {
        $existante = Intervention::where('technicien_id', $technicien->id)
            ->where('chantier_id', $chantier->id)
            ->where('date_prevue_debut', $attributes['date_prevue_debut'])
            ->first();

        if ($existante) {
            $existante->fill(array_merge([
                'emplacement_id' => $emplacement?->id,
                'type_intervention_id' => $type?->id,
                'mode_suivi' => Intervention::MODE_MANUEL,
            ], $attributes));
            $existante->save();

            return $existante;
        }

        $reference = app(ReferenceGeneratorService::class)->generateInterventionReference($chantier->client);

        return Intervention::create(
            array_merge([
                'code_intervention' => $reference,
                'chantier_id' => $chantier->id,
                'emplacement_id' => $emplacement?->id,
                'technicien_id' => $technicien->id,
                'type_intervention_id' => $type?->id,
                'mode_suivi' => Intervention::MODE_MANUEL,
            ], $attributes)
        );
    }

    private function creerNotification(User $user, string $type, string $titre, string $message, Carbon $date, bool $lu): void
    {
        $user->notify(new ClientPortalNotification($type, $titre, $message, 'technicien.dashboard', []));

        $notification = $user->notifications()->latest()->first();
        $notification->forceFill([
            'created_at' => $date,
            'updated_at' => $date,
            'read_at' => $lu ? $date->copy()->addMinutes(5) : null,
        ])->save();
    }
}
