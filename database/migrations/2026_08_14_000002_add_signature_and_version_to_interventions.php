<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            if (!Schema::hasColumn('interventions', 'signature_client_path')) {
                $table->string('signature_client_path')->nullable()->after('observations');
            }
            if (!Schema::hasColumn('interventions', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signature_client_path');
            }
            if (!Schema::hasColumn('interventions', 'status_version')) {
                $table->unsignedInteger('status_version')->default(0)->after('signed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            if (Schema::hasColumn('interventions', 'signature_client_path')) {
                $table->dropColumn('signature_client_path');
            }
            if (Schema::hasColumn('interventions', 'signed_at')) {
                $table->dropColumn('signed_at');
            }
            if (Schema::hasColumn('interventions', 'status_version')) {
                $table->dropColumn('status_version');
            }
        });
    }
};
