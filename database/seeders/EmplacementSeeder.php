<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Emplacement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmplacementSeeder extends Seeder
{
    /**
     * Crée des emplacements de démonstration pour chaque chantier actif.
     */
    public function run(): void
    {
        $defaultEmplacements = [
            ['nom' => 'Bâtiment Principal',  'description' => 'Zone principale du bâtiment.'],
            ['nom' => 'Parking Extérieur',    'description' => 'Espace de stationnement extérieur.'],
            ['nom' => 'Toiture',              'description' => 'Accès toiture et équipements en hauteur.'],
            ['nom' => 'Local Technique',      'description' => 'Salle des équipements techniques et électriques.'],
        ];

        Chantier::where('is_active', true)->each(function (Chantier $chantier) use ($defaultEmplacements) {
            foreach ($defaultEmplacements as $data) {
                Emplacement::firstOrCreate(
                    [
                        'chantier_id' => $chantier->id,
                        'nom'         => $data['nom'],
                    ],
                    [
                        'description' => $data['description'],
                        'qr_code'     => 'EMP-' . strtoupper(Str::random(10)),
                        'is_active'   => true,
                    ]
                );
            }
        });
    }
}
