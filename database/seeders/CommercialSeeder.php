<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CommercialSeeder extends Seeder
{
    public function run(): void
    {
        // Vider le cache des permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // S'assurer que les permissions nécessaires existent
        $permManageClients   = Permission::firstOrCreate(['name' => 'manage-clients']);
        $permManageChantiers = Permission::firstOrCreate(['name' => 'manage-chantiers']);

        // Créer le rôle Commercial
        $commercial = Role::firstOrCreate([
            'name'       => 'Commercial',
            'guard_name' => 'web',
        ]);

        // Assigner les permissions au rôle Commercial
        $commercial->syncPermissions([
            $permManageClients,
            $permManageChantiers,
        ]);

        // Créer un compte Commercial de démonstration
        $user = User::firstOrCreate(
            ['email' => 'commercial@intervention.ma'],
            [
                'name'      => 'Dupont',
                'prenom'    => 'Jean',
                'telephone' => '0600000002',
                'adresse'   => '12 Rue des Orangers, Bourgogne, Casablanca',
                'poste'     => 'Commercial Senior',
                'departement' => 'Ventes Télécom',
                'zone_geographique' => 'Casablanca-Settat',
                'date_entree' => '2023-03-01',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Assigner le rôle Commercial
        if (!$user->hasRole('Commercial')) {
            $user->assignRole('Commercial');
        }

        $this->command->info('✅ Rôle Commercial créé avec les permissions manage-clients et manage-chantiers.');
        $this->command->info('✅ Compte commercial@intervention.ma créé (mot de passe : password).');
    }
}
