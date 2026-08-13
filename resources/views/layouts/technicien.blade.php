<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Metronic Technicien - {{ config('app.name', 'FieldFlow') }}</title>

    <!-- Google Fonts Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --kt-body-bg: #F9F9FB;
            --kt-sidebar-bg: #1E1E2D;
            --kt-sidebar-border: #2B2B40;
            --kt-sidebar-hover: #1B1B28;
            --kt-card-bg: #FFFFFF;
            --kt-card-border: #EFF2F5;
            --kt-text-dark: #181C32;
            --kt-text-muted: #A1A5B7;
            --kt-warning: #FFC700;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--kt-body-bg); color: var(--kt-text-dark); }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .metronic-card {
            background-color: #FFFFFF;
            border: 1px solid var(--kt-card-border);
            box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.03);
            border-radius: 0.85rem;
        }
        .metronic-sidebar { background-color: var(--kt-sidebar-bg); }
        .metronic-nav-link { color: #9899AC; transition: all 0.2s ease; }
        .metronic-nav-link:hover { color: #FFFFFF; background-color: var(--kt-sidebar-hover); }
        .metronic-nav-link.active { color: #FFFFFF; background-color: #1B1B28; border-left: 4px solid var(--kt-warning); }
    </style>
</head>
<body class="h-full antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col md:flex-row">

        <!-- SIDEBAR METRONIC TECHNICIEN -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 metronic-sidebar text-slate-300 transform -translate-x-full md:translate-x-0 md:static md:flex md:flex-col transition-transform duration-300 ease-in-out shrink-0 border-r border-[#2B2B40]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Logo Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-[#2B2B40]">
                <a href="{{ route('technicien.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="h-10 w-10 bg-gradient-to-tr from-amber-500 to-yellow-400 rounded-xl flex items-center justify-center text-slate-950 font-bold text-lg shadow-md">
                        TECH
                    </div>
                    <div>
                        <span class="text-base font-extrabold text-white tracking-tight block font-heading">METRONIC</span>
                        <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">Espace Technicien</span>
                    </div>
                </a>
                <button class="md:hidden text-slate-400 hover:text-white" @click="sidebarOpen = false">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
                <div class="px-3 pt-2 pb-2 text-[10px] font-bold uppercase tracking-widest text-[#565674] font-heading">Opérations Terrain</div>

                <a href="{{ route('technicien.dashboard') }}" class="metronic-nav-link flex items-center justify-between px-3.5 py-3 rounded-lg text-xs font-semibold {{ request()->routeIs('technicien.dashboard') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span>Tableau de Bord</span>
                    </div>
                </a>

                <div class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest text-[#565674] font-heading">Interventions & Rapports</div>

                <a href="{{ route('interventions.index') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('interventions.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    <span>Mes Interventions</span>
                </a>

                <a href="{{ route('planning.index') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('planning.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span>Mon Planning</span>
                </a>

                <a href="{{ route('rapports.index') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Mes Rapports</span>
                </a>

                <a href="{{ route('gps.index') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('gps.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                    <span>Pointage GPS</span>
                </a>
            </nav>

            <div class="p-4 border-t border-[#2B2B40]">
                <div class="p-3 rounded-lg bg-[#1B1B28] flex items-center justify-between text-xs">
                    <span class="text-white font-bold">Technicien On Duty</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold text-amber-400 bg-amber-500/10 rounded">EN MISSION</span>
                </div>
            </div>
        </aside>

        <!-- MAIN METRONIC CONTENT -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-20 bg-white border-b border-[#EFF2F5] flex items-center justify-between px-8 sticky top-0 z-40 shadow-sm">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 text-slate-600 hover:text-slate-900" @click="sidebarOpen = true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-[#181C32] font-heading">Espace Technicien Terrain</h1>
                        <span class="text-xs text-[#A1A5B7]">Metronic 8.2 • Suivi des Missions & Rapports</span>
                    </div>
                </div>

                <div class="flex items-center gap-5">
                    <span class="hidden sm:inline-block px-3 py-1 bg-amber-50 text-amber-600 border border-amber-100 text-xs font-bold rounded-lg font-mono">
                        Technicien Active
                    </span>

                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-sm shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden md:block">
                            <span class="text-xs font-bold text-[#181C32] block leading-tight">{{ Auth::user()->name }}</span>
                            <span class="text-[11px] text-[#A1A5B7]">{{ Auth::user()->email }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 bg-[#F5F8FA] hover:bg-[#EEF0F8] text-[#5E6278] text-xs font-semibold rounded-lg transition">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
