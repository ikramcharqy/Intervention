<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('type_document')->nullable()->after('client_id');
            $table->foreignId('devis_id')->nullable()->after('type_document')->constrained('devis')->nullOnDelete();
            $table->foreignId('chantier_id')->nullable()->after('devis_id')->constrained('chantiers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('devis_id');
            $table->dropConstrainedForeignId('chantier_id');
            $table->dropColumn('type_document');
        });
    }
};
