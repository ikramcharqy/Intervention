<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //vider cache des permissions et des rôles pour éviter les doublons
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //Permissions Admin et Technicien
        $permissions = [
            'manage-users',
            'manage-clients',
            'manage-chantiers',
            'manage-interventions',
            'manage-forms',
            'validate-reports',
            'view-own-interventions',
            'start-intervention',
            'finish-intervention',
            'submit-report',
            'upload-media',
            'scan-qr',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        //Création des rôles
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $adminAlt = Role::firstOrCreate([
            'name' => 'Administrateur',
            'guard_name' => 'web'
        ]);

        $technicien = Role::firstOrCreate([
            'name' => 'technicien',
            'guard_name' => 'web'
        ]);

        $technicienAlt = Role::firstOrCreate([
            'name' => 'Technicien',
            'guard_name' => 'web'
        ]);

        $commercial = Role::firstOrCreate([
            'name' => 'Commercial',
            'guard_name' => 'web'
        ]);

        $clientRole = Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'web'
        ]);

        $adminAlt->SyncPermissions(Permission::all());
        $technicienAlt->SyncPermissions([
            'view-own-interventions',
            'start-intervention',
            'finish-intervention',
            'submit-report',
            'upload-media',
            'scan-qr']);

        //Donner toutes les permissions à superAdmin et admin
        $superAdmin->SyncPermissions(Permission::all());
        $admin->SyncPermissions(Permission::all());
        //Synchroniser les permissions du technicien


        //Permission du technicien
        $technicien->SyncPermissions([
            'view-own-interventions',
            'start-intervention',
            'finish-intervention',
            'submit-report',
            'upload-media',
            'scan-qr']);
    }
}
