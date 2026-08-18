<x-app-layout>
    <x-slot name="header">
        Paramètres du Système & Configuration
    </x-slot>

    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-indigo-300 text-xs font-semibold mb-2">
                    ⚙️ Centre de Configuration Global
                </span>
                <h3 class="text-2xl font-black text-white tracking-tight">Paramètres Généraux de l'Entreprise</h3>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Personnalisez l'identité de l'entreprise, les fréquences de rafraîchissement GPS des techniciens, les paramètres d'impression des rapports et les devises.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {!! session('success') !!}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1 : Identité de l'entreprise & En-tête / Pied-de-page PDF -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-800/60">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wider">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Identité de l'Entreprise, Logo & Mentions Légales PDF
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="company_name" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Raison Sociale / Nom Société</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings['company_name']) }}" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_tagline" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Slogan / Spécialité (Header PDF)</label>
                        <input type="text" name="company_tagline" id="company_tagline" value="{{ old('company_tagline', $settings['company_tagline']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_email" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Email Officiel</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings['company_email']) }}" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_website" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Site Web Officiel</label>
                        <input type="text" name="company_website" id="company_website" value="{{ old('company_website', $settings['company_website']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_phone" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Téléphone Siège / Support</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_fax" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Numéro de Fax (Pied-de-page PDF)</label>
                        <input type="text" name="company_fax" id="company_fax" value="{{ old('company_fax', $settings['company_fax']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_ice" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Numéro ICE (Identifiant Commun de l'Entreprise)</label>
                        <input type="text" name="company_ice" id="company_ice" value="{{ old('company_ice', $settings['company_ice']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="company_rc" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Registre du Commerce (RC)</label>
                        <input type="text" name="company_rc" id="company_rc" value="{{ old('company_rc', $settings['company_rc']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Adresse Siège Social (Pied-de-page PDF)</label>
                        <textarea name="company_address" id="company_address" rows="2" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2 focus:ring-2 focus:ring-indigo-500">{{ old('company_address', $settings['company_address']) }}</textarea>
                    </div>

                    <div>
                        <label for="company_logo" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Logo Officiel de la Société (Header PDF)</label>
                        <input type="file" name="company_logo" id="company_logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @if(!empty($settings['company_logo']))
                            <div class="mt-2 flex items-center gap-2">
                                <span class="text-[10px] text-emerald-600 font-bold">✓ Logo enregistré :</span>
                                <img src="{{ Storage::url($settings['company_logo']) }}" class="h-8 object-contain rounded border border-slate-200 p-1">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="company_color" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Couleur Principale Charte (PDF & Interface)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="company_color" id="company_color" value="{{ old('company_color', $settings['company_color'] ?? '#4338CA') }}" class="w-10 h-10 rounded-lg border border-slate-200 p-1 cursor-pointer">
                            <span class="text-xs font-mono font-semibold text-slate-600 dark:text-slate-300">{{ $settings['company_color'] ?? '#4338CA' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2 : Configuration Télémétrie GPS & Notifications -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-800/60">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wider">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Paramètres Télémétrie GPS & Devise
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="currency" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Devise Principale du Système</label>
                        <select name="currency" id="currency" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                            <option value="MAD" {{ $settings['currency'] === 'MAD' ? 'selected' : '' }}>Dirham Marocain (MAD)</option>
                            <option value="EUR" {{ $settings['currency'] === 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                            <option value="USD" {{ $settings['currency'] === 'USD' ? 'selected' : '' }}>Dollar ($)</option>
                        </select>
                    </div>

                    <div>
                        <label for="gps_interval" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Intervalle d'Envoi GPS (secondes)</label>
                        <input type="number" min="5" max="300" name="gps_interval" id="gps_interval" value="{{ old('gps_interval', $settings['gps_interval']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[10px] text-gray-400 mt-1">Fréquence de capture automatique de la géolocalisation du technicien en cours d'intervention.</p>
                    </div>

                    <div class="space-y-4 pt-1 md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="auto_validate_gps" value="1" {{ $settings['auto_validate_gps'] ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Validation automatique de présence si le technicien est dans le rayon GPS du chantier</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" {{ $settings['email_notifications'] ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Activer les alertes email lors de la clôture des rapports d'interventions</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-teal-500 hover:from-indigo-700 hover:to-teal-600 text-white font-bold rounded-xl shadow-lg transition text-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer les Paramètres de la Société
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
