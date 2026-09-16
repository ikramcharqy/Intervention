<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('emplacements', 'ordre_affichage')) {
            Schema::table('emplacements', function (Blueprint $table) {
                $table->integer('ordre_affichage')->nullable()->after('nom');
            });
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `interventions` MODIFY COLUMN `emplacement_id` BIGINT UNSIGNED NULL");
        } else {
            Schema::table('interventions', function (Blueprint $table) {
                $table->unsignedBigInteger('emplacement_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Pas d'action nécessaire (cohérent avec la migration technicien_id équivalente).
    }
};
