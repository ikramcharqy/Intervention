<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Complète le module Formulaires Dynamiques :
     *
     * 1. `formulaires` — ajoute unique constraint sur type_intervention_id
     *    (1 formulaire par TypeIntervention max).
     *
     * 2. `questions`   — ajoute les champs manquants :
     *    - placeholder        (aide à la saisie)
     *    - valeur_par_defaut  (valeur pré-remplie)
     *    - condition_affichage (JSON — réservé pour champs conditionnels futurs)
     *    - Étend l'enum type_reponse avec les types manquants.
     *
     * 3. `choix_questions` — ajoute libelle (affichage) distinct de valeur (stockage).
     */
    public function up(): void
    {
        // ── 1. formulaires — contrainte unicité TypeIntervention ──────────────
        Schema::table('formulaires', function (Blueprint $table) {
            // Un TypeIntervention ne peut avoir qu'un seul formulaire actif
            $table->unique('type_intervention_id', 'formulaires_type_unique');
        });

        // ── 2. questions — champs complémentaires ─────────────────────────────
        Schema::table('questions', function (Blueprint $table) {
            $table->string('placeholder')->nullable()->after('ordre');
            $table->text('valeur_par_defaut')->nullable()->after('placeholder');
            // Réservé pour les conditions d'affichage futures (stocké en JSON)
            $table->json('condition_affichage')->nullable()->after('valeur_par_defaut');
        });

        // Extension de l'enum type_reponse (MySQL uniquement)
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
                    'Liste',
                    'Checkbox',
                    'Radio',
                    'Photo',
                    'Signature',
                    'Document',
                    'GPS',
                    'QRCode'
                ) NOT NULL
            ");
        }
        // Pour SQLite : l'enum est un VARCHAR — la validation applicative suffit.

        // ── 3. choix_questions — libelle lisible ──────────────────────────────
        Schema::table('choix_questions', function (Blueprint $table) {
            // Libellé affiché à l'utilisateur (peut différer de la valeur stockée)
            $table->string('libelle')->nullable()->after('valeur');
        });
    }

    public function down(): void
    {
        Schema::table('choix_questions', function (Blueprint $table) {
            $table->dropColumn('libelle');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['placeholder', 'valeur_par_defaut', 'condition_affichage']);
        });

        Schema::table('formulaires', function (Blueprint $table) {
            $table->dropUnique('formulaires_type_unique');
        });
    }
};
