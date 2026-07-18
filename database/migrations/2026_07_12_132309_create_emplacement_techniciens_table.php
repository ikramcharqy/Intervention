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
        Schema::create('emplacement_techniciens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technicien_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('emplacement_id')->constrained('emplacements')->cascadeOnDelete();
            $table->boolean('is_principal')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emplacement_techniciens');
    }
};
