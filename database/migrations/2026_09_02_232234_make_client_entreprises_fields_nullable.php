<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ICE/IF/RC/Patente ne concernent qu'une partie des clients (type Entreprise) :
     * ces colonnes doivent être nullable pour ne pas bloquer l'insertion d'un client
     * Particulier (aucune ligne client_entreprises n'est créée pour lui, mais la
     * table doit rester structurellement cohérente si elle l'était par erreur).
     * Utilise du SQL brut (pas de Schema::table(...)->change()) : doctrine/dbal
     * n'est pas installé dans ce projet.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `client_entreprises` MODIFY COLUMN `ice` VARCHAR(15) NULL');
            DB::statement('ALTER TABLE `client_entreprises` MODIFY COLUMN `if` VARCHAR(20) NULL');
            DB::statement('ALTER TABLE `client_entreprises` MODIFY COLUMN `rc` VARCHAR(20) NULL');
            DB::statement('ALTER TABLE `client_entreprises` MODIFY COLUMN `patente` VARCHAR(20) NULL');
        }
    }

    public function down(): void
    {
        // Pas de rollback : repasser ces colonnes en NOT NULL romprait les lignes
        // existantes créées avec des valeurs nulles depuis cette migration.
    }
};
