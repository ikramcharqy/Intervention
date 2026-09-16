<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            CommercialSeeder::class,
            TypeInterventionSeeder::class,
            TacheSeeder::class,
            MateriauSeeder::class,
            ClientSeeder::class,
            TechnicienSeeder::class,
            ChantierSeeder::class,
            EmplacementSeeder::class,
            DemoDataSeeder::class,
            FormulaireSeeder::class,
            DocumentSeeder::class,
            NotificationSeeder::class,
            TechnicienDemoDataSeeder::class,
            // Filet de sécurité : rattrape toute intervention "Terminée" restée
            // sans Rapport (impasse UX "Voir le rapport" → 404), quel que soit
            // le seeder ou la création manuelle qui l'a laissée dans cet état.
            // Doit rester en dernier, après tous les seeders créant des
            // interventions "Terminée".
            OrphanedTermineeRapportSeeder::class,
            ]);
        //when run php artisan db:seed, it will call the run method of each seeder class in the order specified in the array.
        //  This allows you to seed your database with initial data for roles, permissions, admin users, intervention types, tasks, and materials.
    }
}