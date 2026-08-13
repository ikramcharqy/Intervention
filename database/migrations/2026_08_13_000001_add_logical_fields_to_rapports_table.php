<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            if (!Schema::hasColumn('rapports', 'travaux_effectues')) {
                $table->text('travaux_effectues')->nullable()->after('intervention_id');
            }
            if (!Schema::hasColumn('rapports', 'observations')) {
                $table->text('observations')->nullable()->after('travaux_effectues');
            }
            if (!Schema::hasColumn('rapports', 'recommandations')) {
                $table->text('recommandations')->nullable()->after('observations');
            }
            if (!Schema::hasColumn('rapports', 'statut_equipement')) {
                $table->string('statut_equipement')->nullable()->default('Conforme')->after('recommandations');
            }
            if (!Schema::hasColumn('rapports', 'qrcode_scanne')) {
                $table->string('qrcode_scanne')->nullable()->after('statut_equipement');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropColumn([
                'travaux_effectues',
                'observations',
                'recommandations',
                'statut_equipement',
                'qrcode_scanne',
            ]);
        });
    }
};
