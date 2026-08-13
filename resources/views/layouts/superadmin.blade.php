<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Metronic SuperAdmin - {{ config('app.name', 'FieldFlow') }}</title>

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
            --kt-danger: #F1416C;
            --kt-danger-light: #FFF5F8;
            --kt-primary: #3E97FF;
            --kt-success: #50CD89;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--kt-body-bg); color: var(--kt-text-dark); }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .metronic-card {
            background-color: #FFFFFF;
            border: 1px solid var(--kt-card-border);
            box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.03);
            border-radius: 0.85rem;
        }
        .metronic-sidebar {
            background-color: var(--kt-sidebar-bg);
        }
        .metronic-nav-link {
            color: #9899AC;
            transition: all 0.2s ease;
        }
        .metronic-nav-link:hover {
            color: #FFFFFF;
            background-color: var(--kt-sidebar-hover);
        }
        .metronic-nav-link.active {
            color: #FFFFFF;
            background-color: #1B1B28;
            border-left: 4px solid var(--kt-danger);
        }
    </style>
</head>
<body class="h-full antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col md:flex-row">

        <!-- SIDEBAR METRONIC SUPER ADMIN -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 metronic-sidebar text-slate-300 transform -translate-x-full md:translate-x-0 md:static md:flex md:flex-col transition-transform duration-300 ease-in-out shrink-0 border-r border-[#2B2B40]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Logo Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-[#2B2B40]">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="h-10 w-10 bg-gradient-to-tr from-rose-600 to-red-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        SA
                    </div>
                    <div>
                        <span class="text-base font-extrabold text-white tracking-tight block font-heading">METRONIC</span>
                        <span class="text-[10px] text-rose-400 font-bold uppercase tracking-wider">Super Admin v8.2</span>
                    </div>
                </a>
                <button class="md:hidden text-slate-400 hover:text-white" @click="sidebarOpen = false">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
                <!-- Section Header -->
                <div class="px-3 pt-2 pb-2 text-[10px] font-bold uppercase tracking-widest text-[#565674] font-heading">Infrastructure</div>

                <a href="{{ route('superadmin.dashboard') }}" class="metronic-nav-link flex items-center justify-between px-3.5 py-3 rounded-lg text-xs font-semibold {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        <span>Dashboard Système</span>
                    </div>
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                </a>

                <!-- Section Header -->
                <div class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest text-[#565674] font-heading">Gestion des Utilisateurs</div>

                <a href="{{ route('superadmin.admins') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('superadmin.admins') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <span>Administrateurs</span>
                </a>

                <a href="{{ route('superadmin.roles') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('superadmin.roles') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    <span>Rôles & Permissions</span>
                </a>

                <a href="{{ route('superadmin.users') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('superadmin.users') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Tous les Comptes</span>
                </a>

                <!-- Section Header -->
                <div class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest text-[#565674] font-heading">Configuration & Monitoring</div>

                <a href="{{ route('superadmin.settings') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                    <span>APIs (Maps, Caméras)</span>
                </a>

                <a href="{{ route('superadmin.logs') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Journaux d'Audit</span>
                </a>

                <a href="{{ route('superadmin.backups') }}" class="metronic-nav-link flex items-center gap-3 px-3.5 py-3 rounded-lg text-xs font-medium {{ request()->routeIs('superadmin.backups') ? 'active' : '' }}">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    <span>Sauvegardes Base SQL</span>
                </a>
            </nav>

            <div class="p-4 border-t border-[#2B2B40]">
                <div class="p-3 rounded-lg bg-[#1B1B28] flex items-center justify-between text-xs">
                    <div>
                        <span class="text-white font-bold block">Status Serveur</span>
                        <span class="text-[#A1A5B7] text-[11px]">PHP {{ PHP_VERSION }}</span>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 rounded">ONLINE</span>
                </div>
            </div>
        </aside>

        <!-- MAIN METRONIC CONTENT -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- TOPBAR WHITE METRONIC -->
            <header class="h-20 bg-white border-b border-[#EFF2F5] flex items-center justify-between px-8 sticky top-0 z-40 shadow-sm">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 text-slate-600 hover:text-slate-900" @click="sidebarOpen = true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-[#181C32] font-heading">Super Admin Console</h1>
                        <span class="text-xs text-[#A1A5B7]">Metronic 8.2 • Système de Gestion d'Infrastructure</span>
                    </div>
                </div>

                <div class="flex items-center gap-5">
                    <span class="hidden sm:inline-block px-3 py-1 bg-rose-50 text-rose-600 border border-rose-100 text-xs font-bold rounded-lg font-mono">
                        Super Admin Mode
                    </span>

                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-rose-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                            SA
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

            <!-- Notifications Flash -->
            @if(session('success'))
                <div class="mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <main class="flex-1 p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
