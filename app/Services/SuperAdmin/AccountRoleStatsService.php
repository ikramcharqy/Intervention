<?php

namespace App\Services\SuperAdmin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Source unique de vérité pour le KPI "Total Comptes" et la "Répartition des Rôles Spatie"
 * du dashboard Super Admin — les deux doivent toujours être dérivés d'ici pour ne plus
 * jamais diverger (bug historique : 11 comptes au total vs 10 répartis sur les rôles,
 * causé par un compte créé sans rôle Spatie assigné).
 *
 * Étape 2 du prompt "Neutralisation d'urgence" : la page "Administrateurs" doit aussi
 * dériver sa requête d'ici plutôt que dupliquer sa propre liste de noms de rôles, pour
 * qu'elle ne puisse plus diverger silencieusement de l'inventaire réel.
 */
class AccountRoleStatsService
{
    /** Rôles considérés comme "à privilège élevé" — inventoriés sur "Administrateurs". */
    public const ADMIN_TIER_ROLE_NAMES = ['Super Admin', 'admin', 'Administrateur'];

    /**
     * Requête canonique des comptes à privilège élevé — utilisée à la fois par
     * SystemController::admins() et par le contrôle de cohérence ci-dessous.
     */
    public function adminTierQuery(): Builder
    {
        $existingRoles = Role::whereIn('name', self::ADMIN_TIER_ROLE_NAMES)->pluck('name')->toArray();

        return $existingRoles !== []
            ? User::role($existingRoles)->with('roles')
            : User::whereRaw('1 = 0');
    }

    public function adminTierCount(): int
    {
        return (clone $this->adminTierQuery())->count();
    }

    /**
     * Étape 2.4 : compare l'inventaire obtenu via le scope Eloquent Spatie (celui utilisé
     * par la page "Administrateurs") avec une requête SQL brute sur le pivot
     * `model_has_roles`, indépendante de tout scope global/relation Eloquent qui
     * pourrait silencieusement exclure un compte réel (ex: soft-delete, guard, cache).
     *
     * @return array{
     *     consistent: bool,
     *     via_eloquent_count: int,
     *     via_raw_sql_count: int,
     *     missing_from_page: array<int>,
     *     extra_on_page: array<int>,
     * }
     */
    public function adminInventoryConsistencyCheck(): array
    {
        $viaEloquent = $this->adminTierQuery()->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

        // INNER JOIN vers `users` (pas seulement le pivot) : une ligne model_has_roles
        // orpheline (utilisateur supprimé sans que son rôle Spatie ait été nettoyé,
        // ex: forceDelete() d'un compte de test) ne doit jamais compter comme un compte
        // à privilège élevé réel encore présent.
        $viaRawSql = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->whereNull('users.deleted_at')
            ->where('model_has_roles.model_type', User::class)
            ->whereIn('roles.name', self::ADMIN_TIER_ROLE_NAMES)
            ->distinct()
            ->pluck('model_has_roles.model_id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        $missing = array_values(array_diff($viaRawSql, $viaEloquent));
        $extra = array_values(array_diff($viaEloquent, $viaRawSql));

        return [
            'consistent' => $missing === [] && $extra === [],
            'via_eloquent_count' => count($viaEloquent),
            'via_raw_sql_count' => count($viaRawSql),
            'missing_from_page' => $missing,
            'extra_on_page' => $extra,
        ];
    }

    /**
     * @return array{
     *     total: int,
     *     by_role: array<string, int>,
     *     unassigned: int,
     * }
     */
    public function breakdown(): array
    {
        $adminRoles = Role::whereIn('name', ['admin', 'Administrateur'])->pluck('name')->toArray();
        $techRoles = Role::whereIn('name', ['technicien', 'Technicien'])->pluck('name')->toArray();

        $byRole = [
            'Super Admin' => Role::where('name', 'Super Admin')->exists() ? User::role('Super Admin')->count() : 0,
            'Administrateur' => !empty($adminRoles) ? User::role($adminRoles)->count() : 0,
            'Commercial' => Role::where('name', 'Commercial')->exists() ? User::role('Commercial')->count() : 0,
            'Technicien' => !empty($techRoles) ? User::role($techRoles)->count() : 0,
            'Client' => Role::where('name', 'Client')->exists() ? User::role('Client')->count() : 0,
        ];

        $total = User::count();
        $unassigned = $this->unassignedQuery()->count();

        return [
            'total' => $total,
            'by_role' => $byRole,
            'unassigned' => $unassigned,
        ];
    }

    public function unassignedQuery()
    {
        return User::doesntHave('roles');
    }

    public function unassignedCount(): int
    {
        return $this->unassignedQuery()->count();
    }
}
