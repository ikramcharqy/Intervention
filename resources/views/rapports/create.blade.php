<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Créer un Rapport</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('rapports.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block font-medium">Intervention <span class="text-red-500">*</span></label>
                        @if ($interventionId && $interventions->count() === 1)
                            <input type="hidden" name="intervention_id" value="{{ $interventionId }}">
                            <p class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 dark:text-gray-300 px-3 py-2">
                                {{ $interventions->first()->code_intervention }}
                            </p>
                        @else
                            <select name="intervention_id" id="intervention_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($interventions as $intervention)
                                    <option value="{{ $intervention->id }}" @selected(old('intervention_id', $interventionId) == $intervention->id)>
                                        {{ $intervention->code_intervention }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('intervention_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_debut" class="block font-medium">Date/Heure de début <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="date_debut" id="date_debut" required
                                value="{{ old('date_debut', now()->subHour()->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('date_debut')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="date_fin" class="block font-medium">Date/Heure de fin <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="date_fin" id="date_fin" required
                                value="{{ old('date_fin', now()->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('date_fin')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="commentaire" class="block font-medium">Commentaire / Observations du technicien</label>
                        <textarea name="commentaire" id="commentaire" rows="5"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('commentaire') }}</textarea>
                        @error('commentaire')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="signature_technicien" class="block font-medium">Signature du technicien (Nom ou trace)</label>
                            <input type="text" name="signature_technicien" id="signature_technicien"
                                value="{{ old('signature_technicien') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('signature_technicien')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="signature_client" class="block font-medium">Signature du client (Nom ou trace)</label>
                            <input type="text" name="signature_client" id="signature_client"
                                value="{{ old('signature_client') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('signature_client')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer le rapport</button>
                        <a href="{{ route('rapports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
