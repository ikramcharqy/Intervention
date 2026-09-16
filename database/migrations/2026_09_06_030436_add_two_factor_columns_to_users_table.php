<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Étape 3.1 : 2FA obligatoire pour le rôle Super Admin. Colonnes additives et
     * nullables sur `users` (aucune valeur par défaut non-nulle, aucune donnée existante
     * modifiée) — n'affecte aucun autre rôle tant que le champ n'est pas renseigné.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')->nullable()->after('remember_token');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_secret', 'two_factor_confirmed_at']);
        });
    }
};
