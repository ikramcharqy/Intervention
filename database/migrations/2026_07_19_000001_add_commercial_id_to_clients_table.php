<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Lien vers le commercial qui a enregistré le client
            $table->foreignId('commercial_id')
                  ->nullable()
                  ->after('is_active')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'commercial_id');
            $table->dropColumn('commercial_id');
        });
    }
};
