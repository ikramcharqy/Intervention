<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Antigravity Intervention') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-gray-900 dark:text-gray-150" x-data="{ sidebarOpen: false, darkMode: false }">
        <div class="min-h-full flex flex-col md:flex-row">
            
            <!-- SIDEBAR -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transform -translate-x-full md:translate-x-0 md:static md:flex md:flex-col transition-transform duration-300 ease-in-out shrink-0 border-r border-slate-800"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                
                <!-- Logo -->
                <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-lg font-bold text-white tracking-wider uppercase">FieldFlow</span>
                    </a>
                    <button class="md:hidden text-slate-400 hover:text-white" @click="sidebarOpen = false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Sidebar -->
                <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                    
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Utilisateurs (Affiché si Admin ou si Spatie pas encore config) -->
                    @if(!auth()->user()->hasRole('Client') && !auth()->user()->hasRole('Technicien'))
                        <div class="space-y-1" x-data="{ open: {{ request()->routeIs('users.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-800 hover:text-white text-left">
                                <span class="flex items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Utilisateurs
                                </span>
                                <svg class="h-4 w-4 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="pl-8 space-y-1" x-show="open" x-collapse>
                                <a href="{{ route('users.index') }}?role=Admin" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->input('role') === 'Admin' ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Administrateurs
                                </a>
                                <a href="{{ route('users.index') }}?role=Commercial" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->input('role') === 'Commercial' ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Commerciaux
                                </a>
                                <a href="{{ route('users.index') }}?role=Technicien" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->input('role') === 'Technicien' ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Techniciens
                                </a>
                                <a href="{{ route('users.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('users.index') && !request()->has('role') ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Tous les comptes
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Clients (Masqué pour Client/Technicien) -->
                    @if(!auth()->user()->hasRole('Client') && !auth()->user()->hasRole('Technicien'))
                        <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('clients.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Clients
                        </a>
                    @endif

                    <!-- CRM Commercial (Prospects + Demandes) -->
                    @if(auth()->user()->hasRole('Commercial') || auth()->user()->hasRole('Admin'))
                        <div class="space-y-1" x-data="{ open: {{ request()->routeIs('prospects.*') || request()->routeIs('demande-interventions.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-800 hover:text-white text-left">
                                <span class="flex items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    CRM Commercial
                                </span>
                                <svg class="h-4 w-4 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="pl-8 space-y-1" x-show="open" x-collapse>
                                <a href="{{ route('prospects.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('prospects.*') ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Prospects
                                </a>
                                <a href="{{ route('demande-interventions.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('demande-interventions.*') ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Demandes d'intervention
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Chantiers & Emplacements -->
                    @if(!auth()->user()->hasRole('Technicien'))
                        <div class="space-y-1" x-data="{ open: {{ request()->routeIs('chantiers.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-800 hover:text-white text-left">
                                <span class="flex items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>{{ Auth::user()->hasRole('Client') ? 'Mes chantiers' : 'Chantiers' }}</span>
                                </span>
                                <svg class="h-4 w-4 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="pl-8 space-y-1" x-show="open" x-collapse>
                                <a href="{{ route('chantiers.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('chantiers.index') ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Liste Chantiers
                                </a>
                                @if(!auth()->user()->hasRole('Client'))
                                    <a href="{{ route('chantiers.create') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('chantiers.create') ? 'text-indigo-400 font-semibold' : '' }}">
                                        • Nouveau Chantier
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Interventions (Admin / Technicien) -->
                    @if(!auth()->user()->hasRole('Client'))
                        <div class="space-y-1" x-data="{ open: {{ request()->routeIs('interventions.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-800 hover:text-white text-left">
                                <span class="flex items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>{{ Auth::user()->hasRole('Technicien') ? 'Mes interventions' : 'Interventions' }}</span>
                                </span>
                                <svg class="h-4 w-4 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="pl-8 space-y-1" x-show="open" x-collapse>
                                <a href="{{ route('interventions.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('interventions.index') ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Liste
                                </a>
                                @can('create interventions')
                                    <a href="{{ route('interventions.create') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('interventions.create') ? 'text-indigo-400 font-semibold' : '' }}">
                                        • Nouvelle intervention
                                    </a>
                                @endcan
                                <a href="{{ route('planning.index') }}" class="block py-1.5 text-xs text-slate-400 hover:text-white transition {{ request()->routeIs('planning.*') ? 'text-indigo-400 font-semibold' : '' }}">
                                    • Planning
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Formulaires (Admin uniquement) -->
                    @if(!auth()->user()->hasRole('Client') && !auth()->user()->hasRole('Technicien'))
                        <a href="{{ route('formulaires.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('formulaires.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Formulaires
                        </a>
                    @endif

                    <!-- Rapports -->
                    <a href="{{ route('rapports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('rapports.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ Auth::user()->hasRole('Client') ? 'Mes rapports' : 'Rapports' }}</span>
                    </a>

                    <!-- Tracking GPS -->
                    @if(!auth()->user()->hasRole('Client'))
                        <a href="{{ route('gps.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('gps.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Tracking GPS
                        </a>
                    @endif

                    <!-- Matériaux (Admin uniquement) -->
                    @if(!auth()->user()->hasRole('Client') && !auth()->user()->hasRole('Technicien'))
                        <a href="{{ route('materiaux.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('materiaux.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Matériels / Pièces
                        </a>

                        <!-- Statistiques -->
                        <a href="{{ route('statistiques.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('statistiques.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            Statistiques
                        </a>

                        <!-- Paramètres -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-500 cursor-not-allowed opacity-60">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Paramètres
                        </a>
                    @endif
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
                                <span class="capitalize">{{ Request::segment(1) ?? 'Dashboard' }}</span>
                            </span>
                            @isset($header)
                                <h1 class="text-base font-bold text-gray-800 dark:text-white leading-tight mt-0.5">
                                    {{ $header }}
                                </h1>
                            @else
                                <h1 class="text-base font-bold text-gray-800 dark:text-white leading-tight mt-0.5">
                                    Field Service App
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

                        <!-- Notifications -->
                        <div class="relative" x-data="{ showNotify: false }">
                            <button @click="showNotify = !showNotify" class="p-1.5 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 relative">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900"></span>
                            </button>
                            
                            <div x-show="showNotify" @click.away="showNotify = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800 font-bold text-xs text-gray-700 dark:text-gray-300">Notifications</div>
                                <div class="px-4 py-3 text-xs text-gray-500">Aucune nouvelle notification.</div>
                            </div>
                        </div>

                        <!-- Menu Profil Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 focus:outline-none">
                                    <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                        {{ substr(Auth::user()->name, 0, 2) }}
                                    </div>
                                    <div class="hidden md:flex flex-col text-left">
                                        <span class="text-xs font-semibold text-gray-750 dark:text-gray-200 leading-3">{{ Auth::user()->name }}</span>
                                        <span class="text-[9px] text-gray-400 font-semibold tracking-wide uppercase mt-0.5">
                                            @if(Auth::user()->roles->isNotEmpty())
                                                {{ Auth::user()->roles->pluck('name')->first() }}
                                            @else
                                                Utilisateur
                                            @endif
                                        </span>
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
