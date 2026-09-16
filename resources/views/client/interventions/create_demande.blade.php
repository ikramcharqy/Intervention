<x-client-layout>
    <x-slot name="header">Nouvelle Demande</x-slot>

    <div class="max-w-3xl mx-auto space-y-4">
        @if($demandeOrigine)
            <div class="p-3.5 rounded-2xl text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 flex items-center gap-2">
                <i class="fas fa-circle-info"></i>
                Cette demande fait suite à la demande refusée <strong>{{ $demandeOrigine->reference }}</strong> — les champs ci-dessous ont été pré-remplis, vous pouvez les ajuster.
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6" x-data="demandeForm()">
            <div class="border-b border-slate-100 pb-4">
                <h1 class="text-sm font-extrabold text-[#1e2530]">Formulaire de Demande d'Intervention</h1>
                <p class="text-xs text-slate-400 mt-1">Remplissez les détails ci-dessous. Votre responsable commercial étudiera votre demande et vous recontactera par téléphone ou email.</p>
            </div>

            <form action="{{ route('client.demandes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="clearDraft">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Chantier concerné <span class="text-rose-500">*</span></label>
                    <select name="chantier_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                        <option value="">-- Sélectionnez votre chantier --</option>
                        @foreach($chantiers as $c)
                            <option value="{{ $c->id }}" {{ old('chantier_id', $demandeOrigine?->chantier_id) == $c->id ? 'selected' : '' }}>{{ $c->nom }} ({{ $c->code_chantier }})</option>
                        @endforeach
                    </select>
                    @error('chantier_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Type d'intervention souhaité <span class="text-rose-500">*</span></label>
                        <select name="type_intervention_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                            <option value="">-- Type d'intervention --</option>
                            @foreach($typesIntervention as $t)
                                <option value="{{ $t->id }}" {{ old('type_intervention_id', $demandeOrigine?->type_intervention_id) == $t->id ? 'selected' : '' }}>{{ $t->nom }}</option>
                            @endforeach
                        </select>
                        @error('type_intervention_id') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Degré de priorité <span class="text-rose-500">*</span></label>
                        <select name="priorite" x-model="priorite" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                            <option value="Faible" {{ old('priorite', $demandeOrigine?->priorite) == 'Faible' ? 'selected' : '' }}>Faible – Pas d'urgence</option>
                            <option value="Normale" {{ old('priorite', $demandeOrigine?->priorite ?? 'Normale') == 'Normale' ? 'selected' : '' }}>Normale – Standard</option>
                            <option value="Haute" {{ old('priorite', $demandeOrigine?->priorite) == 'Haute' ? 'selected' : '' }}>Haute – Problème bloquant</option>
                            <option value="Urgente" {{ old('priorite', $demandeOrigine?->priorite) == 'Urgente' ? 'selected' : '' }}>Urgente – Arrêt d'activité</option>
                        </select>
                        <div class="mt-1.5">
                            @foreach(['Faible','Normale','Haute','Urgente'] as $p)
                                <span x-show="priorite === '{{ $p }}'" x-cloak><x-soft-badge status="{{ $p }}" /></span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Objet de la demande <span class="text-rose-500">*</span></label>
                    <input type="text" name="objet" x-model="objet" required value="{{ old('objet', $demandeOrigine?->objet) }}" placeholder="Ex: Maintenance préventive réseau, Panne armoire électrique..." class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                    @error('objet') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description détaillée des besoins ou symptômes <span class="text-rose-500">*</span></label>
                    <textarea name="description" x-model="description" required rows="5" placeholder="Décrivez précisément votre besoin, la nature de la panne ou les contraintes d'accès sur votre site..." class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm resize-none">{{ old('description', $demandeOrigine?->description) }}</textarea>
                    @error('description') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    <p class="text-[10px] text-slate-400 mt-1" x-show="draftRestored" x-cloak>
                        <i class="fas fa-clock-rotate-left"></i> Brouillon restauré automatiquement.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Créneau souhaité <span class="text-slate-400 font-normal">(optionnel)</span></label>
                        <input type="text" name="creneau_souhaite" value="{{ old('creneau_souhaite') }}" placeholder="Ex: Mardi matin, avant le 15/09..." class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Contact sur site <span class="text-slate-400 font-normal">(si différent du responsable)</span></label>
                        <input type="text" name="contact_sur_site" value="{{ old('contact_sur_site') }}" placeholder="Nom et téléphone" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Photos <span class="text-slate-400 font-normal">(panne, plan d'accès — optionnel)</span></label>
                    <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-600 file:text-xs file:font-bold hover:file:bg-slate-200">
                    @error('photos.*') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <a href="{{ route('client.demandes.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Soumettre la demande au Commercial</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function demandeForm() {
            const draftKey = 'demande_draft_{{ auth()->id() }}';
            return {
                priorite: @js(old('priorite', $demandeOrigine?->priorite ?? 'Normale')),
                objet: @js(old('objet', $demandeOrigine?->objet ?? '')),
                description: @js(old('description', $demandeOrigine?->description ?? '')),
                draftRestored: false,
                init() {
                    if (!this.objet && !this.description) {
                        try {
                            const saved = JSON.parse(localStorage.getItem(draftKey) || 'null');
                            if (saved) {
                                this.objet = saved.objet || '';
                                this.description = saved.description || '';
                                this.draftRestored = !!(saved.objet || saved.description);
                            }
                        } catch (e) {}
                    }
                    this.$watch('objet', () => this.saveDraft());
                    this.$watch('description', () => this.saveDraft());
                },
                saveDraft() {
                    try {
                        localStorage.setItem(draftKey, JSON.stringify({ objet: this.objet, description: this.description }));
                    } catch (e) {}
                },
                clearDraft() {
                    try { localStorage.removeItem(draftKey); } catch (e) {}
                },
            };
        }
    </script>
</x-client-layout>
