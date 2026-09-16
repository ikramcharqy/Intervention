<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Options pour le type "Nombre"
            $table->decimal('nombre_min', 12, 2)->nullable()->after('valeur_par_defaut');
            $table->decimal('nombre_max', 12, 2)->nullable()->after('nombre_min');
            $table->string('nombre_unite', 20)->nullable()->after('nombre_max');

            // Option pour les types "Photo" et "Document"
            $table->unsignedTinyInteger('fichiers_max')->nullable()->after('nombre_unite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['nombre_min', 'nombre_max', 'nombre_unite', 'fichiers_max']);
        });
    }
};
