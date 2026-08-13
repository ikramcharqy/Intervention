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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chantier_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nom');

            $table->string('code_zone')->unique();

            $table->text('description')->nullable();

            $table->string('qr_code')->nullable();

            $table->string('nfc_uid')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
