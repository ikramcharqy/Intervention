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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full antialiased text-slate-900 bg-slate-50" x-data="{ sidebarOpen: false }">
    <div class="h-screen flex bg-slate-50 overflow-hidden">

        <!-- OVERLAY MOBILE -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 md:hidden"></div>

        <!-- SIDEBAR (blanche, façon Noona Kasir) -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-slate-500 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:static md:h-full md:z-auto border-r border-slate-100 shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100">
                <a href="{{ route('commercial.dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold shadow-sm">
                        <i class="fas fa-handshake text-xs"></i>
                    </div>
                    <span class="font-bold text-sm text-slate-900 tracking-tight">Techni<span class="text-emerald-600">Track</span></span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-0.5">
                @php
                    $navItem = function (string $routePattern, string $href, string $icon, string $label, ?int $badge = null) {
                        return ['active' => request()->routeIs($routePattern), 'href' => $href, 'icon' => $icon, 'label' => $label, 'badge' => $badge];
                    };
                    $devisEnAttenteBadge = \App\Models\Devis::whereIn('statut', ['Envoyé', 'En attente'])->count();
                @endphp

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Menu Principal</div>

                @foreach([
                    $navItem('commercial.dashboard', route('commercial.dashboard'), 'fa-chart-pie', 'Dashboard'),
                    $navItem('prospects.*', route('prospects.index'), 'fa-user-plus', 'Prospects & Pistes'),
                    $navItem('commercial.devis.*', route('commercial.devis.index'), 'fa-file-invoice-dollar', 'Devis & Propositions', $devisEnAttenteBadge > 0 ? $devisEnAttenteBadge : null),
                    $navItem('commercial.factures.*', route('commercial.factures.index'), 'fa-file-invoice', 'Factures'),
                    $navItem('clients.*', route('clients.index'), 'fa-users', 'Portefeuille Clients'),
                    $navItem('demande-interventions.*', route('demande-interventions.index'), 'fa-inbox', "Demandes d'Intervention"),
                ] as $item)
                    <a href="{{ $item['href'] }}" class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition {{ $item['active'] ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="flex items-center gap-3">
                            <i class="fas {{ $item['icon'] }} w-4 text-center {{ $item['active'] ? 'text-slate-900' : 'text-slate-400' }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </span>
                        @if($item['badge'])
                            <span class="min-w-[18px] h-[18px] px-1 rounded-full bg-slate-900 text-white text-[10px] font-bold flex items-center justify-center">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-5 mb-2">Autres Outils</div>

                @foreach([
                    $navItem('commercial.documents.*', route('commercial.documents.index'), 'fa-folder-open', 'Documents & Contrats'),
                    $navItem('statistiques.*', route('statistiques.index'), 'fa-chart-line', 'Statistiques Ventes'),
                ] as $item)
                    <a href="{{ $item['href'] }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition {{ $item['active'] ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fas {{ $item['icon'] }} w-4 text-center {{ $item['active'] ? 'text-slate-900' : 'text-slate-400' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-5 mb-2">Paramètres</div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition {{ request()->routeIs('profile.edit') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fas fa-user-circle w-4 text-center {{ request()->routeIs('profile.edit') ? 'text-slate-900' : 'text-slate-400' }}"></i>
                    <span>Mon Profil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition text-left">
                        <i class="fas fa-sign-out-alt w-4 text-center text-slate-400"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </nav>

            <!-- Carte utilisateur fixe en bas -->
            <a href="{{ route('profile.edit') }}" class="p-3 border-t border-slate-100 flex items-center justify-between gap-2.5 hover:bg-slate-50 transition shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <i class="fas fa-chevron-up text-[10px] text-slate-300 shrink-0"></i>
            </a>
        </aside>

        <!-- Main Content Light -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-y-auto">
            <header class="h-16 flex items-center justify-between gap-4 px-6 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-4 min-w-0">
                    <button class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" @click="sidebarOpen = true">
                        <i class="fas fa-bars text-base"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2.5 w-[300px] h-10 px-3.5 rounded-lg bg-white border border-slate-200">
                        <i class="fas fa-search text-slate-400 text-xs shrink-0"></i>
                        <input type="text" placeholder="Search..." class="bg-transparent border-0 outline-none focus:ring-0 p-0 text-sm text-slate-700 placeholder-slate-400 w-full">
                    </div>
                </div>

                <div class="flex items-center gap-6 shrink-0">
                    <button type="button" class="relative text-slate-400 hover:text-slate-600 transition" title="Notifications">
                        <i class="fas fa-bell text-base"></i>
                        @php $countDemandes = \App\Models\DemandeIntervention::where('statut', 'En attente')->count(); @endphp
                        @if($countDemandes > 0)
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-rose-500 border-2 border-white"></span>
                        @endif
                    </button>

                    <div class="flex items-center gap-2.5">
                        @if(Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="" class="w-9 h-9 rounded-full object-cover shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="hidden sm:block leading-tight">
                            <p class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
