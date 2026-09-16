<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Étape 6.2 : nom de démonstration corrigé ("shady" laissait penser à un compte
        // suspect dans le journal d'audit) — remplacé par un nom professionnel cohérent,
        // le domaine @intervention.ma était déjà correct et conservé tel quel.
        // Étape 2 : "name" ne doit contenir que le nom de famille — "prenom" porte le
        // prénom séparément (bug corrigé : "Mohamed Chahmi" + prenom "Mohamed" affichait
        // "Mohamed Chahmi Mohamed" partout où les deux champs sont concaténés).
        $admin = User::firstOrCreate(
            ['email' => 'adminShaMoh@intervention.ma',],
            [
            'name'=> 'Chahmi',
            'prenom'=> 'Mohamed',
            'telephone' => '0612345678',
            'password'=> Hash::make('admin@123'),
            'is_active' => true,
            'adresse' =>'Casablanca, Maroc',
        ]);

        $admin->syncRoles(['admin']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@intervention.ma'],
            [
            'name'=> 'Admin',
            'prenom'=> 'Super',
            'telephone' => '0661223344',
            'password'=> Hash::make('password'),
            'is_active' => true,
            'adresse' =>'Casablanca, Maroc',
        ]);

        $superAdmin->syncRoles(['Super Admin']);
    }
}
