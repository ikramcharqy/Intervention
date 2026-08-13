<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formulaire;
use App\Models\Question;
use App\Models\TypeIntervention;
use App\Models\Intervention;
use App\Models\Chantier;
use App\Models\Emplacement;
use App\Models\User;
use Carbon\Carbon;

class FormulaireSeeder extends Seeder
{
    /**
     * Formulaires dynamiques réalistes pour chaque type d'intervention terrain.
     * Chaque formulaire est conçu selon les critères réels de l'activité correspondante.
     */
    public function run(): void
    {
        // Nettoyer les anciens formulaires avant d'insérer les nouveaux
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('choix_questions')->truncate();
        \DB::table('questions')->truncate();
        \DB::table('formulaires')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedInstallationFibre();
        $this->seedInstallationWifi();
        $this->seedInstallationCamera();
        $this->seedMaintenance();
        $this->seedReparation();
        $this->seedConfigurationRouteur();
        $this->seedConfigurationSwitch();
        $this->seedMaintenanceGPS();
    }

    // =========================================================================
    // 1. Installation Fibre Optique
    // =========================================================================
    private function seedInstallationFibre(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%Fibre%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Rapport Installation Fibre Optique',
            'description'=> 'Formulaire de réception technique à compléter après toute installation de fibre optique FTTH/FTTO.',
            'is_active'  => true,
        ]);

        // Section : Identification du matériel
        $this->q($form, 1,  'Texte',    'Numéro de série de l\'ONT/BOX',                            true,  'Ex : SN-123456789');
        $this->q($form, 2,  'QRCode',   'Scanner le QR Code du boîtier installé',                   true,  'Pointez la caméra vers le QR Code');
        $this->q($form, 3,  'Texte',    'Référence du câble fibre utilisé',                          true,  'Ex : G657A2 – OS2');
        $this->q($form, 4,  'Nombre',   'Longueur totale de câble posée (en mètres)',                true,  'Ex : 45');
        $this->q($form, 5,  'Nombre',   'Nombre de connecteurs SC/APC installés',                   true,  'Ex : 4');

        // Section : Tests techniques
        $this->q($form, 6,  'OuiNon',   'Test OTDR effectué ?',                                      true);
        $this->q($form, 7,  'Nombre',   'Atténuation mesurée (dBm)',                                 true,  'Ex : -24.5');
        $this->q($form, 8,  'OuiNon',   'Signal optique dans les normes (< -28 dBm) ?',              true);
        $this->q($form, 9,  'Liste',    'Résultat du test de débit internet',                        true)->choix()->createMany([
            ['valeur' => '< 100 Mbps',  'libelle' => 'Insuffisant (< 100 Mbps)',  'ordre' => 1],
            ['valeur' => '100-500 Mbps','libelle' => 'Correct (100-500 Mbps)',    'ordre' => 2],
            ['valeur' => '500-1000 Mbps','libelle' => 'Bon (500 Mbps – 1 Gb)',   'ordre' => 3],
            ['valeur' => '> 1000 Mbps', 'libelle' => 'Excellent (> 1 Gb)',        'ordre' => 4],
        ]);
        $this->q($form, 10, 'OuiNon',   'Fibre sécurisée (goulotte, chemin de câble) ?',            true);

        // Section : Photos & documentation
        $this->q($form, 11, 'Photo',    'Photo du boîtier installé et branché',                     true,  'Prenez une photo nette du boîtier');
        $this->q($form, 12, 'Photo',    'Photo du cheminement du câble (goulottes, fixations)',      true,  'Photo du parcours câble visible');
        $this->q($form, 13, 'Photo',    'Photo de la chambre de tirage / point de raccordement',    false, 'Optionnelle si non accessible');
        $this->q($form, 14, 'Video',    'Vidéo de démonstration connexion réussie',                 false, 'Enregistrez le test de connexion');

        // Section : Conformité & réception
        $this->q($form, 15, 'Checkbox', 'Points de vérification checklist de conformité',           true)->choix()->createMany([
            ['valeur' => 'cable_sécurisé',    'libelle' => 'Câble correctement fixé',          'ordre' => 1],
            ['valeur' => 'etiquetage',        'libelle' => 'Étiquetage des câbles effectué',   'ordre' => 2],
            ['valeur' => 'prise_nettoyée',    'libelle' => 'Prises nettoyées avant connexion', 'ordre' => 3],
            ['valeur' => 'epissures_testées', 'libelle' => 'Épissures testées et conformes',   'ordre' => 4],
            ['valeur' => 'doc_remise',        'libelle' => 'Documentation remise au client',   'ordre' => 5],
        ]);
        $this->q($form, 16, 'Radio',    'Niveau de satisfaction du client à la réception',          true)->choix()->createMany([
            ['valeur' => 'tres_satisfait',  'libelle' => '⭐⭐⭐⭐⭐ Très satisfait', 'ordre' => 1],
            ['valeur' => 'satisfait',       'libelle' => '⭐⭐⭐⭐ Satisfait',        'ordre' => 2],
            ['valeur' => 'neutre',          'libelle' => '⭐⭐⭐ Neutre',             'ordre' => 3],
            ['valeur' => 'insatisfait',     'libelle' => '⭐⭐ Insatisfait',          'ordre' => 4],
        ]);
        $this->q($form, 17, 'TexteLong','Observations et remarques techniques',                     false, 'Notez tout problème ou point particulier');
        $this->q($form, 18, 'Signature','Signature du client (réception de l\'installation)',       true);
    }

    // =========================================================================
    // 2. Installation WiFi
    // =========================================================================
    private function seedInstallationWifi(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%WiFi%')
                                ->orWhere('nom', 'like', '%Wifi%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Rapport Installation Réseau WiFi',
            'description'=> 'Formulaire de configuration et réception d\'un réseau WiFi professionnel (bornes, contrôleur, SSID).',
            'is_active'  => true,
        ]);

        // Matériel
        $this->q($form, 1,  'Texte',    'Marque et modèle de la borne/point d\'accès',              true,  'Ex : Ubiquiti UniFi AP-AC-Pro');
        $this->q($form, 2,  'QRCode',   'Scanner le QR Code de chaque borne installée',             true,  'Scannez toutes les bornes l\'une après l\'autre');
        $this->q($form, 3,  'Nombre',   'Nombre de bornes WiFi installées',                          true,  'Ex : 3');
        $this->q($form, 4,  'Texte',    'Nom(s) du/des SSID configuré(s)',                          true,  'Ex : CORP-NET / CORP-GUEST');
        $this->q($form, 5,  'OuiNon',   'Réseau invité isolé (VLAN ou DMZ) configuré ?',            false);

        // Configuration sécurité
        $this->q($form, 6,  'Liste',    'Protocole de sécurité WiFi utilisé',                       true)->choix()->createMany([
            ['valeur' => 'WPA2-Enterprise', 'libelle' => 'WPA2-Enterprise (802.1X)',   'ordre' => 1],
            ['valeur' => 'WPA2-Personal',   'libelle' => 'WPA2-Personal (PSK)',        'ordre' => 2],
            ['valeur' => 'WPA3',            'libelle' => 'WPA3',                       'ordre' => 3],
            ['valeur' => 'WPA2-WPA3',       'libelle' => 'WPA2/WPA3 Mixte',           'ordre' => 4],
        ]);
        $this->q($form, 7,  'OuiNon',   'Mots de passe réseau changés par rapport aux défauts ?',  true);
        $this->q($form, 8,  'OuiNon',   'Firmware des bornes mis à jour ?',                         true);

        // Tests terrain
        $this->q($form, 9,  'Nombre',   'Force du signal WiFi au point le plus éloigné (dBm)',      true,  'Ex : -65');
        $this->q($form, 10, 'OuiNon',   'Test de couverture effectué dans toutes les zones ?',      true);
        $this->q($form, 11, 'Nombre',   'Débit mesuré au point le plus faible (Mbps)',              true,  'Ex : 54');
        $this->q($form, 12, 'OuiNon',   'Test de roaming (passage d\'une borne à l\'autre) OK ?',  false);

        // Documentation
        $this->q($form, 13, 'Photo',    'Photo du tableau réseau (switch PoE / contrôleur)',        true);
        $this->q($form, 14, 'Photo',    'Photo de chaque borne installée avec son emplacement',     true);
        $this->q($form, 15, 'Photo',    'Capture d\'écran du portail d\'administration',            false, 'Si accessible, faites une capture');
        $this->q($form, 16, 'Video',    'Vidéo du test de connexion et de navigation',              false);
        $this->q($form, 17, 'GPS',      'Position GPS de chaque borne (principale)',                false);

        // Bilan
        $this->q($form, 18, 'Checkbox', 'Éléments remis au client',                                true)->choix()->createMany([
            ['valeur' => 'credentials_wifi', 'libelle' => 'Fiche avec identifiants WiFi',          'ordre' => 1],
            ['valeur' => 'plan_réseau',      'libelle' => 'Schéma / plan du réseau',               'ordre' => 2],
            ['valeur' => 'guide_admin',      'libelle' => 'Guide d\'administration',               'ordre' => 3],
            ['valeur' => 'garantie',         'libelle' => 'Certificat de garantie matériel',       'ordre' => 4],
        ]);
        $this->q($form, 19, 'TexteLong','Remarques techniques et recommandations',                  false);
        $this->q($form, 20, 'Signature','Signature de validation du client',                        true);
    }

    // =========================================================================
    // 3. Installation Caméra de surveillance
    // =========================================================================
    private function seedInstallationCamera(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%Caméra%')
                                ->orWhere('nom', 'like', '%Camera%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Rapport Installation Système de Vidéosurveillance',
            'description'=> 'Formulaire de mise en service d\'un système de vidéosurveillance (IP / analogique / NVR/DVR).',
            'is_active'  => true,
        ]);

        // Inventaire matériel
        $this->q($form, 1,  'Texte',    'Marque et modèle de la caméra principale',                 true,  'Ex : Hikvision DS-2CD2143G2');
        $this->q($form, 2,  'Nombre',   'Nombre total de caméras installées',                        true,  'Ex : 8');
        $this->q($form, 3,  'QRCode',   'Scanner le QR Code du NVR/DVR',                            true,  'QR Code de l\'enregistreur central');
        $this->q($form, 4,  'Liste',    'Type de caméras installées',                               true)->choix()->createMany([
            ['valeur' => 'ip_poe',     'libelle' => 'IP PoE (réseau)',          'ordre' => 1],
            ['valeur' => 'ip_wifi',    'libelle' => 'IP WiFi',                  'ordre' => 2],
            ['valeur' => 'analogique', 'libelle' => 'Analogique (AHD/TVI)',     'ordre' => 3],
            ['valeur' => 'ptz',        'libelle' => 'PTZ motorisée',            'ordre' => 4],
        ]);
        $this->q($form, 5,  'OuiNon',   'Caméras en vision nocturne (infrarouge) ?',                true);
        $this->q($form, 6,  'Nombre',   'Capacité de stockage du NVR (To)',                         true,  'Ex : 4');

        // Vérification technique
        $this->q($form, 7,  'Checkbox', 'Zones de surveillance couvertes',                          true)->choix()->createMany([
            ['valeur' => 'entree',     'libelle' => 'Entrée principale',        'ordre' => 1],
            ['valeur' => 'parking',    'libelle' => 'Parking / extérieur',      'ordre' => 2],
            ['valeur' => 'couloirs',   'libelle' => 'Couloirs intérieurs',      'ordre' => 3],
            ['valeur' => 'bureaux',    'libelle' => 'Zone bureaux',             'ordre' => 4],
            ['valeur' => 'serveurs',   'libelle' => 'Salle serveurs',           'ordre' => 5],
            ['valeur' => 'caisse',     'libelle' => 'Zone caisse / coffre',     'ordre' => 6],
        ]);
        $this->q($form, 8,  'OuiNon',   'Toutes les caméras alimentées et en ligne ?',              true);
        $this->q($form, 9,  'OuiNon',   'Enregistrement continu configuré et actif ?',              true);
        $this->q($form, 10, 'Nombre',   'Durée de rétention configurée (jours)',                    true,  'Ex : 30');
        $this->q($form, 11, 'OuiNon',   'Accès à distance (app mobile) configuré et testé ?',      false);
        $this->q($form, 12, 'Radio',    'Qualité d\'image en mode jour',                            true)->choix()->createMany([
            ['valeur' => 'hd_4k',   'libelle' => '4K / 4MP – Excellente',  'ordre' => 1],
            ['valeur' => 'full_hd', 'libelle' => 'Full HD 1080p – Bonne',  'ordre' => 2],
            ['valeur' => 'hd',      'libelle' => 'HD 720p – Correcte',     'ordre' => 3],
            ['valeur' => 'sd',      'libelle' => 'SD – Insuffisante',      'ordre' => 4],
        ]);
        $this->q($form, 13, 'OuiNon',   'Qualité d\'image en mode nuit satisfaisante ?',           true);

        // Position et documentation
        $this->q($form, 14, 'GPS',      'Coordonnées GPS du site d\'installation',                  false);
        $this->q($form, 15, 'Photo',    'Photo du NVR/DVR et du rack câblage',                     true);
        $this->q($form, 16, 'Photo',    'Photo de la vue générale caméra principale',               true);
        $this->q($form, 17, 'Photo',    'Capture d\'écran de l\'interface NVR (live view)',         true,  'Montrez toutes les caméras actives');
        $this->q($form, 18, 'Video',    'Vidéo de démonstration du système (live + playback)',      false);

        // Livraison
        $this->q($form, 19, 'Checkbox', 'Documents et accès remis au client',                       true)->choix()->createMany([
            ['valeur' => 'codes_acces',   'libelle' => 'Codes d\'accès NVR',           'ordre' => 1],
            ['valeur' => 'app_configurée','libelle' => 'Application mobile installée', 'ordre' => 2],
            ['valeur' => 'plan_cameras',  'libelle' => 'Plan d\'implantation caméras', 'ordre' => 3],
            ['valeur' => 'manuel_util',   'libelle' => 'Manuel d\'utilisation',        'ordre' => 4],
        ]);
        $this->q($form, 20, 'TexteLong','Commentaires et recommandations de sécurité',              false);
        $this->q($form, 21, 'Signature','Signature du responsable sécurité / client',               true);
    }

    // =========================================================================
    // 4. Maintenance préventive
    // =========================================================================
    private function seedMaintenance(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%Maintenance%')
                                ->where('nom', 'not like', '%GPS%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Fiche de Maintenance Préventive Réseau',
            'description'=> 'Checklist complète de maintenance préventive périodique des équipements réseau et télécoms.',
            'is_active'  => true,
        ]);

        // Identification
        $this->q($form, 1,  'QRCode',   'Scanner le QR Code de l\'équipement maintenu',            true,  'Identifiant unique de l\'équipement');
        $this->q($form, 2,  'Date',     'Date du dernier entretien précédent',                      true,  'Consultez l\'historique maintenance');
        $this->q($form, 3,  'Liste',    'Type d\'équipement maintenu',                              true)->choix()->createMany([
            ['valeur' => 'routeur',   'libelle' => 'Routeur / Firewall',          'ordre' => 1],
            ['valeur' => 'switch',    'libelle' => 'Switch réseau',               'ordre' => 2],
            ['valeur' => 'borne',     'libelle' => 'Borne WiFi / Point d\'accès', 'ordre' => 3],
            ['valeur' => 'nvr',       'libelle' => 'NVR / DVR',                  'ordre' => 4],
            ['valeur' => 'serveur',   'libelle' => 'Serveur',                    'ordre' => 5],
            ['valeur' => 'onduleur',  'libelle' => 'Onduleur (UPS)',             'ordre' => 6],
            ['valeur' => 'autre',     'libelle' => 'Autre équipement',           'ordre' => 7],
        ]);

        // Inspection physique
        $this->q($form, 4,  'Liste',    'État général de l\'équipement',                            true)->choix()->createMany([
            ['valeur' => 'tres_bon',   'libelle' => 'Très bon – RAS',                'ordre' => 1],
            ['valeur' => 'bon',        'libelle' => 'Bon – Usure normale',            'ordre' => 2],
            ['valeur' => 'moyen',      'libelle' => 'Moyen – Surveillance nécessaire','ordre' => 3],
            ['valeur' => 'degrade',    'libelle' => 'Dégradé – Intervention requise', 'ordre' => 4],
        ]);
        $this->q($form, 5,  'Liste',    'Niveau d\'encrassement (poussière, saletés)',              true)->choix()->createMany([
            ['valeur' => 'propre',    'libelle' => 'Propre – Aucune action',  'ordre' => 1],
            ['valeur' => 'léger',     'libelle' => 'Léger encrassement',      'ordre' => 2],
            ['valeur' => 'modéré',    'libelle' => 'Encrassement modéré',     'ordre' => 3],
            ['valeur' => 'important', 'libelle' => 'Encrassement important',  'ordre' => 4],
        ]);
        $this->q($form, 6,  'OuiNon',   'Ventilateurs / dissipateurs en état de fonctionnement ?', true);
        $this->q($form, 7,  'OuiNon',   'Câblage en bon état (pas de courbures / dommages) ?',     true);
        $this->q($form, 8,  'OuiNon',   'DEL / voyants de statut normaux ?',                       true);

        // Actions effectuées
        $this->q($form, 9,  'Checkbox', 'Opérations de maintenance réalisées',                     true)->choix()->createMany([
            ['valeur' => 'nettoyage',       'libelle' => 'Nettoyage physique (dépoussiérage)',  'ordre' => 1],
            ['valeur' => 'firmware_update', 'libelle' => 'Mise à jour firmware',               'ordre' => 2],
            ['valeur' => 'config_backup',   'libelle' => 'Sauvegarde de la configuration',     'ordre' => 3],
            ['valeur' => 'test_failover',   'libelle' => 'Test de bascule / redondance',       'ordre' => 4],
            ['valeur' => 'logs_analyse',    'libelle' => 'Analyse des journaux d\'erreurs',    'ordre' => 5],
            ['valeur' => 'certificats',     'libelle' => 'Renouvellement certificats SSL',     'ordre' => 6],
            ['valeur' => 'remplacement',    'libelle' => 'Remplacement de pièce(s)',           'ordre' => 7],
        ]);
        $this->q($form, 10, 'OuiNon',   'Redémarrage de l\'équipement effectué après maintenance ?',true);
        $this->q($form, 11, 'OuiNon',   'Tous les services sont opérationnels après intervention ?',true);

        // Métriques
        $this->q($form, 12, 'Nombre',   'Température de l\'équipement (°C)',                        false, 'Ex : 45');
        $this->q($form, 13, 'Nombre',   'Uptime avant intervention (heures)',                       false, 'Ex : 720');

        // Photos
        $this->q($form, 14, 'Photo',    'Photo avant intervention (état initial)',                  true);
        $this->q($form, 15, 'Photo',    'Photo après intervention (état final)',                    true);
        $this->q($form, 16, 'Photo',    'Photo du firmware / version affichée',                    false, 'Capture de l\'interface admin');
        $this->q($form, 17, 'TexteLong','Pièces remplacées et références',                          false, 'Ex : Ventilateur FAN-X80, réf. VT-2023');
        $this->q($form, 18, 'TexteLong','Anomalies détectées et travaux recommandés',               false);
        $this->q($form, 19, 'Date',     'Date du prochain entretien recommandé',                    false);
    }

    // =========================================================================
    // 5. Réparation / Dépannage
    // =========================================================================
    private function seedReparation(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%Réparation%')
                                ->orWhere('nom', 'like', '%Reparation%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Fiche de Dépannage et Réparation',
            'description'=> 'Rapport de diagnostic et réparation d\'un équipement réseau en panne.',
            'is_active'  => true,
        ]);

        // Diagnostic
        $this->q($form, 1,  'QRCode',   'Scanner le QR Code de l\'équipement en panne',            true,  'Pointez vers le code de l\'équipement');
        $this->q($form, 2,  'Texte',    'Référence / numéro de série de l\'équipement',             true,  'Trouvable sur l\'étiquette de l\'appareil');
        $this->q($form, 3,  'TexteLong','Description du problème signalé par le client',            true,  'Décrivez exactement la panne décrite');
        $this->q($form, 4,  'Liste',    'Catégorie de la panne',                                    true)->choix()->createMany([
            ['valeur' => 'panne_reseau',     'libelle' => 'Panne réseau / connectivité',      'ordre' => 1],
            ['valeur' => 'panne_materielle', 'libelle' => 'Défaillance matérielle',           'ordre' => 2],
            ['valeur' => 'config_erreur',    'libelle' => 'Erreur de configuration',          'ordre' => 3],
            ['valeur' => 'firmware',         'libelle' => 'Problème firmware / logiciel',     'ordre' => 4],
            ['valeur' => 'alimentation',     'libelle' => 'Problème d\'alimentation',         'ordre' => 5],
            ['valeur' => 'physique',         'libelle' => 'Dommage physique (choc, eau...)',  'ordre' => 6],
        ]);
        $this->q($form, 5,  'Liste',    'Gravité de la panne',                                      true)->choix()->createMany([
            ['valeur' => 'critique',  'libelle' => '🔴 Critique – Service totalement arrêté',  'ordre' => 1],
            ['valeur' => 'majeur',    'libelle' => '🟠 Majeur – Impact important',             'ordre' => 2],
            ['valeur' => 'mineur',    'libelle' => '🟡 Mineur – Fonctionnement dégradé',       'ordre' => 3],
            ['valeur' => 'cosmétique','libelle' => '🟢 Cosmétique – Pas d\'impact service',   'ordre' => 4],
        ]);

        // Diagnostique terrain
        $this->q($form, 6,  'TexteLong','Diagnostic technique effectué (tests, observations)',       true,  'Décrivez votre analyse complète');
        $this->q($form, 7,  'OuiNon',   'Équipement remplacé ?',                                    true);
        $this->q($form, 8,  'Texte',    'Référence de la pièce/équipement de remplacement',         false, 'Si remplacement effectué');

        // Réparation
        $this->q($form, 9,  'Checkbox', 'Actions correctives réalisées',                            true)->choix()->createMany([
            ['valeur' => 'redemarrage',      'libelle' => 'Redémarrage / reset usine',           'ordre' => 1],
            ['valeur' => 'reconfiguration',  'libelle' => 'Reconfiguration complète',             'ordre' => 2],
            ['valeur' => 'remplacement',     'libelle' => 'Remplacement de l\'équipement',       'ordre' => 3],
            ['valeur' => 'cable',            'libelle' => 'Remplacement câble / connecteur',     'ordre' => 4],
            ['valeur' => 'firmware',         'libelle' => 'Mise à jour / rollback firmware',     'ordre' => 5],
            ['valeur' => 'alimentation',     'libelle' => 'Remplacement alimentation',           'ordre' => 6],
            ['valeur' => 'escalade',         'libelle' => 'Escalade au support N2/N3',          'ordre' => 7],
        ]);
        $this->q($form, 10, 'OuiNon',   'Panne résolue complètement ?',                             true);
        $this->q($form, 11, 'TexteLong','Si non résolu : description des actions en cours',         false, 'À compléter si la panne persiste');

        // Preuves
        $this->q($form, 12, 'Photo',    'Photo de l\'équipement en panne (état initial)',           true);
        $this->q($form, 13, 'Photo',    'Photo après réparation (état résolu)',                     true);
        $this->q($form, 14, 'Video',    'Vidéo du problème ou de la résolution',                   false, 'Si pertinent, filmez la panne / solution');

        // Suivi
        $this->q($form, 15, 'OuiNon',   'Suivi requis sous 24h ?',                                  false);
        $this->q($form, 16, 'TexteLong','Recommandations préventives pour éviter la récidive',      false);
        $this->q($form, 17, 'Signature','Signature du client (réception réparation)',               true);
    }

    // =========================================================================
    // 6. Configuration Routeur / Firewall
    // =========================================================================
    private function seedConfigurationRouteur(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%Routeur%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Rapport de Configuration Routeur / Firewall',
            'description'=> 'Formulaire de configuration et validation d\'un routeur ou firewall professionnel.',
            'is_active'  => true,
        ]);

        // Identification matériel
        $this->q($form, 1,  'QRCode',   'Scanner le QR Code du routeur / firewall',                true,  'Étiquette sur l\'appareil');
        $this->q($form, 2,  'Texte',    'Marque, modèle et version firmware',                       true,  'Ex : Cisco RV345 – FW 1.0.03.29');
        $this->q($form, 3,  'Texte',    'Adresse IP de gestion configurée',                         true,  'Ex : 192.168.1.1');
        $this->q($form, 4,  'Liste',    'Type de connexion WAN',                                    true)->choix()->createMany([
            ['valeur' => 'fibre',   'libelle' => 'Fibre Optique FTTH/FTTO',   'ordre' => 1],
            ['valeur' => 'adsl',    'libelle' => 'ADSL / VDSL',               'ordre' => 2],
            ['valeur' => 'cable',   'libelle' => 'Câble (coaxial)',            'ordre' => 3],
            ['valeur' => '4g5g',    'libelle' => '4G/5G (backup)',             'ordre' => 4],
            ['valeur' => 'mpls',    'libelle' => 'MPLS / SD-WAN',             'ordre' => 5],
        ]);

        // Configuration réseau
        $this->q($form, 5,  'Checkbox', 'Éléments réseau configurés',                              true)->choix()->createMany([
            ['valeur' => 'vlan',       'libelle' => 'VLANs (segmentation réseau)',        'ordre' => 1],
            ['valeur' => 'dhcp',       'libelle' => 'Serveur DHCP',                      'ordre' => 2],
            ['valeur' => 'dns',        'libelle' => 'Résolution DNS',                    'ordre' => 3],
            ['valeur' => 'nat',        'libelle' => 'NAT / PAT',                         'ordre' => 4],
            ['valeur' => 'vpn',        'libelle' => 'VPN Site-to-Site / Remote Access',  'ordre' => 5],
            ['valeur' => 'qos',        'libelle' => 'QoS (priorisation du trafic)',      'ordre' => 6],
            ['valeur' => 'failover',   'libelle' => 'Basculement WAN (dual WAN)',        'ordre' => 7],
        ]);

        // Sécurité
        $this->q($form, 6,  'OuiNon',   'Règles de firewall restrictives configurées ?',            true);
        $this->q($form, 7,  'OuiNon',   'Accès SSH/HTTPS de gestion restreint (IP whitelist) ?',   true);
        $this->q($form, 8,  'OuiNon',   'Mot de passe administrateur changé (non défaut) ?',       true);
        $this->q($form, 9,  'OuiNon',   'Logs système et alertes configurés ?',                    false);
        $this->q($form, 10, 'OuiNon',   'Configuration sauvegardée (fichier backup) ?',            true);

        // Tests
        $this->q($form, 11, 'OuiNon',   'Test de connectivité Internet depuis LAN OK ?',           true);
        $this->q($form, 12, 'OuiNon',   'Test de résolution DNS OK ?',                             true);
        $this->q($form, 13, 'OuiNon',   'Test VPN fonctionnel (si configuré) ?',                  false);
        $this->q($form, 14, 'Nombre',   'Latence (ping) vers Internet (ms)',                        false, 'Ex : 12');
        $this->q($form, 15, 'Nombre',   'Débit WAN mesuré en download (Mbps)',                     false, 'Ex : 480');

        // Documentation
        $this->q($form, 16, 'Photo',    'Capture d\'écran du dashboard de l\'interface admin',     true);
        $this->q($form, 17, 'Photo',    'Photo du routeur en rack / emplacement final',            true);
        $this->q($form, 18, 'Photo',    'Capture des règles firewall principales',                 false);
        $this->q($form, 19, 'TexteLong','Description de l\'architecture réseau mise en place',     false);
        $this->q($form, 20, 'TexteLong','Recommandations sécurité et points d\'attention',         false);
        $this->q($form, 21, 'Signature','Validation client / responsable IT',                       true);
    }

    // =========================================================================
    // 7. Configuration Switch
    // =========================================================================
    private function seedConfigurationSwitch(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%Switch%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Rapport de Configuration Switch Réseau',
            'description'=> 'Formulaire de configuration et validation d\'un switch manageable (L2/L3) en infrastructure réseau.',
            'is_active'  => true,
        ]);

        // Identification
        $this->q($form, 1,  'QRCode',   'Scanner le QR Code du switch',                            true,  'Étiquette QR Code sur le switch');
        $this->q($form, 2,  'Texte',    'Modèle du switch et version firmware',                    true,  'Ex : Cisco Catalyst 2960X – v15.2');
        $this->q($form, 3,  'Nombre',   'Nombre de ports du switch',                               true,  'Ex : 24');
        $this->q($form, 4,  'Liste',    'Type de switch',                                          true)->choix()->createMany([
            ['valeur' => 'l2',    'libelle' => 'L2 – Switch basique (VLANs)',           'ordre' => 1],
            ['valeur' => 'l3',    'libelle' => 'L3 – Switch routant',                  'ordre' => 2],
            ['valeur' => 'poe',   'libelle' => 'PoE – Alimentation sur câble réseau',  'ordre' => 3],
            ['valeur' => 'sfp',   'libelle' => 'SFP – Ports fibre optique',            'ordre' => 4],
        ]);

        // Configuration
        $this->q($form, 5,  'Checkbox', 'Fonctionnalités configurées',                             true)->choix()->createMany([
            ['valeur' => 'vlan_config',  'libelle' => 'Configuration VLANs',               'ordre' => 1],
            ['valeur' => 'trunk',        'libelle' => 'Ports trunk (inter-switches)',       'ordre' => 2],
            ['valeur' => 'stp',          'libelle' => 'Spanning Tree Protocol (STP)',       'ordre' => 3],
            ['valeur' => 'lacp',         'libelle' => 'Agrégation de liens (LACP)',         'ordre' => 4],
            ['valeur' => 'qos',          'libelle' => 'QoS (priorisation)',                'ordre' => 5],
            ['valeur' => 'port_security','libelle' => 'Sécurité des ports (802.1X)',       'ordre' => 6],
            ['valeur' => 'snmp',         'libelle' => 'SNMP (supervision)',               'ordre' => 7],
        ]);
        $this->q($form, 6,  'Nombre',   'Nombre de VLANs créés',                                  false, 'Ex : 5');
        $this->q($form, 7,  'OuiNon',   'Mot de passe administrateur changé ?',                   true);
        $this->q($form, 8,  'OuiNon',   'Configuration sauvegardée (running-config > startup) ?', true);
        $this->q($form, 9,  'OuiNon',   'Tous les ports actifs correctement assignés aux VLANs ?',true);
        $this->q($form, 10, 'OuiNon',   'Test de connectivité inter-VLANs effectué ?',            false);

        // Inventaire ports
        $this->q($form, 11, 'Nombre',   'Nombre de ports actifs / utilisés',                       true,  'Ports avec câble connecté');
        $this->q($form, 12, 'OuiNon',   'Ports inutilisés désactivés (shutdown) ?',               true);

        // Documentation
        $this->q($form, 13, 'Photo',    'Photo du switch câblé en rack / baie',                   true);
        $this->q($form, 14, 'Photo',    'Capture de la configuration des VLANs',                  true,  'Capture d\'écran interface admin');
        $this->q($form, 15, 'Photo',    'Photo de l\'étiquetage des câbles sur le switch',        false);
        $this->q($form, 16, 'TexteLong','Description de l\'architecture de brassage',             false);
        $this->q($form, 17, 'TexteLong','Anomalies ou ports problématiques identifiés',           false);
        $this->q($form, 18, 'Signature','Validation technique par le responsable IT',              true);
    }

    // =========================================================================
    // 8. Maintenance GPS Live
    // =========================================================================
    private function seedMaintenanceGPS(): void
    {
        $type = TypeIntervention::where('nom', 'like', '%GPS%')->first();
        if (!$type) return;

        $form = Formulaire::create([
            'type_intervention_id' => $type->id,
            'nom'        => 'Rapport de Maintenance Système GPS / Télématique',
            'description'=> 'Formulaire de maintenance préventive et corrective des dispositifs GPS embarqués (véhicules, équipements mobiles).',
            'is_active'  => true,
        ]);

        // Identification
        $this->q($form, 1,  'QRCode',   'Scanner le QR Code du boîtier GPS',                      true,  'Code sur le boîtier GPS/traceur');
        $this->q($form, 2,  'Texte',    'Immatriculation du véhicule / ID équipement',             true,  'Ex : 12345-A-6 ou ID-VH-087');
        $this->q($form, 3,  'Texte',    'Modèle du traceur GPS',                                   true,  'Ex : Teltonika FMB140');
        $this->q($form, 4,  'GPS',      'Position GPS actuelle au moment de l\'intervention',      true,  'Votre position courante');

        // État matériel
        $this->q($form, 5,  'Radio',    'État du boîtier GPS (aspect physique)',                   true)->choix()->createMany([
            ['valeur' => 'parfait',  'libelle' => '✅ Parfait état – Aucun dommage',    'ordre' => 1],
            ['valeur' => 'bon',      'libelle' => '🟡 Bon état – Légère usure',         'ordre' => 2],
            ['valeur' => 'abimé',    'libelle' => '🟠 Abîmé – Impacts / rayures',      'ordre' => 3],
            ['valeur' => 'endommagé','libelle' => '🔴 Endommagé – Remplacement requis', 'ordre' => 4],
        ]);
        $this->q($form, 6,  'OuiNon',   'Câblage alimentation en bon état (pas de coupure) ?',    true);
        $this->q($form, 7,  'OuiNon',   'Antenne GPS intacte et bien fixée ?',                    true);
        $this->q($form, 8,  'OuiNon',   'Carte SIM présente et opérationnelle ?',                 true);
        $this->q($form, 9,  'Texte',    'Numéro de la carte SIM (ICCID)',                          false, 'Ex : 8933 1234 5678 9012 3456');

        // Tests fonctionnels
        $this->q($form, 10, 'OuiNon',   'Boîtier GPS allumé et communication active ?',           true);
        $this->q($form, 11, 'Nombre',   'Nombre de satellites GPS captés',                         true,  'Ex : 12 (minimum recommandé : 4)');
        $this->q($form, 12, 'Nombre',   'Précision GPS mesurée (mètres)',                          true,  'Ex : 3');
        $this->q($form, 13, 'OuiNon',   'Données transmises visibles sur la plateforme ?',        true);
        $this->q($form, 14, 'OuiNon',   'Historique des positions correct (pas de trous) ?',      true);

        // Actions réalisées
        $this->q($form, 15, 'Checkbox', 'Actions de maintenance effectuées',                       true)->choix()->createMany([
            ['valeur' => 'firmware',       'libelle' => 'Mise à jour firmware',               'ordre' => 1],
            ['valeur' => 'reconfiguration','libelle' => 'Reconfiguration paramètres',         'ordre' => 2],
            ['valeur' => 'sim_change',     'libelle' => 'Remplacement carte SIM',             'ordre' => 3],
            ['valeur' => 'antenne',        'libelle' => 'Remplacement antenne GPS',           'ordre' => 4],
            ['valeur' => 'cable',          'libelle' => 'Réparation câblage',                'ordre' => 5],
            ['valeur' => 'boitier',        'libelle' => 'Remplacement boîtier complet',       'ordre' => 6],
        ]);

        // Photos et vidéos
        $this->q($form, 16, 'Photo',    'Photo du boîtier GPS installé (emplacement dans véhicule)',true);
        $this->q($form, 17, 'Photo',    'Capture de la plateforme (position en temps réel)',       true,  'Screenshot de la plateforme de tracking');
        $this->q($form, 18, 'Video',    'Vidéo de vérification du tracking en temps réel',        false, 'Filmez la position se déplacer sur la carte');
        $this->q($form, 19, 'TexteLong','Observations et anomalies détectées',                     false);
        $this->q($form, 20, 'TexteLong','Recommandations et prochaine maintenance',               false, 'Date et nature du prochain entretien');
    }

    // =========================================================================
    // Helper : créer une question et retourner l'instance pour chaîner les choix
    // =========================================================================
    private function q(Formulaire $form, int $ordre, string $type, string $question, bool $obligatoire = false, ?string $placeholder = null): Question
    {
        return $form->questions()->create([
            'question'    => $question,
            'type_reponse'=> $type,
            'obligatoire' => $obligatoire,
            'ordre'       => $ordre,
            'placeholder' => $placeholder,
        ]);
    }
}