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

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Emplacements</h3>

        <button
            type="button"
            @click="openCreateModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded">
            Ajouter un emplacement
        </button>
    </div>

    <table class="w-full">
        <thead>
            <tr>
                <th>Nom</th>
                <th>QR</th>
                <th>NFC</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        @foreach($chantier->emplacements as $emplacement)

            <tr>

                <td>{{ $emplacement->nom }}</td>

                <td>{{ $emplacement->qr_code }}</td>

                <td>{{ $emplacement->nfc_uid }}</td>

                <td>

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

                </td>

            </tr>

        @endforeach

        </tbody>
    </table>

    @include('chantiers.partials.emplacement-modal')

</div>