<x-super-admin-layout>
    <div class="space-y-6" x-data="platformSettings()" x-init="init()" @beforeunload.window="confirmLeave($event)">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Paramètres de la Plateforme</h1>
            <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Identité de l'entreprise, télémétrie GPS et conditions de facturation — chaque section s'enregistre indépendamment.</p>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-[6px] bg-[#E3FBF0] dark:bg-emerald-500/10 border border-[#C4F8E2] dark:border-emerald-500/30 text-[#06A561] dark:text-emerald-400 text-xs font-semibold flex items-center gap-3">
                <x-icon name="circle-check" class="w-4 h-4 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Onglets -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-1.5 inline-flex gap-1">
            <button type="button" @click="switchTab('branding')" :class="tab === 'branding' ? 'bg-[#1E5EFF] text-white' : 'text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800'" class="px-4 py-2 rounded-[4px] text-xs font-semibold transition">Identité & Branding</button>
            <button type="button" @click="switchTab('gps')" :class="tab === 'gps' ? 'bg-[#1E5EFF] text-white' : 'text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800'" class="px-4 py-2 rounded-[4px] text-xs font-semibold transition">Opérations & Télémétrie GPS</button>
            <button type="button" @click="switchTab('billing')" :class="tab === 'billing' ? 'bg-[#1E5EFF] text-white' : 'text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800'" class="px-4 py-2 rounded-[4px] text-xs font-semibold transition">Facturation & Conditions Légales</button>
        </div>

        <!-- ================= IDENTITÉ & BRANDING ================= -->
        <div x-show="tab === 'branding'" x-cloak class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <form method="POST" action="{{ route('settings.updateBranding') }}" enctype="multipart/form-data" @input="dirty.branding = true" @change="dirty.branding = true" @submit="onSubmit('branding')">
                @csrf
                <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Identité de l'Entreprise, Logo & Mentions Légales PDF</h3>
                    <button type="button" @click="openPreview()" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-[#1E5EFF] bg-[#EAF0FF] dark:bg-blue-500/10 rounded-[4px] hover:bg-[#D9E4FF] dark:hover:bg-blue-500/20 transition">
                        <x-icon name="document-chart" class="w-3.5 h-3.5" /> Aperçu du PDF
                    </button>
                </div>
                <div class="p-7 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Raison Sociale *</label>
                        <input type="text" name="company_name" x-model="branding.company_name" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        @error('company_name')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Slogan (Header PDF)</label>
                        <input type="text" name="company_tagline" x-model="branding.company_tagline" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Email Officiel *</label>
                        <input type="email" name="company_email" x-model="branding.company_email" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        @error('company_email')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Site Web</label>
                        <input type="text" name="company_website" x-model="branding.company_website" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Téléphone</label>
                        <input type="text" name="company_phone" x-model="branding.company_phone" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Fax</label>
                        <input type="text" name="company_fax" x-model="branding.company_fax" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">ICE (15 chiffres)</label>
                        <input type="text" name="company_ice" x-model="branding.company_ice" maxlength="15" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        @error('company_ice')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Registre du Commerce (RC)</label>
                        <input type="text" name="company_rc" x-model="branding.company_rc" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        @error('company_rc')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Adresse Siège Social</label>
                        <textarea name="company_address" x-model="branding.company_address" rows="2" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none"></textarea>
                    </div>

                    <!-- Upload logo custom -->
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Logo Officiel</label>
                        <label class="flex items-center gap-3 p-3 rounded-[4px] border-2 border-dashed border-[#D7DBEC] dark:border-slate-700 hover:border-[#1E5EFF] cursor-pointer transition">
                            <div class="w-12 h-12 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="logoPreviewUrl">
                                    <img :src="logoPreviewUrl" class="w-full h-full object-contain">
                                </template>
                                <template x-if="!logoPreviewUrl">
                                    <x-icon name="document-chart" class="w-5 h-5 text-[#A1A7C4]" />
                                </template>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-semibold text-[#1E5EFF]" x-text="logoFileName || 'Choisir un fichier…'"></span>
                                <span class="block text-[10px] text-[#A1A7C4]">JPEG, PNG, SVG, WebP — 2 Mo max</span>
                            </div>
                            <input type="file" name="company_logo" accept="image/*" class="hidden" @change="onLogoChange($event)">
                        </label>
                        @error('company_logo')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Couleur de charte réactive -->
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Couleur Principale (PDF & Interface)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="company_color" x-model="branding.company_color" class="w-11 h-11 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 cursor-pointer p-1 bg-white dark:bg-slate-800">
                            <input type="text" x-model="branding.company_color" maxlength="7" class="w-28 px-3 py-2 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs font-mono text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                            <span class="w-8 h-8 rounded-full border border-[#E6E9F4] dark:border-slate-700 shrink-0" :style="`background-color: ${branding.company_color}`"></span>
                        </div>
                        @error('company_color')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="px-7 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700 flex justify-end">
                    <button type="submit" :disabled="submitting.branding" :class="submitting.branding ? 'opacity-60 cursor-not-allowed' : 'hover:bg-[#174ecc]'" class="px-5 py-2.5 bg-[#1E5EFF] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)]" x-text="submitting.branding ? 'Enregistrement…' : 'Enregistrer Identité & Branding'"></button>
                </div>
            </form>
        </div>

        <!-- ================= OPÉRATIONS & TÉLÉMÉTRIE GPS ================= -->
        <div x-show="tab === 'gps'" x-cloak class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <form method="POST" action="{{ route('settings.updateGps') }}" @input="dirty.gps = true" @change="dirty.gps = true" @submit="onSubmit('gps')">
                @csrf
                <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                    <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Paramètres Télémétrie GPS & Validation de Présence</h3>
                </div>
                <div class="p-7 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Intervalle d'Envoi GPS (secondes) *</label>
                        <input type="number" min="10" max="300" name="gps_interval" x-model="gps.gps_interval" required class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1.5">Entre 10 et 300s. Trop bas = décharge la batterie du technicien plus vite ; trop haut = suivi moins précis.</p>
                        @error('gps_interval')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer mt-1">
                            <input type="checkbox" name="auto_validate_gps" value="1" x-model="gps.auto_validate_gps" class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#1E5EFF] focus:ring-[#1E5EFF]">
                            <span class="text-xs font-semibold text-[#131523] dark:text-slate-200">Validation automatique de présence par rayon GPS</span>
                        </label>
                    </div>

                    <div x-show="gps.auto_validate_gps" x-cloak class="md:col-span-2 p-4 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800">
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Rayon de Validation de Présence (mètres) *</label>
                        <input type="number" min="20" max="500" name="gps_validation_radius" x-model="gps.gps_validation_radius" class="w-full max-w-xs px-4 py-2.5 bg-white dark:bg-slate-900 border border-[#E6E9F4] dark:border-slate-700 rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1.5">Entre 20 et 500m (défaut 100m). Trop petit = faux négatifs fréquents (technicien rejeté à tort) ; trop grand = validation peu fiable (présence non garantie).</p>
                        @error('gps_validation_radius')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" x-model="gps.email_notifications" class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#1E5EFF] focus:ring-[#1E5EFF]">
                            <span class="text-xs font-semibold text-[#131523] dark:text-slate-200">Alertes email à la clôture des rapports d'intervention</span>
                        </label>
                    </div>
                </div>
                <div class="px-7 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700 flex justify-end">
                    <button type="submit" :disabled="submitting.gps" :class="submitting.gps ? 'opacity-60 cursor-not-allowed' : 'hover:bg-[#174ecc]'" class="px-5 py-2.5 bg-[#1E5EFF] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)]" x-text="submitting.gps ? 'Enregistrement…' : 'Enregistrer les Paramètres GPS'"></button>
                </div>
            </form>
        </div>

        <!-- ================= FACTURATION & CONDITIONS LÉGALES ================= -->
        <div x-show="tab === 'billing'" x-cloak class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <form method="POST" action="{{ route('settings.updateBilling') }}" @input="dirty.billing = true" @change="dirty.billing = true" @submit="onSubmit('billing')">
                @csrf
                <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                    <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Conditions de Paiement & Mentions Légales par Défaut (Devis PDF)</h3>
                </div>
                <div class="p-7 grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Devise Principale</label>
                        <select name="currency" x-model="billing.currency" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                            <option value="MAD">Dirham Marocain (MAD)</option>
                            <option value="EUR">Euro (€)</option>
                            <option value="USD">Dollar ($)</option>
                        </select>
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1.5">Change uniquement le symbole affiché sur les documents — l'application ne gère pas la conversion de montants entre devises ni les taux de change.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-2">Modes de Paiement Acceptés *</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach($paymentModeOptions as $key => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="payment_mode[]" value="{{ $key }}" x-model="billing.payment_mode" class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#1E5EFF] focus:ring-[#1E5EFF]">
                                    <span class="text-xs font-medium text-[#131523] dark:text-slate-200">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_mode')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Délai de Paiement (jours)</label>
                        <input type="number" min="0" max="365" name="payment_delay_days" x-model="billing.payment_delay_days" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Acompte Demandé (%)</label>
                        <input type="number" min="0" max="100" step="0.01" name="payment_deposit_percent" x-model="billing.payment_deposit_percent" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Mentions Légales (Pied de Page PDF)</label>
                        <textarea name="legal_mentions" x-model="billing.legal_mentions" rows="3" class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none"></textarea>
                    </div>
                </div>
                <div class="px-7 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700 flex justify-end">
                    <button type="submit" :disabled="submitting.billing" :class="submitting.billing ? 'opacity-60 cursor-not-allowed' : 'hover:bg-[#174ecc]'" class="px-5 py-2.5 bg-[#1E5EFF] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)]" x-text="submitting.billing ? 'Enregistrement…' : 'Enregistrer Facturation & Conditions'"></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function platformSettings() {
            return {
                tab: 'branding',
                dirty: { branding: false, gps: false, billing: false },
                // Étape 5 : empêche une double soumission (double-clic, ou clic répété
                // pendant que la requête précédente est encore en vol) — le bouton se
                // désactive dès le premier submit et jusqu'à la navigation qui suit.
                submitting: { branding: false, gps: false, billing: false },
                onSubmit(section) {
                    this.dirty[section] = false;
                    this.submitting[section] = true;
                },
                logoFileName: null,
                logoPreviewUrl: {!! $branding['company_logo'] ? "'" . Storage::url($branding['company_logo']) . "'" : 'null' !!},
                branding: @json($branding),
                gps: @json($gps),
                billing: @json($billing),

                init() {},

                switchTab(next) {
                    if (this.dirty[this.tab] && !confirm('Des modifications non enregistrées seront perdues sur cette section. Continuer ?')) {
                        return;
                    }
                    this.tab = next;
                },

                confirmLeave(e) {
                    if (Object.values(this.dirty).some(v => v)) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                },

                onLogoChange(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.logoFileName = file.name;
                    this.logoPreviewUrl = URL.createObjectURL(file);
                    this.dirty.branding = true;
                },

                openPreview() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("settings.previewPdf") }}';
                    form.target = '_blank';

                    const fields = { _token: '{{ csrf_token() }}', ...this.branding };
                    for (const [key, value] of Object.entries(fields)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value ?? '';
                        form.appendChild(input);
                    }
                    document.body.appendChild(form);
                    form.submit();
                    form.remove();
                },
            };
        }
    </script>
</x-super-admin-layout>
