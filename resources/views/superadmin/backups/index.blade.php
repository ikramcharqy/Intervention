<x-super-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">Sauvegardes Réelles de la Base de Données</h1>
                <p class="text-sm text-slate-400 mt-1">Génération et gestion des dumps SQL réels enregistrés sur le serveur dans <code>storage/app/backups/</code>.</p>
            </div>
            <form method="POST" action="{{ route('superadmin.backups.create') }}">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-bold rounded-xl shadow-lg glow-red transition flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    + Générer Dump SQL Réel
                </button>
            </form>
        </div>

        <!-- Backups Table -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/90 text-slate-400 text-[11px] font-bold uppercase tracking-wider font-heading border-b border-slate-800/80">
                        <tr>
                            <th class="px-6 py-4">Fichier SQL Réel</th>
                            <th class="px-6 py-4">Taille Réelle</th>
                            <th class="px-6 py-4">Date & Heure</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Statut Fichier</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($backups as $b)
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-white flex items-center gap-3">
                                    <span class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-rose-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 2.21 8 4" /></svg>
                                    </span>
                                    <span>{{ $b['filename'] }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-300">{{ $b['size'] }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $b['date'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-slate-900 text-slate-400 border border-slate-800 rounded-lg">
                                        {{ $b['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                                        DISPONIBLE
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.backups.download', $b['filename']) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                        Télécharger SQL
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.backups.delete', $b['filename']) }}" class="inline-block" onsubmit="return confirm('Voulez-vous supprimer ce fichier de sauvegarde ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-rose-400 border border-slate-800 text-xs font-semibold rounded-lg transition">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Aucune sauvegarde SQL présente sur le serveur. Cliquez sur le bouton pour générer une sauvegarde réelle.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-super-admin-layout>
