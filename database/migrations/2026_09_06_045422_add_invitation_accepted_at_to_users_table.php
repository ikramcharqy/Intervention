<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Étape 1 : flux d'invitation par email pour la création d'administrateurs (plus de
     * mot de passe saisi par le créateur). Colonne additive et nullable — les comptes
     * existants (tous rôles) sont rétro-datés à `now()` pour ne jamais apparaître comme
     * "Invitation en attente" ; seuls les nouveaux comptes créés via l'invitation
     * démarrent avec cette colonne à `null` jusqu'à ce qu'ils définissent leur mot de
     * passe.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('invitation_accepted_at')->nullable()->after('email');
        });

        DB::table('users')->update(['invitation_accepted_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('invitation_accepted_at');
        });
    }
};
