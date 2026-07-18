<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chantier;
use App\Models\Client;

class ChantierSeeder extends Seeder
{
    public function run(): void
    {

        $client = Client::first();


        Chantier::UpdateOrCreate([
            'client_id' => $client->id,
            'code_chantier' => 'CHT001',
            'nom' => 'Installation système sécurité',
            'type_local' => 'Usine',
            'adresse' => 'Zone industrielle Casablanca',
            'ville' => 'Casablanca',
            'latitude' => 33.5731,
            'longitude' => 7.5898,
            'responsable' => 'Hatim Ammour',
            'telephone_responsable' => '0612345678',
            'email_responsable' => 'hatimammour234@gmail.com',
            'description' => 'Installation équipements',
            'is_active' => true,
        ]);


        Chantier::UpdateOrCreate([
            'client_id' => $client->id,
            'code_chantier' => 'CHT002',
            'nom' => 'Maintenance bureau',
            'type_local' => 'Bureau',
            'adresse' => 'Centre ville Rabat',
            'ville' => 'Rabat',
            'latitude' => 34.0209,
            'longitude' => 6.8416,
            'responsable' => 'Ali',
            'telephone_responsable' => '0612345678',
            'email_responsable' => 'alirefik234@gmail.com',
            'description' => 'Maintenance annuelle',
            'is_active' => true,
        ]);

    }
}