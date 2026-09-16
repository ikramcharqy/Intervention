<?php

namespace App\Console\Commands;

use App\Services\SuperAdmin\BackupService;
use Illuminate\Console\Command;

/**
 * Étape 2 du prompt "Correction APP_NAME + sécurisation Sauvegardes" : sauvegarde SQL
 * automatique planifiée, réutilisant le même BackupService que l'action manuelle de la
 * page "Sauvegardes" — un seul point de génération, jamais deux implémentations
 * divergentes du dump.
 */
class RunScheduledBackup extends Command
{
    protected $signature = 'backups:run-scheduled';

    protected $description = "Génère une sauvegarde SQL automatique et applique la politique de rétention";

    public function handle(BackupService $backupService): int
    {
        $result = $backupService->create(automatic: true);

        $this->info("Sauvegarde automatique générée : {$result['filename']} ({$result['size_kb']} KB)");

        return self::SUCCESS;
    }
}
