<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.roles') }}" class="text-[#A1A7C4] dark:text-slate-400 hover:text-[#131523] dark:hover:text-white text-xl leading-none">&larr;</a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Permissions Individuelles — Audit des Exceptions</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Liste consolidée de toutes les permissions accordées hors du modèle de rôle standard. Une liste qui grossit anormalement est un signal qu'un nouveau rôle devrait être créé plutôt que de multiplier les exceptions.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Permission</th>
                            <th class="px-6 py-4">Justification</th>
                            <th class="px-6 py-4">Accordée par</th>
                            <th class="px-6 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($exceptions as $exception)
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('superadmin.users.show', $exception->user) }}" class="text-xs font-bold text-[#F0142F] dark:text-rose-400 hover:underline">{{ $exception->user?->prenom }} {{ $exception->user?->name }}</a>
                                    <span class="block text-[11px] text-[#A1A7C4] dark:text-slate-500 font-mono">{{ $exception->user?->email }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-300">{{ $exception->permission?->name }}</td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300 max-w-xs">{{ $exception->justification }}</td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-400">{{ $exception->grantedBy?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-[11px] font-mono text-[#A1A7C4] dark:text-slate-500 whitespace-nowrap">{{ $exception->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucune permission individuelle active — le modèle de rôles standard couvre l'ensemble des besoins actuels.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $exceptions->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
