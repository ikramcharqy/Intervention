<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->date('prochaine_action_date')->nullable()->after('observations');
            $table->string('prochaine_action_description')->nullable()->after('prochaine_action_date');
        });
    }

    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropColumn(['prochaine_action_date', 'prochaine_action_description']);
        });
    }
};
