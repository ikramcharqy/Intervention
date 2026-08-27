<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Étend l'enum type_reponse de la table questions pour ajouter 'Video'.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE questions
                MODIFY COLUMN type_reponse
                ENUM(
                    'Texte',
                    'TexteLong',
                    'Nombre',
                    'Date',
                    'Heure',
                    'DateHeure',
                    'OuiNon',
                    'Oui_Non',
                    'Liste',
                    'Checkbox',
                    'Radio',
                    'Photo',
                    'Video',
                    'Signature',
                    'Document',
                    'GPS',
                    'QRCode',
                    'Materiaux'
                ) NOT NULL
            ");
        } else {
            // SQLite (et autres) : un enum est émulé par une contrainte CHECK
            // qui ne peut pas être étendue in place. On bascule sur une
            // colonne string pour rester portable, comme pour interventions.statut.
            Schema::table('questions', function (Blueprint $table) {
                $table->string('type_reponse', 50)->change();
            });
        }
    }

    public function down(): void
    {
        // L'enum précédente (sans Materiaux ni Video)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE questions
                MODIFY COLUMN type_reponse
                ENUM(
                    'Texte', 'TexteLong', 'Nombre', 'Date', 'Heure', 'DateHeure',
                    'OuiNon', 'Liste', 'Checkbox', 'Radio', 'Photo',
                    'Signature', 'Document', 'GPS', 'QRCode'
                ) NOT NULL
            ");
        }
    }
};
