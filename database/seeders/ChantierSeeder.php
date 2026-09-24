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

        // Clé de correspondance = code_chantier (unique) uniquement : le
        // reste des champs va dans le second tableau (valeurs mises à jour),
        // sinon updateOrCreate() les traite comme critères de recherche —
        // toute correction future d'un champ (ex. latitude/longitude) ne
        // matcherait plus la ligne existante et tenterait une insertion en
        // doublon sur code_chantier (unique), comme observé lors de la
        // correction du signe de la longitude ci-dessous.
        Chantier::updateOrCreate(
            ['code_chantier' => 'CHT001'],
            [
                'client_id' => $client->id,
                'nom' => 'Installation système sécurité',
                'type_local' => 'Usine',
                'adresse' => 'Zone industrielle Casablanca',
                'ville' => 'Casablanca',
                // Casablanca est à l'ouest du méridien de Greenwich : la
                // longitude doit être négative (corrigé — était +7.5898,
                // ce qui plaçait le point en Méditerranée orientale au lieu
                // du Maroc sur les cartes Leaflet/flutter_map).
                'latitude' => 33.5731,
                'longitude' => -7.5898,
                'responsable' => 'Hatim Ammour',
                'telephone_responsable' => '0612345678',
                'email_responsable' => 'hatimammour234@gmail.com',
                'description' => 'Installation équipements',
                'is_active' => true,
            ]
        );

        Chantier::updateOrCreate(
            ['code_chantier' => 'CHT002'],
            [
                'client_id' => $client->id,
                'nom' => 'Maintenance bureau',
                'type_local' => 'Bureau',
                'adresse' => 'Centre ville Rabat',
                'ville' => 'Rabat',
                // Idem : Rabat est aussi à l'ouest de Greenwich (corrigé —
                // était +6.8416).
                'latitude' => 34.0209,
                'longitude' => -6.8416,
                'responsable' => 'Ali',
                'telephone_responsable' => '0612345678',
                'email_responsable' => 'alirefik234@gmail.com',
                'description' => 'Maintenance annuelle',
                'is_active' => true,
            ]
        );
    }
}
