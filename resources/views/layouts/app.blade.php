<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TechniTrack') }} — Console de Gestion</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

        <!-- Leaflet CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
            .font-mono { font-family: 'JetBrains Mono', monospace; }
        </style>
    </head>
    <body class="h-full antialiased text-slate-900 bg-slate-50" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex bg-slate-50">

            <!-- OVERLAY MOBILE -->
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 md:hidden"></div>

            <!-- SIDEBAR LATÉRALE EXECUTIVE -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f172a] text-slate-300 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:z-auto shadow-xl border-r border-slate-800 shrink-0"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                
                <!-- BRAND / LOGO SECTION -->
                <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800/90 bg-[#0f172a]">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-extrabold shadow-md shadow-indigo-500/20 group-hover:bg-indigo-500 transition">
                            <i class="fas fa-layer-group text-sm"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-white tracking-tight">Techni<span class="text-indigo-400">Track</span></span>
                            <span class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase">Field Service OS</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>

                <!-- NAVIGATION SIDEBAR MENU -->
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    
                    <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilotage & Opérations</div>

                    <!-- Tableau de Bord -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                        <i class="fas fa-chart-pie w-4 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-indigo-400' }}"></i>
                        <span>Tableau de Bord</span>
                    </a>

                    <!-- Section Chantiers -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Conducteur', 'Client']))
                        <a href="{{ auth()->user()->hasRole('Client') ? route('client.chantiers.index') : route('chantiers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('chantiers.*') || request()->routeIs('client.chantiers.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-building w-4 text-center {{ request()->routeIs('chantiers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                            <span>{{ auth()->user()->hasRole('Client') ? 'Mes Chantiers' : 'Chantiers' }}</span>
                        </a>
                    @endif

                    <!-- Section Clients -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                        <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('clients.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-users w-4 text-center {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-400' }}"></i>
                            <span>Portefeuille Clients</span>
                        </a>
                    @endif

                    <!-- CRM Commercial -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                        <div class="space-y-0.5" x-data="{ open: {{ request()->routeIs('prospects.*') || request()->routeIs('commercial.devis.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition text-slate-400 hover:bg-slate-800/80 hover:text-slate-100 text-left">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-briefcase w-4 text-center text-slate-400"></i>
                                    Module Commercial
                                </span>
                                <i class="fas fa-chevron-right text-[9px] transform transition-transform" :class="open ? 'rotate-90 text-indigo-400' : ''"></i>
                            </button>
                            <div class="pl-8 space-y-0.5" x-show="open" x-collapse>
                                <a href="{{ route('prospects.index') }}" class="block py-1.5 px-2.5 rounded text-xs text-slate-400 hover:text-white hover:bg-slate-800/50 transition {{ request()->routeIs('prospects.*') ? 'text-indigo-400 font-bold' : '' }}">
                                    • Prospects & Leads
                                </a>
                                <a href="{{ route('commercial.devis.index') }}" class="block py-1.5 px-2.5 rounded text-xs text-slate-400 hover:text-white hover:bg-slate-800/50 transition {{ request()->routeIs('commercial.devis.*') ? 'text-indigo-400 font-bold' : '' }}">
                                    • Devis & Offres
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Demandes Clients -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                        @php
                            $countDemandesClient = \App\Models\DemandeIntervention::where('statut', 'En attente')->count();
                        @endphp
                        <a href="{{ route('demande-interventions.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('demande-interventions.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-inbox w-4 text-center {{ request()->routeIs('demande-interventions.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Demandes Clients</span>
                            </div>
                            @if($countDemandesClient > 0)
                                <span class="bg-indigo-500/30 text-indigo-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $countDemandesClient }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Interventions à Planifier -->
                    @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Conducteur']))
                        @php
                            $countAPlanifier = \App\Models\Intervention::whereIn('statut', [\App\Models\Intervention::STATUT_PLANIFIEE, \App\Models\Intervention::STATUT_DEMANDE])->whereNull('technicien_id')->count();
                        @endphp
                        <a href="{{ route('interventions.a-planifier') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('interventions.a-planifier') ? 'bg-amber-600 text-white font-bold shadow-xs' : 'text-amber-400 hover:bg-slate-800/80 hover:text-amber-300' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-plus w-4 text-center text-amber-400"></i>
                                <span>À Planifier</span>
                            </div>
                            @if($countAPlanifier > 0)
                                <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $countAPlanifier }}</span>
                            @endif
                        </a>

                        @php
                            $countReaffectation = \App\Models\DemandeReaffectation::where('statut', 'En attente')->count();
                        @endphp
                        <a href="{{ route('demandes-reaffectation.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('demandes-reaffectation.*') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-rose-400 hover:bg-slate-800/80 hover:text-rose-300' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-circle w-4 text-center text-rose-400"></i>
                                <span>Refus Techniciens</span>
                            </div>
                            @if($countReaffectation > 0)
                                <span class="bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $countReaffectation }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Toutes les Interventions -->
                    <a href="{{ auth()->user()->hasRole('Client') ? route('client.interventions.index') : route('interventions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('interventions.index') || request()->routeIs('client.interventions.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                        <i class="fas fa-clipboard-list w-4 text-center {{ request()->routeIs('interventions.index') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>{{ auth()->user()->hasRole('Client') ? 'Mes Interventions' : 'Toutes Interventions' }}</span>
                    </a>

                    <!-- Formulaires Dynamiques -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('formulaires.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('formulaires.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-tasks w-4 text-center {{ request()->routeIs('formulaires.*') ? 'text-white' : 'text-slate-400' }}"></i>
                            <span>Formulaires</span>
                        </a>
                    @endif

                    <!-- Rapports d'intervention -->
                    <a href="{{ auth()->user()->hasRole('Client') ? route('client.rapports.index') : route('rapports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('rapports.*') || request()->routeIs('client.rapports.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                        <i class="fas fa-file-signature w-4 text-center {{ request()->routeIs('rapports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>{{ auth()->user()->hasRole('Client') ? 'Rapports Validés' : 'Rapports & Validation' }}</span>
                    </a>

                    <!-- Tracking GPS Live -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('gps.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('gps.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-map-marked-alt w-4 text-center text-slate-400"></i>
                            <span>Tracking GPS Live</span>
                        </a>
                    @endif

                    <!-- Stock & Matériaux -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('materiaux.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('materiaux.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-boxes w-4 text-center text-slate-400"></i>
                            <span>Stock & Matériaux</span>
                        </a>
                    @endif

                    <!-- Administration Section -->
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                        <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2">Administration</div>

                        <a href="{{ route('statistiques.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('statistiques.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-chart-line w-4 text-center text-slate-400"></i>
                            <span>Statistiques & KPI</span>
                        </a>

                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <i class="fas fa-sliders-h w-4 text-center text-slate-400"></i>
                            <span>Configuration Système</span>
                        </a>
                    @endif
                </nav>

                <!-- FOOTER PROFILE IN SIDEBAR -->
                <div class="p-3 border-t border-slate-800 bg-[#0a0f1d]">
                    <div class="flex items-center justify-between gap-3 p-2 rounded-lg bg-slate-900 border border-slate-800">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold truncate">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Déconnexion" class="p-1.5 text-slate-400 hover:text-rose-400 rounded transition">
                                <i class="fas fa-sign-out-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- CONTENU PRINCIPAL ET NAVBAR SUPÉRIEURE LIGHT -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
                
                <!-- TOP NAVBAR BLANCHE ÉPURÉE -->
                <header class="h-16 flex items-center justify-between px-6 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
                    <div class="flex items-center gap-4">
                        <button class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" @click="sidebarOpen = true">
                            <i class="fas fa-bars text-base"></i>
                        </button>
                        
                        <!-- Breadcrumb Header -->
                        <div class="hidden sm:flex flex-col">
                            <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                                <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-indigo-600 transition">Console</a>
                                <i class="fas fa-chevron-right text-[8px] text-slate-400"></i>
                                <span class="text-indigo-600 font-bold capitalize">{{ Request::segment(1) ?? 'Dashboard' }}</span>
                            </div>
                            @isset($header)
                                <h1 class="text-sm font-bold text-slate-900 leading-tight mt-0.5">
                                    {{ $header }}
                                </h1>
                            @else
                                <h1 class="text-sm font-bold text-slate-900 leading-tight mt-0.5">
                                    Tableau de Bord
                                </h1>
                            @endisset
                        </div>
                    </div>

                    <!-- Actions Droite Top Navbar -->
                    <div class="flex items-center gap-3">
                        <!-- Mode Rôle Badge -->
                        <div class="hidden lg:flex items-center gap-2 px-3 py-1 bg-slate-100 border border-slate-200 rounded-full text-slate-700 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Session : <strong class="uppercase text-slate-900">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</strong></span>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition focus:outline-none">
                                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden md:flex flex-col text-left">
                                    <span class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] text-slate-500">{{ Auth::user()->email }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                            </button>

                            <!-- Dropdown Menu items -->
                            <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50">
                                
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-indigo-600 font-bold uppercase mt-0.5">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                    <i class="fas fa-user-circle text-slate-400 text-xs"></i>
                                    Mon Profil & Sécurité
                                </a>

                                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                        <i class="fas fa-cog text-slate-400 text-xs"></i>
                                        Paramètres Système
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                        <i class="fas fa-sign-out-alt text-rose-500 text-xs"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mx-6 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 shadow-xs">
                        <i class="fas fa-check-circle text-emerald-600 text-sm shrink-0"></i>
                        <span>{!! session('success') !!}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mx-6 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-3 shadow-xs">
                        <i class="fas fa-exclamation-circle text-rose-600 text-sm shrink-0"></i>
                        <span>{!! session('error') !!}</span>
                    </div>
                @endif

                <!-- PAGE CONTENT -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
