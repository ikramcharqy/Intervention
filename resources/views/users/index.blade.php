<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Utilisateurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <select name="role" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Tous les rôles</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->name }}" @selected(($role ?? '') == $r->name)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filtrer</button>
                    </form>
                    <a href="{{ route('users.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Nouvel Utilisateur</a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom complet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $u)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $u->prenom }} {{ $u->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $u->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @foreach ($u->roles as $roleObj)
                                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full mr-1">{{ $roleObj->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $u->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $u->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="{{ route('users.show', $u) }}" class="text-blue-500 hover:text-blue-700">Voir</a>
                                    <a href="{{ route('users.edit', $u) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                    @if ($u->is_active)
                                        <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Désactiver cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Désactiver</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('users.restore', $u) }}">
                                            @csrf
                                            <button type="submit" class="text-green-500 hover:text-green-700">Réactiver</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
