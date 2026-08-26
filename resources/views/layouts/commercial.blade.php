<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Espace Commercial & Ventes — {{ config('app.name', 'TechniTrack') }}</title>

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

        <!-- SIDEBAR COMMERCIAL EXECUTIVE -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f172a] text-slate-300 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:z-auto shadow-xl border-r border-slate-800 shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 bg-[#0f172a]">
                <a href="{{ route('commercial.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-sm">
                        <i class="fas fa-handshake text-sm"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-white tracking-tight">Techni<span class="text-emerald-400">Track</span></span>
                        <span class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase">Espace Ventes</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ventes & Suivi</div>

                <a href="{{ route('commercial.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('commercial.dashboard') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-chart-line w-4 text-center {{ request()->routeIs('commercial.dashboard') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span>Dashboard Commercial</span>
                </a>

                <a href="{{ route('prospects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('prospects.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-user-tag w-4 text-center {{ request()->routeIs('prospects.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Prospects & Pistes</span>
                </a>

                <a href="{{ route('commercial.devis.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('commercial.devis.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-file-invoice-dollar w-4 text-center {{ request()->routeIs('commercial.devis.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Devis & Propositions</span>
                </a>

                <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('clients.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-users w-4 text-center {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Portefeuille Clients</span>
                </a>

                <a href="{{ route('demande-interventions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('demande-interventions.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-inbox w-4 text-center {{ request()->routeIs('demande-interventions.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Demandes d'Intervention</span>
                </a>

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2">Documents & KPI</div>

                <a href="{{ route('commercial.documents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('commercial.documents.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-folder-open w-4 text-center text-slate-400"></i>
                    <span>Documents & Contrats</span>
                </a>

                <a href="{{ route('statistiques.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('statistiques.*') ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-chart-pie w-4 text-center text-slate-400"></i>
                    <span>Statistiques Ventes</span>
                </a>
            </nav>

            <!-- Footer Profile -->
            <div class="p-3 border-t border-slate-800 bg-[#0a0f1d]">
                <div class="flex items-center justify-between gap-3 p-2 rounded-lg bg-slate-900 border border-slate-800">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-md bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-emerald-400 font-semibold truncate">Commercial</p>
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

        <!-- Main Content Light -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            <header class="h-16 flex items-center justify-between px-6 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" @click="sidebarOpen = true">
                        <i class="fas fa-bars text-base"></i>
                    </button>
                    <div class="hidden sm:flex flex-col">
                        <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                            <span>CRM</span>
                            <i class="fas fa-chevron-right text-[8px] text-slate-400"></i>
                            <span class="text-emerald-600 font-bold">Ventes & Opportunités</span>
                        </div>
                        <h1 class="text-sm font-bold text-slate-900 leading-tight mt-0.5">Console Commerciale</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-full text-emerald-700 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Performance Ventes</span>
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

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
