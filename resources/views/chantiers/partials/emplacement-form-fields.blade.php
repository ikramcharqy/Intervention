{{-- Shared form fields for Create and Update Emplacement forms --}}
<div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
    <div class="sm:flex sm:items-start">
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
            <h3
                class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                id="modal-title"
                x-text="editMode ? 'Modifier l\'emplacement' : 'Ajouter un emplacement'">
            </h3>
            <div class="mt-4 space-y-4 text-left">

                <div>
                    <label for="nom" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nom"
                        id="nom"
                        required
                        x-model="formData.nom"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="2"
                        x-model="formData.description"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Latitude</label>
                        <input
                            type="number"
                            step="any"
                            name="latitude"
                            id="latitude"
                            x-model="formData.latitude"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="longitude" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Longitude</label>
                        <input
                            type="number"
                            step="any"
                            name="longitude"
                            id="longitude"
                            x-model="formData.longitude"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                </div>

                <div>
                    <label for="nfc_uid" class="block font-medium text-sm text-gray-700 dark:text-gray-300">NFC UID</label>
                    <input
                        type="text"
                        name="nfc_uid"
                        id="nfc_uid"
                        x-model="formData.nfc_uid"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <template x-if="editMode && formData.qr_code">
                    <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm text-gray-600 dark:text-gray-400">
                        <strong>QR Code actuel :</strong> <span x-text="formData.qr_code" class="font-mono"></span>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>
