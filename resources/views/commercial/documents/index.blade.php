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
                                <label class="block text-sm font-medium text-gray-700">Fichier <span class="text-red-500">*</span></label>
                                <input type="file" name="file" required accept=".pdf,.docx,.xlsx,.jpg,.jpeg,.png"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <span class="text-xs text-gray-400 block mt-1">Formats acceptés : PDF, DOCX, XLSX, JPG, PNG. Taille maximale : 10 Mo.</span>
                                @error('file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Type de document -->
                            <div>
                                <label for="type_document" class="block text-sm font-medium text-gray-700">Type de document <span class="text-red-500">*</span></label>
                                <select name="type_document" id="type_document" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach(\App\Models\Document::TYPES as $type)
                                        <option value="{{ $type }}" @selected(old('type_document') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type_document') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Lier à : Type d'entité, puis entité précise -->
                            <div>
                                <label for="entite_type" class="block text-sm font-medium text-gray-700">Lier à</label>
                                <select id="entite_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Aucun</option>
                                    <option value="prospect">Prospect</option>
                                    <option value="client">Client</option>
                                    <option value="devis">Devis</option>
                                    <option value="chantier">Chantier</option>
                                </select>
                            </div>

                            <div id="prospect_group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Sélectionner le Prospect</label>
                                <select name="prospect_id" class="entite-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($prospects as $prospect)
                                        <option value="{{ $prospect->id }}">{{ $prospect->nom_entreprise }}</option>
                                    @endforeach
                                </select>
                                @error('prospect_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div id="client_group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Sélectionner le Client</label>
                                <select name="client_id" class="entite-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div id="devis_group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Sélectionner le Devis</label>
                                <select name="devis_id" class="entite-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($devisList as $devis)
                                        <option value="{{ $devis->id }}">{{ $devis->reference }} @if($devis->client) — {{ $devis->client->nom }} @elseif($devis->prospect) — {{ $devis->prospect->nom_entreprise }} @endif</option>
                                    @endforeach
                                </select>
                                @error('devis_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div id="chantier_group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Sélectionner le Chantier</label>
                                <select name="chantier_id" class="entite-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($chantiers as $chantier)
                                        <option value="{{ $chantier->id }}">{{ $chantier->nom }} @if($chantier->client) — {{ $chantier->client->nom }} @endif</option>
                                    @endforeach
                                </select>
                                @error('chantier_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

                        <form method="GET" action="{{ route('commercial.documents.index') }}" class="flex gap-2 mb-4">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom, type ou entité liée..."
                                class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium">Filtrer</button>
                            @if($search ?? null)
                                <a href="{{ route('commercial.documents.index') }}" class="px-4 py-2 bg-gray-50 text-gray-500 rounded-md hover:bg-gray-100 text-sm font-medium">Réinitialiser</a>
                            @endif
                        </form>

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
                                        @php($entite = $doc->entiteLiee())
                                        <tr>
                                            <td class="border-b py-3 px-4 font-medium text-gray-900">{{ $doc->nom_original }}</td>
                                            <td class="border-b py-3 px-4 text-xs text-gray-500">{{ $doc->type_document ?? (explode('/', $doc->type_mime)[1] ?? 'Fichier') }}</td>
                                            <td class="border-b py-3 px-4">
                                                @if($entite)
                                                    <span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded-full font-medium">{{ $entite['type'] }}: {{ $entite['label'] }}</span>
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

    <!-- Script de toggle des sélections "Lier à" -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const entiteType = document.getElementById('entite_type');
            const groups = {
                prospect: document.getElementById('prospect_group'),
                client: document.getElementById('client_group'),
                devis: document.getElementById('devis_group'),
                chantier: document.getElementById('chantier_group'),
            };

            entiteType.addEventListener('change', function() {
                const selected = this.value;
                Object.keys(groups).forEach(function(key) {
                    if (key === selected) {
                        groups[key].classList.remove('hidden');
                    } else {
                        groups[key].classList.add('hidden');
                        groups[key].querySelector('select').value = '';
                    }
                });
            });
        });
    </script>
</x-commercial-layout>
