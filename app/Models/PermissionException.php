<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

/**
 * Trace d'une permission individuelle accordée à un utilisateur en plus de celles
 * héritées de son rôle (exception au modèle RBAC standard) — cf. prompt "Interface
 * d'assignation Rôles/Permissions", Étape 2/3. Distincte du pivot Spatie
 * `model_has_permissions` : porte la justification obligatoire et un historique
 * append-only (jamais supprimée, seulement marquée révoquée) pour l'audit.
 */
class PermissionException extends Model
{
    protected $fillable = [
        'user_id',
        'permission_id',
        'justification',
        'granted_by',
        'revoked_at',
        'revoked_by',
    ];

    protected $casts = [
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeActives(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }
}
