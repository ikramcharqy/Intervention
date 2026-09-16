<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use App\Models\PermissionException;
use App\Services\SessionSecurityService;
use App\Services\SuperAdmin\RolePermissionService;
use RuntimeException;
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
    public function __construct(private SessionSecurityService $sessionSecurityService)
    {
    }

    // Étape 3 : point d'entrée "Sécurité" manquant dans la sidebar — réutilise le même
    // mécanisme déjà en place côté portail Client (LoginHistory, SessionSecurityService),
    // adapté pour inclure le statut/les actions 2FA propres au rôle Super Admin/Admin.
    public function securite(Request $request): View
    {
        $user = auth()->user();
        $sessionCouranteId = $request->session()->getId();

        // Étape 5 : résumé des tentatives de connexion échouées sur CE compte (mauvais
        // mot de passe + code 2FA invalide, toutes deux journalisées avec "Échec" dans
        // le libellé de l'action — cf. AppServiceProvider et TwoFactorAuthService).
        $echecsRecents = AuditLog::where('user_id', $user->id)
            ->where('category', AuditLog::CATEGORY_SECURITY)
            ->where('action', 'like', '%Échec%')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return view('superadmin.securite.index', [
            'derniereConnexion' => $user->last_login_at ?? null,
            'ip' => $request->ip(),
            'sessions' => $this->sessionSecurityService->sessionsActives($user, $sessionCouranteId),
            'historique' => LoginHistory::where('user_id', $user->id)
                ->latest('logged_in_at')
                ->limit(10)
                ->get(),
            'echecsRecents' => $echecsRecents,
        ]);
    }

    public function securiteRevoquerSession(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();
        $sessionCouranteId = $request->session()->getId();

        if ($id === $sessionCouranteId) {
            return back()->with('error', 'Impossible de déconnecter votre session actuelle depuis cette liste — utilisez le bouton Déconnexion.');
        }

        $this->sessionSecurityService->revoquerSession($user, $id);

        return back()->with('success', 'Session déconnectée avec succès.');
    }

    public function securiteRevoquerAutresSessions(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $sessionCouranteId = $request->session()->getId();

        $nb = $this->sessionSecurityService->revoquerAutresSessions($user, $sessionCouranteId);

        return back()->with('success', $nb > 0
            ? "Déconnecté(e) de {$nb} autre(s) session(s)."
            : 'Aucune autre session active à déconnecter.');
    }
    // Helper pour ajouter un log d'audit réel
    private function logAction(string $action, string $module = 'System', string $severity = 'INFO', ?string $details = null): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => $action,
            'module' => $module,
            'severity' => $severity,
            'category' => AuditLog::categoryForModule($module),
            'ip_address' => request()->ip(),
            'details' => $details,
        ]);
    }

    // Gestion des Administrateurs
    // Étape 2 : requête dérivée d'AccountRoleStatsService::adminTierQuery() — source
    // unique de vérité partagée avec le contrôle de cohérence planifié, pour que cette
    // page ne puisse plus diverger silencieusement de l'inventaire réel des comptes à
    // privilège élevé.
    public function admins(Request $request, \App\Services\SuperAdmin\AccountRoleStatsService $stats): View
    {
        $query = $stats->adminTierQuery();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->latest()->paginate(15);
        $roles = Role::whereIn('name', ['admin', 'Administrateur', 'Super Admin'])->get();

        // Étape 2 : point de défaillance unique si un seul compte Super Admin existe.
        $superAdminCount = User::role('Super Admin')->count();

        return view('superadmin.admins.index', compact('admins', 'roles', 'superAdminCount'));
    }

    // Étape 5 — Rappel manuel (email) pour un administrateur dont l'invitation est
    // acceptée mais la 2FA obligatoire toujours pas confirmée. Action non destructive et
    // réversible (un simple email) — volontairement hors du groupe 'password.confirm'.
    public function resend2FAReminder(User $user, \App\Services\TwoFactorAuthService $twoFactor): RedirectResponse
    {
        $twoFactor->sendActivationReminder($user);

        return redirect()->back()->with('success', "Un rappel d'activation 2FA a été envoyé à {$user->email}.");
    }

    // Étape 1 : création d'un administrateur par invitation par email — plus jamais de
    // mot de passe saisi par le créateur (logique déléguée à AdminInvitationService).
    public function storeAdmin(Request $request, \App\Services\SuperAdmin\AdminInvitationService $invitationService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'role' => 'required|string',
        ]);

        $result = $invitationService->invite($validated);

        if (!$result['sent']) {
            return redirect()->back()->with('error', "Le compte {$result['user']->email} a été créé, mais l'envoi de l'email d'invitation a échoué ({$result['status']}). Vérifiez la configuration mail.");
        }

        return redirect()->back()->with('success', "Le compte administrateur {$result['user']->email} a été créé — un email d'invitation vient de lui être envoyé pour définir son mot de passe.");
    }

    // Toggle statut utilisateur RÉEL (réactivation — pas de step-up, action réversible)
    public function toggleUserStatus(User $user): RedirectResponse
    {
        // Étape 1 : blocage serveur, indépendant du blocage visuel — même compte connecté
        // ne peut pas se désactiver lui-même via cette route non plus (risque d'auto-
        // verrouillage identique à deactivateAdmin ci-dessous).
        if ($user->is_active && $user->id === auth()->id()) {
            return redirect()->back()->with('error', "Vous ne pouvez pas désactiver votre propre compte.");
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'activé' : 'désactivé';
        $this->logAction("Compte utilisateur {$user->email} {$statusStr}", 'Users', 'WARNING');

        return redirect()->back()->with('success', "Le statut du compte {$user->email} a été mis à jour.");
    }

    // Étape 5 — Désactivation d'un compte administrateur : action destructive, protégée
    // par step-up auth (route groupée sous 'password.confirm') et journalisée.
    public function deactivateAdmin(User $user): RedirectResponse
    {
        // Étape 1 (priorité absolue) : un compte ne peut jamais se désactiver lui-même —
        // risque d'auto-verrouillage total, critique en particulier pour le seul compte
        // Super Admin. Un Super Admin reste libre de désactiver un AUTRE Super Admin
        // (le blocage ne porte que sur l'identité de l'acteur, pas sur son rôle).
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', "Vous ne pouvez pas désactiver votre propre compte.");
        }

        $user->is_active = false;
        $user->save();

        $this->logAction("Désactivation du compte administrateur {$user->email}", 'Security', 'WARNING');

        return redirect()->back()->with('success', "Le compte {$user->email} a été désactivé.");
    }

    // Étape 5.4 — Forcer la réinitialisation de la 2FA d'un administrateur ayant perdu
    // l'accès à son application d'authentification et ses codes de secours.
    public function forceTwoFactorReset(User $user, \App\Services\TwoFactorAuthService $twoFactor): RedirectResponse
    {
        $twoFactor->reset($user);

        $this->logAction("Réinitialisation forcée de la 2FA pour l'administrateur {$user->email} (par un Super Admin)", 'Security', 'WARNING');

        return redirect()->back()->with('success', "La 2FA de {$user->email} a été réinitialisée — un nouvel enrôlement sera exigé à sa prochaine connexion.");
    }

    // Gestion des Rôles et Permissions RÉELLES
    public function roles(RolePermissionService $rolePermissionService): View
    {
        // withCount('users') sur le pivot Spatie model_has_roles — nouvel élément utile
        // en liste condensée (Étape 1.2 de la refonte liste + tiroir).
        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::all();
        $permissionsByDomain = $rolePermissionService->permissionsByDomain();
        $exceptionsCount = PermissionException::actives()->count();

        return view('superadmin.roles.index', compact('roles', 'permissions', 'permissionsByDomain', 'exceptionsCount'));
    }

    // Créer un rôle RÉEL
    // Étape 1 : la validation d'unicité/réserve de nom vit dans StoreRoleRequest, en
    // amont du Controller. La création (+ clonage de permissions + journalisation)
    // vit dans RolePermissionService::createRole().
    public function storeRole(\App\Http\Requests\StoreRoleRequest $request, RolePermissionService $rolePermissionService): RedirectResponse
    {
        $role = $rolePermissionService->createRole($request->validated());

        // Étape 4 : enchaînement automatique vers le tiroir de permissions du rôle
        // fraîchement créé — flashé en session, lu par le composant Alpine au chargement
        // (cf. x-init sur la vue) pour ouvrir le tiroir sans étape de recherche.
        return redirect()->route('superadmin.roles')
            ->with('success', "Le rôle {$role->name} a été créé avec succès.")
            ->with('openRoleDrawer', ['id' => $role->id, 'permissions' => $role->permissions->pluck('name')->values()]);
    }

    // Étape 1 — Matrice de permissions par rôle : step-up auth (route groupée sous
    // 'password.confirm'), diff calculé et journalisé dans RolePermissionService.
    public function updateRolePermissions(Role $role, Request $request, RolePermissionService $rolePermissionService): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        try {
            $diff = $rolePermissionService->updateRolePermissions($role, $validated['permissions'] ?? []);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (empty($diff['added']) && empty($diff['removed'])) {
            return redirect()->back()->with('success', "Aucun changement à appliquer sur les permissions du rôle {$role->name}.");
        }

        return redirect()->back()->with('success', "Permissions du rôle {$role->name} mises à jour.");
    }

    // Étape 3 — Vue d'audit consolidée des permissions individuelles actives.
    public function permissionExceptions(): View
    {
        $exceptions = PermissionException::actives()
            ->with(['user', 'permission', 'grantedBy'])
            ->latest()
            ->paginate(20);

        return view('superadmin.roles.exceptions', compact('exceptions'));
    }

    // Étape 2 — Permission individuelle (exception au rôle) : justification obligatoire,
    // step-up auth (route groupée sous 'password.confirm').
    public function grantIndividualPermission(User $user, Request $request, RolePermissionService $rolePermissionService): RedirectResponse
    {
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
            'justification' => 'required|string|max:500',
        ]);

        $rolePermissionService->grantIndividualPermission($user, $validated['permission'], $validated['justification']);

        return redirect()->back()->with('success', "La permission « {$validated['permission']} » a été accordée à {$user->email} (exception individuelle).");
    }

    public function revokeIndividualPermission(User $user, Request $request, RolePermissionService $rolePermissionService): RedirectResponse
    {
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $rolePermissionService->revokeIndividualPermission($user, $validated['permission']);

        return redirect()->back()->with('success', "La permission individuelle « {$validated['permission']} » a été retirée à {$user->email}.");
    }

    // Tous les utilisateurs RÉELS
    public function users(Request $request): View
    {
        $query = User::with('roles');

        if ($request->input('role') === 'unassigned') {
            $query->doesntHave('roles');
        } elseif ($request->filled('role')) {
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

    // Étape 8.3 : fiche compte individuel — statut de sécurité (email vérifié, 2FA,
    // dernière connexion) et résumé de l'activité récente issue du journal d'audit.
    public function showUser(User $user): View
    {
        $user->load('roles');

        // Étape 2 : permissions individuelles actives (exceptions au rôle) — badge
        // "Permission exceptionnelle" + formulaire d'ajout/retrait sur cette fiche.
        $individualPermissions = PermissionException::actives()
            ->where('user_id', $user->id)
            ->with('permission')
            ->latest()
            ->get();

        $allPermissions = Permission::orderBy('name')->get();

        return view('superadmin.users.show', [
            'targetUser' => $user,
            'recentActivity' => AuditLog::where('user_id', $user->id)->latest()->limit(15)->get(),
            'individualPermissions' => $individualPermissions,
            'allPermissions' => $allPermissions,
        ]);
    }

    // Changement de rôle d'un compte existant — action de gouvernance manquante du journal
    // de sécurité (Étape 2) : jusqu'ici seule la création de rôle était journalisée, jamais
    // le changement de rôle d'un compte déjà existant.
    // Étape 3 : logique (diff, marquage élévation, journalisation) déplacée dans
    // RolePermissionService::changeUserRole() — step-up auth déjà garanti par le
    // middleware 'password.confirm' sur cette route.
    public function updateUserRole(User $user, Request $request, RolePermissionService $rolePermissionService): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $result = $rolePermissionService->changeUserRole($user, $validated['role']);

        return redirect()->back()->with('success', "Le rôle de {$user->email} a été mis à jour vers {$validated['role']}.");
    }

    // Étape 5 : les clés API ne sont jamais affichées en clair par défaut — seul un aperçu
    // masqué (4 derniers caractères) est rendu ; la valeur réelle n'est révélée que via
    // revealApiKey() (step-up auth + journalisée).
    private function maskApiKey(string $key): string
    {
        if (strlen($key) <= 4) {
            return str_repeat('•', 8);
        }
        return str_repeat('•', 8) . substr($key, -4);
    }

    // Paramètres généraux & API Configuration RÉELLEMENT PERSISTÉS EN BASE DB
    public function settings(): View
    {
        $googleMapsKey = Setting::get('google_maps_key', env('GOOGLE_MAPS_API_KEY', 'AIzaSyD-EXAMPLE-KEY-98432'));
        $rtspGateway = Setting::get('rtsp_streaming_gateway', 'rtsp://stream.technitrack.ma/live');

        $config = [
            'app_name' => Setting::get('app_name', config('app.name', 'TechniTrack')),
            'app_env' => config('app.env', 'production'),
            'app_url' => config('app.url', 'http://127.0.0.1:8000'),
            'maintenance_mode' => (bool) Setting::get('maintenance_mode', false),
            'maintenance_message' => Setting::get('maintenance_message', ''),

            // Google Maps API — masquée par défaut (Étape 5.1)
            'google_maps_key_masked' => $this->maskApiKey($googleMapsKey),
            'google_maps_key_rotated_at' => Setting::get('google_maps_key_rotated_at'),
            'google_maps_zoom' => Setting::get('google_maps_zoom', 12),

            // Caméras & Vidéos / Médias
            // Étape 7 : un seul disque de stockage est réellement implémenté (le SDK S3
            // n'est pas installé et aucun code applicatif ne lit ce paramètre pour choisir
            // un disque) — champ affiché en lecture seule plutôt qu'un select trompeur.
            'camera_storage_disk' => 'local',
            'max_photo_size_mb' => Setting::get('max_photo_size_mb', 10),
            'max_video_size_mb' => Setting::get('max_video_size_mb', 100),
            'allowed_photo_formats' => Setting::get('allowed_photo_formats', 'jpg,jpeg,png,webp'),
            'allowed_video_formats' => Setting::get('allowed_video_formats', 'mp4,mov,avi,webm'),
            // Étape 4 : masquée par défaut comme la clé Google Maps, au cas où un usage
            // réel futur y intègre des identifiants (rtsp://user:pass@host/...).
            'rtsp_streaming_gateway_masked' => $this->maskApiKey($rtspGateway),

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

        // La clé API n'est plus jamais renvoyée en clair dans le formulaire (Étape 5.1) —
        // elle n'est donc mise à jour que si un opérateur saisit explicitement une nouvelle
        // valeur de rotation ; sinon la clé existante est conservée telle quelle.
        if ($request->filled('google_maps_key_new')) {
            Setting::set('google_maps_key', $request->input('google_maps_key_new'));
            Setting::set('google_maps_key_rotated_at', now()->toDateTimeString());
            $this->logAction('Rotation de la clé API Google Maps', 'Security', 'WARNING');
        }

        // Étape 4 : même principe que la clé Google Maps — l'URL RTSP n'est mise à jour
        // que si une nouvelle valeur est explicitement saisie (jamais renvoyée en clair).
        if ($request->filled('rtsp_streaming_gateway_new')) {
            Setting::set('rtsp_streaming_gateway', $request->input('rtsp_streaming_gateway_new'));
            $this->logAction("Mise à jour de la passerelle de streaming RTSP", 'Security', 'WARNING');
        }

        Setting::set('google_maps_zoom', $request->input('google_maps_zoom'));
        Setting::set('max_photo_size_mb', $request->input('max_photo_size_mb'));
        Setting::set('max_video_size_mb', $request->input('max_video_size_mb'));
        Setting::set('allowed_photo_formats', $request->input('allowed_photo_formats'));
        Setting::set('allowed_video_formats', $request->input('allowed_video_formats'));

        // Étape 5.4 : le changement d'état du Mode Maintenance est journalisé séparément
        // et explicitement (activation/désactivation), distinct du log générique de
        // sauvegarde des paramètres ci-dessous — c'est l'action la plus sensible de cette
        // page (verrouille l'accès de tous les comptes non-admin).
        $maintenanceModeAvant = (bool) Setting::get('maintenance_mode', false);
        $maintenanceModeApres = $request->boolean('maintenance_mode');
        if ($maintenanceModeAvant !== $maintenanceModeApres) {
            $this->logAction(
                $maintenanceModeApres ? 'Activation du Mode Maintenance' : 'Désactivation du Mode Maintenance',
                'Security',
                'WARNING'
            );
        }
        Setting::set('maintenance_mode', $maintenanceModeApres ? '1' : '0');
        Setting::set('maintenance_message', $request->input('maintenance_message', ''));

        $this->logAction('Mise à jour des paramètres système (Caméras, Maintenance)', 'Settings', 'SUCCESS');

        return redirect()->back()->with('success', 'Les configurations APIs (Google Maps, Caméras/Vidéos) et paramètres système ont été enregistrés réellement en base de données.');
    }

    // Étape 5.1 : révélation explicite de la clé API en clair — nécessite une
    // re-confirmation du mot de passe (route groupée sous 'password.confirm') et est
    // elle-même journalisée dans le Journal de Sécurité & Gouvernance.
    public function revealApiKey(Request $request)
    {
        $key = Setting::get('google_maps_key', '');

        $this->logAction('Révélation de la clé API Google Maps en clair', 'Security', 'WARNING');

        return response()->json(['key' => $key]);
    }

    // Étape 4 du prompt "Corrections critiques Paramètres" : même traitement que la clé
    // Google Maps — une URL RTSP peut légitimement embarquer des identifiants
    // (rtsp://user:pass@host/...) dans un usage réel futur.
    public function revealRtspUrl(Request $request)
    {
        $url = Setting::get('rtsp_streaming_gateway', '');

        $this->logAction('Révélation de la passerelle RTSP en clair', 'Security', 'WARNING');

        return response()->json(['url' => $url]);
    }

    // Étape 7.1 : capacités d'action immédiate en cas d'incident, accessibles directement
    // depuis le dashboard racine. Recherche par email pour ne pas exposer d'ID interne
    // dans un formulaire minimaliste.
    public function quickDisableAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return redirect()->back()->with('error', "Aucun compte ne correspond à {$validated['email']}.");
        }

        $user->is_active = false;
        $user->save();

        $this->logAction("Désactivation immédiate (incident) du compte {$user->email}", 'Security', 'WARNING');

        return redirect()->back()->with('success', "Le compte {$user->email} a été désactivé immédiatement.");
    }

    public function quickRevokeSessions(Request $request, \App\Services\SessionSecurityService $sessionSecurityService): RedirectResponse
    {
        $validated = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return redirect()->back()->with('error', "Aucun compte ne correspond à {$validated['email']}.");
        }

        $count = DB::table('sessions')->where('user_id', $user->id)->delete();

        $this->logAction("Révocation immédiate (incident) de {$count} session(s) pour {$user->email}", 'Security', 'WARNING');

        return redirect()->back()->with('success', "{$count} session(s) active(s) de {$user->email} ont été révoquées.");
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
                'category' => AuditLog::CATEGORY_SECURITY,
                'ip_address' => request()->ip(),
            ]);
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Super Admin',
                'action' => 'Connexion de l\'administrateur système',
                'module' => 'Auth',
                'severity' => 'INFO',
                'category' => AuditLog::CATEGORY_SECURITY,
                'ip_address' => request()->ip(),
            ]);
        }

        // Journal de Sécurité & Gouvernance uniquement (cf. Étape 2) — l'activité métier
        // est consultable depuis les dashboards Admin/Commercial.
        $query = AuditLog::with('user')->security();

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

        $logs = $query->latest()->paginate(20)->withQueryString();

        // Répartition par sévérité (toujours sur l'ensemble du journal de sécurité,
        // indépendamment des filtres actifs) pour la rangée de synthèse en tête de page.
        $severityCounts = AuditLog::security()
            ->selectRaw('severity, count(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        return view('superadmin.logs.index', compact('logs', 'severityCounts'));
    }

    // Sauvegardes (Lecture RÉELLE des fichiers .sql sur disque storage/app/backups)
    // Étape 1 du prompt "Correction APP_NAME + sécurisation Sauvegardes" : toute la
    // logique déplacée dans BackupService (préfixe de fichier dérivé de APP_NAME,
    // rétention, détection de sauvegarde automatique en retard) — ce Controller reste un
    // simple point d'entrée.
    public function backups(\App\Services\SuperAdmin\BackupService $backupService): View
    {
        return view('superadmin.backups.index', [
            'backups' => $backupService->list(),
            'retentionDays' => $backupService->retentionDays(),
            'autoEnRetard' => $backupService->derniereAutoEnRetard(),
        ]);
    }

    public function createBackup(\App\Services\SuperAdmin\BackupService $backupService): RedirectResponse
    {
        $result = $backupService->create(automatic: false);

        return redirect()->back()->with('success', "Sauvegarde SQL générée avec succès : {$result['filename']}");
    }

    public function downloadBackup(string $filename, \App\Services\SuperAdmin\BackupService $backupService): BinaryFileResponse|RedirectResponse
    {
        $filepath = $backupService->resolvePath($filename);

        if (!$filepath) {
            return redirect()->back()->with('error', 'Le fichier de sauvegarde demandé n\'existe pas.');
        }

        $backupService->logDownload($filename);

        return response()->download($filepath);
    }

    public function deleteBackup(string $filename, \App\Services\SuperAdmin\BackupService $backupService): RedirectResponse
    {
        if ($backupService->delete($filename)) {
            return redirect()->back()->with('success', "Le fichier {$filename} a été supprimé.");
        }

        return redirect()->back()->with('error', 'Fichier introuvable.');
    }
}
