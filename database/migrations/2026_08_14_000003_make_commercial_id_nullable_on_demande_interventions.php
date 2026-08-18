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
            DB::statement("ALTER TABLE `demande_interventions` MODIFY COLUMN `commercial_id` BIGINT UNSIGNED NULL");
        } else {
            Schema::table('demande_interventions', function (Blueprint $table) {
                $table->unsignedBigInteger('commercial_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas d'action rétrograde nécessaire
    }
};
