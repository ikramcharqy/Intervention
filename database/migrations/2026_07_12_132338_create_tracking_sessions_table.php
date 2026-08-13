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
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intervention_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('technicien_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('mode',[
                'GPS',
                'QR_CODE',
                'NFC',
                'MANUEL'
            ]);

            $table->timestamp('started_at')->nullable();

            $table->timestamp('ended_at')->nullable();

            $table->decimal('start_latitude',10,7)->nullable();

            $table->decimal('start_longitude',10,7)->nullable();

            $table->decimal('end_latitude',10,7)->nullable();

            $table->decimal('end_longitude',10,7)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_sessions');
    }
};
