<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajoute les colonnes manquantes à la table interventions :
     *  - mode_suivi          : présent dans le Model mais absent de la migration initiale
     *  - cree_par            : traçabilité du créateur (admin/planificateur)
     *  - valide_par          : traçabilité du validateur (admin)
     *  - date_soumission_validation : horodatage soumission technicien
     *  - date_validation     : horodatage validation admin
     *  - motif_annulation    : explication si statut = Annulee
     *
     * Étend également l'enum statut pour inclure 'En attente validation'.
     */
    public function up(): void
    {
        Schema::table('interventions', function (Blueprint $table) {

            // Mode de suivi de présence du technicien (manquant dans la migration initiale)
            $table->enum('mode_suivi', ['GPS', 'QR', 'NFC', 'Manuel'])
                  ->nullable()
                  ->after('type_intervention_id');

            // Traçabilité — Créateur de l'intervention
            $table->foreignId('cree_par')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('mode_suivi');

            // Traçabilité — Validateur admin
            $table->foreignId('valide_par')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('cree_par');

            // Horodatage de la soumission par le technicien (transition En cours → En attente validation)
            $table->dateTime('date_soumission_validation')
                  ->nullable()
                  ->after('date_reelle_fin');

            // Horodatage de la validation admin (transition En attente validation → Terminee)
            $table->dateTime('date_validation')
                  ->nullable()
                  ->after('date_soumission_validation');

            // Motif obligatoire lors d'une annulation
            $table->text('motif_annulation')
                  ->nullable()
                  ->after('observations');
        });

        // Extension de l'enum statut pour inclure 'En attente validation'
        // Géré par DB::statement pour compatibilité MySQL.
        // SQLite ne supporte pas la modification d'enum — la validation reste au niveau applicatif.
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE interventions
                MODIFY COLUMN statut
                ENUM('Planifiee', 'En cours', 'Suspendue', 'En attente validation', 'Terminee', 'Annulee')
                NOT NULL
            ");
        }
        // Pour SQLite (développement) : le statut est validé côté application (FormRequest + Service).
        // L'enum SQLite est un simple VARCHAR, la nouvelle valeur sera acceptée automatiquement.
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE interventions
                MODIFY COLUMN statut
                ENUM('Planifiee', 'En cours', 'Suspendue', 'Terminee', 'Annulee')
                NOT NULL
            ");
        }

        Schema::table('interventions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cree_par');
            $table->dropConstrainedForeignId('valide_par');
            $table->dropColumn([
                'mode_suivi',
                'date_soumission_validation',
                'date_validation',
                'motif_annulation',
            ]);
        });
    }
};
