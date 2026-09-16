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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('devis_id')->nullable()->constrained('devis')->nullOnDelete();
            $table->foreignId('commercial_id')->constrained('users')->cascadeOnDelete();
            $table->string('statut')->default('Brouillon'); // Brouillon, Envoyée, Payée, Partiellement payée, En retard, Annulée
            $table->date('date_emission')->nullable();
            $table->date('date_echeance')->nullable();
            $table->decimal('taux_tva', 5, 2)->default(20.00);
            $table->decimal('montant_ht', 15, 2)->default(0.00);
            $table->decimal('montant_tva', 15, 2)->default(0.00);
            $table->decimal('montant_ttc', 15, 2)->default(0.00);
            $table->decimal('montant_paye', 15, 2)->default(0.00);
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
