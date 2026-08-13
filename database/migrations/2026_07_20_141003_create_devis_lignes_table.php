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
        Schema::create('devis_lignes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('devis_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('designation');

            $table->text('description')->nullable();

            $table->decimal('quantite',10,2);

            $table->decimal('prix_unitaire',12,2);

            $table->decimal('montant_ht',12,2);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis_lignes');
    }
};