<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('intervention_historiques')) {
            // La table existe déjà (migration antérieure). On ne recrée pas pour éviter conflit.
            return;
        }

        Schema::create('intervention_historiques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('intervention_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ancien_statut')->nullable();
            $table->string('nouveau_statut')->nullable();
            $table->string('action')->nullable();
            $table->text('motif')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_id')->nullable();
            $table->timestamps();

            $table->foreign('intervention_id')->references('id')->on('interventions')->onDelete('cascade');
            // user_id FK omitted to avoid issues if user is deleted; keep as nullable historical reference
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intervention_historiques');
    }
};
