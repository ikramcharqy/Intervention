<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f5f6fa]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TechniTrack') }} — Console de Gestion</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-technitrack-icon.png') }}">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

        <!-- Leaflet CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; background-color: #f5f6fa; color: #131523; }
            .font-mono { font-family: 'JetBrains Mono', monospace; }
        </style>
    </head>
    <body class="h-full antialiased text-[#131523] bg-[#f5f6fa]" x-data="{ sidebarOpen: false }">
        <div class="h-screen flex flex-col bg-[#f5f6fa] overflow-hidden">

            <!-- TOP NAVBAR PLEINE LARGEUR MODERNIZE -->
            <header class="ds-navbar sticky top-0 z-40 w-full">
                <div class="flex items-center gap-4 sm:gap-6 min-w-0 flex-1">
                    <button class="md:hidden p-2 -ml-2 rounded-[4px] text-[#5A607F] hover:bg-[#F5F6FA] focus:outline-none" @click="sidebarOpen = true">
                        <i class="fas fa-bars text-base"></i>
                    </button>

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0 group">
                        <div class="w-8 h-8 rounded-[4px] bg-[#1E5EFF] flex items-center justify-center group-hover:bg-[#174ecc] transition p-1.5">
                            <img src="{{ asset('images/logo-technitrack-icon.png') }}" alt="TechniTrack" class="w-full h-full object-contain">
                        </div>
                        <span class="hidden sm:inline font-bold text-base text-[#131523] tracking-tight">Techni<span class="text-[#1E5EFF]">Track</span></span>
                    </a>

                    <!-- Recherche responsive : sans encadré, comme la capture, inline dès sm -->
                    <div class="relative flex-1 max-w-xs" x-data="{ searchOpen: false }">
                        <div class="hidden sm:flex items-center gap-2.5 w-full sm:w-40 md:w-64 lg:w-80">
                            <i class="fas fa-search text-[#A1A7C4] text-sm shrink-0"></i>
                            <input type="text" placeholder="Search..." class="bg-transparent border-0 outline-none focus:ring-0 p-0 text-sm text-[#131523] placeholder-[#A1A7C4] w-full">
                        </div>
                        <button type="button" class="sm:hidden p-2 rounded-[4px] text-[#5A607F] hover:bg-[#F5F6FA]" @click="searchOpen = !searchOpen">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                        <div x-show="searchOpen" x-cloak @click.away="searchOpen = false"
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             class="sm:hidden fixed left-4 right-4 top-[60px] flex items-center gap-2.5 px-3.5 py-2.5 rounded-[6px] bg-white border border-[#E6E9F4] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] z-50">
                            <i class="fas fa-search text-[#A1A7C4] text-sm"></i>
                            <input type="text" placeholder="Search..." x-ref="mobileSearch" class="bg-transparent border-0 outline-none focus:ring-0 p-0 text-sm text-[#131523] placeholder-[#A1A7C4] w-full">
                        </div>
                    </div>
                </div>

                <!-- Actions Droite Top Navbar -->
                <div class="flex items-center gap-4 sm:gap-5 shrink-0"
                     x-data="notificationsMenu()" x-init="init()">
                    <!-- Notifications temps réel -->
                    <div class="relative">
                        <button type="button" class="relative text-[#5A607F] hover:text-[#1E5EFF] transition" title="Notifications" @click="open = !open">
                            <i class="fas fa-bell text-lg"></i>
                            <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="absolute -top-1.5 -right-1.5 min-w-[16px] h-[16px] px-1 rounded-full bg-[#1E5EFF] text-white text-[9px] font-bold flex items-center justify-center"></span>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] border border-[#E6E9F4] z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-[#E6E9F4]">
                                <p class="text-xs font-bold text-[#131523]">Notifications</p>
                                <button type="button" x-show="unreadCount > 0" @click="markAllAsRead()" class="text-[10px] font-semibold text-[#1E5EFF] hover:text-[#174ecc]">
                                    Tout marquer comme lu
                                </button>
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-[#E6E9F4]">
                                <template x-if="items.length === 0">
                                    <p class="text-center text-[#A1A7C4] text-xs py-8">Aucune notification pour l'instant.</p>
                                </template>
                                <template x-for="item in items" :key="item.id">
                                    <button type="button" @click="markAsRead(item)" class="w-full text-left px-4 py-3 hover:bg-[#F5F6FA] transition flex items-start gap-3">
                                        <span class="w-2 h-2 rounded-full bg-[#1E5EFF] mt-1.5 shrink-0" x-show="!item.read_at"></span>
                                        <span class="min-w-0" :class="item.read_at ? 'pl-5' : ''">
                                            <span class="block text-xs font-bold text-[#131523] truncate" x-text="item.data.titre"></span>
                                            <span class="block text-[11px] text-[#5A607F] mt-0.5 line-clamp-2" x-text="item.data.message"></span>
                                            <span class="block text-[10px] text-[#A1A7C4] mt-1" x-text="item.timeAgo"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2.5 p-1 rounded-[4px] hover:bg-[#F5F6FA] transition focus:outline-none">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="" class="w-9 h-9 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-full bg-[#131523] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif
                            <span class="hidden sm:inline text-sm font-semibold text-[#131523]">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-[9px] text-[#A1A7C4]"></i>
                        </button>

                        <!-- Dropdown Menu items -->
                        <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] border border-[#E6E9F4] py-1.5 z-50">

                            <div class="px-4 py-2 border-b border-[#E6E9F4]">
                                <p class="text-xs font-bold text-[#131523]">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-[#1E5EFF] font-bold uppercase mt-0.5">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-[#5A607F] hover:bg-[#F5F6FA] hover:text-[#1E5EFF] transition">
                                <i class="fas fa-user-circle text-[#A1A7C4] text-xs"></i>
                                Mon Profil & Sécurité
                            </a>

                            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-[#5A607F] hover:bg-[#F5F6FA] hover:text-[#1E5EFF] transition">
                                    <i class="fas fa-cog text-[#A1A7C4] text-xs"></i>
                                    Paramètres Système
                                </a>
                            @endif

                            <div class="border-t border-[#E6E9F4] my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-[#F0142F] hover:bg-[#FDE3E6] transition text-left">
                                    <i class="fas fa-sign-out-alt text-[#F0142F] text-xs"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex flex-1 min-h-0">

                <!-- OVERLAY MOBILE -->
                <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 md:hidden"></div>

                <!-- SIDEBAR LATÉRALE MODERNIZE -->
                <aside class="fixed bottom-0 top-[68px] left-0 z-30 w-[250px] bg-white text-[#5A607F] flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:h-full md:z-auto border-r border-[#E6E9F4] shrink-0"
                       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

                    <!-- NAVIGATION SIDEBAR MENU -->
                    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                        <div class="ds-sidebar-section-title">Pilotage &amp; Opérations</div>

                        <!-- Tableau de Bord -->
                        <a href="{{ route('dashboard') }}" class="ds-sidebar-item {{ request()->routeIs('dashboard') ? 'ds-sidebar-item-active' : '' }}">
                            <i class="fas fa-chart-pie w-4 text-center {{ request()->routeIs('dashboard') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                            <span>Tableau de Bord</span>
                        </a>

                        <!-- Section Chantiers -->
                        @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Conducteur', 'Client']))
                            <a href="{{ auth()->user()->hasRole('Client') ? route('client.chantiers.index') : route('chantiers.index') }}" class="ds-sidebar-item {{ request()->routeIs('chantiers.*') || request()->routeIs('client.chantiers.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-building w-4 text-center {{ request()->routeIs('chantiers.*') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                                <span>{{ auth()->user()->hasRole('Client') ? 'Mes Chantiers' : 'Chantiers' }}</span>
                            </a>
                        @endif

                        <!-- Section Clients -->
                        @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                            <a href="{{ route('clients.index') }}" class="ds-sidebar-item {{ request()->routeIs('clients.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-users w-4 text-center {{ request()->routeIs('clients.*') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                                <span>Portefeuille Clients</span>
                            </a>
                        @endif

                        <!-- CRM Commercial -->
                        @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                            @php
                                // Le rôle Commercial garde son CRUD complet (ses propres données) ; tous les
                                // autres rôles ayant accès à cette section (Admin, Super Admin) n'ont qu'un
                                // suivi en lecture seule des actions effectuées par les commerciaux.
                                $isCommercial = auth()->user()->hasRole('Commercial');
                                $prospectsUrl = $isCommercial ? route('prospects.index') : route('commercial-suivi.prospects');
                                $devisUrl = $isCommercial ? route('commercial.devis.index') : route('commercial-suivi.devis');
                                $prospectsActive = $isCommercial ? request()->routeIs('prospects.*') : request()->routeIs('commercial-suivi.prospects*');
                                $devisActive = $isCommercial ? request()->routeIs('commercial.devis.*') : request()->routeIs('commercial-suivi.devis*');
                            @endphp
                            <div class="space-y-0.5" x-data="{ open: {{ ($prospectsActive || $devisActive) ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-[4px] text-xs font-semibold transition text-[#5A607F] hover:bg-[#F5F6FA] hover:text-[#131523] text-left">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-briefcase w-4 text-center text-[#A1A7C4]"></i>
                                        Module Commercial
                                    </span>
                                    <i class="fas fa-chevron-right text-[9px] transform transition-transform" :class="open ? 'rotate-90 text-[#1E5EFF]' : ''"></i>
                                </button>
                                <div class="pl-8 space-y-0.5" x-show="open" x-collapse>
                                    <a href="{{ $prospectsUrl }}" class="block py-1.5 px-2.5 rounded text-xs text-[#5A607F] hover:text-[#131523] hover:bg-[#F5F6FA] transition {{ $prospectsActive ? 'text-[#1E5EFF] font-bold' : '' }}">
                                        • {{ $isCommercial ? 'Prospects & Leads' : 'Suivi Prospects & Leads' }}
                                    </a>
                                    <a href="{{ $devisUrl }}" class="block py-1.5 px-2.5 rounded text-xs text-[#5A607F] hover:text-[#131523] hover:bg-[#F5F6FA] transition {{ $devisActive ? 'text-[#1E5EFF] font-bold' : '' }}">
                                        • {{ $isCommercial ? 'Devis & Offres' : 'Suivi Devis & Offres' }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Demandes Clients -->
                        @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Commercial']))
                            @php
                                $countDemandesClient = \App\Models\DemandeIntervention::where('statut', 'En attente')->count();
                            @endphp
                            <a href="{{ route('demande-interventions.index') }}" class="ds-sidebar-item justify-between {{ request()->routeIs('demande-interventions.*') ? 'ds-sidebar-item-active' : '' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-inbox w-4 text-center {{ request()->routeIs('demande-interventions.*') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                                    <span>Demandes Clients</span>
                                </div>
                                @if($countDemandesClient > 0)
                                    <span class="ds-nav-counter" style="background-color:#1E5EFF">{{ $countDemandesClient }}</span>
                                @endif
                            </a>
                        @endif

                        <!-- Interventions à Planifier -->
                        @if(auth()->user()->hasAnyRole(['admin', 'Admin', 'Super Admin', 'superadmin', 'Conducteur']))
                            @php
                                $countAPlanifier = \App\Models\Intervention::whereIn('statut', [\App\Models\Intervention::STATUT_PLANIFIEE, \App\Models\Intervention::STATUT_DEMANDE])->whereNull('technicien_id')->count();
                            @endphp
                            <a href="{{ route('interventions.a-planifier') }}" class="flex items-center justify-between px-3 py-2 rounded-[4px] text-xs font-semibold transition {{ request()->routeIs('interventions.a-planifier') ? 'bg-[#FFF3DE] text-[#B98900] font-bold' : 'text-[#B98900] hover:bg-[#F5F6FA]' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar-plus w-4 text-center text-[#B98900]"></i>
                                    <span>À Planifier</span>
                                </div>
                                @if($countAPlanifier > 0)
                                    <span class="ds-nav-counter" style="background-color:#B98900">{{ $countAPlanifier }}</span>
                                @endif
                            </a>

                            @php
                                $countReaffectation = \App\Models\DemandeReaffectation::where('statut', 'En attente')->count();
                            @endphp
                            <a href="{{ route('demandes-reaffectation.index') }}" class="flex items-center justify-between px-3 py-2 rounded-[4px] text-xs font-semibold transition {{ request()->routeIs('demandes-reaffectation.*') ? 'bg-[#FDE3E6] text-[#F0142F] font-bold' : 'text-[#F0142F] hover:bg-[#F5F6FA]' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-exclamation-circle w-4 text-center text-[#F0142F]"></i>
                                    <span>Refus Techniciens</span>
                                </div>
                                @if($countReaffectation > 0)
                                    <span class="ds-nav-counter animate-pulse" style="background-color:#F0142F">{{ $countReaffectation }}</span>
                                @endif
                            </a>
                        @endif

                        <!-- Toutes les Interventions -->
                        <a href="{{ auth()->user()->hasRole('Client') ? route('client.interventions.index') : route('interventions.index') }}" class="ds-sidebar-item {{ request()->routeIs('interventions.index') || request()->routeIs('client.interventions.*') ? 'ds-sidebar-item-active' : '' }}">
                            <i class="fas fa-clipboard-list w-4 text-center {{ request()->routeIs('interventions.index') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                            <span>{{ auth()->user()->hasRole('Client') ? 'Mes Interventions' : 'Toutes Interventions' }}</span>
                        </a>

                        <!-- Formulaires Dynamiques -->
                        @if(!auth()->user()->hasRole('Client'))
                            <a href="{{ route('formulaires.index') }}" class="ds-sidebar-item {{ request()->routeIs('formulaires.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-tasks w-4 text-center {{ request()->routeIs('formulaires.*') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                                <span>Formulaires</span>
                            </a>
                        @endif

                        <!-- Rapports d'intervention -->
                        <a href="{{ auth()->user()->hasRole('Client') ? route('client.rapports.index') : route('rapports.index') }}" class="ds-sidebar-item {{ request()->routeIs('rapports.*') || request()->routeIs('client.rapports.*') ? 'ds-sidebar-item-active' : '' }}">
                            <i class="fas fa-file-signature w-4 text-center {{ request()->routeIs('rapports.*') ? 'text-[#1E5EFF]' : 'text-[#A1A7C4]' }}"></i>
                            <span>{{ auth()->user()->hasRole('Client') ? 'Rapports Validés' : 'Rapports & Validation' }}</span>
                        </a>

                        <!-- Tracking GPS Live -->
                        @if(!auth()->user()->hasRole('Client'))
                            <a href="{{ route('gps.index') }}" class="ds-sidebar-item {{ request()->routeIs('gps.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-map-marked-alt w-4 text-center text-[#A1A7C4]"></i>
                                <span>Tracking GPS Live</span>
                            </a>
                        @endif

                        <!-- Stock & Matériaux -->
                        @if(!auth()->user()->hasRole('Client'))
                            <a href="{{ route('materiaux.index') }}" class="ds-sidebar-item {{ request()->routeIs('materiaux.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-boxes w-4 text-center text-[#A1A7C4]"></i>
                                <span>Stock &amp; Matériaux</span>
                            </a>
                        @endif

                        <!-- Administration Section -->
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                            <div class="ds-sidebar-section-title mt-4">Administration</div>

                            <a href="{{ route('statistiques.index') }}" class="ds-sidebar-item {{ request()->routeIs('statistiques.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-chart-line w-4 text-center text-[#A1A7C4]"></i>
                                <span>Statistiques &amp; KPI</span>
                            </a>

                            <a href="{{ route('settings.index') }}" class="ds-sidebar-item {{ request()->routeIs('settings.*') ? 'ds-sidebar-item-active' : '' }}">
                                <i class="fas fa-sliders-h w-4 text-center text-[#A1A7C4]"></i>
                                <span>Configuration Système</span>
                            </a>
                        @endif
                    </nav>

                    <!-- FOOTER PROFILE IN SIDEBAR -->
                    <div class="p-3 border-t border-[#E6E9F4]">
                        <div class="flex items-center justify-between gap-3 p-2 rounded-[4px] bg-[#F5F6FA] border border-[#E6E9F4]">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-[4px] bg-[#1E5EFF] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#131523] truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-[#A1A7C4] font-semibold truncate">{{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" title="Déconnexion" class="p-1.5 text-[#A1A7C4] hover:text-[#F0142F] rounded transition">
                                    <i class="fas fa-sign-out-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                <!-- CONTENU PRINCIPAL -->
                <div class="flex-1 flex flex-col min-w-0 bg-[#f5f6fa] overflow-y-auto">

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mx-6 mt-6 p-4 rounded-[6px] bg-[#E3FBF0] border border-[#C4F8E2] text-[#06A561] text-xs font-semibold flex items-center gap-3">
                            <i class="fas fa-check-circle text-[#06A561] text-sm shrink-0"></i>
                            <span>{!! session('success') !!}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mx-6 mt-6 p-4 rounded-[6px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-semibold flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-[#F0142F] text-sm shrink-0"></i>
                            <span>{!! session('error') !!}</span>
                        </div>
                    @endif

                    <!-- PAGE CONTENT -->
                    <main class="flex-1 p-4 sm:p-6 md:p-8">
                        @isset($header)
                            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight mb-6">{{ $header }}</h1>
                        @endisset
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
