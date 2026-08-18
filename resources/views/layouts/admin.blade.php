<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TechniTrack') . ' — Console')</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════════════════
           TECHNITRACK DESIGN SYSTEM — DARK VIOLET EDITION
           Palette: Violet profond + Dark slate + Accents néon
        ═══════════════════════════════════════════════════════ */
        :root {
            /* Backgrounds */
            --ds-bg:           #0d0d1a;
            --ds-bg-2:         #13131f;
            --ds-bg-3:         #1a1a2e;
            --ds-bg-4:         #1f1f35;

            /* Surfaces (cartes) */
            --ds-surface:      #16162a;
            --ds-surface-2:    #1e1e33;
            --ds-border:       rgba(139, 92, 246, 0.12);
            --ds-border-hover: rgba(139, 92, 246, 0.35);

            /* Textes */
            --ds-text-primary:   #e2e8f0;
            --ds-text-secondary: #94a3b8;
            --ds-text-muted:     #64748b;
            --ds-text-caption:   #475569;

            /* Couleurs d'accent Violet */
            --ds-violet-50:  #f5f3ff;
            --ds-violet-400: #a78bfa;
            --ds-violet-500: #8b5cf6;
            --ds-violet-600: #7c3aed;
            --ds-violet-700: #6d28d9;

            /* Gradients Principaux (310deg) */
            --g-primary:   linear-gradient(310deg, #4c1d95 0%, #7c3aed 50%, #a855f7 100%);
            --g-accent:    linear-gradient(310deg, #7c3aed 0%, #ec4899 100%);
            --g-info:      linear-gradient(310deg, #1e40af 0%, #3b82f6 100%);
            --g-success:   linear-gradient(310deg, #065f46 0%, #10b981 100%);
            --g-warning:   linear-gradient(310deg, #92400e 0%, #f59e0b 100%);
            --g-danger:    linear-gradient(310deg, #7f1d1d 0%, #ef4444 100%);
            --g-dark:      linear-gradient(310deg, #0f0f1a 0%, #1e1e33 100%);
            --g-violet-pink: linear-gradient(310deg, #4c1d95 0%, #7c3aed 60%, #ec4899 100%);

            /* Ombres */
            --shadow-card:  0 4px 24px 0 rgba(0,0,0,0.45);
            --shadow-hover: 0 8px 32px 0 rgba(139,92,246,0.2);
            --shadow-glow:  0 0 20px rgba(139,92,246,0.3);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--ds-bg);
            color: var(--ds-text-primary);
            -webkit-font-smoothing: antialiased;
        }

        /* ── SCROLLBAR DARK ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--ds-bg-2); }
        ::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.4); border-radius: 9999px; }

        /* ── CARTES ── */
        .ds-card {
            background: var(--ds-surface);
            border: 1px solid var(--ds-border);
            border-radius: 1rem;
            box-shadow: var(--shadow-card);
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .ds-card-hover:hover {
            border-color: var(--ds-border-hover);
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
        }
        /* Alias rétro-compat */
        .soft-card { background: var(--ds-surface); border: 1px solid var(--ds-border); border-radius: 1rem; box-shadow: var(--shadow-card); }

        /* ── GRADIENTS CLASSES ── */
        .sg-primary   { background-image: var(--g-primary);   }
        .sg-accent    { background-image: var(--g-accent);     }
        .sg-info      { background-image: var(--g-info);       }
        .sg-success   { background-image: var(--g-success);    }
        .sg-warning   { background-image: var(--g-warning);    }
        .sg-danger    { background-image: var(--g-danger);     }
        .sg-dark      { background-image: var(--g-dark);       }
        .sg-vp        { background-image: var(--g-violet-pink); }

        /* Alias rétro-compat */
        .soft-gradient-primary   { background-image: var(--g-primary);   }
        .soft-gradient-secondary { background-image: var(--g-dark);      }
        .soft-gradient-info      { background-image: var(--g-info);      }
        .soft-gradient-success   { background-image: var(--g-success);   }
        .soft-gradient-warning   { background-image: var(--g-warning);   }
        .soft-gradient-danger    { background-image: var(--g-danger);    }
        .soft-gradient-dark      { background-image: var(--g-dark);      }

        /* ── SIDEBAR ── */
        #app-sidebar {
            width: 264px;
            min-height: 100vh;
            flex-shrink: 0;
            background: var(--ds-bg-2);
            border-right: 1px solid var(--ds-border);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 40;
        }
        @media (max-width: 768px) {
            #app-sidebar { position:fixed; top:0; left:0; height:100vh; transform:translateX(-100%); }
            #app-sidebar.open { transform:translateX(0); box-shadow:var(--shadow-glow); }
        }

        .sidebar-nav-item a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--ds-text-secondary);
            text-decoration: none;
            transition: all 0.18s ease;
        }
        .sidebar-nav-item a:hover {
            background: var(--ds-bg-3);
            color: var(--ds-text-primary);
        }
        .sidebar-nav-item a.active {
            background: var(--g-primary);
            background-image: var(--g-primary);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(124,58,237,0.4);
        }
        .sidebar-nav-item a.active .nav-icon-box {
            background: rgba(255,255,255,0.15) !important;
            box-shadow: none !important;
        }
        .nav-icon-box {
            width: 1.875rem;
            height: 1.875rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--ds-bg-3);
            font-size: 0.65rem;
        }
        .sidebar-section-label {
            font-size: 0.58rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--ds-text-muted);
            padding: 0 0.875rem;
            margin-top: 1.25rem;
            margin-bottom: 0.375rem;
        }
        .ds-badge-pill {
            font-size: 0.6rem;
            font-weight: 800;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            color: #fff;
        }

        /* ── NAVBAR ── */
        #app-navbar {
            background: rgba(22,22,42,0.85);
            backdrop-filter: blur(16px);
            border: 1px solid var(--ds-border);
            border-radius: 1rem;
            padding: 0.7rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            position: sticky;
            top: 0.75rem;
            z-index: 30;
            box-shadow: var(--shadow-card);
        }

        /* ── BOUTONS ── */
        .ds-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-radius: 0.75rem;
            padding: 0.6rem 1.25rem;
            transition: opacity 0.15s, transform 0.15s, box-shadow 0.15s;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .ds-btn:hover { opacity: 0.88; transform: translateY(-1px); }
        .ds-btn:disabled { opacity: 0.35; cursor: not-allowed; transform: none; }
        .ds-btn-primary { background-image: var(--g-primary); color: #fff; box-shadow: 0 4px 15px rgba(124,58,237,0.4); }
        .ds-btn-accent  { background-image: var(--g-accent);  color: #fff; }
        .ds-btn-info    { background-image: var(--g-info);    color: #fff; }
        .ds-btn-success { background-image: var(--g-success); color: #fff; }
        .ds-btn-warning { background-image: var(--g-warning); color: #fff; }
        .ds-btn-danger  { background-image: var(--g-danger);  color: #fff; }
        .ds-btn-ghost {
            background: var(--ds-surface-2);
            border: 1px solid var(--ds-border);
            color: var(--ds-text-primary);
        }
        /* Alias rétro-compat */
        .soft-btn { display:inline-flex; align-items:center; gap:0.4rem; font-weight:700; font-size:0.72rem; letter-spacing:0.06em; border-radius:0.75rem; padding:0.6rem 1.25rem; transition:opacity 0.15s,transform 0.15s; cursor:pointer; border:none; text-decoration:none; }
        .soft-btn:hover { opacity:0.88; transform:translateY(-1px); }
        .soft-btn-primary { background-image:var(--g-primary); color:#fff; }
        .soft-btn-info    { background-image:var(--g-info);    color:#fff; }
        .soft-btn-success { background-image:var(--g-success); color:#fff; }
        .soft-btn-warning { background-image:var(--g-warning); color:#fff; }
        .soft-btn-danger  { background-image:var(--g-danger);  color:#fff; }
        .soft-btn-outline { background:var(--ds-surface-2); border:1px solid var(--ds-border); color:var(--ds-text-primary); }

        /* ── INPUTS ── */
        .ds-input {
            width: 100%;
            padding: 0.65rem 1rem;
            border: 1px solid var(--ds-border);
            border-radius: 0.75rem;
            font-size: 0.78rem;
            color: var(--ds-text-primary);
            background: var(--ds-bg-3);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .ds-input:focus {
            border-color: var(--ds-violet-500);
            box-shadow: 0 0 0 3px rgba(139,92,246,0.2);
        }
        .ds-input::placeholder { color: var(--ds-text-muted); }
        .ds-label {
            display: block;
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--ds-text-muted);
            margin-bottom: 0.375rem;
        }
        /* Alias */
        .soft-input { background:var(--ds-bg-3); border:1px solid var(--ds-border); border-radius:0.75rem; padding:0.65rem 1rem; font-size:0.78rem; color:var(--ds-text-primary); outline:none; width:100%; }
        .soft-input:focus { border-color:var(--ds-violet-500); box-shadow:0 0 0 3px rgba(139,92,246,0.2); }
        .soft-label { display:block; font-size:0.62rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:var(--ds-text-muted); margin-bottom:0.375rem; }

        /* ── TABLEAUX ── */
        .ds-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .ds-table thead th {
            font-size: 0.58rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--ds-text-muted);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--ds-border);
            background: var(--ds-bg-2);
            white-space: nowrap;
        }
        .ds-table tbody td {
            font-size: 0.78rem;
            color: var(--ds-text-primary);
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(139,92,246,0.06);
            vertical-align: middle;
        }
        .ds-table tbody tr:hover td { background: var(--ds-bg-3); }
        /* Alias */
        .soft-table { width:100%; }
        .soft-table thead th { font-size:0.58rem; font-weight:800; text-transform:uppercase; letter-spacing:0.12em; color:var(--ds-text-muted); padding:0.75rem 1rem; border-bottom:1px solid var(--ds-border); background:var(--ds-bg-2); }
        .soft-table tbody td { font-size:0.78rem; color:var(--ds-text-primary); padding:0.875rem 1rem; border-bottom:1px solid rgba(139,92,246,0.06); vertical-align:middle; }

        /* ── ALERTES ── */
        .ds-alert { display:flex; align-items:flex-start; gap:0.75rem; padding:0.875rem 1rem; border-radius:0.75rem; font-size:0.78rem; font-weight:600; margin-bottom:0.75rem; border:1px solid; }
        .ds-alert-success { background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.25); color:#6ee7b7; }
        .ds-alert-danger  { background:rgba(239,68,68,0.1);  border-color:rgba(239,68,68,0.25);  color:#fca5a5; }
        .ds-alert-warning { background:rgba(245,158,11,0.1); border-color:rgba(245,158,11,0.25); color:#fcd34d; }
        .ds-alert-info    { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.25); color:#93c5fd; }
        /* Alias */
        .soft-alert { display:flex; align-items:flex-start; gap:0.75rem; padding:0.875rem 1rem; border-radius:0.75rem; font-size:0.78rem; font-weight:600; margin-bottom:0.75rem; border:1px solid; }
        .soft-alert-success { background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.25); color:#6ee7b7; }
        .soft-alert-danger  { background:rgba(239,68,68,0.1);  border-color:rgba(239,68,68,0.25);  color:#fca5a5; }
        .soft-alert-warning { background:rgba(245,158,11,0.1); border-color:rgba(245,158,11,0.25); color:#fcd34d; }
        .soft-alert-info    { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.25); color:#93c5fd; }

        /* ── DIVIDERS ── */
        .ds-divider { height: 1px; background: var(--ds-border); margin: 0; }

        /* ── SEPARATEUR VERTICAL SIDEBAR ── */
        .sidebar-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(139,92,246,0.2), transparent);
            margin: 0 1rem;
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

    <!-- Overlay mobile -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
         style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:35;" aria-hidden="true"></div>

    <div style="display:flex; min-height:100vh;">

        <!-- ══════════════════════════════
             SIDEBAR DARK VIOLET
        ══════════════════════════════ -->
        <aside id="app-sidebar" :class="sidebarOpen ? 'open' : ''">

            <!-- Logo -->
            <div style="padding:1.25rem 1rem; display:flex; align-items:center; gap:0.75rem;">
                <div style="width:2.25rem; height:2.25rem; border-radius:0.75rem; flex-shrink:0;
                            background-image:var(--g-primary); display:flex; align-items:center;
                            justify-content:center; color:#fff; font-size:0.8rem;
                            box-shadow:0 4px 12px rgba(124,58,237,0.5);">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <span style="display:block; font-size:0.72rem; font-weight:900; color:var(--ds-text-primary); letter-spacing:0.06em;">TECHNITRACK</span>
                    <span style="display:block; font-size:0.58rem; color:var(--ds-violet-400); font-weight:700; text-transform:uppercase; letter-spacing:0.1em;">Console Pro</span>
                </div>
                <button @click="sidebarOpen=false" style="margin-left:auto; color:var(--ds-text-muted); background:none; border:none; cursor:pointer; font-size:0.875rem;" class="md:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-divider"></div>

            <!-- Navigation -->
            <nav style="flex:1; overflow-y:auto; padding:0.75rem 0.625rem 1rem;">

                <!-- Pilotage -->
                <p class="sidebar-section-label">Pilotage</p>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:2px;">
                    <li class="sidebar-nav-item">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <div class="nav-icon-box">
                                <i class="fas fa-chart-pie" style="color:var(--ds-violet-400);"></i>
                            </div>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>
                </ul>

                <!-- Opérations Terrain -->
                <p class="sidebar-section-label">Opérations Terrain</p>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:2px;">
                    <li class="sidebar-nav-item">
                        <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-users" style="color:#60a5fa;"></i></div>
                            <span>Clients & Entreprises</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('chantiers.index') }}" class="{{ request()->routeIs('chantiers.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-building" style="color:#34d399;"></i></div>
                            <span>Chantiers</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('interventions.index') }}"
                           class="{{ (request()->routeIs('interventions.*') && !request()->routeIs('interventions.a-planifier')) ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-clipboard-list" style="color:var(--ds-violet-400);"></i></div>
                            <span>Toutes Interventions</span>
                        </a>
                    </li>
                    @php
                        $countAPlanifier = \App\Models\Intervention::where('statut', \App\Models\Intervention::STATUT_PLANIFIEE)->whereNull('technicien_id')->count();
                    @endphp
                    <li class="sidebar-nav-item">
                        <a href="{{ route('interventions.a-planifier') }}" class="{{ request()->routeIs('interventions.a-planifier') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-calendar-plus" style="color:#fbbf24;"></i></div>
                            <span style="flex:1;">À Planifier</span>
                            @if($countAPlanifier > 0)
                                <span class="ds-badge-pill sg-warning">{{ $countAPlanifier }}</span>
                            @endif
                        </a>
                    </li>
                    @php
                        $countReaffectation = \App\Models\DemandeReaffectation::where('statut', 'En attente')->count();
                    @endphp
                    <li class="sidebar-nav-item">
                        <a href="{{ route('demandes-reaffectation.index') }}" class="{{ request()->routeIs('demandes-reaffectation.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-exclamation-triangle" style="color:#f87171;"></i></div>
                            <span style="flex:1;">Refus Techniciens</span>
                            @if($countReaffectation > 0)
                                <span class="ds-badge-pill sg-danger" style="animation:pulse 2s infinite;">{{ $countReaffectation }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('users.index') }}?role=Technicien"
                           class="{{ (request()->routeIs('users.*') && request()->input('role') === 'Technicien') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-user-cog" style="color:#94a3b8;"></i></div>
                            <span>Équipe Techniciens</span>
                        </a>
                    </li>
                </ul>

                <!-- Rapports & Suivi -->
                <p class="sidebar-section-label">Rapports & Suivi</p>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:2px;">
                    <li class="sidebar-nav-item">
                        <a href="{{ route('formulaires.index') }}" class="{{ request()->routeIs('formulaires.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-file-alt" style="color:#60a5fa;"></i></div>
                            <span>Formulaires Dynamiques</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('rapports.index') }}" class="{{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-file-pdf" style="color:#f87171;"></i></div>
                            <span>Rapports Validation</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('planning.index') }}" class="{{ request()->routeIs('planning.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-calendar-alt" style="color:#34d399;"></i></div>
                            <span>Planning Général</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('gps.index') }}" class="{{ request()->routeIs('gps.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-map-marker-alt" style="color:var(--ds-violet-400);"></i></div>
                            <span>Tracking GPS Live</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="{{ route('materiaux.index') }}" class="{{ request()->routeIs('materiaux.*') ? 'active' : '' }}">
                            <div class="nav-icon-box"><i class="fas fa-boxes" style="color:#fbbf24;"></i></div>
                            <span>Stock & Matériaux</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Sidebar Footer -->
            <div style="padding:0.75rem 0.625rem 1.25rem;">
                <div style="background-image:var(--g-primary); border-radius:0.875rem; padding:1rem; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:-15px; right:-15px; width:70px; height:70px; border-radius:50%; background:rgba(255,255,255,0.06);"></div>
                    <div style="display:flex; align-items:center; gap:0.75rem; position:relative;">
                        <div style="width:2rem; height:2rem; border-radius:0.5rem; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:0.75rem; color:#fff; flex-shrink:0;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:900; color:#fff; margin:0 0 0.1rem;">TechniTrack Pro</p>
                            <p style="font-size:0.6rem; color:rgba(255,255,255,0.65); margin:0;">Support & Administration</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ══════════════════════════════
             MAIN CONTENT
        ══════════════════════════════ -->
        <div style="flex:1; display:flex; flex-direction:column; min-width:0; padding:1rem 1.25rem 2rem;">

            <!-- NAVBAR FLOTTANTE DARK VIOLET -->
            <nav id="app-navbar">
                <!-- Gauche -->
                <div style="display:flex; align-items:center; gap:0.875rem;">
                    <button @click="sidebarOpen=!sidebarOpen"
                            style="width:2rem; height:2rem; border-radius:0.5rem; background:var(--ds-bg-3);
                                   border:1px solid var(--ds-border); color:var(--ds-text-secondary);
                                   cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:0.8rem;"
                            class="md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <p style="font-size:0.6rem; color:var(--ds-text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.08em; margin:0;">
                            <a href="{{ route('dashboard') }}" style="color:var(--ds-violet-400); text-decoration:none;">TechniTrack</a>
                            <span style="margin:0 0.25rem;">›</span>
                            <span style="color:var(--ds-text-secondary);">
                                {{ ucwords(str_replace(['.', '-', '_'], ' ', Route::currentRouteName() ?? 'Console')) }}
                            </span>
                        </p>
                        <h6 style="font-size:0.85rem; font-weight:800; color:var(--ds-text-primary); margin:0; line-height:1.2; text-transform:capitalize;">
                            {{ ucwords(str_replace(['.', '-', '_'], ' ', Route::currentRouteName() ?? 'Administration')) }}
                        </h6>
                    </div>
                </div>

                <!-- Droite -->
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    @php
                        $roleStyle = [
                            'Admin'       => 'background-image:var(--g-primary);',
                            'Super Admin' => 'background-image:var(--g-dark); border:1px solid rgba(139,92,246,0.3);',
                            'Commercial'  => 'background-image:var(--g-info);',
                            'Technicien'  => 'background-image:var(--g-success);',
                            'Client'      => 'background-image:var(--g-warning);',
                        ];
                        $userRole = auth()->user()->getRoleNames()->first() ?? '';
                        $rs = $roleStyle[$userRole] ?? 'background-image:var(--g-dark);';
                    @endphp
                    <span style="{{ $rs }} font-size:0.62rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;
                                 padding:0.25rem 0.75rem; border-radius:9999px; color:#fff; display:none;"
                          class="sm:inline-block">
                        {{ $userRole }}
                    </span>

                    <!-- Avatar -->
                    <div style="display:flex; align-items:center; gap:0.625rem;">
                        <div style="width:2rem; height:2rem; border-radius:0.625rem; background-image:var(--g-primary);
                                    display:flex; align-items:center; justify-content:center; color:#fff;
                                    font-size:0.65rem; font-weight:900; flex-shrink:0;
                                    box-shadow:0 0 12px rgba(124,58,237,0.4);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div style="display:none;" class="md:block">
                            <p style="font-size:0.75rem; font-weight:700; color:var(--ds-text-primary); margin:0; line-height:1.2;">{{ auth()->user()->name }}</p>
                            <p style="font-size:0.62rem; color:var(--ds-text-muted); margin:0;">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <!-- Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ds-btn ds-btn-ghost" style="padding:0.45rem 0.875rem; font-size:0.68rem;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span style="display:none;" class="sm:inline">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="ds-alert ds-alert-success">
                    <i class="fas fa-check-circle" style="margin-top:1px; flex-shrink:0;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="ds-alert ds-alert-danger">
                    <i class="fas fa-exclamation-circle" style="margin-top:1px; flex-shrink:0;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="ds-alert ds-alert-warning">
                    <i class="fas fa-exclamation-triangle" style="margin-top:1px; flex-shrink:0;"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- Page Content -->
            <main style="flex:1;">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
