<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type_action', [
                'creation',
                'modification',
                'changement_commercial',
                'changement_statut',
                'appel',
                'note',
                'autre',
            ]);
            $table->string('description');
            $table->json('donnees_avant')->nullable();
            $table->json('donnees_apres')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_activites');
    }
};
