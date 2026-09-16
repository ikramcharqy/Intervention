<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trace qu'un Client provient d'un Prospect converti (manuellement ou
     * automatiquement via DevisObserver) : évite de recréer un doublon de Client
     * si plusieurs devis du même prospect sont acceptés, et permet de retrouver
     * le client existant à partir du prospect d'origine.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('prospect_id')->nullable()->after('commercial_id')->constrained('prospects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prospect_id');
        });
    }
};
