<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table du module de suivi GPS continu pendant une intervention.
     * Indépendante de "tracking_sessions" (pointage ponctuel début/fin).
     */
    public function up(): void
    {
        Schema::create('gps_tracking_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intervention_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('technicien_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Horodatage de début/fin de la session de suivi GPS
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();

            // Distance cumulée parcourue durant la session (en mètres)
            $table->decimal('distance_metres', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_tracking_sessions');
    }
};
