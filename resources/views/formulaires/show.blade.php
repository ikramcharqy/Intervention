<x-dynamic-component :component="$layoutComponent">
    @php
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');
        $roleLabel = $isSuperAdmin ? 'Super Admin' : 'Admin';
    @endphp
    <div class="space-y-6" x-data="gestionChamps({{ $isSuperAdmin ? 'true' : 'false' }})">
        <!-- Titre + Actions -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('formulaires.index') }}" class="w-9 h-9 rounded-[4px] border border-[#E6E9F4] flex items-center justify-center text-[#5A607F] hover:bg-[#F5F6FA] transition shrink-0">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">{{ $formulaire->nom }}</h1>
                    <p class="text-xs text-[#5A607F] mt-1">{{ $formulaire->typeIntervention->nom ?? '—' }}</p>
                </div>
                <span class="ds-badge ds-badge-sm {{ $formulaire->is_active ? 'ds-badge-light-success' : 'ds-badge-light-secondary' }}">
                    {{ $formulaire->is_active ? 'Actif' : 'Inactif' }}
                </span>
                <span class="ds-badge ds-badge-sm ds-badge-light-secondary">
                    <i class="fas fa-user-shield mr-1.5"></i> Connecté en tant que {{ $roleLabel }}
                </span>
            </div>
            @if($isSuperAdmin)
                <a href="{{ route('formulaires.edit', $formulaire) }}" class="ds-btn ds-btn-white ds-btn-sm">
                    <i class="fas fa-pen"></i> Modifier les informations
                </a>
            @endif
        </div>

        <!-- Stepper -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-[#06A561] text-white text-xs font-bold flex items-center justify-center shrink-0">
                    <i class="fas fa-check text-[10px]"></i>
                </span>
                <span class="text-xs font-bold text-[#06A561]">Informations générales</span>
            </div>
            <div class="flex-1 h-px bg-[#E6E9F4]"></div>
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-[#1E5EFF] text-white text-xs font-bold flex items-center justify-center shrink-0">2</span>
                <span class="text-xs font-bold text-[#131523]">Questions &amp; choix</span>
            </div>
        </div>

        <!-- Protocole incomplet -->
        @if ($formulaire->questions->count() === 0)
            <div class="p-4 rounded-[6px] bg-[#FFF3DE] border border-[#FFE7B8] text-[#B98900] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-triangle-exclamation text-sm shrink-0"></i>
                <span>Protocole incomplet : ajoutez au moins une question ci-dessous avant de pouvoir l'activer et l'envoyer aux techniciens.</span>
            </div>
        @endif

        <!-- Alertes -->
        @if (session('success'))
            <div class="p-4 rounded-[6px] bg-[#E3FBF0] border border-[#C4F8E2] text-[#06A561] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle text-[#06A561] text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 rounded-[6px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-[#F0142F] text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 rounded-[6px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-semibold">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <i class="fas fa-exclamation-circle text-[#F0142F] text-sm shrink-0 mt-0.5"></i>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Informations générales -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E6E9F4]">
                <h3 class="text-sm font-bold text-[#131523]">Informations</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] uppercase tracking-wider mb-1">Type d'intervention</p>
                    <p class="text-sm font-semibold text-[#131523]">{{ $formulaire->typeIntervention->nom ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[11px] font-bold text-[#A1A7C4] uppercase tracking-wider mb-1">Description</p>
                    <p class="text-sm text-[#5A607F]">{{ $formulaire->description ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Champs du formulaire -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E6E9F4]">
                <h3 class="text-sm font-bold text-[#131523]">Champs du formulaire ({{ $formulaire->questions->count() }})</h3>
                @if($isSuperAdmin)
                    <button type="button" @click="openModalCreate()" class="ds-btn ds-btn-primary ds-btn-sm">
                        <i class="fas fa-plus"></i> Ajouter un champ
                    </button>
                @endif
            </div>

            <div class="p-6">
                <template x-if="questions.length === 0">
                    <div class="text-center py-16">
                        <div class="w-14 h-14 rounded-full bg-[#EAF0FF] flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-list-check text-xl text-[#1E5EFF]"></i>
                        </div>
                        <p class="text-sm font-bold text-[#131523]">Aucun champ pour ce formulaire</p>
                        <p class="text-xs text-[#A1A7C4] mt-1">Ajoutez votre première question pour commencer.</p>
                    </div>
                </template>

                @if($isSuperAdmin)
                    <p class="text-[11px] text-[#A1A7C4] mb-3" x-show="questions.length > 1">
                        <i class="fas fa-arrows-up-down mr-1"></i> Glissez-déposez une ligne pour changer son ordre d'affichage.
                    </p>
                @else
                    <div class="p-3.5 rounded-[6px] bg-[#EAF0FF] border border-[#D9E4FF] flex items-start gap-2.5 mb-3" x-show="questions.length > 0">
                        <i class="fas fa-info-circle text-[#1E5EFF] text-sm shrink-0 mt-0.5"></i>
                        <p class="text-[11px] text-[#5A607F] leading-relaxed">En tant qu'Admin, vous pouvez gérer les choix des questions à choix multiples. La création de champs et la modification de leur structure sont réservées au Super Admin.</p>
                    </div>
                @endif

                <div class="space-y-2">
                    <template x-for="(q, index) in questions" :key="q.id">
                        <div class="flex items-start gap-3 p-4 rounded-[6px] border border-[#E6E9F4] bg-white"
                             :class="dragIndex === index ? 'opacity-40' : ''"
                             :draggable="isSuperAdmin"
                             @dragstart="onDragStart(index)"
                             @dragover.prevent="onDragOver(index)"
                             @drop.prevent="onDrop(index)"
                             @dragend="onDragEnd()">
                            <span x-show="isSuperAdmin" class="w-8 h-8 rounded-[4px] bg-[#F5F6FA] flex items-center justify-center text-[#A1A7C4] cursor-move shrink-0 mt-0.5">
                                <i class="fas fa-grip-vertical text-xs"></i>
                            </span>

                            <span class="w-7 h-7 rounded-full bg-[#EAF0FF] text-[#1E5EFF] text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5" x-text="index + 1"></span>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-bold text-[#131523]" x-text="q.question"></p>
                                    <span x-show="q.obligatoire" class="text-[#F0142F] text-xs font-bold" title="Obligatoire">*</span>
                                    <span class="ds-badge ds-badge-sm ds-badge-light-primary" x-text="typeLabels[q.type_reponse] || q.type_reponse"></span>
                                </div>

                                <template x-if="necessiteChoix(q.type_reponse) && q.choix && q.choix.length > 0">
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <template x-for="c in q.choix" :key="c.id ?? c.valeur">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] bg-[#F5F6FA] border border-[#E6E9F4] text-[11px] text-[#5A607F]" x-text="c.libelle || c.valeur"></span>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="q.type_reponse === 'Nombre' && (q.nombre_min !== null || q.nombre_max !== null || q.nombre_unite)">
                                    <p class="text-[11px] text-[#A1A7C4] mt-1.5">
                                        <span x-show="q.nombre_min !== null">Min : <span x-text="q.nombre_min"></span></span>
                                        <span x-show="q.nombre_max !== null"> Max : <span x-text="q.nombre_max"></span></span>
                                        <span x-show="q.nombre_unite">Unité : <span x-text="q.nombre_unite"></span></span>
                                    </p>
                                </template>

                                <template x-if="['Photo', 'Document'].includes(q.type_reponse) && q.fichiers_max">
                                    <p class="text-[11px] text-[#A1A7C4] mt-1.5">Max <span x-text="q.fichiers_max"></span> fichier(s)</p>
                                </template>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <template x-if="isSuperAdmin">
                                    <button type="button" @click="openModalEdit(q)" class="w-8 h-8 rounded-[4px] border border-[#E6E9F4] flex items-center justify-center text-[#5A607F] hover:bg-[#F5F6FA] transition">
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>
                                </template>
                                <template x-if="isSuperAdmin">
                                    <button type="button" @click="deleteQuestion(q)" class="w-8 h-8 rounded-[4px] border border-[#F8C4CA] flex items-center justify-center text-[#F0142F] hover:bg-[#FDE3E6] transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </template>
                                <template x-if="!isSuperAdmin && necessiteChoix(q.type_reponse)">
                                    <button type="button" @click="openChoixModal(q)" class="ds-btn ds-btn-white ds-btn-sm">
                                        <i class="fas fa-list-ul"></i> Gérer les choix
                                    </button>
                                </template>
                                <template x-if="!isSuperAdmin && !necessiteChoix(q.type_reponse)">
                                    <span class="text-[#A1A7C4] text-xs" title="Modification réservée au Super Admin">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal Création / Édition -->
        <div x-show="isModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-[6px] w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="closeModal()">
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E6E9F4]">
                    <h3 class="text-sm font-bold text-[#131523]" x-text="isEdit ? 'Modifier le champ' : 'Ajouter un champ'"></h3>
                    <button type="button" @click="closeModal()" class="w-8 h-8 rounded-[4px] flex items-center justify-center text-[#A1A7C4] hover:bg-[#F5F6FA] transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <form :action="formAction" method="POST" @submit="submitting = true" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">

                    <div>
                        <label class="ds-label">Libellé de la question *</label>
                        <input type="text" name="question" x-model="formData.question" required placeholder="Ex : Numéro de série de l'équipement" class="ds-input">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ds-label">Type de champ *</label>
                            <select name="type_reponse" x-model="formData.type_reponse" required class="ds-input">
                                @foreach(\App\Models\Formulaire::TYPES_CHAMPS as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="ds-label">Ordre d'affichage</label>
                            <input type="number" name="ordre" x-model="formData.ordre" min="1" placeholder="Auto" class="ds-input">
                        </div>
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                        <input type="checkbox" name="obligatoire" value="1" x-model="formData.obligatoire" class="w-4 h-4 rounded border-[#D7DBEC] text-[#1E5EFF] focus:ring-[#1E5EFF]">
                        <span class="text-sm font-medium text-[#131523]">Champ obligatoire</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ds-label">Placeholder (aide à la saisie)</label>
                            <input type="text" name="placeholder" x-model="formData.placeholder" class="ds-input">
                        </div>
                        <div>
                            <label class="ds-label">Valeur par défaut</label>
                            <input type="text" name="valeur_par_defaut" x-model="formData.valeur_par_defaut" class="ds-input">
                        </div>
                    </div>

                    <!-- Options spécifiques au type "Nombre" -->
                    <template x-if="formData.type_reponse === 'Nombre'">
                        <div class="p-4 rounded-[6px] border border-[#E6E9F4] bg-[#F5F6FA]">
                            <p class="text-xs font-bold text-[#131523] mb-3">Options du champ Nombre</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="ds-label">Minimum</label>
                                    <input type="number" step="any" name="nombre_min" x-model="formData.nombre_min" placeholder="Ex : 0" class="ds-input !h-9 !text-sm">
                                </div>
                                <div>
                                    <label class="ds-label">Maximum</label>
                                    <input type="number" step="any" name="nombre_max" x-model="formData.nombre_max" placeholder="Ex : 100" class="ds-input !h-9 !text-sm">
                                </div>
                                <div>
                                    <label class="ds-label">Unité</label>
                                    <input type="text" name="nombre_unite" x-model="formData.nombre_unite" placeholder="°C, bar, m..." maxlength="20" class="ds-input !h-9 !text-sm">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Option spécifique aux types "Photo" et "Document" -->
                    <template x-if="['Photo', 'Document'].includes(formData.type_reponse)">
                        <div class="p-4 rounded-[6px] border border-[#E6E9F4] bg-[#F5F6FA]">
                            <label class="ds-label">Nombre maximum de fichiers autorisés</label>
                            <input type="number" step="1" min="1" max="20" name="fichiers_max" x-model="formData.fichiers_max" placeholder="Illimité si vide" class="ds-input !h-9 !text-sm max-w-[160px]">
                        </div>
                    </template>

                    <!-- Choix dynamiques (Liste / Checkbox / Radio) -->
                    <div x-show="['Liste', 'Checkbox', 'Radio'].includes(formData.type_reponse)" x-cloak class="p-4 rounded-[6px] border border-[#E6E9F4] bg-[#F5F6FA]">
                        <p class="text-xs font-bold text-[#131523] mb-3">Choix possibles</p>
                        <div class="space-y-2">
                            <template x-for="(choix, index) in formData.choix" :key="index">
                                <div class="flex gap-2 items-center">
                                    <input type="text" :name="`choix[${index}][valeur]`" x-model="choix.valeur" placeholder="Libellé du choix *" required class="ds-input !h-9 !text-sm flex-1">
                                    <input type="text" :name="`choix[${index}][libelle]`" x-model="choix.libelle" placeholder="Libellé affiché (optionnel, sinon identique)" class="ds-input !h-9 !text-sm flex-1">
                                    <input type="hidden" :name="`choix[${index}][ordre]`" :value="index + 1">
                                    <button type="button" @click="removeChoix(index)" class="w-9 h-9 rounded-[4px] border border-[#F8C4CA] flex items-center justify-center text-[#F0142F] hover:bg-[#FDE3E6] transition shrink-0">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addChoix()" class="ds-btn ds-btn-white ds-btn-sm mt-3">
                            <i class="fas fa-plus"></i> Ajouter un choix
                        </button>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeModal()" class="ds-btn ds-btn-white ds-btn-sm">Annuler</button>
                        <button type="submit" :disabled="submitting" class="ds-btn ds-btn-primary ds-btn-sm">
                            <span x-show="!submitting">Enregistrer</span>
                            <span x-show="submitting">Enregistrement...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formulaire de suppression caché (soumis via JS après confirmation) -->
        <form x-ref="deleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <!-- Modal Gérer les choix (Admin) — structure verrouillée, choix seulement -->
        <div x-show="choixModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-[6px] w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="closeChoixModal()">
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E6E9F4]">
                    <div>
                        <p class="text-[11px] text-[#A1A7C4] mb-0.5">Formulaires &gt; {{ $formulaire->typeIntervention->nom ?? '—' }} &gt; Gérer les champs</p>
                        <h3 class="text-sm font-bold text-[#131523]">Modifier une question</h3>
                    </div>
                    <span class="ds-badge ds-badge-sm ds-badge-light-secondary">
                        <i class="fas fa-user-shield mr-1.5"></i> Connecté en tant qu'{{ $roleLabel }}
                    </span>
                </div>

                <form :action="choixFormAction" method="POST" @submit="submitting = true" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div>
                        <label class="ds-label">Libellé de la question</label>
                        <input type="text" :value="choixFormData.question" disabled class="ds-input opacity-60 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ds-label">Type de champ</label>
                            <input type="text" :value="typeLabels[choixFormData.type_reponse] || choixFormData.type_reponse" disabled class="ds-input opacity-60 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="ds-label">Obligatoire</label>
                            <input type="text" :value="choixFormData.obligatoire ? 'Oui' : 'Non'" disabled class="ds-input opacity-60 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="p-4 rounded-[6px] border border-[#E6E9F4] bg-[#F5F6FA]">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold text-[#131523]">Choix disponibles</p>
                            <span class="text-[11px] text-[#A1A7C4]" x-text="choixFormData.choix.length + ' choix'"></span>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(choix, index) in choixFormData.choix" :key="index">
                                <div class="flex gap-2 items-center">
                                    <input type="text" :name="`choix[${index}][valeur]`" x-model="choix.valeur" placeholder="Libellé du choix *" required class="ds-input !h-9 !text-sm flex-1">
                                    <input type="text" :name="`choix[${index}][libelle]`" x-model="choix.libelle" placeholder="Libellé affiché (optionnel, sinon identique)" class="ds-input !h-9 !text-sm flex-1">
                                    <input type="hidden" :name="`choix[${index}][ordre]`" :value="index + 1">
                                    <button type="button" @click="removeChoixOnly(index)" class="w-9 h-9 rounded-[4px] border border-[#F8C4CA] flex items-center justify-center text-[#F0142F] hover:bg-[#FDE3E6] transition shrink-0">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addChoixOnly()" class="ds-btn ds-btn-white ds-btn-sm mt-3">
                            <i class="fas fa-plus"></i> Ajouter un choix
                        </button>
                    </div>

                    <div class="p-3.5 rounded-[6px] bg-[#EAF0FF] border border-[#D9E4FF] flex items-start gap-2.5">
                        <i class="fas fa-info-circle text-[#1E5EFF] text-sm shrink-0 mt-0.5"></i>
                        <p class="text-[11px] text-[#5A607F] leading-relaxed">En tant qu'{{ $roleLabel }}, vous pouvez ajouter ou retirer des choix pour cette question. La création d'un nouveau type d'intervention ou d'un nouveau formulaire reste réservée au Super Admin.</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeChoixModal()" class="ds-btn ds-btn-white ds-btn-sm">Annuler</button>
                        <button type="submit" :disabled="submitting" class="ds-btn ds-btn-primary ds-btn-sm">
                            <span x-show="!submitting">Enregistrer les modifications</span>
                            <span x-show="submitting">Enregistrement...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $questionsData = $formulaire->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'type_reponse' => $q->type_reponse,
                'obligatoire' => (bool) $q->obligatoire,
                'ordre' => $q->ordre,
                'placeholder' => $q->placeholder,
                'valeur_par_defaut' => $q->valeur_par_defaut,
                'nombre_min' => $q->nombre_min,
                'nombre_max' => $q->nombre_max,
                'nombre_unite' => $q->nombre_unite,
                'fichiers_max' => $q->fichiers_max,
                'choix' => $q->choix->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'libelle' => $c->libelle,
                        'valeur' => $c->valeur,
                    ];
                })->values(),
            ];
        })->values();
    @endphp

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('gestionChamps', (isSuperAdmin) => ({
                isSuperAdmin: isSuperAdmin,
                isModalOpen: false,
                isEdit: false,
                submitting: false,
                formAction: '',
                formulaireId: {{ $formulaire->id }},
                questions: @json($questionsData),
                typeLabels: @json(\App\Models\Formulaire::TYPES_CHAMPS),
                typesAvecChoix: @json(\App\Models\Formulaire::TYPES_AVEC_CHOIX),

                dragIndex: null,
                dragOverIndex: null,

                choixModalOpen: false,
                choixFormAction: '',
                choixFormData: {
                    id: null,
                    question: '',
                    type_reponse: '',
                    obligatoire: false,
                    choix: [],
                },

                openChoixModal(question) {
                    this.choixFormAction = `/formulaires/${this.formulaireId}/questions/${question.id}/choix`;
                    this.choixFormData = {
                        id: question.id,
                        question: question.question,
                        type_reponse: question.type_reponse,
                        obligatoire: !!question.obligatoire,
                        choix: (question.choix && question.choix.length > 0)
                            ? question.choix.map(c => ({ valeur: c.valeur, libelle: c.libelle }))
                            : [{ valeur: '', libelle: '' }, { valeur: '', libelle: '' }],
                    };
                    this.choixModalOpen = true;
                },

                closeChoixModal() {
                    this.choixModalOpen = false;
                },

                addChoixOnly() {
                    this.choixFormData.choix.push({ valeur: '', libelle: '' });
                },

                removeChoixOnly(index) {
                    this.choixFormData.choix.splice(index, 1);
                },

                formData: {
                    id: null,
                    question: '',
                    type_reponse: 'Texte',
                    obligatoire: false,
                    ordre: '',
                    placeholder: '',
                    valeur_par_defaut: '',
                    nombre_min: '',
                    nombre_max: '',
                    nombre_unite: '',
                    fichiers_max: '',
                    choix: []
                },

                necessiteChoix(type) {
                    return this.typesAvecChoix.includes(type);
                },

                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                },

                openModalCreate() {
                    this.isEdit = false;
                    this.formAction = `/formulaires/${this.formulaireId}/questions`;
                    this.formData = {
                        id: null, question: '', type_reponse: 'Texte', obligatoire: false,
                        ordre: '', placeholder: '', valeur_par_defaut: '',
                        nombre_min: '', nombre_max: '', nombre_unite: '', fichiers_max: '',
                        choix: [{ valeur: '', libelle: '' }]
                    };
                    this.isModalOpen = true;
                },

                openModalEdit(question) {
                    this.isEdit = true;
                    this.formAction = `/formulaires/${this.formulaireId}/questions/${question.id}`;
                    this.formData = {
                        id: question.id,
                        question: question.question,
                        type_reponse: question.type_reponse,
                        obligatoire: !!question.obligatoire,
                        ordre: question.ordre,
                        placeholder: question.placeholder || '',
                        valeur_par_defaut: question.valeur_par_defaut || '',
                        nombre_min: question.nombre_min ?? '',
                        nombre_max: question.nombre_max ?? '',
                        nombre_unite: question.nombre_unite || '',
                        fichiers_max: question.fichiers_max ?? '',
                        choix: (question.choix && question.choix.length > 0)
                            ? question.choix.map(c => ({ valeur: c.valeur, libelle: c.libelle }))
                            : [{ valeur: '', libelle: '' }]
                    };
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                },

                addChoix() {
                    this.formData.choix.push({ valeur: '', libelle: '' });
                },

                removeChoix(index) {
                    this.formData.choix.splice(index, 1);
                },

                deleteQuestion(question) {
                    if (!confirm(`Supprimer le champ "${question.question}" ? Cette action supprimera aussi ses choix associés.`)) {
                        return;
                    }
                    const form = this.$refs.deleteForm;
                    form.action = `/formulaires/${this.formulaireId}/questions/${question.id}`;
                    form.submit();
                },

                // Drag & drop réordonnancement (HTML5 natif, sans dépendance)
                onDragStart(index) {
                    if (!this.isSuperAdmin) return;
                    this.dragIndex = index;
                },

                onDragOver(index) {
                    this.dragOverIndex = index;
                },

                onDragEnd() {
                    this.dragIndex = null;
                    this.dragOverIndex = null;
                },

                onDrop(targetIndex) {
                    if (this.dragIndex === null || this.dragIndex === targetIndex) {
                        this.onDragEnd();
                        return;
                    }
                    const moved = this.questions.splice(this.dragIndex, 1)[0];
                    this.questions.splice(targetIndex, 0, moved);
                    this.onDragEnd();
                    this.persistOrder();
                },

                async persistOrder() {
                    const ordre = {};
                    this.questions.forEach((q, i) => { ordre[q.id] = i + 1; });

                    try {
                        await fetch(`/formulaires/${this.formulaireId}/questions/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ ordre }),
                        });
                    } catch (e) {
                        // best effort — un rechargement de page affichera l'ordre réel en base
                    }
                }
            }));
        });
    </script>
</x-dynamic-component>
