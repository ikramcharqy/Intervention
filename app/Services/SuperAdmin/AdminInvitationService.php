<?php

namespace App\Services\SuperAdmin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Étape 1 : création d'un compte administrateur sans jamais faire transiter son mot de
 * passe par le Super Admin créateur. Le compte est créé avec un mot de passe aléatoire
 * inconnu de tous, puis un lien d'invitation est envoyé via le broker de réinitialisation
 * de mot de passe déjà configuré (Mailer + table password_reset_tokens existants).
 */
class AdminInvitationService
{
    /**
     * @param array{name:string,prenom:string,email:string,telephone:?string,adresse:?string,role:string} $data
     * @return array{user: User, status: string, sent: bool}
     */
    public function invite(array $data): array
    {
        $admin = User::create([
            'name' => $data['name'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? '',
            'adresse' => $data['adresse'] ?? 'Casablanca, Maroc',
            'password' => Hash::make(Str::random(40)),
            'is_active' => true,
            'invitation_accepted_at' => null,
        ]);

        $role = Role::firstOrCreate(['name' => $data['role'], 'guard_name' => 'web']);
        $admin->syncRoles([$role]);

        $status = Password::sendResetLink(['email' => $admin->email]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Création de l'administrateur {$admin->email} — invitation envoyée par email",
            'module' => 'Users',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'SUCCESS',
            'ip_address' => request()->ip(),
        ]);

        return [
            'user' => $admin,
            'status' => $status,
            'sent' => $status === Password::RESET_LINK_SENT,
        ];
    }
}
