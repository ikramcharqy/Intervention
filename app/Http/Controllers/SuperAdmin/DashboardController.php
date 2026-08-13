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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $adminRoles = Role::whereIn('name', ['admin', 'Administrateur', 'Super Admin'])->pluck('name')->toArray();
        $adminUserRoles = Role::whereIn('name', ['admin', 'Administrateur'])->pluck('name')->toArray();
        $techRoles = Role::whereIn('name', ['technicien', 'Technicien'])->pluck('name')->toArray();

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

        // Statistiques Système et Métier 100% RÉELLES
        $stats = [
            'total_admins' => !empty($adminRoles) ? User::role($adminRoles)->count() : 0,
            'total_users' => User::count(),
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
        ];

        // Décompte par Rôles
        $usersByRole = [
            'Super Admin' => Role::where('name', 'Super Admin')->exists() ? User::role('Super Admin')->count() : 0,
            'Administrateur' => !empty($adminUserRoles) ? User::role($adminUserRoles)->count() : 0,
            'Commercial' => Role::where('name', 'Commercial')->exists() ? User::role('Commercial')->count() : 0,
            'Technicien' => !empty($techRoles) ? User::role($techRoles)->count() : 0,
            'Client' => Role::where('name', 'Client')->exists() ? User::role('Client')->count() : 0,
        ];

        $recentUsers = User::with('roles')->latest()->limit(5)->get();

        // Récupération RÉELLE des derniers logs d'audit enregistrés en base
        $recentLogs = AuditLog::latest()->limit(6)->get();

        return view('superadmin.dashboard', compact('stats', 'usersByRole', 'recentUsers', 'recentLogs'));
    }
}
