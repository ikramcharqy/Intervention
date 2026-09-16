<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Détection continue des comptes sans rôle Spatie (cf. bug tech@intervention.ma) :
// alerte automatiquement plutôt que d'être découverte manuellement sur le dashboard.
Schedule::command('users:check-unassigned-roles')->hourly();

// Étape 2.4 du prompt "Neutralisation d'urgence" : détection continue d'une éventuelle
// divergence entre la page "Administrateurs" et l'inventaire réel des comptes à
// privilège élevé (cf. incident "Qa").
Schedule::command('admins:check-inventory-consistency')->hourly();

// Étape 2 du prompt "Correction APP_NAME + sécurisation Sauvegardes" : sauvegarde SQL
// automatique quotidienne (heure creuse), avec purge de rétention appliquée à chaque
// génération par BackupService lui-même.
Schedule::command('backups:run-scheduled')->dailyAt('03:00');
