<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\SuperAdmin\AccountRoleStatsService;
use Illuminate\Console\Command;

/**
 * Étape 2.4 du prompt "Neutralisation d'urgence" : compare l'inventaire des comptes à
 * privilège élevé (Super Admin/Administrateur) tel qu'obtenu via le scope Eloquent
 * Spatie (celui affiché sur la page "Administrateurs") avec une requête SQL brute sur
 * le pivot `model_has_roles` — pour détecter automatiquement toute divergence future
 * plutôt que de la découvrir manuellement (cf. incident "Qa" invisible sur cette page).
 */
class CheckAdminInventoryConsistency extends Command
{
    protected $signature = 'admins:check-inventory-consistency';

    protected $description = "Vérifie que la page \"Administrateurs\" liste bien tous les comptes à privilège élevé réels en base";

    public function handle(AccountRoleStatsService $stats): int
    {
        $result = $stats->adminInventoryConsistencyCheck();

        if ($result['consistent']) {
            $this->info("Inventaire cohérent : {$result['via_eloquent_count']} compte(s) à privilège élevé, page et base concordent.");
            return self::SUCCESS;
        }

        $this->error('Divergence détectée entre la page "Administrateurs" et l\'inventaire réel en base :');
        if ($result['missing_from_page']) {
            $this->line('  Manquants sur la page (IDs) : ' . implode(', ', $result['missing_from_page']));
        }
        if ($result['extra_on_page']) {
            $this->line('  En trop sur la page (IDs) : ' . implode(', ', $result['extra_on_page']));
        }

        AuditLog::create([
            'user_id' => null,
            'user_name' => 'System',
            'action' => 'Divergence détectée entre la page "Administrateurs" et l\'inventaire réel des comptes à privilège élevé — '
                . 'manquants: [' . implode(', ', $result['missing_from_page']) . '] '
                . 'en trop: [' . implode(', ', $result['extra_on_page']) . ']',
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => null,
        ]);

        return self::FAILURE;
    }
}
