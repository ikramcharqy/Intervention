<x-super-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">Gestion Globale des Comptes Utilisateurs</h1>
                <p class="text-sm text-slate-400 mt-1">Supervision globale de tous les comptes enregistrés dans l'ensemble des 5 rôles.</p>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.users') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email..." class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    <svg class="h-4 w-4 text-slate-500 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <select name="role" class="w-full sm:w-48 px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                    <option value="">Tous les Rôles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-slate-200 text-xs font-semibold rounded-xl border border-slate-800">Filtrer</button>
            </form>

            <span class="text-xs text-slate-400 font-mono">{{ $users->total() }} Compte(s) inscrit(s)</span>
        </div>

        <!-- Users Table -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/90 text-slate-400 text-[11px] font-bold uppercase tracking-wider font-heading border-b border-slate-800/80">
                        <tr>
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Téléphone</th>
                            <th class="px-6 py-4">Rôle Attribué</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-slate-900 border border-slate-800 text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block leading-tight">{{ $user->name }}</span>
                                            <span class="text-[11px] text-slate-500 font-normal">ID: #USR-{{ $user->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-300">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-slate-400 text-xs">{{ $user->telephone ?? 'Non renseigné' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleName = $user->roles->pluck('name')->first() ?? 'Sans rôle';
                                        $badgeColor = match($roleName) {
                                            'Super Admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                            'admin', 'Administrateur' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                            'Commercial' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'technicien', 'Technicien' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'Client' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            default => 'bg-slate-500/10 text-slate-400 border-slate-500/20'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $badgeColor }}">
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-full">ACTIF</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-full">INACTIF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('superadmin.users.toggleStatus', $user) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-white px-3 py-1 bg-slate-900 border border-slate-800 rounded-lg transition">
                                            {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-950 border-t border-slate-800/80">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
