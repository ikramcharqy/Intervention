<x-super-admin-layout>
    <div class="space-y-6" x-data="{ showCreateModal: false, confirmDeactivate: null, confirmForceReset: null }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Gestion des Administrateurs</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Création et contrôle des privilèges d'administration métier et système.</p>
            </div>
            <button @click="showCreateModal = true" class="inline-flex items-center gap-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold px-4 py-2.5 transition shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Créer un Administrateur</span>
            </button>
        </div>

        <!-- Étape 2 : point de défaillance unique — encart informatif, refermable, non
             bloquant (distinct des alertes rouges "danger" déjà utilisées ailleurs). -->
        @if($superAdminCount === 1)
            <div x-data="{ show: true }" x-show="show" x-cloak class="bg-[#EAF0FF] dark:bg-blue-500/10 border border-[#B8CFFF] dark:border-blue-500/30 rounded-[6px] p-4 flex items-start gap-3">
                <x-icon name="circle-exclamation" class="w-4 h-4 text-[#1E5EFF] dark:text-blue-400 shrink-0 mt-0.5" />
                <p class="text-xs sm:text-sm text-[#1E5EFF] dark:text-blue-300 flex-1">
                    Un seul compte Super Admin existe actuellement — en cas de perte d'accès à ce compte, la console d'administration deviendrait inaccessible. Il est recommandé de créer un second compte Super Admin.
                </p>
                <button type="button" @click="show = false" class="text-[#1E5EFF] dark:text-blue-400 hover:text-[#131523] dark:hover:text-slate-100 text-lg leading-none shrink-0">&times;</button>
            </div>
        @endif

        <!-- Search & Filters Bar -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.admins') }}" class="w-full md:w-96 flex items-center gap-2">
                <div class="relative w-full">
                    <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#A1A7C4]" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email…"
                           class="w-full pl-10 pr-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-slate-100 placeholder-[#A1A7C4] focus:outline-none focus:border-[#1E5EFF] transition">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 transition shrink-0">Rechercher</button>
            </form>

            <span class="text-xs text-[#A1A7C4] dark:text-slate-500 font-mono whitespace-nowrap">{{ $admins->total() }} Administrateur(s) trouvé(s)</span>
        </div>

        <!-- Admins Table -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Administrateur</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Téléphone</th>
                            <th class="px-6 py-4">Rôle Attribué</th>
                            <th class="px-6 py-4">2FA</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($admins as $admin)
                            @php
                                // Étape 4 : non-conformité = compte dont l'invitation est acceptée
                                // (donc utilisable) depuis plus de 48h sans 2FA confirmée.
                                $nonConforme = $admin->invitation_accepted_at
                                    && !$admin->two_factor_confirmed_at
                                    && $admin->invitation_accepted_at->diffInHours(now()) > 48;
                            @endphp
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition {{ $nonConforme ? 'bg-[#FFF3DE] dark:bg-amber-500/5' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-[4px] bg-[#ECF2FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($admin->prenom ?? $admin->name, 0, 1) . substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block leading-tight text-[#131523] dark:text-slate-100 font-bold text-xs">{{ $admin->prenom }} {{ $admin->name }}</span>
                                            <span class="text-[11px] text-[#A1A7C4] dark:text-slate-500 font-normal">ID: #ADM-{{ $admin->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-300">{{ $admin->email }}</td>
                                <td class="px-6 py-4 text-[#5A607F] dark:text-slate-400 text-xs">{{ $admin->telephone ?: 'Non renseigné' }}</td>
                                @php
                                    $roleSlug = $admin->roles->pluck('name')->first();
                                    $roleColor = \App\Models\User::roleBadgeColor($roleSlug);
                                @endphp
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-[4px] {{ $roleColor['bg'] }} {{ $roleColor['text'] }}">
                                        {{ \App\Models\User::roleLabel($roleSlug) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if(!$admin->invitation_accepted_at)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#5A607F] dark:text-slate-400 bg-[#F5F6FA] dark:bg-slate-800 rounded-full">Invitation en attente</span>
                                    @elseif($admin->two_factor_confirmed_at)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">Activée</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#B98900] dark:text-amber-400 bg-[#FFF3DE] dark:bg-amber-500/10 rounded-full">Non activée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($admin->is_active)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        @if($admin->invitation_accepted_at && !$admin->two_factor_confirmed_at)
                                            <form method="POST" action="{{ route('superadmin.admins.resend2FAReminder', $admin) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-[#1E5EFF] dark:text-blue-400 hover:underline px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] transition whitespace-nowrap">
                                                    Renvoyer le rappel d'activation
                                                </button>
                                            </form>
                                        @endif

                                        @if($admin->two_factor_confirmed_at)
                                            <button type="button" @click="confirmForceReset = {{ $admin->id }}" class="text-xs font-semibold text-[#B98900] dark:text-amber-400 hover:underline px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] transition whitespace-nowrap">
                                                Forcer reset 2FA
                                            </button>
                                        @endif

                                        @if($admin->id === auth()->id())
                                            <span class="text-xs font-semibold text-[#A1A7C4] dark:text-slate-600 px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] whitespace-nowrap cursor-not-allowed" title="Vous ne pouvez pas désactiver votre propre compte">
                                                Désactiver
                                            </span>
                                        @elseif($admin->is_active)
                                            <button type="button" @click="confirmDeactivate = {{ $admin->id }}" class="text-xs font-semibold text-[#F0142F] dark:text-rose-400 hover:underline px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] transition whitespace-nowrap">
                                                Désactiver
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('superadmin.users.toggleStatus', $admin) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-[#5A607F] dark:text-slate-300 hover:text-[#131523] dark:hover:text-white px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] transition whitespace-nowrap">
                                                    Activer
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- Étape 5 : confirmation explicite + step-up auth (route password.confirm) -->
                                    <div x-show="confirmDeactivate === {{ $admin->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmDeactivate = null">
                                        <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                                            <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Désactiver {{ $admin->prenom }} {{ $admin->name }} ?</h3>
                                            <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed">Ce compte ne pourra plus se connecter tant qu'il ne sera pas réactivé. Une re-confirmation de votre mot de passe sera demandée.</p>
                                            <div class="flex justify-end gap-3 pt-2">
                                                <button type="button" @click="confirmDeactivate = null" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                                                <form method="POST" action="{{ route('superadmin.admins.deactivate', $admin) }}">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 rounded-[4px] bg-[#F0142F] hover:bg-[#c9102a] text-white text-xs font-bold">Confirmer la désactivation</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="confirmForceReset === {{ $admin->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmForceReset = null">
                                        <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                                            <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Forcer la réinitialisation de la 2FA de {{ $admin->prenom }} {{ $admin->name }} ?</h3>
                                            <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed">À utiliser en cas de perte d'accès à l'application d'authentification et aux codes de secours. Ce compte devra ré-enrôler sa 2FA dès sa prochaine connexion. Une re-confirmation de votre mot de passe sera demandée.</p>
                                            <div class="flex justify-end gap-3 pt-2">
                                                <button type="button" @click="confirmForceReset = null" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                                                <form method="POST" action="{{ route('superadmin.admins.forceTwoFactorReset', $admin) }}">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 rounded-[4px] bg-[#B98900] hover:bg-[#997200] text-white text-xs font-bold">Confirmer la réinitialisation</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun administrateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $admins->links() }}
            </div>
        </div>

        <!-- Modal Création Admin -->
        <div x-show="showCreateModal" class="fixed inset-y-0 inset-x-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" x-cloak>
            <div class="bg-white dark:bg-slate-900 w-full max-w-lg p-6 rounded-[6px] shadow-xl space-y-6">
                <div class="flex justify-between items-center border-b border-[#E6E9F4] dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Nouveau Compte Administrateur</h3>
                    <button @click="showCreateModal = false" class="text-[#A1A7C4] hover:text-[#131523] dark:hover:text-slate-100 text-xl leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('superadmin.admins.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Nom *</label>
                            <input type="text" name="name" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Prénom *</label>
                            <input type="text" name="prenom" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Adresse Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Rôle *</label>
                        <select name="role" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                            <option value="admin">Administrateur Métier</option>
                            <option value="Super Admin">Super Admin Système</option>
                        </select>
                    </div>

                    <div class="p-3 rounded-[4px] bg-[#EAF0FF] dark:bg-blue-500/10 flex items-start gap-2.5">
                        <x-icon name="circle-exclamation" class="w-4 h-4 text-[#1E5EFF] dark:text-blue-400 shrink-0 mt-0.5" />
                        <p class="text-[11px] text-[#1E5EFF] dark:text-blue-300 leading-relaxed">
                            Aucun mot de passe n'est saisi ici : un email d'invitation sera envoyé au nouvel administrateur pour qu'il définisse lui-même son mot de passe. Il devra également activer l'authentification à deux facteurs (obligatoire pour ce rôle) dès sa première connexion.
                        </p>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-[#E6E9F4] dark:border-slate-800">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-[#F5F6FA] dark:bg-slate-800 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px]">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)]">Envoyer l'invitation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
