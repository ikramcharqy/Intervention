<?php

namespace App\Services\SuperAdmin;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Étape 1 du prompt "Correction APP_NAME + sécurisation Sauvegardes" : toute la logique
 * de génération/listage/purge des dumps SQL vivait jusqu'ici directement dans
 * SystemController (violation de l'architecture Controller → Service → Model) et portait
 * un préfixe de fichier codé en dur (nom du projet antérieur) indépendant de
 * `config('app.name')`.
 * Centralisée ici : un seul point de génération, réutilisé à la fois par l'action
 * manuelle et par la tâche planifiée (Étape 2).
 */
class BackupService
{
    private const RETENTION_JOURS_DEFAUT = 30;

    public function backupDir(): string
    {
        $dir = storage_path('app/backups');

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        return $dir;
    }

    /**
     * @return array<int, array{filename: string, filepath: string, size: string, size_bytes: int, date: string, timestamp: int, type: string}>
     */
    public function list(): array
    {
        $files = File::files($this->backupDir());
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'sql') {
                continue;
            }

            $sizeBytes = $file->getSize();

            $backups[] = [
                'filename' => $file->getFilename(),
                'filepath' => $file->getPathname(),
                'size' => $sizeBytes >= 1048576
                    ? number_format($sizeBytes / 1048576, 2) . ' MB'
                    : number_format($sizeBytes / 1024, 2) . ' KB',
                'size_bytes' => $sizeBytes,
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
                'timestamp' => $file->getMTime(),
                'type' => str_contains($file->getFilename(), '-auto-') ? 'Automatique' : 'Manuel',
            ];
        }

        usort($backups, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Génère un dump SQL réel de la base courante. $automatic distingue le nom de
     * fichier (Étape 2.2 — colonne "Type" déjà prévue côté vue) et la sévérité du
     * journal (une génération automatique réussie est un non-événement en soi, INFO
     * suffit ; une génération manuelle reste SUCCESS comme avant).
     *
     * @return array{filename: string, filepath: string, size_kb: float}
     */
    public function create(bool $automatic = false): array
    {
        $dir = $this->backupDir();

        // Préfixe dérivé de APP_NAME (Étape 1) — plus jamais codé en dur indépendamment
        // de la configuration centrale du projet.
        $slug = Str::slug(config('app.name', 'app'));
        $type = $automatic ? 'auto' : 'manuel';
        $filename = "backup-{$slug}-{$type}-" . date('Y-m-d-His') . '.sql';
        $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $keyName = "Tables_in_{$dbName}";

        $sqlDump = "-- " . config('app.name', 'Application') . " Database Dump\n";
        $sqlDump .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- Database: {$dbName}\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$keyName ?? array_values((array) $tableObj)[0];

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createTable[0]->{'Create Table'} ?? '';
            $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sqlDump .= $createSql . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $cols = array_keys($rowArray);
                $vals = array_map(function ($val) {
                    if (is_null($val)) {
                        return 'NULL';
                    }
                    return DB::getPdo()->quote($val);
                }, array_values($rowArray));

                $sqlDump .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sqlDump .= "\n";
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($filepath, $sqlDump);

        $sizeKb = round(filesize($filepath) / 1024, 2);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => ($automatic ? "Sauvegarde SQL automatique planifiée" : "Création d'une sauvegarde SQL manuelle") . " : {$filename}",
            'module' => 'Backup',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => $automatic ? 'INFO' : 'SUCCESS',
            'ip_address' => $automatic ? null : request()->ip(),
            'details' => "Taille: {$sizeKb} KB",
        ]);

        // Étape 3 : purge de rétention appliquée après chaque génération (manuelle ou
        // planifiée) plutôt que comme tâche séparée — plus simple, toujours à jour.
        $this->applyRetentionPolicy();

        return ['filename' => $filename, 'filepath' => $filepath, 'size_kb' => $sizeKb];
    }

    public function resolvePath(string $filename): ?string
    {
        $filepath = $this->backupDir() . DIRECTORY_SEPARATOR . basename($filename);

        return File::exists($filepath) ? $filepath : null;
    }

    public function delete(string $filename): bool
    {
        $filepath = $this->resolvePath($filename);

        if (!$filepath) {
            return false;
        }

        File::delete($filepath);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Suppression de la sauvegarde SQL {$filename}",
            'module' => 'Backup',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);

        return true;
    }

    public function logDownload(string $filename): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Téléchargement du fichier de sauvegarde {$filename}",
            'module' => 'Backup',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'INFO',
            'ip_address' => request()->ip(),
        ]);
    }

    public function retentionDays(): int
    {
        return (int) Setting::get('backup_retention_days', self::RETENTION_JOURS_DEFAUT);
    }

    /**
     * Étape 3 : purge les sauvegardes plus anciennes que la politique de rétention
     * configurée (par défaut 30 jours) — jamais en-dessous d'une sauvegarde restante,
     * pour ne jamais se retrouver sans aucun backup disponible.
     */
    public function applyRetentionPolicy(): int
    {
        $seuil = now()->subDays($this->retentionDays())->timestamp;
        $backups = $this->list();

        if (count($backups) <= 1) {
            return 0;
        }

        $supprimes = 0;
        // On garde toujours au moins la sauvegarde la plus récente, même si elle dépasse
        // la rétention (ex: rétention à 30j mais plus aucune sauvegarde récente créée).
        foreach (array_slice($backups, 1) as $backup) {
            if ($backup['timestamp'] < $seuil) {
                File::delete($backup['filepath']);
                $supprimes++;
            }
        }

        if ($supprimes > 0) {
            AuditLog::create([
                'user_id' => null,
                'user_name' => 'System',
                'action' => "Purge de rétention : {$supprimes} sauvegarde(s) SQL de plus de {$this->retentionDays()} jour(s) supprimée(s)",
                'module' => 'Backup',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'INFO',
                'ip_address' => null,
            ]);
        }

        return $supprimes;
    }

    /**
     * Étape 2.3 : détecte une sauvegarde automatique manquante ou en retard anormal —
     * comparé à deux fois l'intervalle attendu (quotidien) pour tolérer un léger
     * décalage horaire sans fausse alerte.
     */
    public function derniereAutoEnRetard(): bool
    {
        $dernierAuto = collect($this->list())->firstWhere('type', 'Automatique');

        if (!$dernierAuto) {
            return true;
        }

        return $dernierAuto['timestamp'] < now()->subHours(48)->timestamp;
    }
}
