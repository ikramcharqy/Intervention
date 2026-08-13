<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemController extends Controller
{
    // Helper pour ajouter un log d'audit réel
    private function logAction(string $action, string $module = 'System', string $severity = 'INFO', ?string $details = null): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => $action,
            'module' => $module,
            'severity' => $severity,
            'ip_address' => request()->ip(),
            'details' => $details,
        ]);
    }

    // Gestion des Administrateurs
    public function admins(Request $request): View
    {
        $existingAdminRoles = Role::whereIn('name', ['admin', 'Administrateur', 'Super Admin'])->pluck('name')->toArray();
        $query = !empty($existingAdminRoles) ? User::role($existingAdminRoles)->with('roles') : User::whereRaw('1 = 0');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->latest()->paginate(15);
        $roles = Role::whereIn('name', ['admin', 'Administrateur', 'Super Admin'])->get();

        return view('superadmin.admins.index', compact('admins', 'roles'));
    }

    // Création d'un administrateur RÉELLE
    public function storeAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'prenom' => $validated['prenom'] ?? '',
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? '',
            'adresse' => $validated['adresse'] ?? 'Casablanca, Maroc',
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => $validated['role'], 'guard_name' => 'web']);
        $admin->syncRoles([$role]);

        $this->logAction("Création de l'administrateur {$admin->email}", 'Users', 'SUCCESS');

        return redirect()->back()->with('success', "Le compte administrateur {$admin->email} a été créé avec succès.");
    }

    // Toggle statut utilisateur RÉEL
    public function toggleUserStatus(User $user): RedirectResponse
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'activé' : 'désactivé';
        $this->logAction("Compte utilisateur {$user->email} {$statusStr}", 'Users', 'WARNING');

        return redirect()->back()->with('success', "Le statut du compte {$user->email} a été mis à jour.");
    }

    // Gestion des Rôles et Permissions RÉELLES
    public function roles(): View
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('superadmin.roles.index', compact('roles', 'permissions'));
    }

    // Créer un rôle RÉEL
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $this->logAction("Création du nouveau rôle : {$validated['name']}", 'Security', 'SUCCESS');

        return redirect()->back()->with('success', "Le rôle {$validated['name']} a été créé avec succès.");
    }

    // Tous les utilisateurs RÉELS
    public function users(Request $request): View
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->role($request->input('role'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('superadmin.users.index', compact('users', 'roles'));
    }

    // Paramètres généraux & API Configuration RÉELLEMENT PERSISTÉS EN BASE DB
    public function settings(): View
    {
        $config = [
            'app_name' => Setting::get('app_name', config('app.name', 'FieldFlow Enterprise')),
            'app_env' => config('app.env', 'production'),
            'app_url' => config('app.url', 'http://127.0.0.1:8000'),
            'maintenance_mode' => (bool) Setting::get('maintenance_mode', false),
            
            // Google Maps API
            'google_maps_key' => Setting::get('google_maps_key', env('GOOGLE_MAPS_API_KEY', 'AIzaSyD-EXAMPLE-KEY-98432')),
            'google_maps_zoom' => Setting::get('google_maps_zoom', 12),
            
            // Caméras & Vidéos / Médias
            'camera_storage_disk' => Setting::get('camera_storage_disk', 'local'),
            'max_photo_size_mb' => Setting::get('max_photo_size_mb', 10),
            'max_video_size_mb' => Setting::get('max_video_size_mb', 100),
            'allowed_video_formats' => 'mp4, mov, avi, webm',
            'rtsp_streaming_gateway' => Setting::get('rtsp_streaming_gateway', 'rtsp://stream.fieldflow.ma/live'),
            
            // Mail & SMTP
            'mail_mailer' => config('mail.default', 'smtp'),
            'mail_host' => config('mail.mailers.smtp.host', 'smtp.mailtrap.io'),
        ];

        return view('superadmin.settings.index', compact('config'));
    }

    // Mise à jour RÉELLE des paramètres en base DB
    public function updateSettings(Request $request): RedirectResponse
    {
        Setting::set('app_name', $request->input('app_name'));
        Setting::set('google_maps_key', $request->input('google_maps_key'));
        Setting::set('google_maps_zoom', $request->input('google_maps_zoom'));
        Setting::set('camera_storage_disk', $request->input('camera_storage_disk'));
        Setting::set('max_photo_size_mb', $request->input('max_photo_size_mb'));
        Setting::set('max_video_size_mb', $request->input('max_video_size_mb'));
        Setting::set('rtsp_streaming_gateway', $request->input('rtsp_streaming_gateway'));
        Setting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');

        $this->logAction('Mise à jour des paramètres système et clés APIs (Google Maps, Caméras)', 'Settings', 'SUCCESS');

        return redirect()->back()->with('success', 'Les configurations APIs (Google Maps, Caméras/Vidéos) et paramètres système ont été enregistrés réellement en base de données.');
    }

    // Journaux d'audit (Logs) RÉELS depuis la table DB audit_logs
    public function logs(Request $request): View
    {
        // Si aucun log n'existe en base, créer les premiers logs initiaux
        if (AuditLog::count() === 0) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'action' => 'Initialisation du système de logs d\'audit',
                'module' => 'Security',
                'severity' => 'SUCCESS',
                'ip_address' => request()->ip(),
            ]);
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'action' => 'Connexion de l\'administrateur système',
                'module' => 'Auth',
                'severity' => 'INFO',
                'ip_address' => request()->ip(),
            ]);
        }

        $query = AuditLog::with('user');

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(20);

        return view('superadmin.logs.index', compact('logs'));
    }

    // Sauvegardes (Lecture RÉELLE des fichiers .sql sur disque storage/app/backups)
    public function backups(): View
    {
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $files = File::files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $sizeBytes = $file->getSize();
                $sizeFormatted = $sizeBytes >= 1048576 
                    ? number_format($sizeBytes / 1048576, 2) . ' MB' 
                    : number_format($sizeBytes / 1024, 2) . ' KB';

                $backups[] = [
                    'filename' => $file->getFilename(),
                    'filepath' => $file->getPathname(),
                    'size' => $sizeFormatted,
                    'size_bytes' => $sizeBytes,
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'type' => str_contains($file->getFilename(), 'auto') ? 'Automatique' : 'Manuel',
                ];
            }
        }

        // Trier par date décroissante
        usort($backups, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return view('superadmin.backups.index', compact('backups'));
    }

    // Génération RÉELLE du dump de la base de données SQL dans storage/app/backups/
    public function createBackup(): RedirectResponse
    {
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $filename = 'backup-fieldflow-' . date('Y-m-d-His') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Génération du contenu du dump SQL réel via PDO
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $keyName = "Tables_in_{$dbName}";

        $sqlDump = "-- FieldFlow Real Database Dump\n";
        $sqlDump .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- Database: {$dbName}\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$keyName ?? array_values((array)$tableObj)[0];

            // Table Structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createTable[0]->{'Create Table'} ?? '';
            $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sqlDump .= $createSql . ";\n\n";

            // Table Data
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $cols = array_keys($rowArray);
                $vals = array_map(function ($val) {
                    if (is_null($val)) return 'NULL';
                    return DB::getPdo()->quote($val);
                }, array_values($rowArray));

                $sqlDump .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sqlDump .= "\n";
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($filepath, $sqlDump);

        $this->logAction("Création d'une sauvegarde SQL réelle : {$filename}", 'Backup', 'SUCCESS', "Taille: " . number_format(filesize($filepath) / 1024, 2) . " KB");

        return redirect()->back()->with('success', "Sauvegarde SQL réelle générée avec succès : {$filename}");
    }

    // Téléchargement RÉEL du fichier .sql de sauvegarde
    public function downloadBackup(string $filename): BinaryFileResponse|RedirectResponse
    {
        $filepath = storage_path('app/backups/' . basename($filename));

        if (!File::exists($filepath)) {
            return redirect()->back()->with('error', 'Le fichier de sauvegarde demandé n\'existe pas.');
        }

        $this->logAction("Téléchargement du fichier de sauvegarde {$filename}", 'Backup', 'INFO');

        return response()->download($filepath);
    }

    // Suppression RÉELLE du fichier .sql de sauvegarde
    public function deleteBackup(string $filename): RedirectResponse
    {
        $filepath = storage_path('app/backups/' . basename($filename));

        if (File::exists($filepath)) {
            File::delete($filepath);
            $this->logAction("Suppression de la sauvegarde SQL {$filename}", 'Backup', 'WARNING');
            return redirect()->back()->with('success', "Le fichier {$filename} a été supprimé.");
        }

        return redirect()->back()->with('error', 'Fichier introuvable.');
    }
}
