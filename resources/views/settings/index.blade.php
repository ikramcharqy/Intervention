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

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf

            <!-- Section 1 : Identité de l'entreprise & Facturation -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-800/60">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wider">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Identité de l'Entreprise & En-tête des Rapports PDF
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="company_name" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Raison Sociale / Nom</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings['company_name']) }}" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="company_email" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Email Officiel</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings['company_email']) }}" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="company_phone" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Téléphone Support / Contact</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="currency" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Devise Principale du Système</label>
                        <select name="currency" id="currency" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                            <option value="MAD" {{ $settings['currency'] === 'MAD' ? 'selected' : '' }}>Dirham Marocain (MAD)</option>
                            <option value="EUR" {{ $settings['currency'] === 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                            <option value="USD" {{ $settings['currency'] === 'USD' ? 'selected' : '' }}>Dollar ($)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Adresse Siège Social</label>
                        <textarea name="company_address" id="company_address" rows="2" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2 focus:ring-2 focus:ring-indigo-500">{{ old('company_address', $settings['company_address']) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2 : Configuration Télémétrie GPS & Notifications -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-800/60">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wider">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Paramètres Télémétrie GPS & Alertes
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="gps_interval" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Intervalle d'Envoi GPS (secondes)</label>
                        <input type="number" min="5" max="300" name="gps_interval" id="gps_interval" value="{{ old('gps_interval', $settings['gps_interval']) }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs py-2.5 focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[10px] text-gray-400 mt-1">Fréquence de capture automatique de la géolocalisation du technicien en cours d'intervention.</p>
                    </div>

                    <div class="space-y-4 pt-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="auto_validate_gps" value="1" {{ $settings['auto_validate_gps'] ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Validation automatique de présence si dans le rayon GPS du chantier</span>
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
                    Enregistrer les Paramètres
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
