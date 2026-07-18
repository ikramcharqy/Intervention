<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table intervention_historiques.
     * Trace toutes les transitions de statut d'une intervention à des fins
     * d'audit, de reporting et de détection d'anomalies (ex: retours en arrière).
     */
    public function up(): void
    {
        Schema::create('intervention_historiques', function (Blueprint $table) {
            $table->id();

            // Intervention concernée
            $table->foreignId('intervention_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Utilisateur ayant déclenché la transition (admin, technicien)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Statut avant la transition
            $table->string('statut_avant');

            // Nouveau statut après la transition
            $table->string('statut_apres');

            // Motif ou commentaire facultatif
            $table->text('commentaire')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Annule la création.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_historiques');
    }
};
