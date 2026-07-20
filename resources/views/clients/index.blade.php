<x-app-layout>
    <x-slot name="header">Gestion des Clients</x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
            <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col md:flex-row gap-3 items-end flex-wrap">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom, code, email, téléphone..."
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                @if($commerciaux->isNotEmpty())
                <div class="min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Commercial</label>
                    <select name="commercial_id" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                        <option value="">Tous les commerciaux</option>
                        @foreach($commerciaux as $com)
                            <option value="{{ $com->id }}" @selected(($commercialFilter ?? '') == $com->id)>
                                {{ $com->prenom }} {{ $com->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm shadow-sm">
                        Filtrer
                    </button>
                    @if($search || $commercialFilter)
                        <a href="{{ route('clients.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 transition text-sm">
                            Réinitialiser
                        </a>
                    @endif
                </div>

                <div class="md:ml-auto">
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un Client
                    </a>
                </div>
            </form>
        </div>

        {{-- Notifications --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl dark:bg-green-950/20 dark:text-green-400 dark:border-green-800 text-sm font-medium">
                {!! session('success') !!}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-5 py-4">Code</th>
                            <th class="px-5 py-4">Nom / Raison Sociale</th>
                            <th class="px-5 py-4">Type</th>
                            <th class="px-5 py-4">Téléphone</th>
                            <th class="px-5 py-4">Ville</th>
                            <th class="px-5 py-4">Commercial</th>
                            <th class="px-5 py-4">Statut</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition {{ !$client->is_active ? 'opacity-60' : '' }}">
                                <td class="px-5 py-3.5 font-mono font-semibold text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/20 w-28">
                                    {{ $client->code_client }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white">
                                    {{ $client->nom }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        {{ $client->type_client }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $client->telephone }}</td>
                                <td class="px-5 py-3.5">{{ $client->ville }}</td>
                                <td class="px-5 py-3.5">
                                    @if($client->commercial)
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[9px] font-bold shrink-0">
                                                {{ strtoupper(substr($client->commercial->prenom ?? '', 0, 1) . substr($client->commercial->name ?? '', 0, 1)) }}
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                                {{ $client->commercial->prenom }} {{ $client->commercial->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Non assigné</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $client->is_active ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' }}">
                                        {{ $client->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-semibold text-sm">Voir</a>
                                        <a href="{{ route('clients.edit', $client) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                        @if ($client->is_active)
                                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Désactiver ce client ?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Désactiver</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('clients.restore', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800 font-semibold text-sm">Réactiver</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Aucun client enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
