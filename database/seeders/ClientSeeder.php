<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use App\Models\Chantier;
use App\Models\Intervention;
use App\Models\TypeIntervention;
use App\Models\Rapport;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // 1. S'assurer que le rôle Spatie 'Client' existe
        $clientRole = Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'web'
        ]);

        // Commercial de démonstration : ces deux clients lui sont rattachés (cohérent avec
        // les prospects/devis générés à son nom dans DemoDataSeeder).
        $commercialDemo = User::where('email', 'commercial@intervention.ma')->first();

        // 2. Création des fiches Client (Table clients)
        $client1 = Client::updateOrCreate(
            ['code_client' => 'CL001'],
            [
                'type_client' => 'Entreprise',
                'nom' => 'Société ABC',
                'nom_contact' => 'Mohamed Ali',
                'telephone' => '0611111111',
                'email' => 'client@abc.com',
                'adresse_facturation' => '120 Boulevard Zerktouni',
                'ville' => 'Casablanca',
                'pays' => 'Maroc',
                'observations' => 'Client entreprise VIP',
                'is_active' => true,
                'commercial_id' => $commercialDemo?->id,
            ]
        );

        $client2 = Client::updateOrCreate(
            ['code_client' => 'CL002'],
            [
                'type_client' => 'Particulier',
                'nom' => 'Fatima Zahra',
                'nom_contact' => 'Fatima Zahra',
                'telephone' => '0622222222',
                'email' => 'fatima@test.com',
                'adresse_facturation' => '15 Avenue Hassan II',
                'ville' => 'Rabat',
                'pays' => 'Maroc',
                'observations' => 'Client particulier',
                'is_active' => true,
                'commercial_id' => $commercialDemo?->id,
            ]
        );

        // 3. Création des utilisateurs d'accès (Table users)
        $userClient1 = User::updateOrCreate(
            ['email' => 'client@abc.com'],
            [
                // Le nom de la société ("Société ABC") vit déjà proprement et séparément
                // dans clients.nom — il ne doit jamais être concaténé ici dans le nom
                // de la personne (bug corrigé : "Mohamed Ali (ABC)" affiché tel quel
                // sur Mon Profil et le Dashboard). "name" ne porte que le nom de famille
                // (bug lié corrigé : le prénom "Mohamed" était dupliqué dans "name").
                'name' => 'Ali',
                'prenom' => 'Mohamed',
                'telephone' => '0611111111',
                'adresse' => '120 Boulevard Zerktouni, Casablanca',
                'password' => Hash::make('password'),
                'is_active' => true,
                'client_id' => $client1->id,
            ]
        );
        $userClient1->syncRoles([$clientRole]);

        $userClient2 = User::updateOrCreate(
            ['email' => 'fatima@test.com'],
            [
                'name' => 'Zahra',
                'prenom' => 'Fatima',
                'telephone' => '0622222222',
                'adresse' => '15 Avenue Hassan II, Rabat',
                'password' => Hash::make('password'),
                'is_active' => true,
                'client_id' => $client2->id,
            ]
        );
        $userClient2->syncRoles([$clientRole]);

        // Technicien pour assignation
        $technicienRole = Role::firstOrCreate(['name' => 'technicien', 'guard_name' => 'web']);
        $technicien = User::role('technicien')->first() ?? User::firstOrCreate(
            ['email' => 'tech@intervention.ma'],
            [
                'name' => 'Technicien Test',
                'prenom' => 'Karim',
                'telephone' => '0699999999',
                'adresse' => 'Casablanca, Maroc',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        // Bug corrigé : ce compte pouvait être créé sans rôle Spatie assigné (comptait dans
        // "Total Comptes" sans apparaître dans "Répartition des Rôles Spatie" -> divergence KPI).
        if ($technicien->roles->isEmpty()) {
            $technicien->assignRole($technicienRole);
        }

        // Type d'intervention
        $typeInterv = TypeIntervention::first() ?? TypeIntervention::create([
            'nom' => 'Maintenance Réseau & Sécurité',
            'description' => 'Intervention globale',
            'is_active' => true,
        ]);

        // 4. Chantiers pour Société ABC
        $chantier1 = Chantier::updateOrCreate(
            ['code_chantier' => 'CHT-ABC-01'],
            [
                'client_id' => $client1->id,
                'nom' => 'Siège Social Casablanca',
                'type_local' => 'Bureau',
                'adresse' => '120 Boulevard Zerktouni',
                'ville' => 'Casablanca',
                'latitude' => 33.589886,
                'longitude' => -7.632551,
                'responsable' => 'Mohamed Ali',
                'telephone_responsable' => '0611111111',
                'email_responsable' => 'client@abc.com',
                'description' => 'Installation et surveillance caméras & fibre optique.',
                'is_active' => true,
            ]
        );

        $chantier2 = Chantier::updateOrCreate(
            ['code_chantier' => 'CHT-ABC-02'],
            [
                'client_id' => $client1->id,
                'nom' => 'Entrepôt Logistique Mohammedia',
                'type_local' => 'Usine',
                'adresse' => 'Zone Industrielle',
                'ville' => 'Mohammedia',
                'latitude' => 33.686072,
                'longitude' => -7.382977,
                'responsable' => 'Youssef Mansouri',
                'telephone_responsable' => '0655555555',
                'email_responsable' => 'youssef@abc.com',
                'description' => 'Maintenance annuelle du réseau Wi-Fi industriel.',
                'is_active' => true,
            ]
        );

        // Emplacement
        $emplacement1 = \App\Models\Emplacement::firstOrCreate(
            ['chantier_id' => $chantier1->id, 'nom' => 'Bâtiment Principal - Etage 1'],
            ['description' => 'Zone de bureaux et serveur', 'qr_code' => 'EMP-ABC-01', 'is_active' => true]
        );
        $emplacement2 = \App\Models\Emplacement::firstOrCreate(
            ['chantier_id' => $chantier2->id, 'nom' => 'Hangar A'],
            ['description' => 'Zone de stockage et logistique', 'qr_code' => 'EMP-ABC-02', 'is_active' => true]
        );

        // 5. Interventions pour Société ABC
        $intervTerminee = Intervention::updateOrCreate(
            ['code_intervention' => 'INT-ABC-001'],
            [
                'chantier_id' => $chantier1->id,
                'emplacement_id' => $emplacement1->id,
                'technicien_id' => $technicien->id,
                'type_intervention_id' => $typeInterv->id,
                'mode_suivi' => 'GPS',
                'priorite' => Intervention::PRIORITE_HAUTE,
                'statut' => Intervention::STATUT_TERMINEE,
                'date_prevue_debut' => Carbon::now()->subDays(5)->setHour(9),
                'date_prevue_fin' => Carbon::now()->subDays(5)->setHour(12),
                'date_reelle_debut' => Carbon::now()->subDays(5)->setHour(9)->addMinutes(10),
                'date_reelle_fin' => Carbon::now()->subDays(5)->setHour(11)->addMinutes(45),
                'duree_prevue' => 180,
                'duree_reelle' => 155,
                'pourcentage_global' => 100,
                'description' => 'Installation des caméras de surveillance extérieures.',
                'observations' => 'Tout s\'est déroulé conformément au cahier des charges.',
            ]
        );

        $intervEnCours = Intervention::updateOrCreate(
            ['code_intervention' => 'INT-ABC-002'],
            [
                'chantier_id' => $chantier1->id,
                'emplacement_id' => $emplacement1->id,
                'technicien_id' => $technicien->id,
                'type_intervention_id' => $typeInterv->id,
                'mode_suivi' => 'QR',
                'priorite' => Intervention::PRIORITE_NORMALE,
                'statut' => Intervention::STATUT_EN_COURS,
                'date_prevue_debut' => Carbon::now()->setHour(10),
                'date_prevue_fin' => Carbon::now()->setHour(16),
                'date_reelle_debut' => Carbon::now()->setHour(10)->addMinutes(5),
                'duree_prevue' => 360,
                'pourcentage_global' => 60,
                'description' => 'Configuration des switchs réseau et optimisation bande passante.',
                'observations' => 'Intervention en cours d\'exécution par le technicien.',
            ]
        );

        $intervPlanifiee = Intervention::updateOrCreate(
            ['code_intervention' => 'INT-ABC-003'],
            [
                'chantier_id' => $chantier2->id,
                'emplacement_id' => $emplacement2->id,
                'technicien_id' => $technicien->id,
                'type_intervention_id' => $typeInterv->id,
                'mode_suivi' => 'GPS',
                'priorite' => Intervention::PRIORITE_URGENTE,
                'statut' => Intervention::STATUT_PLANIFIEE,
                'date_prevue_debut' => Carbon::now()->addDays(2)->setHour(14),
                'date_prevue_fin' => Carbon::now()->addDays(2)->setHour(17),
                'duree_prevue' => 180,
                'pourcentage_global' => 0,
                'description' => 'Audit préventif du réseau Wi-Fi dans l\'entrepôt.',
            ]
        );

        // 6. Rapport d'intervention
        Rapport::updateOrCreate(
            ['intervention_id' => $intervTerminee->id],
            [
                'date_debut' => $intervTerminee->date_reelle_debut,
                'date_fin' => $intervTerminee->date_reelle_fin,
                'commentaire' => 'Toutes les caméras ont été testées et sont fonctionnelles.',
                'signature_client' => 'Client ABC',
                'signature_technicien' => 'Karim (Tech)',
                'pdf_path' => null,
            ]
        );

        // 7. Documents rattachés au Client : entièrement délégué à DocumentSeeder (qui
        // s'exécute après celui-ci dans DatabaseSeeder), pour centraliser la génération
        // des PDF structurés au même endroit que le reste des documents de démonstration.
    }
}