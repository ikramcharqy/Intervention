<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Étend l'enum statut pour inclure 'Acceptee' et 'Formulaire rempli'.
     * Le statut 'En attente validation' est retiré logiquement (bien qu'il restera dans l'enum MySQL pour ne pas casser l'existant).
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE interventions
                MODIFY COLUMN statut
                ENUM('Planifiee', 'Acceptee', 'En cours', 'Formulaire rempli', 'Suspendue', 'En attente validation', 'Terminee', 'Annulee')
                NOT NULL
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE interventions
                MODIFY COLUMN statut
                ENUM('Planifiee', 'En cours', 'Suspendue', 'En attente validation', 'Terminee', 'Annulee')
                NOT NULL
            ");
        }
    }
};
