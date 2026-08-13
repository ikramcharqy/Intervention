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
        $admin = User::firstOrCreate(
            ['email' => 'adminShaMoh@intervention.ma',],
            [
            'name'=> 'shady',
            'prenom'=> 'mohamed',
            'telephone' => '0612345678',
            'password'=> Hash::make('admin@123'),
            'is_active' => true,
            'adresse' =>'Casablanca, Maroc',
        ]);

        $admin->syncRoles(['admin']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@intervention.ma'],
            [
            'name'=> 'Super',
            'prenom'=> 'Admin',
            'telephone' => '0600000000',
            'password'=> Hash::make('password'),
            'is_active' => true,
            'adresse' =>'Casablanca, Maroc',
        ]);

        $superAdmin->syncRoles(['Super Admin']);
    }
}
