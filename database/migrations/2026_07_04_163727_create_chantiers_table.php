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
        Schema::create('chantiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('code_chantier')->unique();
            $table->string('nom');
            $table->enum('type_local',['Maison',
            'Appartement',
            'Magasin',
            'Bureau',
            'Usine',
            'Restaurant',
            'Hotel',
            'Hopital',
            'Ecole',
            'Administration',
            'Autre']);
            $table->text('adresse')->required();
            $table->string('ville');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('responsable');
            $table->string('telephone_responsable');
            $table->string('email_responsable');            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantiers');
    }
};
