<?php

namespace App\Services\SuperAdmin;

use App\Models\AuditLog;
use App\Models\PermissionException;
use App\Models\User;
use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Étape 1/2/3 du prompt "Interface d'assignation Rôles/Permissions" : matrice de
 * permissions par rôle (flux RBAC principal) + permissions individuelles (exceptions,
 * justifiées et journalisées). Toute la logique de calcul de diff, de garde-fou sur
 * Super Admin, et de journalisation vit ici — les Controllers restent de simples
 * points d'entrée (validation + appel Service).
 */
class RolePermissionService
{
    /**
     * Regroupement par domaine fonctionnel pour la lisibilité de la matrice — toute
     * permission non listée ici (rôle personnalisé créé depuis cette page, futur ajout)
     * tombe dans "Autres" plutôt que d'être masquée.
     */
    private const DOMAINES = [
        'Utilisateurs' => ['manage-users'],
        'Clients' => ['manage-clients'],
        'Chantiers' => ['manage-chantiers'],
        'Interventions' => ['manage-interventions', 'view-own-interventions', 'start-intervention', 'finish-intervention', 'submit-report', 'validate-reports'],
        'Formulaires' => ['manage-forms'],
        'Terrain' => ['upload-media', 'scan-qr'],
    ];

    /** Rôles considérés comme à privilège élevé — cf. AccountRoleStatsService. */
    private const PRIVILEGED_ROLES = ['Super Admin', 'admin', 'Administrateur'];

    /**
     * Étape 3 du prompt "Neutralisation d'urgence" : change le rôle d'un compte
     * utilisateur — journalise systématiquement, et marque explicitement toute
     * élévation vers un rôle à privilège élevé (sévérité renforcée dans le journal).
     * Le step-up auth est déjà garanti en amont par le middleware 'password.confirm'
     * sur la route ; la 2FA obligatoire pour un nouveau Super Admin est déjà forcée par
     * EnsureTwoFactorForSuperAdmin dès sa prochaine visite d'une route /superadmin/*
     * (aucune action supplémentaire nécessaire ici).
     *
     * @return array{old: string, new: string, isElevation: bool}
     */
    public function changeUserRole(User $user, string $newRoleName): array
    {
        $oldRoles = $user->roles->pluck('name');
        $oldRolesLabel = $oldRoles->isNotEmpty() ? $oldRoles->implode(', ') : 'Sans rôle';

        $user->syncRoles([$newRoleName]);

        $isElevation = in_array($newRoleName, self::PRIVILEGED_ROLES, true);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Changement de rôle pour {$user->email} : « {$oldRolesLabel} » → « {$newRoleName} »"
                . ($isElevation ? ' — ÉLÉVATION vers un rôle à privilège élevé' : ''),
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => $isElevation ? 'WARNING' : 'INFO',
            'ip_address' => request()->ip(),
        ]);

        return ['old' => $oldRolesLabel, 'new' => $newRoleName, 'isElevation' => $isElevation];
    }

    /**
     * Étape 1/3/6 du prompt "Créer un Nouveau Rôle" : création via le point d'entrée
     * unique (la validation d'unicité/réserve vit dans StoreRoleRequest, en amont).
     * Clone les permissions d'un rôle source si demandé, journalise systématiquement.
     *
     * @param array{name: string, description: ?string, clone_from: ?int} $data
     */
    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'guard_name' => 'web',
        ]);

        $clonedFrom = null;
        if (!empty($data['clone_from'])) {
            $source = Role::find($data['clone_from']);
            if ($source) {
                $role->syncPermissions($source->permissions);
                $clonedFrom = $source->name;
            }
        }

        $permissionNames = $role->permissions()->pluck('name')->all();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Création du rôle « {$role->name} »"
                . ($clonedFrom ? " (permissions copiées depuis « {$clonedFrom} »)" : '')
                . ($permissionNames ? ' — permissions initiales : ' . implode(', ', $permissionNames) : ' — sans permission initiale'),
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'SUCCESS',
            'ip_address' => request()->ip(),
        ]);

        return $role->fresh('permissions');
    }

    /**
     * @return array<string, Collection<int, Permission>>
     */
    public function permissionsByDomain(): array
    {
        $all = Permission::orderBy('name')->get();
        $grouped = [];
        $classees = [];

        foreach (self::DOMAINES as $domaine => $names) {
            $perms = $all->whereIn('name', $names)->values();
            if ($perms->isNotEmpty()) {
                $grouped[$domaine] = $perms;
                $classees = [...$classees, ...$names];
            }
        }

        $reste = $all->whereNotIn('name', $classees)->values();
        if ($reste->isNotEmpty()) {
            $grouped['Autres'] = $reste;
        }

        return $grouped;
    }

    /**
     * Applique la matrice de permissions d'un rôle via syncPermissions(), journalise le
     * diff (ajoutées/retirées) dans le Journal de Sécurité. Refuse de vider entièrement
     * les permissions du rôle Super Admin — seul rôle système dont la perte totale de
     * privilèges bloquerait la console elle-même.
     *
     * @param array<string> $permissionNames
     * @return array{added: array<string>, removed: array<string>}
     */
    public function updateRolePermissions(Role $role, array $permissionNames): array
    {
        if ($role->name === 'Super Admin' && empty($permissionNames)) {
            throw new RuntimeException(
                "Impossible de retirer la totalité des permissions du rôle Super Admin — ce rôle doit conserver au moins une permission pour éviter un verrouillage complet de la console."
            );
        }

        $avant = $role->permissions->pluck('name')->all();

        $role->syncPermissions($permissionNames);

        $ajoutees = array_values(array_diff($permissionNames, $avant));
        $retirees = array_values(array_diff($avant, $permissionNames));

        if (empty($ajoutees) && empty($retirees)) {
            return ['added' => [], 'removed' => []];
        }

        $resume = [];
        if ($ajoutees) {
            $resume[] = 'ajoutées: ' . implode(', ', $ajoutees);
        }
        if ($retirees) {
            $resume[] = 'retirées: ' . implode(', ', $retirees);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Permissions du rôle « {$role->name} » modifiées — " . implode(' / ', $resume),
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);

        return ['added' => $ajoutees, 'removed' => $retirees];
    }

    /**
     * Accorde une permission individuelle (exception au rôle) — toujours accompagnée
     * d'une justification obligatoire, tracée dans permission_exceptions (Étape 3 :
     * vue d'audit consolidée) et dans le Journal de Sécurité.
     */
    public function grantIndividualPermission(User $user, string $permissionName, string $justification): void
    {
        $permission = Permission::where('name', $permissionName)->firstOrFail();

        $user->givePermissionTo($permission);

        PermissionException::create([
            'user_id' => $user->id,
            'permission_id' => $permission->id,
            'justification' => $justification,
            'granted_by' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Permission individuelle « {$permissionName} » accordée à {$user->email} — justification : {$justification}",
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Retire une permission individuelle précédemment accordée en exception — marque la
     * trace `permission_exceptions` correspondante comme révoquée (jamais supprimée, pour
     * conserver l'historique d'audit) plutôt que de la retirer purement et simplement.
     */
    public function revokeIndividualPermission(User $user, string $permissionName): void
    {
        $permission = Permission::where('name', $permissionName)->firstOrFail();

        $user->revokePermissionTo($permission);

        PermissionException::actives()
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->latest()
            ->first()
            ?->update(['revoked_at' => now(), 'revoked_by' => auth()->id()]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Permission individuelle « {$permissionName} » retirée à {$user->email}",
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);
    }
}
