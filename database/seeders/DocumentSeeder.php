<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Devis;
use App\Models\DevisLigne;
use App\Models\Document;
use App\Models\Facture;
use App\Models\Intervention;
use App\Models\Setting;
use App\Services\FactureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Données de démonstration pour "Documents & Contrats" et "Facturation & Devis"
 * (portail Client), pour le compte de démonstration "Société ABC" (CL001) déjà
 * créé par ClientSeeder. Exécuté après ClientSeeder/DemoDataSeeder : dépend de
 * leurs Client/Chantier/Intervention/Rapport. Gère aussi désormais les 2 documents
 * historiquement seedés par ClientSeeder (Contrat, Plan caméras) — centralisé ici.
 *
 * Les PDF sont de vrais documents structurés (templates resources/views/pdf/*),
 * pas du texte placeholder : mêmes gabarits (en-tête de marque, sections, pied de
 * page paginé) que ceux utilisés en production par DevisService/FactureService/
 * ChantierService, ou — pour la fiche d'intervention — le vrai template
 * resources/views/rapports/pdf.blade.php déjà utilisé par RapportController.
 *
 * Limitation constatée (documentée plutôt que contournée par un changement de
 * schéma non validé) : le modèle Document n'a pas de colonne intervention_id —
 * un document ne peut être rattaché qu'à un Rapport (rapport_id, donc une
 * intervention déjà clôturée avec rapport) ou à un Chantier (chantier_id).
 * INT-ABC-003 (Planifiée) n'a pas encore de rapport : son document technique
 * est donc rattaché au chantier (CHT-ABC-02), pas à l'intervention elle-même.
 * Idem, aucune colonne de délai contractuel/SLA n'existe sur Document ni
 * ailleurs (confirmé précédemment) : le tableau SLA affiché dans le PDF de
 * contrat est un contenu de démonstration réaliste, pas une donnée stockée en
 * base — une vraie alerte d'échéance nécessiterait une évolution de schéma
 * séparée.
 */
class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::where('code_client', 'CL001')->first();
        if (!$client) {
            return; // ClientSeeder non exécuté : rien à rattacher.
        }

        $commercialId = $client->commercial_id;
        $chantier1 = \App\Models\Chantier::where('code_chantier', 'CHT-ABC-01')->first();
        $chantier2 = \App\Models\Chantier::where('code_chantier', 'CHT-ABC-02')->first();
        $intervTerminee = Intervention::where('code_intervention', 'INT-ABC-001')->first();
        $rapport = $intervTerminee?->rapport;

        $this->seedContratsEtSla($client);
        [$devisAccepte, $devisEnvoye] = $this->seedDevis($client, $commercialId);
        $this->seedFactures($devisAccepte, $client, $commercialId);
        $this->seedDocumentsTechniques($rapport, $chantier1, $chantier2);
        $this->seedAutre($client);
    }

    private function company(): array
    {
        return [
            'name' => Setting::get('company_name', 'TechInterv Solutions'),
            'tagline' => Setting::get('company_tagline', 'Excellence en Maintenance & Interventions Techniques'),
            'address' => Setting::get('company_address', '12, Boulevard Hassan II – Casablanca, Maroc 20250'),
            'phone' => Setting::get('company_phone', '+212 5 22 45 88 99'),
            'email' => Setting::get('company_email', 'contact@techinterv.ma'),
            'logo' => Setting::get('company_logo') ? storage_path('app/public/' . Setting::get('company_logo')) : null,
            'color' => Setting::get('company_color', '#10b981'),
        ];
    }

    private function seedContratsEtSla(Client $client): void
    {
        $sla = [
            ['priorite' => 'Urgente', 'delai' => '4 heures ouvrées', 'description' => 'Arrêt d\'activité ou risque de sécurité immédiat.'],
            ['priorite' => 'Haute', 'delai' => '1 jour ouvré', 'description' => 'Problème bloquant impactant significativement l\'exploitation.'],
            ['priorite' => 'Normale', 'delai' => '3 jours ouvrés', 'description' => 'Demande standard sans impact critique immédiat.'],
            ['priorite' => 'Faible', 'delai' => '5 jours ouvrés', 'description' => 'Amélioration ou maintenance préventive non urgente.'],
        ];
        $clauses = [
            ['titre' => 'Objet', 'texte' => "Le présent contrat couvre la maintenance préventive et corrective des installations réseau, sécurité et fibre optique du Client, sur l'ensemble des chantiers rattachés à son compte."],
            ['titre' => 'Obligations du prestataire', 'texte' => "TechniTrack s'engage à respecter les délais d'intervention garantis définis à l'article 3, à fournir un rapport d'intervention signé pour chaque passage, et à assurer la disponibilité d'un interlocuteur commercial dédié."],
            ['titre' => 'Obligations du client', 'texte' => "Le Client s'engage à faciliter l'accès aux sites concernés, à signaler toute panne dans les meilleurs délais via le portail ou le Support, et à régler les factures selon les conditions convenues."],
            ['titre' => 'Résiliation', 'texte' => "Le contrat peut être résilié par l'une ou l'autre des parties moyennant un préavis écrit de 30 jours."],
        ];

        Document::updateOrCreate(
            ['client_id' => $client->id, 'nom_original' => 'Contrat_Maintenance_ABC_2026.pdf'],
            [
                'type_document' => 'Contrat',
                'chemin' => $this->ecrirePdf('documents/contrat_abc.pdf', 'pdf.contrat', [
                    'company' => $this->company(),
                    'client' => $client,
                    'contrat' => [
                        'type' => 'Contrat de maintenance annuel',
                        'date_debut' => now()->subMonths(8),
                        'date_fin' => now()->addDays(12),
                        'proche_expiration' => true,
                        'reconduction' => 'Tacite, annuelle, sauf préavis de 30 jours',
                        'sla' => $sla,
                        'clauses' => $clauses,
                    ],
                    'documentTitle' => 'Contrat de Maintenance',
                    'documentReference' => 'CTR-ABC-2026',
                ]),
                'type_mime' => 'application/pdf',
            ]
        );

        // Avenant proche de l'expiration (pour tester une future alerte d'échéance —
        // non stockée en base, voir note de classe).
        Document::updateOrCreate(
            ['client_id' => $client->id, 'nom_original' => 'Avenant_SLA_Maintenance_ABC_2026.pdf'],
            [
                'type_document' => 'Contrat',
                'chemin' => $this->ecrirePdf('documents/sla_avenant_abc.pdf', 'pdf.contrat', [
                    'company' => $this->company(),
                    'client' => $client,
                    'contrat' => [
                        'type' => 'Avenant — Renforcement SLA',
                        'date_debut' => now()->subDays(18),
                        'date_fin' => now()->addDays(12),
                        'proche_expiration' => true,
                        'reconduction' => 'Alignée sur le contrat principal CTR-ABC-2026',
                        'sla' => $sla,
                        'clauses' => [
                            ['titre' => 'Objet de l\'avenant', 'texte' => "Le présent avenant renforce les délais d'intervention garantis sur le chantier Entrepôt Logistique Mohammedia (CHT-ABC-02), sans modification des autres clauses du contrat principal."],
                        ],
                    ],
                    'documentTitle' => 'Avenant SLA',
                    'documentReference' => 'AVN-ABC-2026-01',
                ]),
                'type_mime' => 'application/pdf',
            ]
        );
    }

    /**
     * @return array{0: Devis, 1: Devis} [devis accepté existant/créé, devis envoyé]
     */
    private function seedDevis(Client $client, ?int $commercialId): array
    {
        $devisAccepte = Devis::where('client_id', $client->id)->where('statut', 'Accepté')->first();

        if (!$devisAccepte) {
            $devisAccepte = Devis::create([
                'reference' => $this->referenceDevis(),
                'client_id' => $client->id,
                'commercial_id' => $commercialId,
                'statut' => 'Accepté',
                'date_emission' => now()->subDays(20),
                'date_expiration' => now()->addDays(10),
                'taux_tva' => 20,
                'observations' => 'Installation fibre optique — Hangar A.',
            ]);
            $this->creerLignes($devisAccepte, [
                ['designation' => 'Installation fibre optique', 'quantite' => 1, 'prix_unitaire' => 8700],
                ['designation' => 'Configuration switchs réseau', 'quantite' => 2, 'prix_unitaire' => 870],
            ]);
        }

        $devisEnvoye = Devis::updateOrCreate(
            ['client_id' => $client->id, 'reference' => 'DEV-2026-DEMO-01'],
            [
                'commercial_id' => $commercialId,
                'statut' => 'Envoyé',
                'date_emission' => now()->subDays(3),
                'date_expiration' => now()->addDays(27),
                'taux_tva' => 20,
                'observations' => 'Extension caméras de surveillance — Siège Casablanca.',
            ]
        );
        $this->creerLignes($devisEnvoye, [
            ['designation' => 'Caméra de surveillance IP', 'quantite' => 4, 'prix_unitaire' => 1200],
        ]);

        Devis::updateOrCreate(
            ['client_id' => $client->id, 'reference' => 'DEV-2026-DEMO-02'],
            [
                'commercial_id' => $commercialId,
                'statut' => 'Refusé',
                'date_emission' => now()->subDays(45),
                'date_expiration' => now()->subDays(15),
                'taux_tva' => 20,
                'montant_ht' => 3200,
                'montant_tva' => 640,
                'montant_ttc' => 3840,
                'observations' => 'Refusé par le client — budget non validé pour cet exercice.',
            ]
        );

        return [$devisAccepte, $devisEnvoye];
    }

    private function creerLignes(Devis $devis, array $lignes): void
    {
        if ($devis->lignes()->exists()) {
            return;
        }

        $montantHt = 0;
        foreach ($lignes as $ligne) {
            $montantHt += $ligne['quantite'] * $ligne['prix_unitaire'];
            DevisLigne::create([
                'devis_id' => $devis->id,
                'designation' => $ligne['designation'],
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'montant_ht' => $ligne['quantite'] * $ligne['prix_unitaire'],
            ]);
        }

        $montantTva = $montantHt * ((float) $devis->taux_tva / 100);
        $devis->update([
            'montant_ht' => $montantHt,
            'montant_tva' => $montantTva,
            'montant_ttc' => $montantHt + $montantTva,
        ]);
    }

    private function seedFactures(Devis $devisAccepte, Client $client, ?int $commercialId): void
    {
        if (!$devisAccepte->factures()->exists()) {
            try {
                $facture = app(FactureService::class)->createFromDevis($devisAccepte);
                $facture->update([
                    'montant_paye' => $facture->montant_ttc,
                    'statut' => Facture::STATUT_PAYEE,
                    'date_emission' => now()->subDays(15),
                    'date_echeance' => now()->addDays(15),
                ]);
            } catch (\RuntimeException) {
                // Facture déjà existante pour ce devis : rien à faire.
            }
        }

        Facture::updateOrCreate(
            ['client_id' => $client->id, 'reference' => 'FAC-2026-DEMO-01'],
            [
                'devis_id' => null,
                'commercial_id' => $commercialId,
                'statut' => Facture::STATUT_ENVOYEE,
                'date_emission' => now()->subDays(5),
                'date_echeance' => now()->addDays(25),
                'taux_tva' => 20,
                'montant_ht' => 2400,
                'montant_tva' => 480,
                'montant_ttc' => 2880,
                'montant_paye' => 0,
                'observations' => 'Facture intermédiaire — maintenance trimestrielle.',
            ]
        );
    }

    private function seedDocumentsTechniques(?\App\Models\Rapport $rapport, ?\App\Models\Chantier $chantier1, ?\App\Models\Chantier $chantier2): void
    {
        if ($rapport) {
            // Réutilise le VRAI template de rapport (resources/views/rapports/pdf.blade.php,
            // déjà utilisé par RapportController) plutôt que d'en dupliquer un nouveau —
            // le document de démonstration est donc un rapport réellement structuré,
            // avec les vraies données de l'intervention INT-ABC-001.
            $rapport->loadMissing(['intervention.chantier.client', 'intervention.technicien', 'intervention.emplacement', 'intervention.typeIntervention', 'intervention.materiaux.materiau', 'photos', 'reponses.question']);

            Document::updateOrCreate(
                ['rapport_id' => $rapport->id, 'nom_original' => 'Fiche_Intervention_INT-ABC-001.pdf'],
                [
                    'type_document' => 'Fiche technique',
                    'chemin' => $this->ecrirePdf('documents/fiche_int_abc_001.pdf', 'rapports.pdf', ['rapport' => $rapport]),
                    'type_mime' => 'application/pdf',
                ]
            );

            Document::updateOrCreate(
                ['rapport_id' => $rapport->id, 'nom_original' => 'Photo_Camera_Installee_Facade.jpg'],
                [
                    'type_document' => 'Fiche technique',
                    'chemin' => self::ecrireFichierDemo('documents/photo_camera_facade.jpg', 'jpg'),
                    'type_mime' => 'image/jpeg',
                ]
            );
        }

        if ($chantier1) {
            // Cette pièce était auparavant rattachée par client_id seul (chantier_id NULL) :
            // supprimée pour éviter un doublon visuel avant recréation rattachée au chantier,
            // ce qui est plus précis (elle concerne spécifiquement CHT-ABC-01).
            Document::where('client_id', $chantier1->client_id)
                ->whereNull('chantier_id')
                ->where('nom_original', 'Plan_Installation_Caméras.pdf')
                ->delete();

            Document::updateOrCreate(
                ['chantier_id' => $chantier1->id, 'nom_original' => 'Plan_Installation_Cameras.pdf'],
                [
                    'type_document' => 'Fiche technique',
                    'chemin' => $this->ecrirePdf('documents/plan_cameras.pdf', 'pdf.autre', [
                        'company' => $this->company(),
                        'documentTitle' => 'Plan d\'Installation Caméras',
                        'documentReference' => 'PLN-ABC-CAM-01',
                        'destinataire' => 'Société ABC — Siège Casablanca',
                        'sousTitre' => 'Implantation des caméras de surveillance',
                        'corps' => "Ce document décrit l'implantation des caméras de surveillance installées sur le site du Siège Social Casablanca (CHT-ABC-01), dans le cadre de l'intervention INT-ABC-001.\n\nQuatre caméras IP ont été positionnées aux points d'accès principaux (entrée, parking, quai de livraison, façade) avec recouvrement des angles morts identifiés lors de l'audit préalable.",
                        'tableau' => [
                            'colonnes' => ['Emplacement', 'Modèle', 'Résolution', 'Vision nocturne'],
                            'lignes' => [
                                ['Entrée principale', 'IP-Dome 4MP', '4 MP', 'Oui (30m)'],
                                ['Parking', 'IP-Bullet 4MP', '4 MP', 'Oui (30m)'],
                                ['Quai de livraison', 'IP-Bullet 4MP', '4 MP', 'Oui (30m)'],
                                ['Façade', 'IP-Dome 4MP', '4 MP', 'Oui (30m)'],
                            ],
                        ],
                    ]),
                    'type_mime' => 'application/pdf',
                ]
            );
        }

        // INT-ABC-003 n'a pas encore de rapport (Planifiée) : rattachement au
        // chantier, pas à l'intervention (voir note de classe).
        if ($chantier2) {
            Document::updateOrCreate(
                ['chantier_id' => $chantier2->id, 'nom_original' => 'Plan_Reseau_Wifi_Entrepot_Mohammedia.pdf'],
                [
                    'type_document' => 'Fiche technique',
                    'chemin' => $this->ecrirePdf('documents/plan_wifi_entrepot.pdf', 'pdf.autre', [
                        'company' => $this->company(),
                        'documentTitle' => 'Plan Réseau Wi-Fi',
                        'documentReference' => 'PLN-ABC-WIFI-02',
                        'destinataire' => 'Société ABC — Entrepôt Logistique Mohammedia',
                        'sousTitre' => 'Couverture Wi-Fi industrielle — préparation audit',
                        'corps' => "Ce document préparatoire recense les points d'accès Wi-Fi existants sur l'Entrepôt Logistique Mohammedia (CHT-ABC-02), en amont de l'audit préventif programmé (INT-ABC-003).\n\nL'objectif de l'audit est de vérifier la couverture du Hangar A et d'identifier les zones de recouvrement insuffisant en périphérie du bâtiment.",
                        'tableau' => [
                            'colonnes' => ['Zone', 'Point d\'accès', 'État constaté'],
                            'lignes' => [
                                ['Hangar A — entrée', 'AP-01', 'Opérationnel'],
                                ['Hangar A — fond', 'AP-02', 'Signal faible (à vérifier)'],
                                ['Zone de stockage', 'AP-03', 'Opérationnel'],
                            ],
                        ],
                    ]),
                    'type_mime' => 'application/pdf',
                ]
            );
        }
    }

    private function seedAutre(Client $client): void
    {
        Document::updateOrCreate(
            ['client_id' => $client->id, 'nom_original' => 'Attestation_Assurance_Responsabilite_Civile.pdf'],
            [
                'type_document' => 'Autre',
                'chemin' => $this->ecrirePdf('documents/attestation_assurance.pdf', 'pdf.autre', [
                    'company' => $this->company(),
                    'documentTitle' => 'Attestation d\'Assurance',
                    'documentReference' => 'ASS-2026-RC-014',
                    'destinataire' => $client->nom,
                    'sousTitre' => 'Responsabilité Civile Professionnelle',
                    'corps' => "Nous soussignés, {$this->company()['name']}, attestons être couverts par une police d'assurance Responsabilité Civile Professionnelle en cours de validité, garantissant les dommages pouvant résulter de nos interventions techniques chez nos clients, dont " . $client->nom . ".\n\nCette attestation est délivrée à la demande du client pour les besoins de ses propres formalités administratives et n'a pas valeur contractuelle.",
                ]),
                'type_mime' => 'application/pdf',
            ]
        );
    }

    private function referenceDevis(): string
    {
        $prefix = 'DEV-' . now()->format('Y') . '-';
        $n = 1;
        do {
            $ref = $prefix . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
            $n++;
        } while (Devis::where('reference', $ref)->exists());

        return $ref;
    }

    /**
     * Rend un template Blade en PDF (via dompdf, comme le reste de la plateforme)
     * et l'écrit sur le disque public, pour que "Voir en aperçu" / "Télécharger"
     * fonctionnent réellement en démo avec un vrai document structuré.
     */
    private function ecrirePdf(string $path, string $view, array $data): string
    {
        $pdf = Pdf::loadView($view, $data);
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Écrit un fichier image factice mais valide sur le disque public (photo de
     * démonstration — les PDF passent désormais par ecrirePdf() ci-dessus).
     */
    public static function ecrireFichierDemo(string $path, string $type): string
    {
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $type === 'jpg' ? self::pixelJpeg() : self::minimalPdf());
        }

        return $path;
    }

    private static function minimalPdf(): string
    {
        return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj\n4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n5 0 obj<</Length 70>>stream\nBT /F1 12 Tf 20 100 Td (Document de demonstration - TechniTrack) Tj ET\nendstream\nendobj\nxref\n0 6\ntrailer<</Size 6/Root 1 0 R>>\n%%EOF";
    }

    private static function pixelJpeg(): string
    {
        // JPEG blanc minimal 1x1 valide (placeholder photo de démonstration).
        return base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAACP/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AVN//2Q==');
    }
}
