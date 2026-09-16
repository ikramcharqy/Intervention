<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materiaus', function (Blueprint $table) {
            $table->string('marque')->nullable()->after('categorie');
            $table->string('modele_fabricant')->nullable()->after('marque');
            $table->string('qr_code')->nullable()->unique()->after('reference');
            $table->string('fiche_technique_path')->nullable()->after('image_path');
        });

        // Backfill : QR code logique pour les matériaux existants (même
        // convention que EmplacementService::generateUniqueQrCode()).
        $materiaux = DB::table('materiaus')->whereNull('qr_code')->get(['id']);

        foreach ($materiaux as $materiau) {
            do {
                $qrCode = 'MAT-QR-' . strtoupper(bin2hex(random_bytes(8)));
            } while (DB::table('materiaus')->where('qr_code', $qrCode)->exists());

            DB::table('materiaus')->where('id', $materiau->id)->update(['qr_code' => $qrCode]);
        }
    }

    public function down(): void
    {
        Schema::table('materiaus', function (Blueprint $table) {
            $table->dropColumn(['marque', 'modele_fabricant', 'qr_code', 'fiche_technique_path']);
        });
    }
};
