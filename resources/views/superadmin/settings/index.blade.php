<x-super-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">Paramètres & Configurations APIs</h1>
                <p class="text-sm text-slate-400 mt-1">Gestion centralisée des clés Google Maps, serveurs caméras/vidéos et paramètres système.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.settings.update') }}" class="space-y-8">
            @csrf

            <!-- Section 1: Google Maps API Configuration -->
            <div class="glass-card p-6 md:p-8 rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-800/80 pb-4">
                    <div class="p-2.5 bg-blue-500/10 text-blue-400 rounded-xl border border-blue-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white font-heading">Configuration API Google Maps & Géolocalisation</h2>
                        <p class="text-xs text-slate-400">Pour l'affichage des cartes interactives, le calcul d'itinéraires et le suivi GPS des techniciens.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Clé API Google Maps (API Key) *</label>
                        <input type="text" name="google_maps_key" value="{{ $config['google_maps_key'] }}" required class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs font-mono text-white focus:border-blue-500 focus:outline-none">
                        <p class="text-[11px] text-slate-500 mt-1">Nécessite la clé activée sur Google Cloud Console (Maps JavaScript API, Geocoding API).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Niveau de Zoom Carte par Défaut</label>
                        <select name="google_maps_zoom" class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs font-mono text-white focus:border-blue-500 focus:outline-none">
                            <option value="10" {{ $config['google_maps_zoom'] == 10 ? 'selected' : '' }}>10 (Vue Région / Régionale)</option>
                            <option value="12" {{ $config['google_maps_zoom'] == 12 ? 'selected' : '' }}>12 (Vue Ville / Standard)</option>
                            <option value="15" {{ $config['google_maps_zoom'] == 15 ? 'selected' : '' }}>15 (Vue Chantier / Zoomé)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Caméras & Stockage Vidéo Configuration -->
            <div class="glass-card p-6 md:p-8 rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-800/80 pb-4">
                    <div class="p-2.5 bg-rose-500/10 text-rose-400 rounded-xl border border-rose-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white font-heading">Paramètres Caméras, Vidéos & Captures Terrain</h2>
                        <p class="text-xs text-slate-400">Configuration des limites d'upload de fichiers médias et des passerelles de streaming RTSP.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Disque de Stockage Médias</label>
                        <select name="camera_storage_disk" class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs font-mono text-white focus:border-rose-500 focus:outline-none">
                            <option value="local" {{ $config['camera_storage_disk'] == 'local' ? 'selected' : '' }}>Stockage Local (storage/app/public)</option>
                            <option value="s3" {{ $config['camera_storage_disk'] == 's3' ? 'selected' : '' }}>Amazon S3 / Cloud Storage</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Taille Max Photo (Mo)</label>
                        <input type="number" name="max_photo_size_mb" value="{{ $config['max_photo_size_mb'] }}" min="1" max="50" class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs font-mono text-white focus:border-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Taille Max Vidéo (Mo)</label>
                        <input type="number" name="max_video_size_mb" value="{{ $config['max_video_size_mb'] }}" min="5" max="500" class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs font-mono text-white focus:border-rose-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Passerelle de Streaming Vidéo RTSP / Live Stream</label>
                    <input type="text" name="rtsp_streaming_gateway" value="{{ $config['rtsp_streaming_gateway'] }}" class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs font-mono text-white focus:border-rose-500 focus:outline-none">
                    <p class="text-[11px] text-slate-500 mt-1">Format URL RTSP pour la visualisation en direct des caméras de sécurité sur les chantiers.</p>
                </div>
            </div>

            <!-- Section 3: General System Settings & Maintenance -->
            <div class="glass-card p-6 md:p-8 rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-800/80 pb-4">
                    <div class="p-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white font-heading">Paramètres Système & Mode Maintenance</h2>
                        <p class="text-xs text-slate-400">Règles générales de l'application et état des accès.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 font-heading">Nom de la Plateforme</label>
                        <input type="text" name="app_name" value="{{ $config['app_name'] }}" required class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs text-white focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-950/90 border border-slate-800">
                        <div>
                            <span class="text-sm font-bold text-white block">Mode Maintenance</span>
                            <span class="text-xs text-slate-400">Verrouille temporairement les accès utilisateurs non-admin.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ $config['maintenance_mode'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-bold rounded-xl shadow-lg glow-red transition-all duration-200">
                    Enregistrer les Configurations APIs & Système
                </button>
            </div>
        </form>
    </div>
</x-super-admin-layout>
