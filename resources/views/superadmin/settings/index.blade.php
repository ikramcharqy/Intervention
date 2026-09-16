<x-super-admin-layout>
    <div class="space-y-8" x-data="{
            confirmMaintenance: false,
            maintenanceEnabled: {{ $config['maintenance_mode'] ? 'true' : 'false' }},
            onMaintenanceToggle(next) {
                // Activation seulement : la désactivation ne nécessite pas de confirmation
                // renforcée (elle lève un blocage, elle n'en impose pas un). L'état visuel
                // de la case est piloté à 100% par :checked="maintenanceEnabled" — le
                // toggle natif est intercepté (@click.prevent) pour ne jamais s'activer
                // avant confirmation explicite.
                if (next && !this.maintenanceEnabled) {
                    this.confirmMaintenance = true;
                    return;
                }
                this.maintenanceEnabled = next;
            }
         }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Paramètres & Configurations APIs</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Gestion centralisée des clés Google Maps, serveurs caméras/vidéos et paramètres système.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.settings.update') }}" class="space-y-8">
            @csrf

            <!-- Section 1: Google Maps API Configuration -->
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 p-6 md:p-8 space-y-6">
                <div class="flex items-center gap-3 border-b border-[#E6E9F4] dark:border-slate-800 pb-4">
                    <div class="p-2.5 bg-[#EAF0FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400 rounded-xl border border-[#B8CFFF] dark:border-blue-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#131523] dark:text-slate-100">Configuration API Google Maps & Géolocalisation</h2>
                        <p class="text-xs text-[#5A607F] dark:text-slate-400">Pour l'affichage des cartes interactives, le calcul d'itinéraires et le suivi GPS des techniciens.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div x-data="{ revealed: false, value: '{{ $config['google_maps_key_masked'] }}' }">
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Clé API Google Maps (API Key)</label>
                        <div class="flex gap-2">
                            <input type="text" readonly :value="value" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:outline-none">
                            <button type="button"
                                @click="
                                    if (revealed) { value = '{{ $config['google_maps_key_masked'] }}'; revealed = false; return; }
                                    fetch('{{ route('superadmin.settings.revealKey') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                                        .then(r => (r.ok && r.headers.get('content-type')?.includes('application/json')) ? r.json() : Promise.reject(r))
                                        .then(data => { value = data.key; revealed = true; })
                                        .catch(() => { window.location.href = '{{ route('password.confirm') }}'; })
                                "
                                class="px-4 py-3 bg-[#F5F6FA] dark:bg-slate-900 border border-[#D7DBEC] dark:border-slate-800 rounded-[4px] text-xs font-semibold text-[#5A607F] dark:text-slate-300 hover:text-[#131523] dark:hover:text-white shrink-0">
                                <span x-text="revealed ? 'Masquer' : 'Révéler'"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">
                            Masquée par défaut — la révélation nécessite une re-confirmation du mot de passe et est journalisée.
                            @if($config['google_maps_key_rotated_at'])
                                Dernière rotation : {{ \Illuminate\Support\Carbon::parse($config['google_maps_key_rotated_at'])->format('d/m/Y H:i') }}.
                            @else
                                Aucune rotation enregistrée depuis la mise en place de ce suivi.
                            @endif
                        </p>
                        <label class="block text-[11px] font-bold text-[#5A607F] dark:text-slate-400 uppercase tracking-wider mt-3 mb-1">Nouvelle clé (rotation — laisser vide pour conserver l'actuelle)</label>
                        <input type="text" name="google_maps_key_new" placeholder="Laisser vide pour ne pas changer la clé" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#1E5EFF] focus:outline-none">
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">Nécessite la clé activée sur Google Cloud Console (Maps JavaScript API, Geocoding API).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Niveau de Zoom Carte par Défaut</label>
                        <select name="google_maps_zoom" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#1E5EFF] focus:outline-none">
                            <option value="10" {{ $config['google_maps_zoom'] == 10 ? 'selected' : '' }}>10 (Vue Région / Régionale)</option>
                            <option value="12" {{ $config['google_maps_zoom'] == 12 ? 'selected' : '' }}>12 (Vue Ville / Standard)</option>
                            <option value="15" {{ $config['google_maps_zoom'] == 15 ? 'selected' : '' }}>15 (Vue Chantier / Zoomé)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Caméras & Stockage Vidéo Configuration -->
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 p-6 md:p-8 space-y-6">
                <div class="flex items-center gap-3 border-b border-[#E6E9F4] dark:border-slate-800 pb-4">
                    <div class="p-2.5 bg-[#FDE3E6] dark:bg-rose-500/10 text-[#F0142F] dark:text-rose-400 rounded-xl border border-[#F8C4CA] dark:border-rose-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#131523] dark:text-slate-100">Paramètres Caméras, Vidéos & Captures Terrain</h2>
                        <p class="text-xs text-[#5A607F] dark:text-slate-400">Configuration des limites d'upload de fichiers médias et des passerelles de streaming RTSP.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Disque de Stockage Médias</label>
                        {{-- Étape 7 : un seul disque est réellement implémenté (SDK S3 non
                             installé, aucun code applicatif ne lit ce paramètre) — champ en
                             lecture seule plutôt qu'un select suggérant un choix qui n'a
                             aucun effet réel. --}}
                        <div class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#5A607F] dark:text-slate-400 flex items-center justify-between">
                            <span>Stockage Local (storage/app/public)</span>
                            <span class="text-[10px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase">Seule option active</span>
                        </div>
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">Amazon S3 n'est pas encore implémenté dans le code applicatif — ce champ redeviendra modifiable une fois ce support ajouté.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Taille Max Photo (Mo)</label>
                        <input type="number" name="max_photo_size_mb" value="{{ $config['max_photo_size_mb'] }}" min="1" max="50" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#F0142F] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Taille Max Vidéo (Mo)</label>
                        <input type="number" name="max_video_size_mb" value="{{ $config['max_video_size_mb'] }}" min="5" max="500" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#F0142F] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Extensions Photo Autorisées</label>
                        <input type="text" name="allowed_photo_formats" value="{{ $config['allowed_photo_formats'] }}" placeholder="jpg,jpeg,png,webp" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#F0142F] focus:outline-none">
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">Séparées par des virgules, sans point ni espace. Appliqué réellement à la validation des uploads (rapports d'intervention, formulaires).</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Extensions Vidéo Autorisées</label>
                        <input type="text" name="allowed_video_formats" value="{{ $config['allowed_video_formats'] }}" placeholder="mp4,mov,avi,webm" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#F0142F] focus:outline-none">
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">Séparées par des virgules, sans point ni espace. Appliqué réellement à la validation des uploads.</p>
                    </div>
                </div>

                <div x-data="{ revealed: false, value: '{{ $config['rtsp_streaming_gateway_masked'] }}' }">
                    <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Passerelle de Streaming Vidéo RTSP / Live Stream</label>
                    <div class="flex gap-2">
                        <input type="text" readonly :value="value" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:outline-none">
                        <button type="button"
                            @click="
                                if (revealed) { value = '{{ $config['rtsp_streaming_gateway_masked'] }}'; revealed = false; return; }
                                fetch('{{ route('superadmin.settings.revealRtsp') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                                    .then(r => (r.ok && r.headers.get('content-type')?.includes('application/json')) ? r.json() : Promise.reject(r))
                                    .then(data => { value = data.url; revealed = true; })
                                    .catch(() => { window.location.href = '{{ route('password.confirm') }}'; })
                            "
                            class="px-4 py-3 bg-[#F5F6FA] dark:bg-slate-900 border border-[#D7DBEC] dark:border-slate-800 rounded-[4px] text-xs font-semibold text-[#5A607F] dark:text-slate-300 hover:text-[#131523] dark:hover:text-white shrink-0">
                            <span x-text="revealed ? 'Masquer' : 'Révéler'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 mt-1">
                        Masquée par défaut — un usage réel futur peut y intégrer des identifiants (rtsp://user:pass@host/...). La révélation nécessite une re-confirmation du mot de passe et est journalisée.
                    </p>
                    <label class="block text-[11px] font-bold text-[#5A607F] dark:text-slate-400 uppercase tracking-wider mt-3 mb-1">Nouvelle URL (laisser vide pour conserver l'actuelle)</label>
                    <input type="text" name="rtsp_streaming_gateway_new" placeholder="rtsp://stream.technitrack.ma/live" class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs font-mono text-[#131523] dark:text-white focus:border-[#F0142F] focus:outline-none">
                </div>
            </div>

            <!-- Section 3: General System Settings & Maintenance -->
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 p-6 md:p-8 space-y-6">
                <div class="flex items-center gap-3 border-b border-[#E6E9F4] dark:border-slate-800 pb-4">
                    <div class="p-2.5 bg-[#E3FBF0] dark:bg-emerald-500/10 text-[#06A561] dark:text-emerald-400 rounded-xl border border-emerald-200 dark:border-emerald-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#131523] dark:text-slate-100">Paramètres Système & Mode Maintenance</h2>
                        <p class="text-xs text-[#5A607F] dark:text-slate-400">Règles générales de l'application et état des accès.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Nom de la Plateforme</label>
                        <input type="text" name="app_name" value="{{ $config['app_name'] }}" required class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs text-[#131523] dark:text-white focus:border-[#06A561] focus:outline-none">
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-950 border border-[#E6E9F4] dark:border-slate-800">
                        <div>
                            <span class="text-sm font-bold text-[#131523] dark:text-white block">Mode Maintenance</span>
                            <span class="text-xs text-[#5A607F] dark:text-slate-400">Verrouille temporairement les accès utilisateurs non-admin. Le Super Admin garde toujours accès.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-4">
                            <input type="checkbox" :checked="maintenanceEnabled" @click.prevent="onMaintenanceToggle(!maintenanceEnabled)" class="sr-only peer">
                            <div class="w-11 h-6 bg-[#D7DBEC] dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                        <!-- Champ réel envoyé au serveur, piloté par l'état Alpine (permet
                             d'intercepter l'activation avec une modale de confirmation
                             avant que la case ne soit réellement cochée). -->
                        <input type="hidden" name="maintenance_mode" :value="maintenanceEnabled ? '1' : '0'">
                    </div>
                </div>

                <div x-show="maintenanceEnabled" x-cloak>
                    <label class="block text-xs font-bold text-[#5A607F] dark:text-slate-300 uppercase tracking-wider mb-2">Message affiché aux utilisateurs pendant la maintenance</label>
                    <textarea name="maintenance_message" rows="2" placeholder="Ex: Maintenance planifiée jusqu'à 18h — merci de votre patience." class="w-full px-4 py-3 bg-[#F5F6FA] dark:bg-slate-950 border border-transparent dark:border-slate-800 rounded-[4px] text-xs text-[#131523] dark:text-white focus:border-[#06A561] focus:outline-none">{{ $config['maintenance_message'] }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3 bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold rounded-[4px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] transition">
                    Enregistrer les Configurations APIs & Système
                </button>
            </div>
        </form>

        <!-- Étape 5.1 : confirmation explicite avant activation du Mode Maintenance,
             rappelant l'impact réel (verrouillage de tous les comptes non-admin). -->
        <div x-show="confirmMaintenance" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmMaintenance = false">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-[#F0142F] dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Activer le Mode Maintenance ?</h3>
                </div>
                <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed">
                    Tous les comptes non-admin (Commercial, Technicien, Client) seront immédiatement bloqués et verront le message de maintenance à leur prochaine requête. Seul le compte Super Admin conserve l'accès pour désactiver ce mode. Cette activation sera journalisée. Le changement ne sera effectif qu'après avoir cliqué sur "Enregistrer les Configurations".
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="confirmMaintenance = false" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                    <button type="button" @click="maintenanceEnabled = true; confirmMaintenance = false" class="px-4 py-2 rounded-[4px] bg-[#F0142F] hover:bg-[#c9102a] text-white text-xs font-bold">Confirmer l'activation</button>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
