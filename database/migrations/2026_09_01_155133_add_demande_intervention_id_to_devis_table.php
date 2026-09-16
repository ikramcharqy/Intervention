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
        Schema::table('devis', function (Blueprint $table) {
            $table->foreignId('demande_intervention_id')->nullable()->after('prospect_id')
                ->constrained('demande_interventions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropForeign(['demande_intervention_id']);
            $table->dropColumn('demande_intervention_id');
        });
    }
};
