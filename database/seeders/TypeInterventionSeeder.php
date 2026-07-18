<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeIntervention;

class TypeInterventionSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['nom' => 'Installation Fibre', 'description' => 'Installation de la fibre optique'],
            ['nom' => 'Installation WiFi', 'description' => 'Installation et configuration WiFi'],
            ['nom' => 'Installation Caméra', 'description' => 'Installation des caméras'],
            ['nom' => 'Maintenance', 'description' => 'Maintenance préventive'],
            ['nom' => 'Réparation', 'description' => 'Réparation des équipements'],
            ['nom' => 'Configuration Routeur', 'description' => 'Configuration des routeurs'],
            ['nom' => 'Configuration Switch', 'description' => 'Configuration des switchs'],
        ];

        foreach ($types as $type) {
            TypeIntervention::firstOrCreate(
                ['nom' => $type['nom']],
                [
                    'description' => $type['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}