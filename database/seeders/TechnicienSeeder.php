<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TechnicienSeeder extends Seeder
{
    public function run(): void
    {
        $technicien1=User::UpdateOrCreate([
            'name' => 'Ahmed',
            'prenom' => 'Benali',
            'email' => 'ahmed.tech@test.com',
            'telephone' => '0612345678',
            'adresse' => 'Casablanca',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $technicien1->assignRole('technicien');

        $technicien2=User::UpdateOrCreate([
            'name' => 'Yassine',
            'prenom' => 'Amine',
            'email' => 'yassine.tech@test.com',
            'telephone' => '0623456789',
            'adresse' => 'Rabat',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $technicien2->assignRole('technicien');
    }
}