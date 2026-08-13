<x-super-admin-layout>
    <div class="space-y-8" x-data="{ showRoleModal: false }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">Rôles & Permissions (Spatie)</h1>
                <p class="text-sm text-slate-400 mt-1">Matrice de contrôle d'accès basée sur les rôles et permissions Spatie Laravel.</p>
            </div>
            <button @click="showRoleModal = true" class="px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-bold rounded-xl shadow-lg glow-red transition flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                + Ajouter un Rôle
            </button>
        </div>

        <!-- Role Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
                <div class="glass-card p-6 rounded-2xl border border-slate-800/80 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-bold text-white font-heading">{{ $role->name }}</h3>
                            <span class="px-2.5 py-1 text-[10px] font-mono font-bold bg-slate-900 text-slate-400 border border-slate-800 rounded-lg">Guard: {{ $role->guard_name }}</span>
                        </div>

                        <div class="pt-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3 font-heading">Permissions Associées ({{ $role->permissions->count() }}) :</span>
                            <div class="flex flex-wrap gap-1.5 max-h-48 overflow-y-auto custom-scrollbar">
                                @forelse($role->permissions as $perm)
                                    <span class="px-2.5 py-1 text-[11px] font-mono text-slate-300 bg-slate-900/90 border border-slate-800 rounded-md">
                                        {{ $perm->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-500 italic">Aucune permission explicite assignée.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex justify-between items-center text-xs text-slate-500 font-mono">
                        <span>Role ID: #ROLE-{{ $role->id }}</span>
                        <span class="text-emerald-400">Actif</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Role Creation Modal -->
        <div x-show="showRoleModal" class="fixed inset-y-0 inset-x-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4" x-cloak>
            <div class="glass-card w-full max-w-md p-6 rounded-2xl border border-slate-800 shadow-2xl space-y-6">
                <div class="flex justify-between items-center border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-white font-heading">Créer un Nouveau Rôle</h3>
                    <button @click="showRoleModal = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form method="POST" action="{{ route('superadmin.roles.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nom du Rôle *</label>
                        <input type="text" name="name" required placeholder="Ex: Superviseur, Auditeur..." class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:border-rose-500 focus:outline-none">
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-800">
                        <button type="button" @click="showRoleModal = false" class="px-4 py-2 bg-slate-900 text-slate-300 text-xs font-semibold rounded-xl border border-slate-800">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl glow-red shadow-lg">Enregistrer Rôle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
