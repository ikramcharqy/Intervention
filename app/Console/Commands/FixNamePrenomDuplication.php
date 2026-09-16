<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Étape 2.3 : migration de données — corrige les comptes (tous rôles) où "name" contient
 * le prénom en préfixe dupliqué (ex: name="Mohamed Ali", prenom="Mohamed" → aurait dû
 * être name="Ali"). Ne touche jamais un compte où le préfixe ne correspond pas
 * exactement au prénom, pour ne jamais deviner à tort un nom de famille.
 */
class FixNamePrenomDuplication extends Command
{
    protected $signature = 'users:fix-name-prenom-duplication {--dry-run : N\'affiche que ce qui serait modifié, sans écrire en base}';

    protected $description = 'Corrige les comptes utilisateurs (tous rôles) dont le champ "name" duplique le prénom en préfixe';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $fixed = 0;

        User::withTrashed()->whereNotNull('prenom')->where('prenom', '!=', '')->chunkById(100, function ($users) use (&$fixed, $dryRun) {
            foreach ($users as $user) {
                $prenom = trim($user->prenom);
                $name = trim($user->name);

                if ($prenom === '' || $name === '') {
                    continue;
                }

                if (!Str::startsWith(Str::lower($name), Str::lower($prenom) . ' ')) {
                    continue;
                }

                $newName = trim(Str::substr($name, Str::length($prenom)));

                if ($newName === '') {
                    continue;
                }

                $this->line("#{$user->id} {$user->email} : name \"{$name}\" → \"{$newName}\" (prenom \"{$prenom}\" inchangé)");

                if (!$dryRun) {
                    $user->forceFill(['name' => $newName])->save();
                }

                $fixed++;
            }
        });

        if ($fixed === 0) {
            $this->info('Aucun compte à corriger.');
            return self::SUCCESS;
        }

        $this->info(($dryRun ? '[dry-run] ' : '') . "{$fixed} compte(s) corrigé(s).");

        if (!$dryRun) {
            AuditLog::create([
                'user_id' => null,
                'user_name' => 'System',
                'action' => "Migration de données : {$fixed} compte(s) corrigé(s) pour la duplication nom/prénom",
                'module' => 'Security',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'INFO',
                'ip_address' => null,
            ]);
        }

        return self::SUCCESS;
    }
}
