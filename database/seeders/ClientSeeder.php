<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::UpdateOrCreate([
            'code_client' => 'CL001',
            'type_client' => 'Entreprise',
            'nom' => 'Société ABC',
            'nom_contact' => 'Mohamed Ali',
            'telephone' => '0611111111',
            'email' => 'contact@abc.com',
            'adresse_facturation' => 'Casablanca',
            'ville' => 'Casablanca',
            'pays' => 'Morocco',
            'observations' => 'Client important',
            'is_active' => true,
        ]);


        Client::UpdateOrCreate([
            'code_client' => 'CL002',
            'type_client' => 'Particulier',
            'nom' => 'Fatima Zahra',
            'nom_contact' => 'Fatima',
            'telephone' => '0622222222',
            'email' => 'fatima@test.com',
            'adresse_facturation' => 'Rabat',
            'ville' => 'Rabat',
            'pays' => 'Morocco',
            'is_active' => true,
        ]);
    }
}