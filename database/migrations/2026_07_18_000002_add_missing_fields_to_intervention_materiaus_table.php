<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes manquantes à intervention_materiaus :
     *  - is_valide : validation des matériaux par l'admin (déclaré dans le Model, absent en DB)
     *  - unite     : unité propre à l'utilisation (peut différer de l'unité de base du matériau)
     */
    public function up(): void
    {
        Schema::table('intervention_materiaus', function (Blueprint $table) {

            // Unité de mesure pour cette utilisation spécifique
            $table->string('unite')
                  ->nullable()
                  ->after('quantite');

            // Validation admin des matériaux consommés
            $table->boolean('is_valide')
                  ->default(false)
                  ->after('commentaire');
        });
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        Schema::table('intervention_materiaus', function (Blueprint $table) {
            $table->dropColumn(['unite', 'is_valide']);
        });
    }
};
