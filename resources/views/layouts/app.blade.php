<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'InterventionPRO') }} — Platforme Professionnelle</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

        <!-- Leaflet CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .font-mono { font-family: 'JetBrains Mono', monospace; }
            .glass-navbar {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            .dark .glass-navbar {
                background: rgba(15, 23, 42, 0.85);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased h-full text-slate-800 dark:text-slate-200">
        <div class="min-h-screen flex bg-slate-50 dark:bg-slate-950" x-data="{ sidebarOpen: false }">

            <!-- OVERLAY MOBILE -->
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 md:hidden"></div>

            <!-- SIDEBAR LATÉRALE PROFESSIONNELLE METRONIC STYLE -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 text-slate-300 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:z-auto shadow-2xl border-r border-slate-800/80"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                
                <!-- BRAND / LOGO SECTION -->
                <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800/80 bg-slate-950">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center text-white font-extrabold shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-sm text-white tracking-tight group-hover:text-indigo-400 transition">Intervention<span class="text-indigo-400">PRO</span></span>
                            <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase">Gestion Terrain Live</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- NAVIGATION SIDEBAR MENU -->
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-thin scrollbar-thumb-slate-800">
                    
                    <div class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-2">Menu Principal</div>

                    <!-- Tableau de Bord -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Tableau de bord</span>
                    </a>

                    <!-- Section Chantiers -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Conducteur', 'Client']))
                        <a href="{{ auth()->user()->hasRole('Client') ? route('client.chantiers.index') : route('chantiers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('chantiers.*') || request()->routeIs('client.chantiers.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ auth()->user()->hasRole('Client') ? 'Mes Chantiers' : 'Gestion des Chantiers' }}</span>
                        </a>
                    @endif

                    <!-- Section Clients (Admin/Commercial) -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                        <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('clients.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Portefeuille Clients</span>
                        </a>
                    @endif

                    <!-- CRM Commercial (Commercial / Admin) -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                        <div class="space-y-1" x-data="{ open: {{ request()->routeIs('prospects.*') || request()->routeIs('commercial.devis.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition text-slate-400 hover:bg-slate-900 hover:text-white text-left">
                                <span class="flex items-center gap-3">
                                    <svg class="h-4 w-4 shrink-0 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Module Ventes & CRM
                                </span>
                                <svg class="h-3.5 w-3.5 transform transition-transform" :class="open ? 'rotate-90 text-indigo-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="pl-9 space-y-1" x-show="open" x-collapse>
                                <a href="{{ route('prospects.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('prospects.*') ? 'text-indigo-400 font-bold' : '' }}">
                                    • Prospects & Leads
                                </a>
                                <a href="{{ route('commercial.devis.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('commercial.devis.*') ? 'text-indigo-400 font-bold' : '' }}">
                                    • Devis & Offres
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Demandes d'interventions (Pour Client) -->
                    @if(auth()->user()->hasRole('Client'))
                        <a href="{{ route('demande-interventions.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition bg-emerald-600/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-600 hover:text-white">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Créer une Demande</span>
                        </a>
                    @endif

                    <!-- Demandes Clients (Commercial / Admin) -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                        @php
                            $countDemandesClient = \App\Models\DemandeIntervention::where('statut', 'En attente')->count();
                        @endphp
                        <a href="{{ route('demande-interventions.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('demande-interventions.*') ? 'bg-cyan-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Demandes Clients</span>
                            </div>
                            @if($countDemandesClient > 0)
                                <span class="bg-cyan-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $countDemandesClient }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Interventions à Planifier (Admin / Super Admin / Conducteur) -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Conducteur']))
                        @php
                            $countAPlanifier = \App\Models\Intervention::whereIn('statut', [\App\Models\Intervention::STATUT_PLANIFIEE, \App\Models\Intervention::STATUT_DEMANDE])->whereNull('technicien_id')->count();
                        @endphp
                        <a href="{{ route('interventions.a-planifier') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('interventions.a-planifier') ? 'bg-amber-600 text-white shadow-lg font-extrabold' : 'text-amber-400 hover:bg-slate-900 hover:text-amber-300' }}">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Interventions à Planifier</span>
                            </div>
                            @if($countAPlanifier > 0)
                                <span class="bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $countAPlanifier }}</span>
                            @endif
                        </a>

                        @php
                            $countReaffectation = \App\Models\DemandeReaffectation::where('statut', 'En attente')->count();
                        @endphp
                        <a href="{{ route('demandes-reaffectation.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('demandes-reaffectation.*') ? 'bg-rose-600 text-white shadow-lg font-extrabold' : 'text-rose-400 hover:bg-slate-900 hover:text-rose-300' }}">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Refus Techniciens</span>
                            </div>
                            @if($countReaffectation > 0)
                                <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse">{{ $countReaffectation }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Toutes les Interventions -->
                    <a href="{{ auth()->user()->hasRole('Client') ? route('client.interventions.index') : route('interventions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('interventions.index') || request()->routeIs('client.interventions.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>{{ auth()->user()->hasRole('Client') ? 'Mes Interventions' : 'Toutes les Interventions' }}</span>
                    </a>

                    <!-- Formulaires Dynamiques (Admin & Technicien) -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('formulaires.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('formulaires.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Formulaires Dynamiques</span>
                        </a>
                    @endif

                    <!-- Rapports d'intervention -->
                    <a href="{{ auth()->user()->hasRole('Client') ? route('client.rapports.index') : route('rapports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('rapports.*') || request()->routeIs('client.rapports.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ auth()->user()->hasRole('Client') ? 'Rapports Validés' : 'Centre des Rapports' }}</span>
                    </a>

                    <!-- Documents & Contrats (Pour Client) -->
                    @if(auth()->user()->hasRole('Client'))
                        <a href="{{ route('client.documents.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('client.documents.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Documents & Contrats</span>
                        </a>
                    @endif

                    <!-- Tracking GPS Live (Strictement Masqué pour les Clients) -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('gps.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('gps.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Tracking GPS Live</span>
                        </a>
                    @endif

                    <!-- Stock & Matériaux (Admin / Technicien) -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('materiaux.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('materiaux.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Stock & Consommables</span>
                        </a>
                    @endif

                    <!-- Statistiques & Paramètres Système (Admin / Commercial) -->
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Commercial'))
                        <div class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mt-4 mb-2">Administration</div>

                        <a href="{{ route('statistiques.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('statistiques.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            <span>Statistiques & KPI</span>
                        </a>

                        @if(auth()->user()->hasRole('Admin'))
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white shadow-lg font-extrabold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Paramètres Système</span>
                            </a>
                        @endif
                    @endif
                </nav>
            </aside>

            <!-- CONTENU PRINCIPAL ET NAVBAR SUPÉRIEURE GLASSMORPHIC -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950">
                
                <!-- TOP NAVBAR CRÉATIVE ET SÉCURISÉE -->
                <header class="h-16 flex items-center justify-between px-6 glass-navbar border-b border-slate-200/80 dark:border-slate-800/80 shadow-sm sticky top-0 z-40">
                    <div class="flex items-center gap-4">
                        <button class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none" @click="sidebarOpen = true">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Breadcrumb Dynamic Header -->
                        <div class="hidden sm:flex flex-col">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 flex items-center gap-1.5 uppercase tracking-wider">
                                <span>InterventionPRO</span>
                                <svg class="h-3 w-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                <span class="text-indigo-600 dark:text-indigo-400 font-extrabold capitalize">{{ Request::segment(1) ?? 'Dashboard' }}</span>
                            </span>
                            @isset($header)
                                <h1 class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight mt-0.5">
                                    {{ $header }}
                                </h1>
                            @else
                                <h1 class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight mt-0.5">
                                    Tableau de Bord
                                </h1>
                            @endisset
                        </div>
                    </div>

                    <!-- Actions Droite Top Navbar -->
                    <div class="flex items-center gap-3">
                        <!-- Mode Rôle & Live Status Badge -->
                        <div class="hidden lg:flex items-center gap-2 px-3 py-1 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-full text-indigo-700 dark:text-indigo-300 text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>Session : <strong class="uppercase text-indigo-600 dark:text-indigo-400">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</strong></span>
                        </div>

                        <!-- Dark Mode Toggle Button -->
                        <button @click="document.documentElement.classList.toggle('dark')" class="p-2 text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Profile Dropdown (Metronic 8 Style) -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition focus:outline-none">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white font-extrabold text-xs flex items-center justify-center shadow-md">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden md:flex flex-col text-left">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">{{ Auth::user()->email }}</span>
                                </div>
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu items -->
                            <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-2 z-50">
                                
                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold uppercase mt-0.5">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    Mon Profil & Sécurité
                                </a>

                                @if(auth()->user()->hasRole('Admin'))
                                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                                        Paramètres Système
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-left">
                                        <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- PAGE CONTENT INJECTED HERE -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
