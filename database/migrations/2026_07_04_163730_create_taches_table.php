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
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('nom');

            $table->text('description')->nullable();

            $table->foreignId('parent_id')->nullable()->constrained('taches')->nullOnDelete(); 
            //parent_id est une clé étrangère qui fait référence à l'id de la même table taches, 
            // ce qui permet de créer une relation hiérarchique entre les tâches (une tâche peut avoir une tâche parente).

            $table->integer('ordre')->default(1);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
