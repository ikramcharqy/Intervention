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
            TypeInterventionSeeder::class,
            TacheSeeder::class,
            MateriauSeeder::class,
            ClientSeeder::class,
            TechnicienSeeder::class,
            ChantierSeeder::class,
            EmplacementSeeder::class,
            FormulaireSeeder::class,
            ]);
        //when run php artisan db:seed, it will call the run method of each seeder class in the order specified in the array.
        //  This allows you to seed your database with initial data for roles, permissions, admin users, intervention types, tasks, and materials.
    }
}