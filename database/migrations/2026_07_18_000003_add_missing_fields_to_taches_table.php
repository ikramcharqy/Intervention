<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes manquantes à la table taches :
     *  - duree_estimee  : durée estimée de la tâche en minutes (déclarée dans Model, absente en DB)
     *  - is_obligatoire : si true, la tâche doit être terminée avant soumission validation
     */
    public function up(): void
    {
        Schema::table('taches', function (Blueprint $table) {

            // Durée estimée en minutes pour cette tâche
            $table->integer('duree_estimee')
                  ->nullable()
                  ->after('ordre');

            // Tâche obligatoire — bloque la soumission si non complétée
            $table->boolean('is_obligatoire')
                  ->default(false)
                  ->after('duree_estimee');
        });
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            $table->dropColumn(['duree_estimee', 'is_obligatoire']);
        });
    }
};
