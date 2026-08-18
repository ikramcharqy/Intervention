<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Helper to safely execute a schema change ignoring duplicate index errors.
     */
    private function safeSchemaTable(string $tableName, callable $callback): void
    {
        if (!Schema::hasTable($tableName)) return;
        try {
            Schema::table($tableName, $callback);
        } catch (\Throwable $e) {
            // Ignore duplicate index / constraint errors
        }
    }

    /**
     * Run the migrations.
     * Solidification de la base de données : SoftDeletes, Index, Contraintes Unique composites et traçabilité.
     */
    public function up(): void
    {
        // 1. AJOUT DU SOFTDELETES SUR LES TABLES CORE
        $softDeleteTables = [
            'users',
            'clients',
            'chantiers',
            'emplacements',
            'materiaus',
            'type_interventions',
            'formulaires',
            'prospects',
        ];

        foreach ($softDeleteTables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                $this->safeSchemaTable($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        // 2. AJOUT DU CHAMP SOURCE POUR L'AUDIT COMPLET DANS INTERVENTION_HISTORIQUES
        if (Schema::hasTable('intervention_historiques')) {
            if (!Schema::hasColumn('intervention_historiques', 'source')) {
                $this->safeSchemaTable('intervention_historiques', function (Blueprint $table) {
                    $table->string('source', 50)->default('Web')->after('commentaire');
                });
            }
            $this->safeSchemaTable('intervention_historiques', function (Blueprint $table) {
                $table->index(['intervention_id', 'created_at']);
            });
            $this->safeSchemaTable('intervention_historiques', function (Blueprint $table) {
                $table->index('source');
            });
        }

        // 3. INDEXES DE PERFORMANCE SUR LA TABLE INTERVENTIONS
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index('statut'); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index('priorite'); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index('date_prevue_debut'); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index('date_prevue_fin'); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index('date_reelle_debut'); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index('created_at'); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index(['statut', 'technicien_id']); });
        $this->safeSchemaTable('interventions', function (Blueprint $table) { $table->index(['statut', 'date_prevue_debut']); });

        // 4. INDEXES DE PERFORMANCE SUR LES AUTRES TABLES CRITIQUES
        $this->safeSchemaTable('demande_interventions', function (Blueprint $table) { $table->index('statut'); });
        $this->safeSchemaTable('demande_interventions', function (Blueprint $table) { $table->index('created_at'); });

        $this->safeSchemaTable('demande_reaffectations', function (Blueprint $table) { $table->index('statut'); });
        $this->safeSchemaTable('demande_reaffectations', function (Blueprint $table) { $table->index('created_at'); });

        $this->safeSchemaTable('questions', function (Blueprint $table) { $table->index(['formulaire_id', 'ordre']); });

        $this->safeSchemaTable('rapports', function (Blueprint $table) { $table->index('created_at'); });
        if (Schema::hasColumn('rapports', 'statut_validation')) {
            $this->safeSchemaTable('rapports', function (Blueprint $table) { $table->index('statut_validation'); });
        }

        $this->safeSchemaTable('materiaus', function (Blueprint $table) { $table->index('is_active'); });

        $this->safeSchemaTable('taches', function (Blueprint $table) { $table->index('is_active'); });
        $this->safeSchemaTable('taches', function (Blueprint $table) { $table->index('type_intervention_id'); });

        // 5. CONTRAINTES UNIQUE COMPOSITES
        if (Schema::hasTable('intervention_materiaus')) {
            DB::statement("
                DELETE t1 FROM intervention_materiaus t1
                INNER JOIN intervention_materiaus t2 
                WHERE t1.id < t2.id 
                  AND t1.intervention_id = t2.intervention_id 
                  AND t1.materiau_id = t2.materiau_id
            ");
            $this->safeSchemaTable('intervention_materiaus', function (Blueprint $table) {
                $table->unique(['intervention_id', 'materiau_id'], 'int_mat_unique');
            });
        }

        if (Schema::hasTable('intervention_taches')) {
            DB::statement("
                DELETE t1 FROM intervention_taches t1
                INNER JOIN intervention_taches t2 
                WHERE t1.id < t2.id 
                  AND t1.intervention_id = t2.intervention_id 
                  AND t1.tache_id = t2.tache_id
            ");
            $this->safeSchemaTable('intervention_taches', function (Blueprint $table) {
                $table->unique(['intervention_id', 'tache_id'], 'int_tache_unique');
            });
        }

        if (Schema::hasTable('reponses')) {
            DB::statement("
                DELETE t1 FROM reponses t1
                INNER JOIN reponses t2 
                WHERE t1.id < t2.id 
                  AND t1.rapport_id = t2.rapport_id 
                  AND t1.question_id = t2.question_id
            ");
            $this->safeSchemaTable('reponses', function (Blueprint $table) {
                $table->unique(['rapport_id', 'question_id'], 'rap_quest_unique');
            });
        }

        if (Schema::hasTable('zone_techniciens')) {
            DB::statement("
                DELETE t1 FROM zone_techniciens t1
                INNER JOIN zone_techniciens t2 
                WHERE t1.id < t2.id 
                  AND t1.zone_id = t2.zone_id 
                  AND t1.technicien_id = t2.technicien_id
            ");
            $this->safeSchemaTable('zone_techniciens', function (Blueprint $table) {
                $table->unique(['zone_id', 'technicien_id'], 'zone_tech_unique');
            });
        }

        if (Schema::hasTable('emplacement_techniciens')) {
            DB::statement("
                DELETE t1 FROM emplacement_techniciens t1
                INNER JOIN emplacement_techniciens t2 
                WHERE t1.id < t2.id 
                  AND t1.emplacement_id = t2.emplacement_id 
                  AND t1.technicien_id = t2.technicien_id
            ");
            $this->safeSchemaTable('emplacement_techniciens', function (Blueprint $table) {
                $table->unique(['emplacement_id', 'technicien_id'], 'empl_tech_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $softDeleteTables = [
            'users',
            'clients',
            'chantiers',
            'emplacements',
            'materiaus',
            'type_interventions',
            'formulaires',
            'prospects',
        ];

        foreach ($softDeleteTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                $this->safeSchemaTable($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }

        if (Schema::hasTable('intervention_historiques') && Schema::hasColumn('intervention_historiques', 'source')) {
            $this->safeSchemaTable('intervention_historiques', function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
