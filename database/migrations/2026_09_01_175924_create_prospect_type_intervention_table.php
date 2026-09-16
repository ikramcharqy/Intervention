<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_type_intervention', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('type_intervention_id')->constrained('type_interventions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['prospect_id', 'type_intervention_id'], 'prospect_type_intervention_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_type_intervention');
    }
};
