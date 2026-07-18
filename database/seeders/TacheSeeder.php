<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tache;

class TacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taches = [
            'Tirage de câble',
            'Installation des équipements',
            'Configuration',
            'Tests',
            'Validation',
            'Nettoyage du chantier',
        ];

        foreach ($taches as $tache) {
            Tache::firstOrCreate(
                ['nom' => $tache],
                [
                    'is_active' => true,
                ]
            );
        }
    }
}
