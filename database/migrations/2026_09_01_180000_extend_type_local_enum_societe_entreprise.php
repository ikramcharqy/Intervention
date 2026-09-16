<?php

use App\Models\Chantier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            $values = implode(',', array_map(fn ($v) => "'".addslashes($v)."'", Chantier::TYPES_LOCAL));
            DB::statement("ALTER TABLE `chantiers` MODIFY COLUMN `type_local` ENUM({$values}) NOT NULL");
        }
    }

    public function down(): void
    {
        // Pas de rollback : réduire l'enum risquerait de tronquer des données existantes.
    }
};
