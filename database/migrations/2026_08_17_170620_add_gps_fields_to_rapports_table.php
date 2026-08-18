<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            // GPS Coordinates captured at intervention site
            $table->decimal('gps_latitude', 10, 7)->nullable()->after('qrcode_scanne');
            $table->decimal('gps_longitude', 10, 7)->nullable()->after('gps_latitude');
            $table->string('gps_adresse', 500)->nullable()->after('gps_longitude');

            // Photo categorization
            $table->json('photos_meta')->nullable()->after('gps_adresse'); // {type: 'avant'|'apres'|'probleme'}
        });
    }

    public function down(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropColumn(['gps_latitude', 'gps_longitude', 'gps_adresse', 'photos_meta']);
        });
    }
};
