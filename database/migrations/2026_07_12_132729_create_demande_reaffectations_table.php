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
        Schema::create('demande_reaffectations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intervention_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('technicien_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('raison');

            $table->text('commentaire')->nullable();

            $table->enum('statut',[
                'En attente',
                'Acceptée',
                'Refusée'
            ])->default('En attente');

            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('validated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_reaffectations');
    }
};
