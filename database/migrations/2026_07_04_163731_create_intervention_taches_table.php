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
        Schema::create('intervention_taches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intervention_id')->constrained()->cascadeOnDelete();

            $table->foreignId('tache_id')->constrained()->cascadeOnDelete();

            $table->enum('statut',[
                'Non commencee',
                'En cours',
                'Terminee'
            ]);

            $table->dateTime('date_debut')->nullable();

            $table->dateTime('date_fin')->nullable();

            $table->integer('duree')->nullable();

            $table->integer('pourcentage')->default(0);

            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_taches');
    }
};
