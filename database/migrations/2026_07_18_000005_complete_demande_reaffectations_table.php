<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Complète la table demande_reaffectations (précédemment vide).
     * Un technicien peut demander à être remplacé sur une intervention.
     * L'admin traite la demande et peut approuver ou refuser.
     */
    public function up(): void
    {
        Schema::table('demande_reaffectations', function (Blueprint $table) {

            // Intervention concernée par la demande
            $table->foreignId('intervention_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->after('id');

            // Technicien qui formule la demande
            $table->foreignId('technicien_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->after('intervention_id');

            // Admin qui traite la demande (nullable jusqu'au traitement)
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('technicien_id');

            // Nouveau technicien proposé par le demandeur (facultatif)
            $table->foreignId('nouveau_technicien_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('admin_id');

            // Motif de la demande (obligatoire)
            $table->text('motif')
                  ->after('nouveau_technicien_id');

            // Statut de traitement de la demande
            $table->enum('statut', ['En attente', 'Acceptee', 'Refusee'])
                  ->default('En attente')
                  ->after('motif');

            // Commentaire de l'admin lors du traitement
            $table->text('commentaire_admin')
                  ->nullable()
                  ->after('statut');

            // Date de traitement par l'admin
            $table->dateTime('date_traitement')
                  ->nullable()
                  ->after('commentaire_admin');
        });
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        Schema::table('demande_reaffectations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('intervention_id');
            $table->dropConstrainedForeignId('technicien_id');
            $table->dropConstrainedForeignId('admin_id');
            $table->dropConstrainedForeignId('nouveau_technicien_id');
            $table->dropColumn([
                'motif',
                'statut',
                'commentaire_admin',
                'date_traitement',
            ]);
        });
    }
};
