<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * numero_cin est stocké chiffré (Crypt::encryptString, voir ClientParticulier)
     * donc trop long/variable pour un VARCHAR indexé : la colonne text() garde le
     * texte chiffré, et numero_cin_hash (empreinte SHA-256 déterministe du CIN en
     * clair) porte la contrainte unique réelle pour les vérifications de doublon.
     */
    public function up(): void
    {
        Schema::create('client_particuliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('numero_cin')->nullable();
            $table->string('numero_cin_hash', 64)->nullable()->unique();
            $table->date('date_naissance')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_particuliers');
    }
};
