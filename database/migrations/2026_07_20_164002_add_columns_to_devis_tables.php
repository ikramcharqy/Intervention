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
            $table->string('reference')->unique()->nullable();
            $table->foreignId('prospect_id')->nullable()->constrained('prospects')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('commercial_id')->constrained('users')->cascadeOnDelete();
            $table->string('statut')->default('Brouillon'); // Brouillon, Envoyé, Accepté, Refusé
            $table->date('date_emission')->nullable();
            $table->date('date_expiration')->nullable();
            $table->decimal('taux_tva', 5, 2)->default(20.00);
            $table->decimal('montant_ht', 15, 2)->default(0.00);
            $table->decimal('montant_tva', 15, 2)->default(0.00);
            $table->decimal('montant_ttc', 15, 2)->default(0.00);
            $table->text('observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['commercial_id']);
            $table->dropColumn([
                'reference',
                'prospect_id',
                'client_id',
                'commercial_id',
                'statut',
                'date_emission',
                'date_expiration',
                'taux_tva',
                'montant_ht',
                'montant_tva',
                'montant_ttc',
                'observations',
            ]);
        });
    }
};
