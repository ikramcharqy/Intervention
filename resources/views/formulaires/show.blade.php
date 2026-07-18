<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Formulaire : {{ $formulaire->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Infos générales --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 flex justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Informations</h3>
                    <p><strong>Type d'intervention :</strong> {{ $formulaire->typeIntervention->nom ?? '-' }}</p>
                    <p><strong>Description :</strong> {{ $formulaire->description ?? '-' }}</p>
                    <p><strong>Statut :</strong> <span class="{{ $formulaire->is_active ? 'text-green-500' : 'text-red-500' }}">{{ $formulaire->is_active ? 'Actif' : 'Inactif' }}</span></p>
                </div>
                <div>
                    <a href="{{ route('formulaires.edit', $formulaire) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Modifier</a>
                </div>
            </div>

            {{-- Liste des champs (questions) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100" x-data="gestionChamps()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Champs du formulaire ({{ $formulaire->questions->count() }})</h3>
                    <button @click="openModalCreate()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Ajouter un champ</button>
                </div>

                <div class="space-y-4">
                    @foreach($formulaire->questions as $question)
                        <div class="border border-gray-200 dark:border-gray-700 p-4 rounded-md relative flex justify-between items-start">
                            <div>
                                <div class="font-medium text-lg">
                                    {{ $question->ordre }}. {{ $question->question }}
                                    @if($question->obligatoire) <span class="text-red-500" title="Obligatoire">*</span> @endif
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Type : {{ \App\Models\Formulaire::TYPES_CHAMPS[$question->type_reponse] ?? $question->type_reponse }}</div>
                                
                                @if($question->necessiteChoix() && $question->choix->count() > 0)
                                    <div class="mt-2 text-sm">
                                        <strong>Choix possibles :</strong>
                                        <ul class="list-disc ml-5 text-gray-600 dark:text-gray-400">
                                            @foreach($question->choix as $choix)
                                                <li>{{ $choix->libelle }} ({{ $choix->valeur }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <button @click="openModalEdit({{ json_encode($question) }}, {{ json_encode($question->choix) }})" class="text-yellow-500 hover:text-yellow-700">Modifier</button>
                                <form method="POST" action="{{ route('formulaires.questions.destroy', [$formulaire, $question]) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce champ ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Modal Création / Edition --}}
                <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="closeModal()">
                        <h3 class="text-xl font-bold mb-4" x-text="isEdit ? 'Modifier le champ' : 'Ajouter un champ'"></h3>
                        
                        <form :action="formAction" method="POST">
                            @csrf
                            <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                            
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Intitulé de la question / champ *</label>
                                <input type="text" name="question" x-model="formData.question" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block font-medium mb-1">Type de réponse *</label>
                                    <select name="type_reponse" x-model="formData.type_reponse" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        @foreach(\App\Models\Formulaire::TYPES_CHAMPS as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-medium mb-1">Ordre</label>
                                    <input type="number" name="ordre" x-model="formData.ordre" min="1" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="obligatoire" value="1" x-model="formData.obligatoire" class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 font-medium">Champ obligatoire</span>
                                </label>
                            </div>

                            {{-- Champs additionnels --}}
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block font-medium mb-1">Placeholder (aide)</label>
                                    <input type="text" name="placeholder" x-model="formData.placeholder" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                </div>
                                <div>
                                    <label class="block font-medium mb-1">Valeur par défaut</label>
                                    <input type="text" name="valeur_par_defaut" x-model="formData.valeur_par_defaut" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                </div>
                            </div>

                            {{-- Gestion dynamique des choix (si type = Liste, Checkbox, Radio) --}}
                            <div x-show="['Liste', 'Checkbox', 'Radio'].includes(formData.type_reponse)" class="mb-4 p-4 border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <h4 class="font-medium mb-2">Choix possibles</h4>
                                <template x-for="(choix, index) in formData.choix" :key="index">
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" :name="`choix[${index}][valeur]`" x-model="choix.valeur" placeholder="Valeur stockée *" required class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm">
                                        <input type="text" :name="`choix[${index}][libelle]`" x-model="choix.libelle" placeholder="Libellé affiché (optionnel)" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm">
                                        <input type="hidden" :name="`choix[${index}][ordre]`" :value="index + 1">
                                        <button type="button" @click="removeChoix(index)" class="text-red-500 px-2 font-bold text-xl">&times;</button>
                                    </div>
                                </template>
                                <button type="button" @click="addChoix()" class="text-sm bg-gray-200 dark:bg-gray-700 px-3 py-1 rounded hover:bg-gray-300 dark:hover:bg-gray-600 mt-2">+ Ajouter un choix</button>
                            </div>

                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" @click="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</button>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Alpine.js script for Modal management -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('gestionChamps', () => ({
                isModalOpen: false,
                isEdit: false,
                formAction: '',
                formulaireId: {{ $formulaire->id }},
                
                formData: {
                    id: null,
                    question: '',
                    type_reponse: 'Texte',
                    obligatoire: false,
                    ordre: '',
                    placeholder: '',
                    valeur_par_defaut: '',
                    choix: []
                },

                openModalCreate() {
                    this.isEdit = false;
                    this.formAction = `/formulaires/${this.formulaireId}/questions`;
                    this.formData = {
                        id: null, question: '', type_reponse: 'Texte', obligatoire: false, 
                        ordre: '', placeholder: '', valeur_par_defaut: '', choix: [{valeur: '', libelle: ''}]
                    };
                    this.isModalOpen = true;
                },

                openModalEdit(question, choixData) {
                    this.isEdit = true;
                    this.formAction = `/formulaires/${this.formulaireId}/questions/${question.id}`;
                    this.formData = {
                        id: question.id,
                        question: question.question,
                        type_reponse: question.type_reponse,
                        obligatoire: question.obligatoire == 1,
                        ordre: question.ordre,
                        placeholder: question.placeholder || '',
                        valeur_par_defaut: question.valeur_par_defaut || '',
                        choix: choixData.length > 0 ? choixData : [{valeur: '', libelle: ''}]
                    };
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                },

                addChoix() {
                    this.formData.choix.push({valeur: '', libelle: ''});
                },

                removeChoix(index) {
                    this.formData.choix.splice(index, 1);
                }
            }));
        });
    </script>
</x-app-layout>
