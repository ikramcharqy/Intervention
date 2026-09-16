<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TechnicienSeeder extends Seeder
{
    public function run(): void
    {
        // name = nom de famille, prenom = prénom — même convention que
        // AdminSeeder/CommercialSeeder (ex: name: 'Chahmi', prenom: 'Mohamed').
        // Ces deux comptes avaient les valeurs inversées (prenom contenait un
        // nom de famille), d'où l'affichage "Amine" au lieu de "Yassine" sur
        // le Profil pour yassine.tech@test.com (la logique d'affichage
        // préfère `prenom`, correctement — c'était la donnée elle-même qui
        // était inversée).
        // Email comme clé de correspondance (pas tout le tableau) — l'appel
        // précédent passait un seul tableau à updateOrCreate(), qui le traite
        // alors ENTIÈREMENT comme critère de recherche ; `password` étant
        // re-haché à chaque exécution (valeur différente à chaque fois), la
        // recherche ne matchait donc plus jamais après le premier run, et
        // chaque ré-exécution créait un compte en double au lieu de mettre à
        // jour le compte existant — ce qui aurait empêché la correction
        // name/prenom ci-dessus de s'appliquer au compte réellement utilisé.
        $technicien1 = User::updateOrCreate(
            ['email' => 'ahmed.tech@test.com'],
            [
                'name' => 'Benali',
                'prenom' => 'Ahmed',
                'telephone' => '0612345678',
                'adresse' => 'Casablanca',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $technicien1->assignRole('technicien');

        $technicien2 = User::updateOrCreate(
            ['email' => 'yassine.tech@test.com'],
            [
                'name' => 'Amine',
                'prenom' => 'Yassine',
                'telephone' => '0623456789',
                'adresse' => 'Rabat',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $technicien2->assignRole('technicien');
    }
}