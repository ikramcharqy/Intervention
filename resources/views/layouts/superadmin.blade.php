<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Super Admin Infrastructure — {{ config('app.name', 'TechniTrack') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

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

        <!-- SIDEBAR SUPERADMIN EXECUTIVE -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f172a] text-slate-300 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:z-auto shadow-xl border-r border-slate-800 shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 bg-[#0f172a]">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-rose-600 flex items-center justify-center text-white font-bold shadow-sm">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-white tracking-tight">Techni<span class="text-rose-400">Track</span></span>
                        <span class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase">Super Admin</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Infrastructure & Core</div>

                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-server w-4 text-center {{ request()->routeIs('superadmin.dashboard') ? 'text-white' : 'text-rose-400' }}"></i>
                    <span>Dashboard Système</span>
                </a>

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2">Comptes & Sécurité</div>

                <a href="{{ route('superadmin.admins') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.admins') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-user-shield w-4 text-center {{ request()->routeIs('superadmin.admins') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Administrateurs</span>
                </a>

                <a href="{{ route('superadmin.roles') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.roles') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-key w-4 text-center {{ request()->routeIs('superadmin.roles') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Rôles & Permissions</span>
                </a>

                <a href="{{ route('superadmin.users') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.users') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-users-cog w-4 text-center {{ request()->routeIs('superadmin.users') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Tous les Comptes</span>
                </a>

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2">Monitoring & Backup</div>

                <a href="{{ route('superadmin.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.settings') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-sliders-h w-4 text-center {{ request()->routeIs('superadmin.settings') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Configuration APIs</span>
                </a>

                <a href="{{ route('superadmin.logs') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.logs') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-list-alt w-4 text-center {{ request()->routeIs('superadmin.logs') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Journaux d'Audit</span>
                </a>

                <a href="{{ route('superadmin.backups') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.backups') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-database w-4 text-center {{ request()->routeIs('superadmin.backups') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Sauvegardes SQL</span>
                </a>
            </nav>

            <!-- Footer Server Status -->
            <div class="p-3 border-t border-slate-800 bg-[#0a0f1d]">
                <div class="flex items-center justify-between gap-3 p-2 rounded-lg bg-slate-900 border border-slate-800">
                    <div>
                        <span class="text-xs font-bold text-white block">PHP {{ PHP_VERSION }}</span>
                        <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> MySQL Online</span>
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

        <!-- Main Content Light -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            <header class="h-16 flex items-center justify-between px-6 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" @click="sidebarOpen = true">
                        <i class="fas fa-bars text-base"></i>
                    </button>
                    <div class="hidden sm:flex flex-col">
                        <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                            <span>Infrastructure</span>
                            <i class="fas fa-chevron-right text-[8px] text-slate-400"></i>
                            <span class="text-rose-600 font-bold">Root Console</span>
                        </div>
                        <h1 class="text-sm font-bold text-slate-900 leading-tight mt-0.5">Super Admin Platform</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-rose-50 border border-rose-200 rounded-full text-rose-700 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Privilèges Root</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ui-btn ui-btn-secondary text-xs py-1.5 px-3">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </header>

            @if(session('success'))
                <div class="mx-6 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 shadow-xs">
                    <i class="fas fa-check-circle text-emerald-600 text-sm shrink-0"></i>
                    <span>{!! session('success') !!}</span>
                </div>
            @endif

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
