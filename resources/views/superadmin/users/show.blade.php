<x-super-admin-layout>
    <div class="space-y-6" x-data="{ showGrantModal: false, confirmRevoke: null }">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $targetUser->name }}</h1>
                <p class="text-sm text-slate-500 font-mono">{{ $targetUser->email }} · #USR-{{ $targetUser->id }}</p>
            </div>
            <a href="{{ route('superadmin.users') }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc]">&larr; Retour</a>
        </div>

        <!-- Étape 8.3 : statut de sécurité du compte -->
        <div class="bg-white rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] p-7 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-[11px] font-bold text-[#A1A7C4] uppercase mb-1">Rôle</p>
                <p class="text-sm font-bold text-[#131523]">{{ $targetUser->roles->pluck('name')->first() ?? 'Sans rôle' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#A1A7C4] uppercase mb-1">Statut</p>
                <p class="text-sm font-bold {{ $targetUser->is_active ? 'text-[#06A561]' : 'text-[#F0142F]' }}">{{ $targetUser->is_active ? 'Actif' : 'Inactif' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#A1A7C4] uppercase mb-1">Email vérifié</p>
                <p class="text-sm font-bold {{ $targetUser->email_verified_at ? 'text-[#06A561]' : 'text-[#F0142F]' }}">{{ $targetUser->email_verified_at ? 'Oui' : 'Non' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#A1A7C4] uppercase mb-1">2FA activée</p>
                <p class="text-sm font-bold {{ $targetUser->two_factor_confirmed_at ? 'text-[#06A561]' : 'text-[#F0142F]' }}">{{ $targetUser->two_factor_confirmed_at ? 'Oui' : 'Non' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#A1A7C4] uppercase mb-1">Dernière connexion</p>
                <p class="text-sm font-bold text-[#131523]">{{ $targetUser->last_login_at?->diffForHumans() ?? 'Jamais' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#A1A7C4] uppercase mb-1">Dernière IP</p>
                <p class="text-sm font-mono text-[#131523]">{{ $targetUser->last_login_ip ?? '—' }}</p>
            </div>
        </div>

        <!-- Étape 2 : Permissions individuelles (exceptions) — section distincte du flux
             principal (matrice par rôle, page "Rôles & Permissions"), réservée aux cas
             exceptionnels et toujours justifiée. -->
        <div class="bg-white rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] p-7">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#E6E9F4]">
                <div>
                    <h3 class="text-[16px] font-bold text-[#131523]">Permissions Individuelles (Exceptions)</h3>
                    <p class="text-xs text-[#A1A7C4] mt-1">En plus des permissions héritées du rôle « {{ \App\Models\User::roleLabel($targetUser->roles->pluck('name')->first()) }} » — réservé aux cas exceptionnels, toujours justifié.</p>
                </div>
                <button type="button" @click="showGrantModal = true" class="px-4 py-2.5 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold rounded-[4px] transition shrink-0">
                    + Accorder une exception
                </button>
            </div>

            <div class="space-y-3">
                @forelse($individualPermissions as $exception)
                    <div class="flex items-center justify-between gap-3 p-3 bg-[#FFF3DE] border border-[#F5DFAE] rounded-[4px]">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-[#131523]">{{ $exception->permission?->name }}</span>
                                <span class="px-2 py-0.5 text-[9px] font-bold text-[#B98900] bg-[#FFF3DE] border border-[#F5DFAE] rounded-full whitespace-nowrap">Permission exceptionnelle</span>
                            </div>
                            <p class="text-[11px] text-[#5A607F] mt-1">{{ $exception->justification }} — accordée {{ $exception->created_at?->diffForHumans() }}</p>
                        </div>
                        <button type="button" @click="confirmRevoke = '{{ $exception->permission?->name }}'" class="text-xs font-semibold text-[#F0142F] hover:underline px-3 py-1.5 bg-white rounded-[4px] shrink-0 whitespace-nowrap">
                            Retirer
                        </button>

                        <div x-show="confirmRevoke === '{{ $exception->permission?->name }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmRevoke = null">
                            <div class="bg-white w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                                <h3 class="text-base font-bold text-[#131523]">Retirer la permission « {{ $exception->permission?->name }} » ?</h3>
                                <p class="text-xs text-[#5A607F] leading-relaxed">Cette permission individuelle ne sera plus accordée à {{ $targetUser->prenom }} {{ $targetUser->name }}. Une re-confirmation de votre mot de passe sera demandée.</p>
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="confirmRevoke = null" class="px-4 py-2 text-xs font-semibold text-[#5A607F] hover:text-[#131523]">Annuler</button>
                                    <form method="POST" action="{{ route('superadmin.users.permissions.revoke', $targetUser) }}">
                                        @csrf
                                        <input type="hidden" name="permission" value="{{ $exception->permission?->name }}">
                                        <button type="submit" class="px-4 py-2 rounded-[4px] bg-[#F0142F] hover:bg-[#c9102a] text-white text-xs font-bold">Confirmer le retrait</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#A1A7C4] italic text-center py-4">Aucune permission individuelle — ce compte n'a que les permissions héritées de son rôle.</p>
                @endforelse
            </div>

            <!-- Modale d'ajout : justification obligatoire + confirmation avant application. -->
            <div x-show="showGrantModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="showGrantModal = false">
                <div class="bg-white w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4">
                    <div class="flex justify-between items-center border-b border-[#E6E9F4] pb-4">
                        <h3 class="text-base font-bold text-[#131523]">Accorder une permission exceptionnelle</h3>
                        <button type="button" @click="showGrantModal = false" class="text-[#A1A7C4] hover:text-[#131523] text-xl leading-none">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('superadmin.users.permissions.grant', $targetUser) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-[#5A607F] uppercase mb-1">Permission *</label>
                            <select name="permission" required class="w-full px-4 py-2.5 bg-[#F5F6FA] border border-transparent rounded-[4px] text-xs text-[#131523] focus:border-[#1E5EFF] focus:outline-none">
                                @foreach($allPermissions as $perm)
                                    <option value="{{ $perm->name }}">{{ $perm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5A607F] uppercase mb-1">Justification *</label>
                            <textarea name="justification" required maxlength="500" rows="3" placeholder="Raison précise de cette exception (obligatoire, conservée pour l'audit)…" class="w-full px-4 py-2.5 bg-[#F5F6FA] border border-transparent rounded-[4px] text-xs text-[#131523] focus:border-[#1E5EFF] focus:outline-none"></textarea>
                        </div>
                        <div class="p-3 rounded-[4px] bg-[#FFF3DE] flex items-start gap-2.5">
                            <x-icon name="circle-exclamation" class="w-4 h-4 text-[#B98900] shrink-0 mt-0.5" />
                            <p class="text-[11px] text-[#8A6800] leading-relaxed">
                                Réservé aux cas exceptionnels. Pour un besoin récurrent partagé par plusieurs comptes, créez ou ajustez un rôle plutôt que de multiplier les exceptions individuelles.
                            </p>
                        </div>
                        <div class="pt-2 flex justify-end gap-3 border-t border-[#E6E9F4]">
                            <button type="button" @click="showGrantModal = false" class="px-4 py-2 bg-[#F5F6FA] text-[#5A607F] text-xs font-semibold rounded-[4px]">Annuler</button>
                            <button type="submit" class="px-5 py-2 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-[4px]">Accorder (re-confirmation requise)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Résumé de l'activité récente -->
        <div class="bg-white rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] p-7">
            <h3 class="text-[16px] font-bold text-[#131523] pb-4 mb-4 border-b border-[#E6E9F4]">Activité Récente</h3>
            <div class="divide-y divide-[#E6E9F4]">
                @forelse($recentActivity as $log)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#131523] truncate">{{ $log->action }}</p>
                            <p class="text-[11px] text-[#A1A7C4]">{{ $log->module }} · IP: {{ $log->ip_address }}</p>
                        </div>
                        <span class="text-[10px] font-mono text-[#A1A7C4] whitespace-nowrap">{{ $log->created_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-xs text-[#A1A7C4] py-4 italic text-center">Aucune activité enregistrée pour ce compte.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-super-admin-layout>
