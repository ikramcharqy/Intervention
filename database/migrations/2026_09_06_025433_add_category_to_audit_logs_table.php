<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Étape 2 de la restructuration Super Admin : sépare le "Journal de Sécurité &
     * Gouvernance" du "Journal d'Activité Métier". Colonne additive, non destructive —
     * les lignes existantes sont reclassées par leur `module` déjà connu, sans changer
     * leur signification.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('category', 20)->default('security')->after('severity');
        });

        DB::table('audit_logs')
            ->whereIn('module', ['Interventions', 'Commercial', 'Devis', 'Prospects'])
            ->update(['category' => 'business']);
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
