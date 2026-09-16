<x-super-admin-layout>
    <div class="space-y-8" x-data="{
            showRoleModal: {{ $errors->hasAny(['name', 'description', 'clone_from']) ? 'true' : 'false' }},
            drawerRole: null,
            confirming: false,
            closeAttempt: false,
            initial: [],
            selected: [],
            dirty: false,
            openDrawer(roleId, perms) {
                this.drawerRole = roleId;
                this.initial = [...perms];
                this.selected = [...perms];
                this.confirming = false;
                this.closeAttempt = false;
                this.dirty = false;
                this.$nextTick(() => this.$refs['drawerPanel' + roleId]?.querySelector('button, input')?.focus());
            },
            requestClose() {
                this.dirty = JSON.stringify([...this.selected].sort()) !== JSON.stringify([...this.initial].sort());
                if (this.dirty && !this.confirming) { this.closeAttempt = true; return; }
                this.drawerRole = null; this.closeAttempt = false; this.confirming = false;
            },
            toggle(perm) {
                const i = this.selected.indexOf(perm);
                if (i === -1) { this.selected.push(perm); } else { this.selected.splice(i, 1); }
            },
            // Étape 3.3 : piège à focus minimal (sans dépendance Alpine supplémentaire) —
            // Tab/Shift+Tab bouclent entre le premier et le dernier élément focusable du
            // panneau ouvert, tant que le tiroir est affiché.
            trapFocus(e) {
                if (!this.drawerRole) return;
                const container = this.$refs['drawerPanel' + this.drawerRole];
                if (!container) return;
                const focusables = container.querySelectorAll('button:not([disabled]), input:not([disabled]), [href], [tabindex]:not([tabindex=&quot;-1&quot;])');
                if (!focusables.length) return;
                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
         }"
         @keydown.escape.window="if (drawerRole && !closeAttempt) requestClose()"
         @keydown.tab="trapFocus($event)"
         x-init="@if(session('openRoleDrawer')) openDrawer({{ session('openRoleDrawer')['id'] }}, {{ collect(session('openRoleDrawer')['permissions'])->values()->toJson() }}) @endif">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Rôles & Permissions (Spatie)</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Matrice de contrôle d'accès basée sur les rôles et permissions Spatie Laravel.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.roles.exceptions') }}" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-[#D7DBEC] dark:border-slate-700 hover:border-amber-400 text-[#B98900] dark:text-amber-400 text-xs font-bold rounded-[4px] transition flex items-center gap-2 whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    Exceptions actives ({{ $exceptionsCount }})
                </a>
                <button @click="showRoleModal = true" class="px-4 py-2.5 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] transition flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    + Ajouter un Rôle
                </button>
            </div>
        </div>

        <!-- Étape 1 : liste verticale des rôles (à gauche) + panneau de permissions
             affiché à côté (à droite) quand un rôle est sélectionné — la liste se
             rétrécit au lieu d'être masquée sous une superposition plein écran. -->
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 overflow-hidden divide-y divide-[#E6E9F4] dark:divide-slate-800 w-full transition-all duration-300"
                 :class="drawerRole ? 'lg:w-[400px] lg:shrink-0' : ''">
                @foreach($roles as $role)
                    @php
                        $isSuperAdmin = $role->name === 'Super Admin';
                        $permNames = $role->permissions->pluck('name')->values();
                    @endphp
                    <button type="button"
                            @click="openDrawer({{ $role->id }}, {{ $permNames->toJson() }}); $event.currentTarget.blur()"
                            :class="drawerRole === {{ $role->id }} ? 'bg-[#F5F6FA] dark:bg-slate-800/60' : ''"
                            class="w-full text-left flex items-center gap-4 px-6 py-5 hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1E5EFF] focus-visible:ring-inset">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm {{ $isSuperAdmin ? 'bg-[#FDE3E6] dark:bg-rose-500/10 text-[#F0142F] dark:text-rose-400 border border-[#F8C4CA] dark:border-rose-500/30' : 'bg-[#EAF0FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400 border border-[#B8CFFF] dark:border-blue-500/30' }}">
                            {{ strtoupper(substr($role->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">{{ \App\Models\User::roleLabel($role->name) }}</h3>
                                <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-[#F5F6FA] dark:bg-slate-800 text-[#5A607F] dark:text-slate-400 border border-[#E6E9F4] dark:border-slate-700 rounded-lg" x-show="!drawerRole">Guard: {{ $role->guard_name }}</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $isSuperAdmin ? 'bg-[#FDE3E6] dark:bg-rose-500/10 text-[#F0142F] dark:text-rose-400' : 'bg-[#EAF0FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400' }}">
                                    {{ $role->permissions->count() }} permission(s)
                                </span>
                            </div>
                            <p class="text-xs text-[#A1A7C4] dark:text-slate-500 mt-1">{{ $role->users_count }} compte(s) rattaché(s) à ce rôle</p>
                            @if($role->description)
                                <p class="text-xs text-[#5A607F] dark:text-slate-400 mt-1 italic">{{ $role->description }}</p>
                            @endif
                        </div>

                        <span class="text-[10px] font-bold text-[#06A561] dark:text-emerald-400 shrink-0" x-show="!drawerRole">Actif</span>
                        <svg class="h-4 w-4 shrink-0 transition-transform" :class="drawerRole === {{ $role->id }} ? 'text-[#1E5EFF] dark:text-blue-400 rotate-90' : 'text-[#A1A7C4] dark:text-slate-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </button>
                @endforeach
            </div>

            <!-- Étape 2/3 : panneau de permissions — affiché à côté de la liste (pas en
                 superposition plein écran), un seul par page, ré-hydraté par openDrawer()
                 selon le rôle cliqué. -->
            <template x-if="drawerRole">
                <div class="w-full flex-1 min-w-0 bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 flex flex-col lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)]"
                     role="dialog" aria-modal="false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 lg:translate-x-4"
                     x-transition:enter-end="opacity-100 lg:translate-x-0">

                    @foreach($roles as $role)
                        <template x-if="drawerRole === {{ $role->id }}">
                            <div class="flex flex-col h-full min-h-0" x-ref="drawerPanel{{ $role->id }}">
                                <!-- En-tête -->
                                <div class="flex items-center justify-between gap-3 p-6 border-b border-[#E6E9F4] dark:border-slate-800 shrink-0">
                                    <div>
                                        <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">{{ \App\Models\User::roleLabel($role->name) }}</h3>
                                        <p class="text-xs text-[#A1A7C4] dark:text-slate-500 mt-0.5">Permissions du rôle — {{ $role->users_count }} compte(s) concerné(s)</p>
                                    </div>
                                    <button type="button" @click="requestClose()" class="text-[#A1A7C4] dark:text-slate-400 hover:text-[#131523] dark:hover:text-white shrink-0" aria-label="Fermer">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <!-- Corps : liste des permissions, groupées par domaine, en toggles -->
                                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-6 space-y-6" x-show="!confirming">
                                    @foreach($permissionsByDomain as $domaine => $perms)
                                        <div>
                                            <p class="text-[11px] font-bold text-[#F0142F] dark:text-rose-400 uppercase tracking-wider mb-3">{{ $domaine }}</p>
                                            <div class="space-y-1">
                                                @foreach($perms as $perm)
                                                    <label class="flex items-center justify-between gap-3 py-2.5 px-1 cursor-pointer group">
                                                        <span class="text-xs font-mono text-[#5A607F] dark:text-slate-300 group-hover:text-[#131523] dark:group-hover:text-white transition">{{ $perm->name }}</span>
                                                        <button type="button"
                                                                role="switch"
                                                                :aria-checked="selected.includes('{{ $perm->name }}').toString()"
                                                                @click="toggle('{{ $perm->name }}')"
                                                                :class="selected.includes('{{ $perm->name }}') ? 'bg-emerald-500' : 'bg-[#D7DBEC] dark:bg-slate-700'"
                                                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                                                            <span :class="selected.includes('{{ $perm->name }}') ? 'translate-x-5' : 'translate-x-1'"
                                                                  class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"></span>
                                                        </button>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Étape 2.3 : résumé de confirmation avant application réelle -->
                                <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-4" x-show="confirming" x-cloak>
                                    <h4 class="text-sm font-bold text-[#131523] dark:text-white">Confirmer les changements ?</h4>
                                    <div class="space-y-3 text-xs">
                                        <div>
                                            <p class="text-[#06A561] dark:text-emerald-400 font-bold mb-1.5">Ajoutées</p>
                                            <template x-for="p in selected.filter(p => !initial.includes(p))" :key="p">
                                                <span x-text="p" class="inline-block px-2 py-1 mr-1.5 mb-1.5 bg-[#E3FBF0] dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/30 text-[#06A561] dark:text-emerald-400 font-mono rounded"></span>
                                            </template>
                                            <p class="text-[#A1A7C4] dark:text-slate-600 italic" x-show="!selected.some(p => !initial.includes(p))">Aucune</p>
                                        </div>
                                        <div>
                                            <p class="text-[#F0142F] dark:text-rose-400 font-bold mb-1.5">Retirées</p>
                                            <template x-for="p in initial.filter(p => !selected.includes(p))" :key="p">
                                                <span x-text="p" class="inline-block px-2 py-1 mr-1.5 mb-1.5 bg-[#FDE3E6] dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/30 text-[#F0142F] dark:text-rose-400 font-mono rounded"></span>
                                            </template>
                                            <p class="text-[#A1A7C4] dark:text-slate-600 italic" x-show="!initial.some(p => !selected.includes(p))">Aucune</p>
                                        </div>
                                    </div>
                                    <template x-if="{{ $role->name === 'Super Admin' ? 'true' : 'false' }} && selected.length === 0">
                                        <div class="p-3 rounded-lg bg-[#FDE3E6] dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/40 text-[#8A0E20] dark:text-rose-300 text-xs font-semibold">
                                            ⚠ Ce rôle Super Admin perdrait la totalité de ses permissions — la console d'administration deviendrait inaccessible pour tout compte n'ayant que ce rôle. Enregistrement bloqué.
                                        </div>
                                    </template>
                                </div>

                                <!-- Confirmation de fermeture sans enregistrer -->
                                <div class="p-6 space-y-4 border-t border-amber-300 dark:border-amber-500/30 bg-[#FFF3DE] dark:bg-amber-500/5" x-show="closeAttempt" x-cloak>
                                    <p class="text-xs font-semibold text-[#8A6800] dark:text-amber-300">Des modifications non enregistrées seront perdues. Fermer sans enregistrer ?</p>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="closeAttempt = false" class="px-4 py-2 bg-white dark:bg-slate-900 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-xl border border-[#D7DBEC] dark:border-slate-800">Continuer l'édition</button>
                                        <button type="button" @click="drawerRole = null; closeAttempt = false" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl">Fermer sans enregistrer</button>
                                    </div>
                                </div>

                                <!-- Pied de panneau sticky -->
                                <form method="POST" action="{{ route('superadmin.roles.permissions.update', $role) }}" class="shrink-0 border-t border-[#E6E9F4] dark:border-slate-800 p-6 flex justify-end gap-3 bg-white dark:bg-slate-900 rounded-b-[6px]" x-show="!closeAttempt">
                                    @csrf
                                    <template x-for="p in selected" :key="p">
                                        <input type="hidden" name="permissions[]" :value="p">
                                    </template>

                                    <template x-if="!confirming">
                                        <button type="button" @click="requestClose()" class="px-4 py-2 bg-[#F5F6FA] dark:bg-slate-800 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-xl border border-[#D7DBEC] dark:border-slate-700">Annuler</button>
                                    </template>
                                    <template x-if="!confirming">
                                        <button type="button" @click="confirming = true" class="px-5 py-2 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-xl">Enregistrer les modifications</button>
                                    </template>
                                    <template x-if="confirming">
                                        <button type="button" @click="confirming = false" class="px-4 py-2 bg-[#F5F6FA] dark:bg-slate-800 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-xl border border-[#D7DBEC] dark:border-slate-700">Retour</button>
                                    </template>
                                    <template x-if="confirming">
                                        <button type="submit"
                                                :disabled="{{ $role->name === 'Super Admin' ? 'true' : 'false' }} && selected.length === 0"
                                                :class="({{ $role->name === 'Super Admin' ? 'true' : 'false' }} && selected.length === 0) ? 'opacity-40 cursor-not-allowed' : ''"
                                                class="px-5 py-2 bg-[#F0142F] hover:bg-[#c9102a] text-white text-xs font-bold rounded-xl">Confirmer</button>
                                    </template>
                                </form>
                            </div>
                        </template>
                    @endforeach
                </div>
            </template>
        </div>

        <!-- Role Creation Modal -->
        <div x-show="showRoleModal" class="fixed inset-y-0 inset-x-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] border border-[#E6E9F4] dark:border-slate-800 shadow-2xl space-y-6">
                <div class="flex justify-between items-center border-b border-[#E6E9F4] dark:border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-[#131523] dark:text-slate-100">Créer un Nouveau Rôle</h3>
                    <button @click="showRoleModal = false" class="text-[#A1A7C4] dark:text-slate-400 hover:text-[#131523] dark:hover:text-white text-xl leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('superadmin.roles.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-300 uppercase mb-1">Nom du Rôle *</label>
                        <input type="text" name="name" required maxlength="50" placeholder="Ex: Superviseur, Auditeur..." class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs text-[#131523] dark:text-white focus:border-[#1E5EFF] focus:outline-none">
                        @error('name')
                            <p class="text-[11px] text-[#F0142F] dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-300 uppercase mb-1">Description</label>
                        <input type="text" name="description" maxlength="255" placeholder="Rôle et responsabilités en une phrase (optionnel)…" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs text-[#131523] dark:text-white focus:border-[#1E5EFF] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-300 uppercase mb-1">Copier les permissions depuis</label>
                        <select name="clone_from" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs text-[#131523] dark:text-white focus:border-[#1E5EFF] focus:outline-none">
                            <option value="">Aucun — partir d'un rôle vide</option>
                            @foreach($roles as $existingRole)
                                <option value="{{ $existingRole->id }}">{{ \App\Models\User::roleLabel($existingRole->name) }} ({{ $existingRole->permissions->count() }} permission(s))</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">Vous pourrez ajuster les permissions juste après la création.</p>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-[#E6E9F4] dark:border-slate-800">
                        <button type="button" @click="showRoleModal = false" class="px-4 py-2 bg-[#F5F6FA] dark:bg-slate-900 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px]">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-[4px] shadow-lg">Enregistrer Rôle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
