<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Prospects') }}
            </h2>
            <a href="{{ route('prospects.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Nouveau Prospect
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b py-2 px-4">Entreprise</th>
                                <th class="border-b py-2 px-4">Contact</th>
                                <th class="border-b py-2 px-4">Téléphone</th>
                                <th class="border-b py-2 px-4">Commercial</th>
                                <th class="border-b py-2 px-4">Statut</th>
                                <th class="border-b py-2 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prospects as $prospect)
                                <tr>
                                    <td class="border-b py-2 px-4 font-medium">{{ $prospect->nom_entreprise }}</td>
                                    <td class="border-b py-2 px-4">{{ $prospect->nom_contact }}<br><span class="text-xs text-gray-500">{{ $prospect->email }}</span></td>
                                    <td class="border-b py-2 px-4">{{ $prospect->telephone }}</td>
                                    <td class="border-b py-2 px-4">{{ $prospect->commercial ? $prospect->commercial->name : 'Aucun' }}</td>
                                    <td class="border-b py-2 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                            {{ $prospect->statut }}
                                        </span>
                                    </td>
                                    <td class="border-b py-2 px-4 text-right space-x-2">
                                        <a href="{{ route('prospects.edit', $prospect) }}" class="text-blue-600 hover:underline text-sm">Modifier</a>
                                        @if($prospect->statut !== 'Converti')
                                            <form action="{{ route('prospects.convert', $prospect) }}" method="POST" class="inline" onsubmit="return confirm('Convertir ce prospect en client ?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:underline text-sm font-semibold">Convertir en Client</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($prospects->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-500">Aucun prospect trouvé.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
