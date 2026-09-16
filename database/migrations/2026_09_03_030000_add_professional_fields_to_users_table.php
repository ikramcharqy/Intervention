<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('poste')->nullable()->after('prenom');
            $table->string('departement')->nullable()->after('poste');
            $table->string('zone_geographique')->nullable()->after('departement');
            $table->date('date_entree')->nullable()->after('zone_geographique');
            // Préférences de notification par utilisateur (clé => bool), ex:
            // {"intervention_updates": true, "nouveaux_prospects_assignes": true, "relances_devis": true}.
            $table->json('notification_preferences')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['poste', 'departement', 'zone_geographique', 'date_entree', 'notification_preferences']);
        });
    }
};
