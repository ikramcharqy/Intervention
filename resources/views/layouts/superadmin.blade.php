<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Super Admin Infrastructure — {{ config('app.name', 'TechniTrack') }}</title>

    <!-- Applique le thème stocké avant le premier rendu, pour éviter tout flash clair→sombre -->
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (dark) document.documentElement.classList.add('dark');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .dark body { background-color: #0b0f1a; color: #e2e8f0; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full antialiased text-slate-900 dark:text-slate-200 bg-slate-50 dark:bg-[#0b0f1a]"
      x-data="{ sidebarOpen: false, dark: document.documentElement.classList.contains('dark') }">
    <div class="h-screen flex bg-slate-50 dark:bg-[#0b0f1a] overflow-hidden">

        <!-- OVERLAY MOBILE -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 md:hidden"></div>

        <!-- SIDEBAR SUPERADMIN EXECUTIVE : suit le thème clair/sombre comme le reste de la console -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-[#0a0e18] text-slate-600 dark:text-slate-300 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:z-auto shadow-xl dark:shadow-none border-r border-slate-200 dark:border-slate-800 shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200 dark:border-slate-800">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-600 to-indigo-600 flex items-center justify-center shadow-sm p-1.5">
                        <img src="{{ asset('images/logo-technitrack-icon.png') }}" alt="TechniTrack" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-slate-900 dark:text-white tracking-tight">Techni<span class="text-rose-500 dark:text-rose-400">Track</span></span>
                        <span class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase">Super Admin</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <div class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Infrastructure & Core</div>

                @php
                    $navItem = function (string $routeName, string $pattern, string $icon, string $label) {
                        $active = request()->routeIs($pattern);
                        return compact('routeName', 'pattern', 'icon', 'label', 'active');
                    };
                    // Une seule couleur neutre pour toutes les icônes de navigation (état inactif) —
                    // le rouge/rose est réservé à l'état actif, au badge "Privilèges Root" et aux
                    // alertes de sécurité critiques, jamais utilisé de façon décorative ici.
                    $navLinkClass = fn (array $item) => $item['active']
                        ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100';
                    $navIconClass = fn (array $item) => $item['active']
                        ? 'text-rose-600 dark:text-rose-400'
                        : 'text-slate-400 dark:text-slate-500';

                    $navItems = [
                        $navItem('superadmin.dashboard', 'superadmin.dashboard', 'grid', 'Dashboard Système'),
                        $navItem('types-intervention.index', 'types-intervention.*', 'clipboard-list', "Types d'Intervention & Formulaires"),
                        $navItem('settings.index', 'settings.index', 'briefcase', 'Paramètres de la Plateforme'),
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['routeName']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $navLinkClass($item) }}">
                        <x-icon :name="$item['icon']" class="w-[17px] h-[17px] shrink-0 {{ $navIconClass($item) }}" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <div class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-4 mb-2">Supervision Métier</div>

                @foreach([
                    $navItem('superadmin.supervision.chantiers', 'superadmin.supervision.chantiers', 'city', 'Chantiers'),
                    $navItem('superadmin.supervision.interventions', 'superadmin.supervision.interventions', 'gauge', 'Interventions'),
                    $navItem('superadmin.supervision.emplacements', 'superadmin.supervision.emplacements', 'qrcode', 'Emplacements & QR Codes'),
                    $navItem('superadmin.supervision.clients', 'superadmin.supervision.clients', 'users', 'Comptes Clients'),
                ] as $item)
                    <a href="{{ route($item['routeName']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $navLinkClass($item) }}">
                        <x-icon :name="$item['icon']" class="w-[17px] h-[17px] shrink-0 {{ $navIconClass($item) }}" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <div class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-4 mb-2">Comptes & Sécurité</div>

                @foreach([
                    $navItem('superadmin.admins', 'superadmin.admins', 'user-plus', 'Administrateurs'),
                    $navItem('superadmin.roles', 'superadmin.roles', 'key', 'Rôles & Permissions'),
                    $navItem('superadmin.users', 'superadmin.users*', 'users', 'Tous les Comptes'),
                    $navItem('superadmin.securite.index', 'superadmin.securite.*', 'shield-half', 'Sécurité'),
                ] as $item)
                    <a href="{{ route($item['routeName']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $navLinkClass($item) }}">
                        <x-icon :name="$item['icon']" class="w-[17px] h-[17px] shrink-0 {{ $navIconClass($item) }}" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <div class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-4 mb-2">Monitoring & Backup</div>

                @foreach([
                    $navItem('superadmin.settings', 'superadmin.settings', 'settings', 'Configuration APIs'),
                    $navItem('superadmin.logs', 'superadmin.logs', 'document-chart', "Journaux d'Audit"),
                    $navItem('superadmin.backups', 'superadmin.backups', 'circle-stack', 'Sauvegardes SQL'),
                ] as $item)
                    <a href="{{ route($item['routeName']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $navLinkClass($item) }}">
                        <x-icon :name="$item['icon']" class="w-[17px] h-[17px] shrink-0 {{ $navIconClass($item) }}" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Footer Server Status + Compte -->
            <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#0a0f1d]">
                <div class="flex items-center justify-between gap-2.5 p-2 rounded-lg bg-slate-100 dark:bg-slate-900/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-500 to-indigo-500 flex items-center justify-center text-white text-[11px] font-bold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'SA', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate">{{ \App\Models\User::roleLabel(auth()->user()->roles->pluck('name')->first()) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" title="Déconnexion" class="p-1.5 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 rounded transition">
                            <x-icon name="logout" class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-[#0b0f1a] overflow-x-hidden">
            <header class="h-16 flex items-center justify-between gap-4 px-6 bg-white dark:bg-[#0f1420] border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 shadow-2xs dark:shadow-none">
                <div class="flex items-center gap-4 shrink-0">
                    <button class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none" @click="sidebarOpen = true">
                        <x-icon name="bars" class="w-5 h-5" />
                    </button>
                    <div class="hidden sm:flex flex-col">
                        <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                            <span>Infrastructure</span>
                            <x-icon name="chevron-right" class="w-2.5 h-2.5 text-slate-400" />
                            <span class="text-rose-600 dark:text-rose-400 font-bold">Root Console</span>
                        </div>
                        <h1 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight mt-0.5">Super Admin Platform</h1>
                    </div>
                </div>

                <!-- Recherche : redirige vers "Tous les Comptes" filtré -->
                <form method="GET" action="{{ route('superadmin.users') }}" class="hidden md:flex flex-1 max-w-sm">
                    <div class="relative w-full">
                        <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" placeholder="Rechercher un compte…"
                               class="w-full pl-9 pr-3 py-2 rounded-full text-xs bg-slate-100 dark:bg-slate-800 border border-transparent focus:border-[#1E5EFF] dark:text-slate-100 placeholder-slate-400 focus:outline-none">
                    </div>
                </form>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="hidden lg:flex items-center gap-2 px-3 py-1 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 rounded-full text-rose-700 dark:text-rose-400 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Privilèges Root</span>
                    </div>

                    <!-- Bascule Clair / Sombre -->
                    <button type="button"
                            @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                            class="w-9 h-9 flex items-center justify-center rounded-full text-slate-500 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            title="Basculer le thème clair/sombre">
                        <x-icon name="moon" class="w-[18px] h-[18px]" x-show="!dark" />
                        <x-icon name="sun" class="w-[18px] h-[18px]" x-show="dark" x-cloak />
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ui-btn ui-btn-secondary text-xs py-1.5 px-3 gap-2">
                            <x-icon name="logout" class="w-4 h-4" />
                            <span class="hidden sm:inline">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </header>

            @if(session('success'))
                <div class="mx-6 mt-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-400 text-xs font-semibold flex items-center gap-3 shadow-xs">
                    <x-icon name="circle-check" class="w-[18px] h-[18px] text-emerald-600 dark:text-emerald-400 shrink-0" />
                    <span>{!! session('success') !!}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-400 text-xs font-semibold flex items-center gap-3 shadow-xs">
                    <x-icon name="circle-exclamation" class="w-[18px] h-[18px] text-rose-600 dark:text-rose-400 shrink-0" />
                    <span>{!! session('error') !!}</span>
                </div>
            @endif

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
