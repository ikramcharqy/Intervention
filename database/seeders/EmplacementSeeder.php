<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Emplacement;
use App\Services\ReferenceGeneratorService;
use Illuminate\Database\Seeder;

class EmplacementSeeder extends Seeder
{
    /**
     * Crée des emplacements de démonstration pour chaque chantier actif.
     */
    public function run(): void
    {
        $referenceGenerator = app(ReferenceGeneratorService::class);

        $defaultEmplacements = [
            ['nom' => 'Bâtiment Principal',  'description' => 'Zone principale du bâtiment.'],
            ['nom' => 'Parking Extérieur',    'description' => 'Espace de stationnement extérieur.'],
            ['nom' => 'Toiture',              'description' => 'Accès toiture et équipements en hauteur.'],
            ['nom' => 'Local Technique',      'description' => 'Salle des équipements techniques et électriques.'],
        ];

        Chantier::where('is_active', true)->each(function (Chantier $chantier) use ($defaultEmplacements, $referenceGenerator) {
            foreach ($defaultEmplacements as $data) {
                Emplacement::firstOrCreate(
                    [
                        'chantier_id' => $chantier->id,
                        'nom'         => $data['nom'],
                    ],
                    [
                        'description' => $data['description'],
                        // Format centralisé EMP-[INITIALES_CLIENT]-[NN] — plus
                        // le hex aléatoire d'origine, jamais mis à jour depuis
                        // l'introduction de ReferenceGeneratorService.
                        'qr_code'     => $referenceGenerator->generateEmplacementReference($chantier),
                        'is_active'   => true,
                    ]
                );
            }
        });
    }
}
