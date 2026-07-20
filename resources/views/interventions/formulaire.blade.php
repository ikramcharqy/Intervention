<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Formulaire : {{ $formulaire->nom }} (Intervention {{ $intervention->code_intervention }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($formulaire->description)
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-gray-700 border-l-4 border-blue-400 text-blue-700 dark:text-blue-300">
                        {{ $formulaire->description }}
                    </div>
                @endif

                <form method="POST" action="{{ route('interventions.formulaire.store', $intervention) }}" enctype="multipart/form-data">
                    @csrf

                    @foreach($formulaire->questions as $question)
                        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <label class="block font-medium mb-2 text-lg">
                                {{ $question->ordre }}. {{ $question->question }}
                                @if($question->obligatoire) <span class="text-red-500">*</span> @endif
                            </label>
                            
                            @php
                                $valeur = old('reponses.' . $question->id, $reponsesExistantes[$question->id] ?? $question->valeur_par_defaut);
                            @endphp

                            {{-- Texte, TexteLong, Nombre, Date, Heure, DateHeure, GPS, QRCode --}}
                            @if(in_array($question->type_reponse, ['Texte', 'GPS', 'QRCode']))
                                <input type="text" name="reponses[{{ $question->id }}]" value="{{ $valeur }}"
                                       placeholder="{{ $question->placeholder }}" {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">

                            @elseif($question->type_reponse === 'TexteLong')
                                <textarea name="reponses[{{ $question->id }}]" rows="4"
                                          placeholder="{{ $question->placeholder }}" {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ $valeur }}</textarea>

                            @elseif($question->type_reponse === 'Nombre')
                                <input type="number" step="any" name="reponses[{{ $question->id }}]" value="{{ $valeur }}"
                                       placeholder="{{ $question->placeholder }}" {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">

                            @elseif($question->type_reponse === 'Date')
                                <input type="date" name="reponses[{{ $question->id }}]" value="{{ $valeur }}"
                                       {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">

                            @elseif($question->type_reponse === 'Heure')
                                <input type="time" name="reponses[{{ $question->id }}]" value="{{ $valeur }}"
                                       {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">

                            @elseif($question->type_reponse === 'DateHeure')
                                <input type="datetime-local" name="reponses[{{ $question->id }}]" value="{{ $valeur ? \Carbon\Carbon::parse($valeur)->format('Y-m-d\TH:i') : '' }}"
                                       {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">

                            @elseif($question->type_reponse === 'OuiNon')
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="reponses[{{ $question->id }}]" value="1" {{ $valeur == '1' ? 'checked' : '' }} {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                                        <span class="ml-2">Oui</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="reponses[{{ $question->id }}]" value="0" {{ $valeur == '0' ? 'checked' : '' }} {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                                        <span class="ml-2">Non</span>
                                    </label>
                                </div>

                            @elseif($question->type_reponse === 'Liste')
                                <select name="reponses[{{ $question->id }}]" {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="">Sélectionnez...</option>
                                    @foreach($question->choix as $choix)
                                        <option value="{{ $choix->id }}" {{ $valeur == $choix->id ? 'selected' : '' }}>{{ $choix->libelle ?? $choix->valeur }}</option>
                                    @endforeach
                                </select>

                            @elseif($question->type_reponse === 'Radio')
                                <div class="space-y-2">
                                    @foreach($question->choix as $choix)
                                        <label class="flex items-center">
                                            <input type="radio" name="reponses[{{ $question->id }}]" value="{{ $choix->id }}" {{ $valeur == $choix->id ? 'checked' : '' }} {{ $question->obligatoire ? 'required' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                                            <span class="ml-2">{{ $choix->libelle ?? $choix->valeur }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            @elseif($question->type_reponse === 'Checkbox')
                                @php
                                    $valeurs = is_array($valeur) ? $valeur : [];
                                @endphp
                                <div class="space-y-2">
                                    @foreach($question->choix as $choix)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="reponses[{{ $question->id }}][]" value="{{ $choix->id }}" {{ in_array($choix->id, $valeurs) ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                                            <span class="ml-2">{{ $choix->libelle ?? $choix->valeur }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            @elseif(in_array($question->type_reponse, ['Photo', 'Signature', 'Document']))
                                @if($valeur && !is_array($valeur))
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($valeur) }}" target="_blank" class="text-blue-500 hover:underline">Voir le fichier actuel</a>
                                    </div>
                                @endif
                                
                                @if(!$isReadOnly)
                                    <input type="file" name="reponses[{{ $question->id }}]" {{ $question->obligatoire && !$valeur ? 'required' : '' }}
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @endif
                            @elseif($question->type_reponse === 'Materiaux')
                                @php
                                    $lignesMateriaux = is_array($valeur) ? $valeur : [];
                                @endphp
                                <div class="materiaux-repeater" data-question-id="{{ $question->id }}">
                                    <table class="min-w-full text-sm mb-3">
                                        <thead>
                                            <tr class="text-left text-gray-500 dark:text-gray-400">
                                                <th class="px-2 py-1">Matériau</th>
                                                <th class="px-2 py-1">Quantité</th>
                                                <th class="px-2 py-1">Commentaire</th>
                                                @if(!$isReadOnly)<th></th>@endif
                                            </tr>
                                        </thead>
                                        <tbody class="materiaux-rows">
                                            @foreach($lignesMateriaux as $i => $ligne)
                                                <tr class="materiaux-row">
                                                    <td class="px-2 py-1">
                                                        <select name="reponses[{{ $question->id }}][{{ $i }}][materiau_id]" {{ $isReadOnly ? 'disabled' : '' }} class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                            <option value="">Sélectionnez...</option>
                                                            @foreach($materiauxDisponibles as $materiau)
                                                                <option value="{{ $materiau->id }}" {{ ($ligne['materiau_id'] ?? null) == $materiau->id ? 'selected' : '' }}>{{ $materiau->nom }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="px-2 py-1">
                                                        <input type="number" step="any" min="0.01" name="reponses[{{ $question->id }}][{{ $i }}][quantite]" value="{{ $ligne['quantite'] ?? '' }}" {{ $isReadOnly ? 'disabled' : '' }} class="w-24 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                    </td>
                                                    <td class="px-2 py-1">
                                                        <input type="text" name="reponses[{{ $question->id }}][{{ $i }}][commentaire]" value="{{ $ligne['commentaire'] ?? '' }}" {{ $isReadOnly ? 'disabled' : '' }} class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                    </td>
                                                    @if(!$isReadOnly)<td class="px-2 py-1"><button type="button" class="text-red-500 text-xs materiaux-remove-row">Retirer</button></td>@endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if(!$isReadOnly)
                                        <button type="button" class="text-sm text-blue-600 hover:text-blue-800 materiaux-add-row">+ Ajouter un matériau</button>
                                    @endif
                                </div>

                            @endif

                        </div>
                    @endforeach

                    <div class="flex justify-end gap-2 mt-6">
                        <a href="{{ route('interventions.show', $intervention) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Retour</a>
                        @if(!$isReadOnly)
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const optionsMateriaux = @json($materiauxDisponibles->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom]));

            function buildOptions(selectedId) {
                let html = '<option value="">Sélectionnez...</option>';
                optionsMateriaux.forEach(function (m) {
                    html += '<option value="' + m.id + '"' + (String(m.id) === String(selectedId) ? ' selected' : '') + '>' + m.nom + '</option>';
                });
                return html;
            }

            document.querySelectorAll('.materiaux-repeater').forEach(function (repeater) {
                const questionId = repeater.dataset.questionId;
                const tbody = repeater.querySelector('.materiaux-rows');
                const addBtn = repeater.querySelector('.materiaux-add-row');
                let index = tbody.querySelectorAll('.materiaux-row').length;

                if (addBtn) {
                    addBtn.addEventListener('click', function () {
                        const tr = document.createElement('tr');
                        tr.className = 'materiaux-row';
                        tr.innerHTML =
                            '<td class="px-2 py-1"><select name="reponses[' + questionId + '][' + index + '][materiau_id]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">' + buildOptions(null) + '</select></td>' +
                            '<td class="px-2 py-1"><input type="number" step="any" min="0.01" name="reponses[' + questionId + '][' + index + '][quantite]" class="w-24 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></td>' +
                            '<td class="px-2 py-1"><input type="text" name="reponses[' + questionId + '][' + index + '][commentaire]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></td>' +
                            '<td class="px-2 py-1"><button type="button" class="text-red-500 text-xs materiaux-remove-row">Retirer</button></td>';
                        tbody.appendChild(tr);
                        index++;
                    });
                }

                tbody.addEventListener('click', function (e) {
                    if (e.target.classList.contains('materiaux-remove-row')) {
                        e.target.closest('tr').remove();
                    }
                });
            });
        });
    </script>
</x-app-layout>
