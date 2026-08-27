<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Devis;
use App\Models\DevisLigne;
use App\Models\Emplacement;
use App\Models\Intervention;
use App\Models\Prospect;
use App\Models\Rapport;
use App\Models\TypeIntervention;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Jeu de données de démonstration pour Prospect, Devis, Intervention et Rapport
     * (au-delà du strict minimum déjà couvert par ClientSeeder pour le client "ABC").
     */
    public function run(): void
    {
        $commercial = User::where('email', 'commercial@fieldflow.test')->first()
            ?? User::role('Commercial')->first();

        // 1. Prospects
        $prospect1 = Prospect::updateOrCreate(
            ['nom_entreprise' => 'Groupe Atlas Industrie'],
            [
                'nom_contact' => 'Nabil Berrada',
                'email' => 'n.berrada@atlas-industrie.ma',
                'telephone' => '0661010101',
                'adresse' => 'Zone Industrielle Aïn Sebaâ, Casablanca',
                'statut' => 'Qualifié',
                'observations' => 'Intéressé par une installation de vidéosurveillance complète.',
                'commercial_id' => $commercial?->id,
            ]
        );

        $prospect2 = Prospect::updateOrCreate(
            ['nom_entreprise' => 'Riad Yasmine'],
            [
                'nom_contact' => 'Salma El Idrissi',
                'email' => 'contact@riad-yasmine.ma',
                'telephone' => '0662020202',
                'adresse' => 'Medina, Marrakech',
                'statut' => 'Négociation',
                'observations' => 'Devis envoyé, en attente de retour sur le budget.',
                'commercial_id' => $commercial?->id,
            ]
        );

        Prospect::updateOrCreate(
            ['nom_entreprise' => 'Pharmacie Centrale Fès'],
            [
                'nom_contact' => 'Younes Tazi',
                'email' => 'y.tazi@pharmaciecentrale.ma',
                'telephone' => '0663030303',
                'adresse' => 'Avenue Hassan II, Fès',
                'statut' => 'Nouveau',
                'observations' => 'Premier contact pris lors du salon professionnel.',
                'commercial_id' => $commercial?->id,
            ]
        );

        // 2. Devis
        $client1 = Client::where('code_client', 'CL001')->first();

        $devisProspect = Devis::updateOrCreate(
            ['reference' => 'DEV-2026-001'],
            [
                'prospect_id' => $prospect1->id,
                'commercial_id' => $commercial?->id,
                'statut' => 'Envoyé',
                'date_emission' => Carbon::now()->subDays(4),
                'date_expiration' => Carbon::now()->addDays(26),
                'taux_tva' => 20.00,
                'montant_ht' => 0,
                'montant_tva' => 0,
                'montant_ttc' => 0,
                'observations' => 'Installation vidéosurveillance 12 caméras + enregistreur.',
            ]
        );

        $devisProspect->lignes()->delete();
        DevisLigne::create([
            'devis_id' => $devisProspect->id,
            'designation' => 'Caméra IP extérieure 4MP',
            'description' => 'Vision nocturne, résistante intempéries',
            'quantite' => 12,
            'prix_unitaire' => 850.00,
            'montant_ht' => 10200.00,
        ]);
        DevisLigne::create([
            'devis_id' => $devisProspect->id,
            'designation' => 'Enregistreur NVR 16 canaux',
            'description' => 'Avec disque dur 4 To',
            'quantite' => 1,
            'prix_unitaire' => 3200.00,
            'montant_ht' => 3200.00,
        ]);
        $totalHt = $devisProspect->lignes()->sum('montant_ht');
        $devisProspect->update([
            'montant_ht' => $totalHt,
            'montant_tva' => round($totalHt * 0.20, 2),
            'montant_ttc' => round($totalHt * 1.20, 2),
        ]);

        if ($client1) {
            $devisClient = Devis::updateOrCreate(
                ['reference' => 'DEV-2026-002'],
                [
                    'client_id' => $client1->id,
                    'commercial_id' => $commercial?->id,
                    'statut' => 'Accepté',
                    'date_emission' => Carbon::now()->subDays(20),
                    'date_expiration' => Carbon::now()->subDays(-10),
                    'taux_tva' => 20.00,
                    'montant_ht' => 0,
                    'montant_tva' => 0,
                    'montant_ttc' => 0,
                    'observations' => 'Extension du réseau Wi-Fi industriel — entrepôt Mohammedia.',
                ]
            );

            $devisClient->lignes()->delete();
            DevisLigne::create([
                'devis_id' => $devisClient->id,
                'designation' => 'Point d\'accès Wi-Fi industriel',
                'quantite' => 6,
                'prix_unitaire' => 1450.00,
                'montant_ht' => 8700.00,
            ]);
            $totalHt = $devisClient->lignes()->sum('montant_ht');
            $devisClient->update([
                'montant_ht' => $totalHt,
                'montant_tva' => round($totalHt * 0.20, 2),
                'montant_ttc' => round($totalHt * 1.20, 2),
            ]);
        }

        // 3. Intervention + Rapport pour le second client (Fatima Zahra), non couvert par ClientSeeder
        $client2 = Client::where('code_client', 'CL002')->first();
        $technicien = User::role('technicien')->first() ?? User::where('email', 'tech@intervention.ma')->first();
        $typeInterv = TypeIntervention::first();

        if ($client2 && $technicien && $typeInterv) {
            $chantier3 = Chantier::updateOrCreate(
                ['code_chantier' => 'CHT-FZ-01'],
                [
                    'client_id' => $client2->id,
                    'nom' => 'Résidence Fatima Zahra',
                    'type_local' => 'Appartement',
                    'adresse' => '15 Avenue Hassan II',
                    'ville' => 'Rabat',
                    'latitude' => 34.020882,
                    'longitude' => -6.841650,
                    'responsable' => 'Fatima Zahra',
                    'telephone_responsable' => '0622222222',
                    'email_responsable' => 'fatima@test.com',
                    'description' => 'Installation domotique et alarme.',
                    'is_active' => true,
                ]
            );

            $emplacement3 = Emplacement::firstOrCreate(
                ['chantier_id' => $chantier3->id, 'nom' => 'Appartement principal'],
                ['description' => 'Salon et chambres', 'qr_code' => 'EMP-FZ-01', 'is_active' => true]
            );

            $intervFZ = Intervention::updateOrCreate(
                ['code_intervention' => 'INT-FZ-001'],
                [
                    'chantier_id' => $chantier3->id,
                    'emplacement_id' => $emplacement3->id,
                    'technicien_id' => $technicien->id,
                    'type_intervention_id' => $typeInterv->id,
                    'mode_suivi' => 'Manuel',
                    'priorite' => Intervention::PRIORITE_NORMALE,
                    'statut' => Intervention::STATUT_TERMINEE,
                    'date_prevue_debut' => Carbon::now()->subDays(2)->setHour(9),
                    'date_prevue_fin' => Carbon::now()->subDays(2)->setHour(11),
                    'date_reelle_debut' => Carbon::now()->subDays(2)->setHour(9)->addMinutes(5),
                    'date_reelle_fin' => Carbon::now()->subDays(2)->setHour(10)->addMinutes(50),
                    'duree_prevue' => 120,
                    'duree_reelle' => 105,
                    'pourcentage_global' => 100,
                    'description' => 'Installation d\'une alarme et de deux détecteurs de mouvement.',
                    'observations' => 'Client satisfait, système testé et fonctionnel.',
                ]
            );

            Rapport::updateOrCreate(
                ['intervention_id' => $intervFZ->id],
                [
                    'date_debut' => $intervFZ->date_reelle_debut,
                    'date_fin' => $intervFZ->date_reelle_fin,
                    'travaux_effectues' => 'Pose de la centrale d\'alarme et des détecteurs, tests de fonctionnement.',
                    'observations' => 'RAS.',
                    'statut_equipement' => 'Conforme',
                    'commentaire' => 'Intervention réalisée sans incident.',
                    'duree_reelle' => 105,
                    'pourcentage_global' => 100,
                    'statut_validation' => 'Validé',
                    'signature_client' => 'Fatima Zahra',
                    'signature_technicien' => $technicien->name,
                ]
            );
        }
    }
}
