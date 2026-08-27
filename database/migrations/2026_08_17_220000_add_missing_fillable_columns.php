<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes déclarées dans les $fillable des modèles mais
     * absentes des migrations existantes.
     */
    public function up(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->integer('duree_reelle')->nullable()->after('pdf_path');
            $table->integer('pourcentage_global')->default(0)->after('duree_reelle');
            $table->string('statut_validation')->nullable()->after('pourcentage_global');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->string('nom_original')->nullable()->after('rapport_id');
            $table->string('type_photo')->nullable()->after('description');
            $table->decimal('latitude', 10, 7)->nullable()->after('type_photo');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->dateTime('date_prise')->nullable()->after('longitude');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->string('nom_original')->nullable()->after('rapport_id');
            $table->integer('duree')->nullable()->after('description');
            $table->decimal('latitude', 10, 7)->nullable()->after('duree');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->dateTime('date_prise')->nullable()->after('longitude');
        });

        Schema::table('reponses', function (Blueprint $table) {
            $table->date('reponse_date')->nullable()->after('reponse_fichier');
            $table->time('reponse_heure')->nullable()->after('reponse_date');
            $table->dateTime('reponse_datetime')->nullable()->after('reponse_heure');
            $table->boolean('reponse_boolean')->nullable()->after('reponse_datetime');
        });

        Schema::table('type_interventions', function (Blueprint $table) {
            $table->enum('mode_suivi_defaut', ['GPS', 'QR', 'NFC', 'Manuel'])
                  ->nullable()
                  ->after('duree_estimee');
        });

        Schema::table('intervention_taches', function (Blueprint $table) {
            $table->integer('ordre_execution')->nullable()->after('commentaire');
            $table->boolean('is_validee')->default(false)->after('ordre_execution');
        });

        Schema::table('materiaus', function (Blueprint $table) {
            $table->decimal('stock', 10, 2)->default(0)->after('prix_unitaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropColumn(['duree_reelle', 'pourcentage_global', 'statut_validation']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['nom_original', 'type_photo', 'latitude', 'longitude', 'date_prise']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['nom_original', 'duree', 'latitude', 'longitude', 'date_prise']);
        });

        Schema::table('reponses', function (Blueprint $table) {
            $table->dropColumn(['reponse_date', 'reponse_heure', 'reponse_datetime', 'reponse_boolean']);
        });

        Schema::table('type_interventions', function (Blueprint $table) {
            $table->dropColumn('mode_suivi_defaut');
        });

        Schema::table('intervention_taches', function (Blueprint $table) {
            $table->dropColumn(['ordre_execution', 'is_validee']);
        });

        Schema::table('materiaus', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
