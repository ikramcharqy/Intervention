<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statut de présence "En service"/"Hors service" déclaré par le
     * technicien depuis l'app mobile (cf. PresenceToggle côté Flutter) —
     * jusqu'ici purement local (SharedPreferences), sans persistance
     * serveur. Ne pilote aucun tracking GPS (question de conformité non
     * tranchée, voir commentaires PresenceService.php côté Flutter).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('en_service')->default(false)->after('is_active');
            $table->timestamp('en_service_maj_le')->nullable()->after('en_service');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['en_service', 'en_service_maj_le']);
        });
    }
};
