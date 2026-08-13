<x-commercial-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Espace Documents Commerciaux') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire d'import de document -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Importer un Document</h3>
                        <form action="{{ route('commercial.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <!-- Fichier -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fichier *</label>
                                <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <span class="text-xs text-gray-400 block mt-1">Taille maximale : 10 Mo</span>
                            </div>

                            <!-- Lier à un Prospect / Client -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lier à (Optionnel)</label>
                                <select id="link_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Ne pas lier</option>
                                    <option value="prospect">Prospect</option>
                                    <option value="client">Client</option>
                                </select>
                            </div>

                            <!-- Prospect Select -->
                            <div id="prospect_group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Sélectionner le Prospect</label>
                                <select name="prospect_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Aucun</option>
                                    @foreach($prospects as $prospect)
                                        <option value="{{ $prospect->id }}">{{ $prospect->nom_entreprise }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Client Select -->
                            <div id="client_group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Sélectionner le Client</label>
                                <select name="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Aucun</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                    Téléverser le document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste des documents -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Mes Documents</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border-b py-3 px-4">Nom du Document</th>
                                        <th class="border-b py-3 px-4">Type</th>
                                        <th class="border-b py-3 px-4">Lien</th>
                                        <th class="border-b py-3 px-4">Date</th>
                                        <th class="border-b py-3 px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $doc)
                                        <tr>
                                            <td class="border-b py-3 px-4 font-medium text-gray-900">{{ $doc->nom_original }}</td>
                                            <td class="border-b py-3 px-4 text-xs text-gray-500 uppercase">{{ explode('/', $doc->type_mime)[1] ?? 'Fichier' }}</td>
                                            <td class="border-b py-3 px-4">
                                                @if($doc->prospect)
                                                    <span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded-full font-medium">Prospect: {{ $doc->prospect->nom_entreprise }}</span>
                                                @elseif($doc->client)
                                                    <span class="px-2 py-0.5 text-xs bg-green-50 text-green-700 rounded-full font-medium">Client: {{ $doc->client->nom }}</span>
                                                @else
                                                    <span class="text-gray-400 text-xs">Aucun</span>
                                                @endif
                                            </td>
                                            <td class="border-b py-3 px-4 text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="border-b py-3 px-4 text-right space-x-2">
                                                <a href="{{ route('commercial.documents.download', $doc) }}" class="text-blue-600 hover:underline font-semibold">Télécharger</a>
                                                <form action="{{ route('commercial.documents.destroy', $doc) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce document ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($documents->isEmpty())
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-gray-400">Aucun document importé pour le moment.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de toggle des sélections -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const linkType = document.getElementById('link_type');
            const prospectGroup = document.getElementById('prospect_group');
            const clientGroup = document.getElementById('client_group');

            linkType.addEventListener('change', function() {
                if (this.value === 'prospect') {
                    prospectGroup.classList.remove('hidden');
                    clientGroup.classList.add('hidden');
                    clientGroup.querySelector('select').value = '';
                } else if (this.value === 'client') {
                    clientGroup.classList.remove('hidden');
                    prospectGroup.classList.add('hidden');
                    prospectGroup.querySelector('select').value = '';
                } else {
                    prospectGroup.classList.add('hidden');
                    clientGroup.classList.add('hidden');
                    prospectGroup.querySelector('select').value = '';
                    clientGroup.querySelector('select').value = '';
                }
            });
        });
    </script>
</x-commercial-layout>
