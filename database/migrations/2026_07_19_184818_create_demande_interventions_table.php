<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_interventions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('commercial_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chantier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('type_intervention_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('priorite')->default('Normale'); // Faible, Normale, Haute, Urgente
            $table->string('statut')->default('En attente'); // En attente, Acceptée, Refusée, Planifiée
            
            $table->string('objet');
            $table->text('description')->nullable();
            
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_interventions');
    }
};
