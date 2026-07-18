<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Materiau;

class MateriauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiaux = [
            ['nom'=>'Câble RJ45','unite'=>'m'],

            ['nom'=>'Fibre Optique','unite'=>'m'],

            ['nom'=>'Connecteur RJ45','unite'=>'pièce'],

            ['nom'=>'Switch','unite'=>'pièce'],

            ['nom'=>'Routeur','unite'=>'pièce'],

            ['nom'=>'Point d\'accès WiFi','unite'=>'pièce'],

            ['nom'=>'Caméra IP','unite'=>'pièce'],

            ['nom'=>'Goulotte','unite'=>'m'],

        ];

        foreach($materiaux as $materiau){
            Materiau::firstOrCreate(
            ['nom' => $materiau['nom']],
            [
                'unite' => $materiau['unite'],
                'is_active' => true,
            ]
        );
    }
}
  
}
