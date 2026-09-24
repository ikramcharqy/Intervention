<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TechniTrack') . ' — Console Admin')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-technitrack-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-900 bg-slate-50" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex bg-slate-50">
        <!-- Overlay mobile -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 md:hidden"></div>

        <!-- SIDEBAR ADMIN EXECUTIVE -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f172a] text-slate-300 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:z-auto shadow-xl border-r border-slate-800 shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 bg-[#0f172a]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-500/20 p-1.5">
                        <img src="{{ asset('images/logo-technitrack-icon.png') }}" alt="TechniTrack" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-white tracking-tight">Techni<span class="text-indigo-400">Track</span></span>
                        <span class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase">Admin Console</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilotage & Opérations</div>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-chart-pie w-4 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-indigo-400' }}"></i>
                    <span>Tableau de Bord</span>
                </a>

                <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('clients.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-users w-4 text-center {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Clients & Entreprises</span>
                </a>

                <a href="{{ route('chantiers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('chantiers.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-building w-4 text-center {{ request()->routeIs('chantiers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Chantiers</span>
                </a>

                <a href="{{ route('interventions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ (request()->routeIs('interventions.*') && !request()->routeIs('interventions.a-planifier')) ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-clipboard-list w-4 text-center {{ request()->routeIs('interventions.index') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Toutes Interventions</span>
                </a>

                @php
                    $countAPlanifier = \App\Models\Intervention::where('statut', \App\Models\Intervention::STATUT_PLANIFIEE)->whereNull('technicien_id')->count();
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

                <a href="{{ route('users.index') }}?role=Technicien" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ (request()->routeIs('users.*') && request()->input('role') === 'Technicien') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-user-cog w-4 text-center text-slate-400"></i>
                    <span>Équipe Techniciens</span>
                </a>

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2">Suivi & Rapports</div>

                <a href="{{ route('formulaires.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('formulaires.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-tasks w-4 text-center text-slate-400"></i>
                    <span>Formulaires</span>
                </a>

                <a href="{{ route('rapports.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('rapports.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-file-signature w-4 text-center text-slate-400"></i>
                    <span>Rapports Validation</span>
                </a>

                <a href="{{ route('planning.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('planning.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-calendar-alt w-4 text-center text-slate-400"></i>
                    <span>Planning Général</span>
                </a>

                <a href="{{ route('gps.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('gps.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-map-marked-alt w-4 text-center text-slate-400"></i>
                    <span>Tracking GPS Live</span>
                </a>

                <a href="{{ route('materiaux.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('materiaux.*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-100' }}">
                    <i class="fas fa-boxes w-4 text-center text-slate-400"></i>
                    <span>Stock & Matériaux</span>
                </a>
            </nav>

            <!-- Footer Profile -->
            <div class="p-3 border-t border-slate-800 bg-[#0a0f1d]">
                <div class="flex items-center justify-between gap-3 p-2 rounded-lg bg-slate-900 border border-slate-800">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold truncate">{{ auth()->user()->roles->pluck('name')->first() ?? 'Admin' }}</p>
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

        <!-- Main View Area Light -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            <!-- Topbar -->
            <header class="h-16 flex items-center justify-between px-6 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" @click="sidebarOpen = true">
                        <i class="fas fa-bars text-base"></i>
                    </button>
                    <div class="hidden sm:flex flex-col">
                        <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                            <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-indigo-600 transition">Console</a>
                            <i class="fas fa-chevron-right text-[8px] text-slate-400"></i>
                            <span class="text-indigo-600 font-bold capitalize">{{ Request::segment(1) ?? 'Admin' }}</span>
                        </div>
                        <h1 class="text-sm font-bold text-slate-900 leading-tight mt-0.5">
                            @yield('page_title', 'Administration')
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden lg:flex items-center gap-2 px-3 py-1 bg-slate-100 border border-slate-200 rounded-full text-slate-700 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Mode : <strong class="uppercase text-slate-900">{{ auth()->user()->roles->pluck('name')->first() ?? 'Admin' }}</strong></span>
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

            <!-- Alerts -->
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

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
