<x-super-admin-layout>
    <div class="space-y-8" x-data="{ confirmRoleChange: null, pendingRole: null, pendingUserName: '' }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Gestion Globale des Comptes Utilisateurs</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Supervision globale de tous les comptes enregistrés dans l'ensemble des 5 rôles.</p>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.users') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email..." class="w-full pl-10 pr-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-white placeholder-[#A1A7C4] dark:placeholder-slate-500 focus:outline-none focus:border-[#1E5EFF]">
                    <svg class="h-4 w-4 text-[#A1A7C4] dark:text-slate-500 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <select name="role" onchange="this.form.submit()" class="w-full sm:w-48 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-white focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Tous les Rôles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ \App\Models\User::roleLabel($r->name) }}</option>
                    @endforeach
                    <option value="unassigned" {{ request('role') === 'unassigned' ? 'selected' : '' }}>Sans rôle</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px] border border-[#D7DBEC] dark:border-slate-700">Rechercher</button>
            </form>

            <span class="text-xs text-[#A1A7C4] dark:text-slate-400 font-mono whitespace-nowrap">{{ $users->total() }} Compte(s) inscrit(s)</span>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Téléphone</th>
                            <th class="px-6 py-4">Rôle Attribué</th>
                            <th class="px-6 py-4">2FA</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($users as $user)
                            @php
                                $roleName = $user->roles->pluck('name')->first();
                                $roleColor = \App\Models\User::roleBadgeColor($roleName);
                                $isPrivilegedRole = in_array($roleName, ['Super Admin', 'admin', 'Administrateur'], true);
                            @endphp
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700 text-[#131523] dark:text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('superadmin.users.show', $user) }}" class="block leading-tight font-bold text-xs text-[#131523] dark:text-slate-100 hover:text-[#1E5EFF] hover:underline">{{ $user->name }}</a>
                                            <span class="text-[11px] text-[#A1A7C4] dark:text-slate-500">ID: #USR-{{ $user->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-300">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-[#5A607F] dark:text-slate-400 text-xs">{{ $user->telephone ?? 'Non renseigné' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-[4px] {{ $roleColor['bg'] }} {{ $roleColor['text'] }}">
                                        {{ \App\Models\User::roleLabel($roleName) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->two_factor_confirmed_at)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">Activée</span>
                                    @elseif($isPrivilegedRole)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">Non activée</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#A1A7C4] dark:text-slate-500 bg-[#F5F6FA] dark:bg-slate-800 rounded-full">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <select @change="confirmRoleChange = { userId: {{ $user->id }}, from: '{{ $roleName ?? 'Sans rôle' }}', to: $event.target.value, userName: '{{ addslashes($user->name) }}' }; $event.target.value = ''"
                                                class="text-xs bg-[#F5F6FA] dark:bg-slate-800 border border-[#D7DBEC] dark:border-slate-700 rounded-lg text-[#5A607F] dark:text-slate-300 px-2 py-1.5 focus:outline-none focus:border-[#1E5EFF]">
                                            <option value="" selected>Changer rôle…</option>
                                            @foreach($roles as $r)
                                                <option value="{{ $r->name }}">{{ \App\Models\User::roleLabel($r->name) }}</option>
                                            @endforeach
                                        </select>
                                        <form method="POST" action="{{ route('superadmin.users.toggleStatus', $user) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-white px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 border border-[#D7DBEC] dark:border-slate-700 rounded-lg transition">
                                                {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Étape 3 : confirmation explicite avant tout changement de rôle (ancien →
             nouveau), avec un second palier de vigilance distinct pour toute élévation
             vers Super Admin/Administrateur. Step-up auth (re-confirmation du mot de
             passe) déjà garanti côté serveur par le middleware 'password.confirm' sur
             cette route, indépendamment de cette confirmation cliente. -->
        <template x-if="confirmRoleChange">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmRoleChange = null">
                <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                    <template x-if="!['Super Admin', 'admin', 'Administrateur'].includes(confirmRoleChange.to)">
                        <div>
                            <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Changer le rôle de <span x-text="confirmRoleChange.userName"></span> ?</h3>
                            <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed mt-2">
                                <span class="font-mono" x-text="confirmRoleChange.from"></span> → <span class="font-mono font-bold text-[#131523] dark:text-slate-100" x-text="confirmRoleChange.to"></span>.
                                Une re-confirmation de votre mot de passe sera demandée.
                            </p>
                        </div>
                    </template>
                    <template x-if="['Super Admin', 'admin', 'Administrateur'].includes(confirmRoleChange.to)">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-[#F0142F] dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                                <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Élévation vers un rôle à privilège élevé</h3>
                            </div>
                            <div class="p-3 rounded-lg bg-[#FDE3E6] dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/40 text-[#8A0E20] dark:text-rose-300 text-xs leading-relaxed">
                                <span x-text="confirmRoleChange.userName"></span> obtiendrait le rôle <strong x-text="confirmRoleChange.to"></strong> (accès étendu à la console d'administration). Ancien rôle : <span class="font-mono" x-text="confirmRoleChange.from"></span>.
                                Cette action sera journalisée comme une élévation de privilège. Une re-confirmation de votre mot de passe sera demandée.
                            </div>
                        </div>
                    </template>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="confirmRoleChange = null" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                        <form method="POST" :action="'/superadmin/users/' + confirmRoleChange.userId + '/role'">
                            @csrf
                            <input type="hidden" name="role" :value="confirmRoleChange.to">
                            <button type="submit" :class="['Super Admin', 'admin', 'Administrateur'].includes(confirmRoleChange.to) ? 'bg-[#F0142F] hover:bg-[#c9102a]' : 'bg-[#1E5EFF] hover:bg-[#174ecc]'" class="px-4 py-2 rounded-[4px] text-white text-xs font-bold">Confirmer le changement</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-super-admin-layout>
