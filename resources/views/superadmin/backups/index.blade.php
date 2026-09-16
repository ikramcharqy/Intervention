<x-super-admin-layout>
    <div class="space-y-8" x-data="{ confirmCreate: false }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Sauvegardes de la Base de Données</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Génération et gestion des dumps SQL enregistrés sur le serveur dans <code>storage/app/backups/</code>. Rétention : {{ $retentionDays }} jours.</p>
            </div>
            <button type="button" @click="confirmCreate = true" class="px-5 py-2.5 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] transition flex items-center gap-2 shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                + Générer un Dump SQL
            </button>
        </div>

        <!-- Étape 2.3 : alerte si la dernière sauvegarde automatique est manquante/en retard -->
        @if($autoEnRetard)
            <div class="bg-[#FFF3DE] dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-[6px] p-4 flex items-center gap-3">
                <svg class="h-4 w-4 text-[#B98900] dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                <p class="text-xs sm:text-sm text-[#8A6800] dark:text-amber-300 font-semibold">
                    Aucune sauvegarde automatique récente (moins de 48h) détectée — vérifiez que la tâche planifiée (<code>backups:run-scheduled</code>) s'exécute correctement.
                </p>
            </div>
        @endif

        <!-- Backups Table -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Fichier</th>
                            <th class="px-6 py-4">Taille</th>
                            <th class="px-6 py-4">Date & Heure</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($backups as $b)
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-[#131523] dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <span class="p-2 bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700 rounded-lg text-[#5A607F] dark:text-slate-400 shrink-0">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 2.21 8 4" /></svg>
                                        </span>
                                        <span>{{ $b['filename'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-300">{{ $b['size'] }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-[#A1A7C4] dark:text-slate-500">{{ $b['date'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg {{ $b['type'] === 'Automatique' ? 'bg-[#EAF0FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400' : 'bg-[#F5F6FA] dark:bg-slate-800 text-[#5A607F] dark:text-slate-400' }}">
                                        {{ $b['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">
                                        DISPONIBLE
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('superadmin.backups.download', $b['filename']) }}" class="px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 hover:bg-[#E6E9F4] dark:hover:bg-slate-700 text-[#1E5EFF] dark:text-blue-400 text-xs font-semibold rounded-lg border border-[#D7DBEC] dark:border-slate-700 transition">
                                            Télécharger SQL
                                        </a>
                                        <form method="POST" action="{{ route('superadmin.backups.delete', $b['filename']) }}" class="inline-block" onsubmit="return confirm('Voulez-vous supprimer ce fichier de sauvegarde ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 hover:bg-[#FDE3E6] dark:hover:bg-rose-500/10 text-[#F0142F] dark:text-rose-400 border border-[#D7DBEC] dark:border-slate-700 text-xs font-semibold rounded-lg transition">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucune sauvegarde SQL présente sur le serveur. Cliquez sur le bouton pour en générer une.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Étape 6 : documenté sans implémenter — réplication vers un stockage externe
             (S3 ou équivalent) et chiffrement au repos des fichiers de sauvegarde,
             actuellement stockés en clair uniquement sur le disque local du serveur.
             Non implémenté dans cette passe : implique une dépendance/coût
             d'infrastructure supplémentaire à valider avant d'engager ce chantier. --}}
        <div class="bg-[#F5F6FA] dark:bg-slate-800/60 border border-[#E6E9F4] dark:border-slate-800 rounded-[6px] p-4">
            <p class="text-xs text-[#5A607F] dark:text-slate-400">
                <strong class="text-[#131523] dark:text-slate-200">Amélioration future recommandée :</strong>
                les sauvegardes sont actuellement stockées en clair, uniquement sur le disque local du serveur. Une réplication vers un stockage externe (S3 ou équivalent) et un chiffrement au repos sont recommandés avant une mise en production, mais impliquent une dépendance/coût d'infrastructure à valider séparément.
            </p>
        </div>

        <!-- Étape 5.1 : confirmation explicite avant génération, avec rappel de sensibilité -->
        <div x-show="confirmCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmCreate = false">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Générer un dump SQL complet ?</h3>
                <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed">
                    Ce fichier contiendra l'intégralité des données de la base — y compris les données personnelles des clients/techniciens et les mots de passe hashés des comptes. Conservez-le avec la même vigilance qu'un accès direct à la base de données.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="confirmCreate = false" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                    <form method="POST" action="{{ route('superadmin.backups.create') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold">Confirmer la génération</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
