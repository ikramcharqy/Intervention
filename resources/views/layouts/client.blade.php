<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f6f8fa]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Espace Client — {{ config('app.name', 'TechniTrack') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-technitrack-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f6f8fa; color: #1e2530; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full antialiased text-[#1e2530] bg-[#f6f8fa]"
      x-data="{
          sidebarOpen: false,
          sidebarCompact: false,
          init() {
              this.sidebarCompact = localStorage.getItem('client_sidebar_compact') === '1';
              this.$watch('sidebarCompact', (value) => localStorage.setItem('client_sidebar_compact', value ? '1' : '0'));
          },
      }">
    @php
        // Libellés harmonisés entre sidebar, breadcrumb et titres de page :
        // une seule source de vérité, indexée par le pattern de route courant.
        $pageMeta = collect([
            'client.dashboard'       => ['label' => 'Tableau de bord', 'icon' => 'gauge'],
            'client.chantiers.*'     => ['label' => 'Mes Chantiers', 'icon' => 'city'],
            'client.demandes.*'      => ['label' => 'Mes Demandes', 'icon' => 'paper-plane'],
            'client.interventions.*' => ['label' => 'Mes Interventions', 'icon' => 'clipboard-list'],
            'client.rapports.*'      => ['label' => 'Mes Interventions', 'icon' => 'clipboard-list'],
            'client.factures.*'      => ['label' => 'Facturation & Devis', 'icon' => 'invoice'],
            'client.documents.*'     => ['label' => 'Documents & Contrats', 'icon' => 'folder'],
            'client.support.*'       => ['label' => 'Support', 'icon' => 'headset'],
            'client.notifications.*' => ['label' => 'Notifications', 'icon' => 'bell'],
            'client.profile.*'       => ['label' => 'Mon profil', 'icon' => 'user'],
            'client.securite.*'      => ['label' => 'Sécurité', 'icon' => 'shield-half'],
        ])->first(fn ($meta, $pattern) => request()->routeIs($pattern)) ?? ['label' => 'Tableau de bord', 'icon' => 'gauge'];

        // Compteur de la cloche = vraies notifications non lues (table `notifications`,
        // même mécanisme que Technicien/API), et non plus un calcul ad hoc déconnecté
        // de ce qui est réellement affiché sur la page Notifications.
        $alertesCount = auth()->user()->unreadNotifications()->count();
    @endphp

    <div class="h-screen flex bg-[#f6f8fa] overflow-hidden">

        <!-- OVERLAY MOBILE -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-40 md:hidden"></div>

        <!-- SIDEBAR -->
        <aside class="fixed inset-y-0 left-0 z-50 h-screen bg-white text-slate-500 flex flex-col transition-all duration-300 transform md:translate-x-0 md:static md:z-auto border-r border-gray-100 shrink-0"
               :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCompact ? 'md:w-[84px] w-64' : 'w-64']">

            <!-- Brand -->
            <div class="h-20 flex items-center justify-between px-6 shrink-0">
                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2.5 group min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center shadow-sm shadow-emerald-500/30 shrink-0 p-1.5">
                        <img src="{{ asset('images/logo-technitrack-icon.png') }}" alt="TechniTrack" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col min-w-0" x-show="!sidebarCompact" x-cloak>
                        <span class="font-extrabold text-base text-[#1e2530] tracking-tight truncate leading-none">Techni<span class="text-emerald-500">Track</span></span>
                        <span class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase truncate mt-0.5">Portail Client</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 shrink-0">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto overflow-x-hidden px-4 py-2 space-y-1">
                <div class="px-3 text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-2" x-show="!sidebarCompact" x-cloak>Mon Compte & Activité</div>

                @php
                    $navItem = function (string $routeName, string $pattern, string $icon, string $label, string $accent) {
                        $active = request()->routeIs($pattern);
                        return compact('routeName', 'pattern', 'icon', 'label', 'active', 'accent');
                    };
                    // Icônes colorées sans fond — la couleur identifie la section (cf. sidebar
                    // Super Admin), l'état actif se distingue par le fond de la ligne.
                    $clientAccents = [
                        'emerald' => 'text-emerald-600',
                        'blue'    => 'text-blue-600',
                        'violet'  => 'text-violet-600',
                        'amber'   => 'text-amber-600',
                        'cyan'    => 'text-cyan-600',
                        'fuchsia' => 'text-fuchsia-600',
                        'rose'    => 'text-rose-600',
                    ];
                    $navItems = [
                        $navItem('client.dashboard', 'client.dashboard', 'gauge', 'Tableau de bord', 'emerald'),
                        $navItem('client.chantiers.index', 'client.chantiers.*', 'city', 'Mes Chantiers', 'blue'),
                        $navItem('client.demandes.index', 'client.demandes.*', 'paper-plane', 'Mes Demandes', 'violet'),
                        $navItem('client.interventions.index', 'client.interventions.*|client.rapports.*', 'clipboard-list', 'Mes Interventions', 'amber'),
                        $navItem('client.factures.index', 'client.factures.*', 'invoice', 'Facturation & Devis', 'cyan'),
                        $navItem('client.documents.index', 'client.documents.*', 'folder', 'Documents & Contrats', 'fuchsia'),
                        $navItem('client.support.index', 'client.support.*', 'headset', 'Support', 'rose'),
                        $navItem('client.notifications.index', 'client.notifications.*', 'bell', 'Notifications', 'blue'),
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['routeName']) }}" title="{{ $item['label'] }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition {{ $item['active'] ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        <x-icon :name="$item['icon']" class="w-[17px] h-[17px] shrink-0 {{ $clientAccents[$item['accent']] }}" />
                        <span x-show="!sidebarCompact" x-cloak class="truncate">{{ $item['label'] }}</span>
                        @if($item['active'])
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" x-show="!sidebarCompact" x-cloak></span>
                        @endif
                    </a>
                @endforeach

                <div class="pt-4 mt-3 border-t border-gray-100">
                    <div class="px-3 text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-2 mt-3" x-show="!sidebarCompact" x-cloak>Paramètres</div>

                    <a href="{{ route('client.profile.edit') }}" title="Mon profil" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition {{ request()->routeIs('client.profile.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        <x-icon name="user" class="w-[17px] h-[17px] shrink-0 text-slate-500" />
                        <span x-show="!sidebarCompact" x-cloak class="truncate">Mon profil</span>
                    </a>
                    <a href="{{ route('client.securite.index') }}" title="Sécurité" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition {{ request()->routeIs('client.securite.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        <x-icon name="shield-half" class="w-[17px] h-[17px] shrink-0 text-slate-500" />
                        <span x-show="!sidebarCompact" x-cloak class="truncate">Sécurité</span>
                    </a>
                </div>
            </nav>

            <!-- Collapse toggle (tablet/desktop) -->
            <button @click="sidebarCompact = !sidebarCompact"
                    :title="sidebarCompact ? 'Afficher le menu complet' : 'Réduire le menu'"
                    class="hidden md:flex items-center justify-center py-3 text-slate-400 hover:text-emerald-600 border-t border-gray-100 shrink-0">
                <x-icon name="angles-left" class="w-4 h-4" x-show="!sidebarCompact" />
                <x-icon name="angles-right" class="w-4 h-4" x-show="sidebarCompact" x-cloak />
            </button>

            <!-- Footer Profile -->
            <div class="p-4 border-t border-gray-100 shrink-0">
                <div class="flex items-center gap-2.5 min-w-0 p-2 rounded-xl bg-slate-50">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0" x-show="!sidebarCompact" x-cloak>
                        <p class="text-xs font-bold text-[#1e2530] truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold truncate">Client</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            <header class="h-20 flex items-center justify-between gap-4 px-5 sm:px-8 bg-[#f6f8fa] sticky top-0 z-30">
                <div class="flex items-center gap-4 min-w-0">
                    <button class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-white focus:outline-none shrink-0" @click="sidebarOpen = true">
                        <x-icon name="bars" class="w-5 h-5" />
                    </button>
                    <div class="hidden sm:flex flex-col min-w-0">
                        <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                            <span>Portail Client</span>
                            <x-icon name="chevron-right" class="w-2.5 h-2.5 text-slate-300" />
                            <span class="text-emerald-600 font-bold truncate">{{ $pageMeta['label'] }}</span>
                        </div>
                        <h1 class="text-lg font-extrabold text-[#1e2530] leading-tight mt-0.5 truncate">{{ $pageMeta['label'] }}</h1>
                    </div>
                </div>

                <!-- Recherche globale -->
                <form action="{{ route('client.interventions.index') }}" method="GET" class="hidden lg:flex flex-1 max-w-sm">
                    <div class="relative w-full">
                        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="q" placeholder="Rechercher une intervention, un chantier…"
                               class="w-full pl-10 pr-3 py-2.5 text-xs rounded-full border-0 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition">
                    </div>
                </form>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <a href="{{ route('client.demandes.create') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-sm shadow-emerald-500/30 transition">
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Nouvelle Demande</span>
                    </a>

                    <a href="{{ route('client.notifications.index') }}" class="relative w-10 h-10 flex items-center justify-center rounded-full bg-white text-slate-500 hover:text-emerald-600 shadow-sm transition" title="Notifications">
                        <x-icon name="bell" class="w-[18px] h-[18px]" />
                        @if($alertesCount > 0)
                            <span class="absolute top-1 right-1.5 inline-flex items-center justify-center min-w-[15px] h-[15px] px-1 rounded-full bg-rose-500 text-white text-[9px] font-bold ring-2 ring-[#f6f8fa]">{{ $alertesCount > 9 ? '9+' : $alertesCount }}</span>
                        @endif
                    </a>

                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 pl-1 pr-1 py-1 rounded-full hover:bg-white transition focus:outline-none">
                                <div class="w-9 h-9 rounded-full bg-emerald-500 text-white font-bold text-[11px] flex items-center justify-center shrink-0 shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2.5 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('client.profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                                <x-icon name="user" class="w-3.5 h-3.5 text-slate-400" /> Profil
                            </a>
                            <a href="{{ route('client.securite.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                                <x-icon name="shield-half" class="w-3.5 h-3.5 text-slate-400" /> Sécurité
                            </a>
                            <a href="{{ route('client.support.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                                <x-icon name="headset" class="w-3.5 h-3.5 text-slate-400" /> Support
                            </a>
                            <div class="border-t border-slate-100 mt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                                        <x-icon name="logout" class="w-3.5 h-3.5" /> Déconnexion
                                    </button>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto px-5 sm:px-8 pb-8">
                @if(session('success'))
                    <div class="mb-5 p-3.5 rounded-2xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-2">
                        <x-icon name="circle-check" class="w-4 h-4" /> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-5 p-3.5 rounded-2xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 flex items-center gap-2">
                        <x-icon name="circle-exclamation" class="w-4 h-4" /> {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
