<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">
            {{ __('Utilisateurs') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Outils et filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ $search ?? '' }}" placeholder="Nom, Prénom, Email..."
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="w-full md:w-64">
                    <label for="role" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Rôle</label>
                    <select name="role" id="role" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                        <option value="">Tous les rôles</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" @selected(($role ?? '') == $r->name)>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="bg-indigo-650 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm flex-1 md:flex-none text-center shadow-sm">
                        Filtrer
                    </button>
                    @if($search || $role)
                        <a href="{{ route('users.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-750 transition text-sm text-center">
                            Réinitialiser
                        </a>
                    @endif
                </div>

                <div class="w-full md:w-auto md:ml-auto">
                    <a href="{{ route('users.create') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Nouvel Utilisateur
                    </a>
                </div>
            </form>
        </div>

        <!-- Table des utilisateurs -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Nom complet</th>
                            <th class="px-6 py-4">E-mail</th>
                            <th class="px-6 py-4">Rôles</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-855">
                        @forelse ($users as $u)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition {{ !$u->is_active ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $u->prenom }} {{ $u->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $u->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($u->roles as $roleObj)
                                            <span class="px-2 py-0.5 text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400 rounded-md">
                                                {{ $roleObj->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->is_active ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' }}">
                                        {{ $u->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('users.show', $u) }}" class="text-indigo-650 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm">Voir</a>
                                        <a href="{{ route('users.edit', $u) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                        @if ($u->is_active)
                                            <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Désactiver cet utilisateur ?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-750 font-semibold text-sm">Désactiver</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('users.restore', $u) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-850 font-semibold text-sm">Réactiver</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
