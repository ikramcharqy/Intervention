<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouveau Prospect') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('prospects.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Type de prospect -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type de prospect *</label>
                                <div class="flex gap-4">
                                    @foreach(\App\Models\Prospect::TYPES_PROSPECT as $tp)
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                            <input type="radio" name="type_prospect" value="{{ $tp }}" onchange="toggleNomLabel()"
                                                   {{ old('type_prospect', 'Entreprise') == $tp ? 'checked' : '' }}
                                                   class="text-indigo-600 focus:ring-indigo-500">
                                            {{ $tp }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            @if(isset($commerciaux) && $commerciaux->isNotEmpty())
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Assigné à</label>
                                    <select name="commercial_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">— Sélectionner —</option>
                                        @foreach($commerciaux as $c)
                                            <option value="{{ $c->id }}" {{ old('commercial_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Entreprise -->
                            <div>
                                <label id="label-nom-entreprise" class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                                <input type="text" name="nom_entreprise" required value="{{ old('nom_entreprise') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('nom_entreprise')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <!-- Nom Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom du contact</label>
                                <input type="text" name="nom_contact" value="{{ old('nom_contact') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500">Renseignez au moins un moyen de contact : téléphone ou e-mail.</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="0612345678" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('telephone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <!-- Adresse -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                                <input type="text" name="adresse" value="{{ old('adresse') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <!-- Statut -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Statut</label>
                                <select name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="Nouveau" {{ old('statut') == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                                    <option value="Qualifié" {{ old('statut') == 'Qualifié' ? 'selected' : '' }}>Qualifié</option>
                                    <option value="Négociation" {{ old('statut') == 'Négociation' ? 'selected' : '' }}>Négociation</option>
                                    <option value="Perdu" {{ old('statut') == 'Perdu' ? 'selected' : '' }}>Perdu</option>
                                </select>
                            </div>

                            <!-- Observations -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Observations</label>
                                <textarea name="observations" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('observations') }}</textarea>
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('prospects.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 mr-2">Annuler</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleNomLabel() {
            const particulier = document.querySelector('input[name="type_prospect"][value="Particulier"]').checked;
            document.getElementById('label-nom-entreprise').textContent = particulier ? 'Nom complet *' : "Nom de l'entreprise *";
        }
        document.addEventListener('DOMContentLoaded', toggleNomLabel);
    </script>
</x-app-layout>
