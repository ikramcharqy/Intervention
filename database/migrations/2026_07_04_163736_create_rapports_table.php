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
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id')->unique()->constrained()->cascadeOnDelete();

            $table->dateTime('date_debut');

            $table->dateTime('date_fin');

            $table->text('commentaire')->nullable();

            $table->string('signature_client')->nullable();

            $table->string('signature_technicien')->nullable();

            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};
