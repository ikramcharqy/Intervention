<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Antigravity Intervention') }} — Espace Commercial</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-gray-900 dark:text-gray-150" x-data="{ sidebarOpen: false, darkMode: false }">
        <div class="min-h-full flex flex-col md:flex-row">

            <!-- SIDEBAR COMMERCIAL -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-emerald-950 text-emerald-100 transform -translate-x-full md:translate-x-0 md:static md:flex md:flex-col transition-transform duration-300 ease-in-out shrink-0 border-r border-emerald-900"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

                <!-- Logo -->
                <div class="h-16 flex items-center justify-between px-6 bg-emerald-975 bg-emerald-900/40 border-b border-emerald-900">
                    <a href="{{ route('commercial.dashboard') }}" class="flex items-center gap-2">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-lg font-bold text-white tracking-wider uppercase">FieldFlow</span>
                    </a>
                    <button class="md:hidden text-emerald-300 hover:text-white" @click="sidebarOpen = false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-3 text-[10px] font-bold tracking-widest uppercase text-emerald-400 border-b border-emerald-900">
                    Espace Commercial
                </div>

                <!-- Navigation Sidebar -->
                <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">

                    <!-- Tableau de bord -->
                    <a href="{{ route('commercial.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('commercial.dashboard') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Tableau de bord
                    </a>

                    <!-- Prospects -->
                    <a href="{{ route('prospects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('prospects.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Prospects
                    </a>

                    <!-- Clients -->
                    <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('clients.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Clients
                    </a>

                    <!-- Chantiers / Emplacements -->
                    <a href="{{ route('chantiers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('chantiers.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Emplacements / Chantiers
                    </a>

                    <!-- Demandes d'intervention -->
                    <a href="{{ route('demande-interventions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('demande-interventions.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Demandes d'intervention
                    </a>

                    <!-- Devis -->
                    <a href="{{ route('commercial.devis.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('commercial.devis.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
                        </svg>
                        Devis
                    </a>

                    <!-- Documents -->
                    <a href="{{ route('commercial.documents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('commercial.documents.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Documents
                    </a>

                    <!-- Statistiques -->
                    <a href="{{ route('statistiques.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('statistiques.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                        </svg>
                        Statistiques
                    </a>

                    <!-- Profil -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('profile.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-200 hover:bg-emerald-900 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil
                    </a>
                </nav>
            </aside>

            <!-- CONTENU PRINCIPAL -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-955">

                <!-- TOPBAR -->
                <header class="h-16 flex items-center justify-between px-6 bg-white dark:bg-gray-900 border-b border-gray-250 dark:border-gray-800 shadow-sm sticky top-0 z-40">
                    <div class="flex items-center gap-4">
                        <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none" @click="sidebarOpen = true">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Breadcrumb -->
                        <div class="hidden sm:flex flex-col">
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <span>FieldFlow</span>
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                <span class="capitalize">Espace Commercial</span>
                            </span>
                            @isset($header)
                                <h1 class="text-base font-bold text-gray-800 dark:text-white leading-tight mt-0.5">
                                    {{ $header }}
                                </h1>
                            @else
                                <h1 class="text-base font-bold text-gray-800 dark:text-white leading-tight mt-0.5">
                                    Espace Commercial
                                </h1>
                            @endisset
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="document.documentElement.classList.toggle('dark')" class="p-1.5 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Menu Profil Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 focus:outline-none">
                                    <div class="h-8 w-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                        {{ substr(Auth::user()->name, 0, 2) }}
                                    </div>
                                    <div class="hidden md:flex flex-col text-left">
                                        <span class="text-xs font-semibold text-gray-750 dark:text-gray-200 leading-3">{{ Auth::user()->name }}</span>
                                        <span class="text-[9px] text-gray-400 font-semibold tracking-wide uppercase mt-0.5">Commercial</span>
                                    </div>
                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    Profil
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        Déconnexion
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <!-- Zone de contenu -->
                <main class="flex-1 p-6 md:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
