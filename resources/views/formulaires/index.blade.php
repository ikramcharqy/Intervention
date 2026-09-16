<x-dynamic-component :component="$layoutComponent">
    @php $isSuperAdmin = auth()->user()->hasRole('Super Admin'); @endphp
    @unless($isSuperAdmin)
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">Formulaires Dynamiques</h2>
        </x-slot>
    @endunless

    <div class="space-y-6">
        @if($isSuperAdmin)
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Formulaires Dynamiques</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Protocoles de clôture terrain, un par Type d'Intervention.</p>
            </div>
        @endif
        <!-- Outils et filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('formulaires.index') }}" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom..."
                        class="w-full md:w-80 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-650 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm shadow-sm">
                        Rechercher
                    </button>
                    @if(isset($search) && $search)
                        <a href="{{ route('formulaires.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-750 transition text-sm">
                            Réinitialiser
                        </a>
                    @endif
                </form>
                @if($isSuperAdmin)
                    <a href="{{ route('formulaires.create') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-[#1E5EFF] hover:bg-[#174ecc] text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Créer un formulaire
                    </a>
                @endif
            </div>
        </div>

        <!-- Table des formulaires -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Nom</th>
                            <th class="px-6 py-4">Type d'intervention</th>
                            <th class="px-6 py-4">Champs</th>
                            <th class="px-6 py-4">Actif</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-855">
                        @forelse ($formulaires as $formulaire)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition {{ !$formulaire->is_active ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $formulaire->nom }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $formulaire->typeIntervention->nom ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-950 dark:text-gray-200">
                                    {{ $formulaire->questions_count }} champs
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $formulaire->is_active ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' }}">
                                        {{ $formulaire->is_active ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('formulaires.show', $formulaire) }}" class="text-indigo-650 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm">Gérer les champs</a>
                                        @if($isSuperAdmin)
                                            <a href="{{ route('formulaires.edit', $formulaire) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Aucun formulaire trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($formulaires->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $formulaires->links() }}
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
