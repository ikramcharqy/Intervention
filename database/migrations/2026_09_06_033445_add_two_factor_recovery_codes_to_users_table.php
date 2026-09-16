<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flux d'enrôlement 2FA complet : codes de secours à usage unique, chiffrés en base
     * (cast `encrypted` sur le modèle User) — additive, nullable, aucune donnée existante
     * modifiée.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_recovery_codes');
        });
    }
};
