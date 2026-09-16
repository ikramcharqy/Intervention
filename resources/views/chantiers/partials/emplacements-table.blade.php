<div
    class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100"

    x-data="{
        showModal: false,
        editMode: false,

        updateBaseUrl: '{{ url('emplacements') }}',
        storeUrl: '{{ route('emplacements.store', $chantier) }}',

        formData: {
            id: null,
            nom: '',
            description: '',
            latitude: '',
            longitude: '',
            nfc_uid: '',
            qr_code: ''
        },

        init() {
            console.log('✅ Alpine initialized');
        },

        openCreateModal() {

            console.log('✅ Create clicked');

            this.editMode = false;

            this.formData = {
                id: null,
                nom: '',
                description: '',
                latitude: '',
                longitude: '',
                nfc_uid: '',
                qr_code: ''
            };

            this.showModal = true;
        },

        openEditModal(id, nom, description, latitude, longitude, nfc_uid, qr_code) {

            console.log('✅ Edit clicked');

            this.editMode = true;

            this.formData = {
                id: id,
                nom: nom ?? '',
                description: description ?? '',
                latitude: latitude ?? '',
                longitude: longitude ?? '',
                nfc_uid: nfc_uid ?? '',
                qr_code: qr_code ?? ''
            };

            console.log(this.formData);

            this.showModal = true;
        }
    }">

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm dark:bg-red-950/20 dark:text-red-400 dark:border-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Emplacements</h3>

        @can('create', \App\Models\Emplacement::class)
            <button
                type="button"
                @click="openCreateModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded">
                Ajouter un emplacement
            </button>
        @endcan
    </div>

    <table class="w-full">
        <thead>
            <tr>
                <th>Ordre</th>
                <th>Nom</th>
                <th>QR</th>
                <th>NFC</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        @forelse($chantier->emplacements as $emplacement)

            <tr class="{{ !$emplacement->is_active ? 'opacity-50' : '' }}">

                <td class="whitespace-nowrap">
                    <span class="mr-1">{{ $loop->iteration }}</span>
                    @can('update', $emplacement)
                        @if(!$loop->first)
                            <form method="POST" action="{{ route('emplacements.moveUp', $emplacement) }}" class="inline">
                                @csrf
                                <button type="submit" title="Monter" class="text-gray-500 hover:text-gray-900">↑</button>
                            </form>
                        @endif
                        @if(!$loop->last)
                            <form method="POST" action="{{ route('emplacements.moveDown', $emplacement) }}" class="inline">
                                @csrf
                                <button type="submit" title="Descendre" class="text-gray-500 hover:text-gray-900">↓</button>
                            </form>
                        @endif
                    @endcan
                </td>

                <td>{{ $emplacement->nom }}</td>

                <td>{{ $emplacement->qr_code }}</td>

                <td>
                    @if($emplacement->nfc_uid)
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Associé</span>
                        <span class="font-mono text-xs text-gray-500">{{ $emplacement->nfc_uid }}</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Non associé</span>
                        @can('update', $emplacement)
                            <button
                                type="button"
                                @click="openEditModal(
                                    {{ $emplacement->id }},
                                    {{ Js::from($emplacement->nom) }},
                                    {{ Js::from($emplacement->description) }},
                                    {{ Js::from($emplacement->latitude) }},
                                    {{ Js::from($emplacement->longitude) }},
                                    {{ Js::from($emplacement->nfc_uid) }},
                                    {{ Js::from($emplacement->qr_code) }}
                                )"
                                class="text-blue-600 text-xs">
                                Associer un tag
                            </button>
                        @endcan
                    @endif
                </td>

                <td>{{ $emplacement->is_active ? 'Actif' : 'Inactif' }}</td>

                <td class="space-x-3 whitespace-nowrap">

                    <a href="{{ route('emplacements.show', $emplacement) }}" class="text-indigo-600">Voir</a>

                    @can('update', $emplacement)
                        <button
                            type="button"

                            @click="openEditModal(
                                {{ $emplacement->id }},
                                {{ Js::from($emplacement->nom) }},
                                {{ Js::from($emplacement->description) }},
                                {{ Js::from($emplacement->latitude) }},
                                {{ Js::from($emplacement->longitude) }},
                                {{ Js::from($emplacement->nfc_uid) }},
                                {{ Js::from($emplacement->qr_code) }}
                            )"

                            class="text-blue-600">

                            Modifier

                        </button>
                    @endcan

                    @can('delete', $emplacement)
                        @if($emplacement->is_active)
                            <form method="POST" action="{{ route('emplacements.destroy', $emplacement) }}" class="inline" onsubmit="return confirm('Désactiver cet emplacement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Désactiver</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('emplacements.restore', $emplacement) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600">Réactiver</button>
                            </form>
                        @endif
                    @endcan

                </td>

            </tr>

        @empty
            <tr>
                <td colspan="6" class="text-center text-gray-400 py-4">Aucun emplacement enregistré pour ce chantier.</td>
            </tr>
        @endforelse

        </tbody>
    </table>

    @include('chantiers.partials.emplacement-modal')

</div>
