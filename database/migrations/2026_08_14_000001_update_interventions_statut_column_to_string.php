<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `interventions` MODIFY COLUMN `statut` VARCHAR(100) NOT NULL DEFAULT 'Planifiee'");
        } else {
            Schema::table('interventions', function (Blueprint $table) {
                $table->string('statut', 100)->default('Planifiee')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas d'action nécessaire pour la rétrocompatibilité
    }
};
