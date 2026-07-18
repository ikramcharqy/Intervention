<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Points GPS successifs relevés durant une GpsTrackingSession.
     */
    public function up(): void
    {
        Schema::create('gps_tracking_points', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gps_tracking_session_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Instant de la mesure GPS (peut différer de created_at en cas d'envoi différé)
            $table->dateTime('captured_at');

            $table->timestamps();

            $table->index(['gps_tracking_session_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_tracking_points');
    }
};
