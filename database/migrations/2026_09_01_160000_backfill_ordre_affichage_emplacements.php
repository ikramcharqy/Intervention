<?php

use App\Models\Chantier;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Chantier::withTrashed()->each(function (Chantier $chantier) {
            $chantier->emplacements()
                ->withTrashed()
                ->orderBy('ordre_affichage')
                ->orderBy('nom')
                ->get()
                ->values()
                ->each(function ($emplacement, $index) {
                    $emplacement->updateQuietly(['ordre_affichage' => $index + 1]);
                });
        });
    }

    public function down(): void
    {
        // Pas d'action nécessaire (backfill non destructif).
    }
};
