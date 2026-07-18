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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulaire_id')->constrained()->cascadeOnDelete();

            $table->text('question');

            $table->enum('type_reponse',[
                'Texte',
                'Nombre',
                'Oui_Non',
                'Liste',
                'Radio',
                'Checkbox',
                'Date',
                'Heure',
                'DateHeure',
                'Photo',
                'Video',
                'Document'
            ]);

            $table->boolean('obligatoire')->default(false);

            $table->integer('ordre');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
