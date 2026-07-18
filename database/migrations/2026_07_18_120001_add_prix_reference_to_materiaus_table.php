<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes manquantes à materiaus :
     *  - reference   : référence unique du matériau (code article, SKU, etc.)
     *  - prix_unitaire : prix unitaire HT (pour calcul du coût dans les interventions)
     */
    public function up(): void
    {
        Schema::table('materiaus', function (Blueprint $table) {
            // Référence article (optionnelle mais unique si renseignée)
            $table->string('reference')
                  ->nullable()
                  ->unique()
                  ->after('id');

            // Prix unitaire HT
            $table->decimal('prix_unitaire', 10, 2)
                  ->nullable()
                  ->default(0)
                  ->after('unite');
        });
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        Schema::table('materiaus', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn(['reference', 'prix_unitaire']);
        });
    }
};
