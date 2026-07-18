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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->string('code_intervention')->unique();

            $table->foreignId('chantier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('emplacement_id')->constrained()->cascadeOnDelete();

            $table->foreignId('technicien_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('type_intervention_id')->constrained()->cascadeOnDelete();

            $table->enum('priorite',[
                'Faible',
                'Normale',
                'Haute',
                'Urgente'
            ]);

            $table->enum('statut',[
                'Planifiee',
                'En cours',
                'Suspendue',
                'Terminee',
                'Annulee'
            ]);

            $table->dateTime('date_prevue_debut');

            $table->dateTime('date_prevue_fin');

            $table->dateTime('date_reelle_debut')->nullable();

            $table->dateTime('date_reelle_fin')->nullable();

            $table->integer('duree_prevue')->nullable();

            $table->integer('duree_reelle')->nullable();

            $table->integer('pourcentage_global')->default(0);

            $table->text('description')->nullable();

            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
