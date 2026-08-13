<x-super-admin-layout>
    <div class="space-y-8" x-data="{ showCreateModal: false }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">Gestion des Administrateurs</h1>
                <p class="text-sm text-slate-400 mt-1">Création et contrôle des privilèges d'administration métier et système.</p>
            </div>
            <button @click="showCreateModal = true" class="px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-bold rounded-xl shadow-lg glow-red transition flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                + Créer un Administrateur
            </button>
        </div>

        <!-- Search & Filters Bar -->
        <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.admins') }}" class="w-full md:w-96 flex items-center gap-2">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email..." class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 transition">
                    <svg class="h-4 w-4 text-slate-500 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-slate-200 text-xs font-semibold rounded-xl border border-slate-800">Filtrer</button>
            </form>

            <span class="text-xs text-slate-400 font-mono">{{ $admins->total() }} Administrateur(s) trouvé(s)</span>
        </div>

        <!-- Admins Table -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/90 text-slate-400 text-[11px] font-bold uppercase tracking-wider font-heading border-b border-slate-800/80">
                        <tr>
                            <th class="px-6 py-4">Administrateur</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Téléphone</th>
                            <th class="px-6 py-4">Rôle Attribué</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($admins as $admin)
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block leading-tight">{{ $admin->name }} {{ $admin->prenom }}</span>
                                            <span class="text-[11px] text-slate-500 font-normal">ID: #ADM-{{ $admin->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-300">{{ $admin->email }}</td>
                                <td class="px-6 py-4 text-slate-400 text-xs">{{ $admin->telephone ?? 'Non renseigné' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        {{ $admin->roles->pluck('name')->first() ?? 'Admin' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($admin->is_active)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-full">ACTIF</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-full">INACTIF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('superadmin.users.toggleStatus', $admin) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-white px-3 py-1 bg-slate-900 border border-slate-800 rounded-lg transition">
                                            {{ $admin->is_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Aucun administrateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-950 border-t border-slate-800/80">
                {{ $admins->links() }}
            </div>
        </div>

        <!-- Modal Création Admin -->
        <div x-show="showCreateModal" class="fixed inset-y-0 inset-x-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4" x-cloak>
            <div class="glass-card w-full max-w-lg p-6 rounded-2xl border border-slate-800 shadow-2xl space-y-6">
                <div class="flex justify-between items-center border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-white font-heading">Nouveau Compte Administrateur</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form method="POST" action="{{ route('superadmin.admins.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nom *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:border-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Prénom</label>
                        <input type="text" name="prenom" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:border-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Adresse Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:border-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Rôle *</label>
                        <select name="role" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:border-rose-500 focus:outline-none">
                            <option value="admin">Administrateur Métier (admin)</option>
                            <option value="Super Admin">Super Admin Système (Super Admin)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Mot de passe *</label>
                        <input type="password" name="password" required minlength="6" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:border-rose-500 focus:outline-none">
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-800">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-900 text-slate-300 text-xs font-semibold rounded-xl border border-slate-800">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl glow-red shadow-lg">Créer l'Administrateur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
