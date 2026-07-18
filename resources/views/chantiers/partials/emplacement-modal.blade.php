<div
    x-show="showModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg">

        <form
            method="POST"
            :action="editMode
                ? updateBaseUrl + '/' + formData.id
                : storeUrl">

            @csrf

            <template x-if="editMode">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <input type="hidden" name="chantier_id" value="{{ $chantier->id }}">

            @include('chantiers.partials.emplacement-form-fields')

            @include('chantiers.partials.emplacement-form-buttons')

        </form>

    </div>

</div>
