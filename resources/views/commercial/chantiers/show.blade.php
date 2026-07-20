<x-commercial-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Chantier : {{ $chantier->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Code Chantier:</strong> {{ $chantier->code_chantier }}</div>
                    <div><strong>Type de Local:</strong> {{ $chantier->type_local }}</div>
                    <div><strong>Adresse:</strong> {{ $chantier->adresse }}</div>
                    <div><strong>Ville:</strong> {{ $chantier->ville }}</div>
                    <div><strong>Responsable:</strong> {{ $chantier->responsable }}</div>
                    <div><strong>Téléphone Responsable:</strong> {{ $chantier->telephone_responsable }}</div>
                    <div><strong>E-mail Responsable:</strong> {{ $chantier->email_responsable }}</div>
                </div>
            </div>
            
            <!-- Emplacements (Nested Component) -->
            @include('chantiers.partials.emplacements-table')
        </div>
    </div>
</x-commercial-layout>
