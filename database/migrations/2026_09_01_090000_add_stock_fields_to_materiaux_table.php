<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materiaus', function (Blueprint $table) {
            if (!Schema::hasColumn('materiaus', 'categorie')) {
                $table->string('categorie')->nullable()->after('description');
            }
            if (!Schema::hasColumn('materiaus', 'seuil_alerte')) {
                $table->decimal('seuil_alerte', 10, 2)->default(5)->after('stock');
            }
            if (!Schema::hasColumn('materiaus', 'image_path')) {
                $table->string('image_path')->nullable()->after('seuil_alerte');
            }
            if (!Schema::hasColumn('materiaus', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Backfill : les matériaux existants n'ont pas de référence. On génère
        // un identifiant technique interne déterministe (pas une donnée métier
        // inventée) pour respecter la contrainte "reference obligatoire" qui
        // ne s'applique désormais qu'aux nouvelles créations.
        $materiaux = DB::table('materiaus')->whereNull('reference')->get(['id', 'nom']);

        foreach ($materiaux as $materiau) {
            $slug = Str::of($materiau->nom)->ascii()->upper()->replaceMatches('/[^A-Z0-9]+/', '-')->trim('-');
            $reference = $slug . '-' . str_pad((string) $materiau->id, 5, '0', STR_PAD_LEFT);

            DB::table('materiaus')->where('id', $materiau->id)->update(['reference' => $reference]);
        }
    }

    public function down(): void
    {
        Schema::table('materiaus', function (Blueprint $table) {
            $table->dropColumn(['categorie', 'seuil_alerte', 'image_path', 'deleted_at']);
        });
    }
};
