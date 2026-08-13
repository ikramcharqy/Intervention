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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();
            $table->string('code_client')->unique();
            $table->enum('type_client',['Entreprise',
            'Particulier',
            'Administration'
            ]);
            $table->string('nom');
            $table->string('nom_contact')->nullable();
            $table->string('telephone');
            $table->string('telephone_secondaire')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse_facturation')->nullable();
            $table->string('ville');
            $table->string('pays')->default('Maroc');
            $table->string('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();//created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
