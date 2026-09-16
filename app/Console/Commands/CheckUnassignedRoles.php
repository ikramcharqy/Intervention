<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\SuperAdmin\AccountRoleStatsService;
use Illuminate\Console\Command;

/**
 * Détection continue des comptes sans rôle Spatie assigné (cf. bug tech@intervention.ma).
 * Destiné à tourner en tâche planifiée ; journalise une alerte de sécurité si des comptes
 * sans rôle sont détectés, pour qu'ils ne soient plus découverts manuellement.
 */
class CheckUnassignedRoles extends Command
{
    protected $signature = 'users:check-unassigned-roles';

    protected $description = 'Détecte les comptes utilisateurs sans rôle Spatie assigné et alerte si nécessaire';

    public function handle(AccountRoleStatsService $stats): int
    {
        $unassigned = $stats->unassignedQuery()->get(['id', 'email', 'name']);

        if ($unassigned->isEmpty()) {
            $this->info('Aucun compte sans rôle Spatie détecté.');
            return self::SUCCESS;
        }

        $this->warn("{$unassigned->count()} compte(s) sans rôle Spatie détecté(s) :");
        foreach ($unassigned as $user) {
            $this->line("  - #{$user->id} {$user->name} <{$user->email}>");
        }

        AuditLog::create([
            'user_id' => null,
            'user_name' => 'System',
            'action' => "{$unassigned->count()} compte(s) détecté(s) sans rôle Spatie assigné : " . $unassigned->pluck('email')->implode(', '),
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => null,
        ]);

        return self::SUCCESS;
    }
}
