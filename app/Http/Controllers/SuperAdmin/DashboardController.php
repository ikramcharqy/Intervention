<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Chantier;
use App\Models\Intervention;
use App\Models\Rapport;
use App\Models\Formulaire;
use App\Models\Materiau;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use App\Services\SuperAdmin\AccountRoleStatsService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(AccountRoleStatsService $roleStats): View
    {
        $adminRoles = Role::whereIn('name', ['admin', 'Administrateur', 'Super Admin'])->pluck('name')->toArray();

        // Calcul RÉEL de la taille de la base de données MySQL
        $dbName = config('database.connections.mysql.database');
        $dbSizeBytes = 0;
        try {
            $dbSizeResult = DB::select("SELECT SUM(data_length + index_length) as db_size FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            $dbSizeBytes = $dbSizeResult[0]->db_size ?? 0;
        } catch (\Exception $e) {
            $dbSizeBytes = 0;
        }

        $dbSizeFormatted = $dbSizeBytes > 1048576 
            ? number_format($dbSizeBytes / 1048576, 2) . ' MB' 
            : number_format($dbSizeBytes / 1024, 2) . ' KB';

        // Total Comptes / Répartition des Rôles Spatie : dérivés d'une source unique
        // (AccountRoleStatsService) pour ne plus jamais diverger entre les deux widgets.
        $roleBreakdown = $roleStats->breakdown();

        // Statistiques Système et Métier 100% RÉELLES
        $stats = [
            'total_admins' => !empty($adminRoles) ? User::role($adminRoles)->count() : 0,
            'total_users' => $roleBreakdown['total'],
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'active_sessions' => User::where('is_active', true)->count(),
            'total_clients' => Client::count(),
            'total_chantiers' => Chantier::count(),
            'total_interventions' => Intervention::count(),
            'total_rapports' => Rapport::count(),
            'db_size' => $dbSizeFormatted,
            'system_status' => 'Opérationnel',
            'php_version' => PHP_VERSION,
            'unassigned_accounts' => $roleBreakdown['unassigned'],
        ];

        // Décompte par Rôles (+ "Sans rôle" si des comptes non catégorisés existent,
        // pour que la somme affichée corresponde toujours au Total Comptes ci-dessus).
        $usersByRole = $roleBreakdown['by_role'];
        if ($roleBreakdown['unassigned'] > 0) {
            $usersByRole['Sans rôle'] = $roleBreakdown['unassigned'];
        }

        $recentUsers = User::with('roles')->latest()->limit(5)->get();

        // Étape 3.2 : historique de connexion du compte Super Admin courant, en réutilisant
        // le mécanisme déjà défini pour le portail Client (LoginHistory est déjà alimenté
        // pour tous les rôles via l'écouteur d'événement Login — cf. AppServiceProvider).
        $myLoginHistory = LoginHistory::where('user_id', auth()->id())
            ->latest('logged_in_at')
            ->limit(5)
            ->get();

        // Journal de Sécurité & Gouvernance uniquement (cf. Étape 2) — l'activité métier
        // (conversions prospects, validations d'intervention, devis) vit désormais sur les
        // dashboards Admin/Commercial via AuditLog::business().
        $recentLogs = AuditLog::security()->latest()->limit(6)->get();

        return view('superadmin.dashboard', compact('stats', 'usersByRole', 'recentUsers', 'recentLogs', 'myLoginHistory'));
    }
}
