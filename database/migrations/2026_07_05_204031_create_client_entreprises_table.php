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
        Schema::create('client_entreprises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

            $table->string('ice')->unique();//ice=identifiant commun de l'entreprise

            $table->string('if');//if=identifiant fiscal

            $table->string('rc');//rc=registre de commerce

            $table->string('patente');//patente=patente de l'entreprise
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_entreprises');
    }
};
