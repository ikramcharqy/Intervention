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
        Schema::table('interventions', function (Blueprint $table) {
            if (!Schema::hasColumn('interventions', 'intervention_parente_id')) {
                $table->foreignId('intervention_parente_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('interventions')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('interventions', 'motif_suspension')) {
                $table->text('motif_suspension')->nullable()->after('motif_annulation');
            }

            if (!Schema::hasColumn('interventions', 'motif_report')) {
                $table->text('motif_report')->nullable()->after('motif_suspension');
            }

            if (!Schema::hasColumn('interventions', 'resultat_intervention')) {
                $table->string('resultat_intervention', 100)->nullable()->after('statut');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            if (Schema::hasColumn('interventions', 'intervention_parente_id')) {
                $table->dropConstrainedForeignId('intervention_parente_id');
            }
            $table->dropColumn(['motif_suspension', 'motif_report', 'resultat_intervention']);
        });
    }
};
