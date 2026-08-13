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
        Schema::create('devis', function (Blueprint $table) {

            table->id();
            $table->string('reference')->unique();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            $table->foreignId('commercial_id')->nullable()->constrained('users');
            $table->string('objet');
            $table->decimal('montant_ht', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2)->default(0);
            $table->enum('statut', ['Brouillon', 'Envoyé', 'Accepté', 'Refusé'])->default('Brouillon');
            $table->date('date_emission');
            $table->date('date_validite')->nullable();
            $table->text('conditions_reglement')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
