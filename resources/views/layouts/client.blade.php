<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FieldFlow') }} — Espace Client</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --kt-body-bg: #f5f5f5;
                --kt-sidebar-bg: #1e1e2d;
                --kt-sidebar-border: #2b2b40;
                --kt-sidebar-hover: #1b1b28;
                --kt-primary: #3e97ff;
                --kt-primary-hover: #2884ef;
                --kt-success: #50cd89;
                --kt-warning: #ffc700;
                --kt-danger: #f1416c;
                --kt-info: #7239ea;
                --kt-card-bg: #ffffff;
                --kt-card-border: #eff2f5;
                --kt-text-muted: #a1a5b7;
                --kt-text-dark: #181c32;
                --kt-border-dashed: #e4e6ef;
            }

            * { box-sizing: border-box; margin: 0; padding: 0; }

            html, body {
                height: 100%;
                font-family: 'Inter', sans-serif;
                background-color: var(--kt-body-bg);
                color: var(--kt-text-dark);
                font-size: 13px;
                -webkit-font-smoothing: antialiased;
            }

            .kt-wrapper { display: flex; height: 100vh; overflow: hidden; }

            /* ===== SIDEBAR CLIENT ===== */
            .kt-aside {
                width: 265px;
                background-color: var(--kt-sidebar-bg);
                display: flex;
                flex-direction: column;
                flex-shrink: 0;
                position: relative;
                z-index: 100;
                transition: transform 0.3s ease;
            }

            .kt-aside-logo {
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 26px;
                border-bottom: 1px dashed var(--kt-sidebar-border);
                background-color: var(--kt-sidebar-bg);
                flex-shrink: 0;
            }

            .kt-aside-logo .brand-logo {
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
            }

            .kt-aside-logo .brand-logo .logo-icon {
                width: 34px;
                height: 34px;
                background: linear-gradient(135deg, #50cd89, #20d489);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(80,205,137,0.35);
            }

            .kt-aside-logo .brand-logo .logo-icon svg { color: white; width: 18px; height: 18px; }

            .kt-aside-logo .brand-name {
                font-size: 16px;
                font-weight: 700;
                color: white;
                letter-spacing: 0.5px;
            }

            .kt-aside-menu-wrapper {
                flex: 1;
                overflow-y: auto;
                padding: 12px 0;
                scrollbar-width: thin;
                scrollbar-color: #2b2b40 transparent;
            }

            .kt-aside-menu-wrapper::-webkit-scrollbar { width: 4px; }
            .kt-aside-menu-wrapper::-webkit-scrollbar-track { background: transparent; }
            .kt-aside-menu-wrapper::-webkit-scrollbar-thumb { background: #2b2b40; border-radius: 2px; }

            .menu-section {
                padding: 12px 26px 6px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 1.2px;
                text-transform: uppercase;
                color: #565674;
            }

            .menu-item { padding: 2px 10px; }

            .menu-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 16px;
                border-radius: 8px;
                text-decoration: none;
                color: #a1a5b7;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .menu-link:hover {
                background-color: var(--kt-sidebar-hover);
                color: white;
            }

            .menu-link.active {
                background: linear-gradient(135deg, rgba(80,205,137,0.15), rgba(80,205,137,0.08));
                color: var(--kt-success);
            }

            .menu-link.active .menu-icon { color: var(--kt-success); opacity: 1; }

            .menu-icon {
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                opacity: 0.75;
            }

            .menu-link:hover .menu-icon { opacity: 1; }
            .menu-title { flex: 1; }

            /* ===== MAIN CONTENT ===== */
            .kt-content { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden; }

            .kt-header {
                height: 70px;
                background-color: #ffffff;
                border-bottom: 1px solid #eff2f5;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 30px;
                flex-shrink: 0;
                box-shadow: 0 1px 0 0 #eff2f5;
                position: sticky;
                top: 0;
                z-index: 50;
            }

            .kt-header-left { display: flex; align-items: center; gap: 20px; }

            .kt-breadcrumb { display: flex; flex-direction: column; }

            .kt-breadcrumb-title {
                font-size: 16px;
                font-weight: 700;
                color: var(--kt-text-dark);
                line-height: 1.3;
            }

            .kt-breadcrumb-nav {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: var(--kt-text-muted);
                margin-top: 2px;
            }

            .kt-breadcrumb-nav a { color: var(--kt-text-muted); text-decoration: none; }
            .kt-breadcrumb-nav a:hover { color: var(--kt-success); }
            .kt-breadcrumb-separator { font-size: 10px; }

            .kt-header-right { display: flex; align-items: center; gap: 12px; }

            .kt-user-menu {
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                padding: 8px 14px;
                border-radius: 8px;
                transition: background 0.2s;
                position: relative;
            }

            .kt-user-menu:hover { background: #f5f8fa; }

            .kt-user-avatar {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                background: linear-gradient(135deg, #50cd89, #20d489);
                color: white;
                font-size: 13px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 8px rgba(80,205,137,0.3);
            }

            .kt-user-info { display: flex; flex-direction: column; }
            .kt-user-name { font-size: 13px; font-weight: 600; color: var(--kt-text-dark); }
            .kt-user-role { font-size: 11px; color: var(--kt-text-muted); font-weight: 500; }

            .kt-dropdown { position: relative; }

            .kt-dropdown-menu {
                display: none;
                position: absolute;
                top: calc(100% + 8px);
                right: 0;
                background: white;
                border: 1px solid #eff2f5;
                border-radius: 10px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.12);
                min-width: 200px;
                z-index: 200;
                padding: 8px 0;
                animation: fadeIn 0.15s ease;
            }

            .kt-dropdown.open .kt-dropdown-menu { display: block; }

            .kt-dropdown-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 18px;
                font-size: 13px;
                color: var(--kt-text-dark);
                text-decoration: none;
                transition: background 0.15s;
                cursor: pointer;
                border: none;
                background: none;
                width: 100%;
                text-align: left;
                font-family: 'Inter', sans-serif;
                font-weight: 500;
            }

            .kt-dropdown-item:hover { background: #f5f8fa; color: var(--kt-success); }
            .kt-dropdown-separator { height: 1px; background: #eff2f5; margin: 6px 0; }

            .kt-page-wrapper {
                flex: 1;
                overflow-y: auto;
                padding: 28px 30px;
            }

            /* ===== CARDS & UTILS ===== */
            .kt-card {
                background: white;
                border-radius: 12px;
                border: 1px solid var(--kt-card-border);
                box-shadow: 0 0 20px 0 rgba(76,87,125,.02);
                overflow: hidden;
            }

            .kt-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 18px 24px;
                border-bottom: 1px dashed var(--kt-border-dashed);
                min-height: 60px;
            }

            .kt-card-title { font-size: 15px; font-weight: 700; color: var(--kt-text-dark); }
            .kt-card-body { padding: 24px; }

            .kt-stat-card {
                background: white;
                border-radius: 12px;
                border: 1px solid var(--kt-card-border);
                padding: 22px 24px;
                box-shadow: 0 0 20px 0 rgba(76,87,125,.02);
                display: flex;
                align-items: center;
                gap: 16px;
                transition: box-shadow 0.2s, transform 0.2s;
                text-decoration: none;
            }

            .kt-stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); transform: translateY(-1px); }

            .kt-stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .kt-stat-icon svg { width: 24px; height: 24px; }
            .kt-stat-label { font-size: 12px; font-weight: 600; color: var(--kt-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
            .kt-stat-value { font-size: 26px; font-weight: 800; color: var(--kt-text-dark); line-height: 1; }

            .kt-table { width: 100%; border-collapse: collapse; }
            .kt-table thead tr th {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                color: var(--kt-text-muted);
                padding: 10px 14px;
                border-bottom: 1px dashed var(--kt-border-dashed);
                white-space: nowrap;
                background: #f9f9f9;
            }

            .kt-table tbody tr { border-bottom: 1px dashed #f0f0f0; transition: background 0.15s; }
            .kt-table tbody tr:hover { background: #f5f8ff; }
            .kt-table tbody td { padding: 14px 14px; font-size: 13px; color: #3f4254; vertical-align: middle; }

            .kt-badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.3px;
            }

            .kt-badge-primary { background: rgba(62,151,255,0.12); color: var(--kt-primary); }
            .kt-badge-success { background: rgba(80,205,137,0.12); color: #47be7d; }
            .kt-badge-warning { background: rgba(255,199,0,0.12); color: #e9b500; }
            .kt-badge-danger  { background: rgba(241,65,108,0.12); color: var(--kt-danger); }
            .kt-badge-info    { background: rgba(114,57,234,0.12); color: var(--kt-info); }
            .kt-badge-gray    { background: #f3f4f8; color: #7e8299; }

            .kt-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 9px 18px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.2s;
                border: 1px solid transparent;
                white-space: nowrap;
                font-family: 'Inter', sans-serif;
            }

            .kt-btn-sm { padding: 6px 12px; font-size: 12px; }
            .kt-btn-primary { background: var(--kt-primary); color: white; }
            .kt-btn-primary:hover { background: var(--kt-primary-hover); }
            .kt-btn-success { background: var(--kt-success); color: white; }
            .kt-btn-success:hover { background: #47be7d; }
            .kt-btn-light { background: #f5f8fa; color: #5e6278; border-color: #eff2f5; }
            .kt-btn-light:hover { background: #eff2f5; }
            .kt-btn-light-primary { background: rgba(62,151,255,0.1); color: var(--kt-primary); }
            .kt-btn-light-primary:hover { background: var(--kt-primary); color: white; }
            .kt-btn-light-success { background: rgba(80,205,137,0.1); color: #47be7d; }
            .kt-btn-light-success:hover { background: var(--kt-success); color: white; }

            .kt-form-group { margin-bottom: 20px; }
            .kt-form-label { display: block; font-size: 13px; font-weight: 600; color: var(--kt-text-dark); margin-bottom: 6px; }
            .kt-form-control {
                display: block;
                width: 100%;
                padding: 10px 14px;
                font-size: 13px;
                font-family: 'Inter', sans-serif;
                background: #f5f8fa;
                border: 1.5px solid transparent;
                border-radius: 8px;
                color: var(--kt-text-dark);
                transition: border-color 0.2s, background 0.2s;
                outline: none;
            }

            .kt-form-control:focus { background: white; border-color: var(--kt-success); box-shadow: 0 0 0 3px rgba(80,205,137,0.1); }
            .kt-form-error { color: var(--kt-danger); font-size: 11px; margin-top: 4px; font-weight: 500; }
            .kt-separator { border: none; border-top: 1px dashed var(--kt-border-dashed); }

            .kt-alert {
                display: flex;
                align-items: flex-start;
                gap: 14px;
                padding: 16px 20px;
                border-radius: 10px;
                margin-bottom: 20px;
                font-size: 13px;
                font-weight: 500;
            }
            .kt-alert-success { background: rgba(80,205,137,0.12); color: #47be7d; border: 1px solid rgba(80,205,137,0.2); }

            .kt-empty-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 60px 20px;
                color: var(--kt-text-muted);
            }
            .kt-empty-state svg { width: 48px; height: 48px; opacity: 0.4; margin-bottom: 12px; }

            .kt-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }

            @media (max-width: 768px) {
                .kt-aside { position: fixed; left: 0; top: 0; bottom: 0; transform: translateX(-100%); z-index: 110; }
                .kt-aside.mobile-open { transform: translateX(0); }
                .kt-grid-4 { grid-template-columns: 1fr; }
            }

            @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    </head>
    <body>
        <div class="kt-wrapper" x-data="{ sidebarOpen: false }">

            <!-- Overlay mobile -->
            <div class="kt-overlay" :class="sidebarOpen ? 'mobile-open' : ''" @click="sidebarOpen = false"></div>

            <!-- ===== ASIDE SIDEBAR CLIENT ===== -->
            <aside class="kt-aside" :class="sidebarOpen ? 'mobile-open' : ''">

                <!-- Logo -->
                <div class="kt-aside-logo">
                    <a href="{{ route('client.dashboard') }}" class="brand-logo">
                        <div class="logo-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="brand-name">FieldFlow</span>
                    </a>
                </div>

                <!-- Menu Client Uniquement -->
                <div class="kt-aside-menu-wrapper">
                    <div class="menu-section">Menu Client</div>

                    <!-- Dashboard -->
                    <div class="menu-item">
                        <a href="{{ route('client.dashboard') }}" class="menu-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>

                    <!-- Mon Profil -->
                    <div class="menu-item">
                        <a href="{{ route('client.profile.edit') }}" class="menu-link {{ request()->routeIs('client.profile.*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <span class="menu-title">Mon Profil</span>
                        </a>
                    </div>

                    <!-- Mes Chantiers -->
                    <div class="menu-item">
                        <a href="{{ route('client.chantiers.index') }}" class="menu-link {{ request()->routeIs('client.chantiers.*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </span>
                            <span class="menu-title">Mes chantiers</span>
                        </a>
                    </div>

                    <!-- Mes Interventions -->
                    <div class="menu-item">
                        <a href="{{ route('client.interventions.index') }}" class="menu-link {{ request()->routeIs('client.interventions.*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                            <span class="menu-title">Mes interventions</span>
                        </a>
                    </div>

                    <!-- Rapports -->
                    <div class="menu-item">
                        <a href="{{ route('client.rapports.index') }}" class="menu-link {{ request()->routeIs('client.rapports.*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span class="menu-title">Rapports</span>
                        </a>
                    </div>

                    <!-- Documents -->
                    <div class="menu-item">
                        <a href="{{ route('client.documents.index') }}" class="menu-link {{ request()->routeIs('client.documents.*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="menu-title">Documents</span>
                        </a>
                    </div>

                    <!-- Notifications -->
                    <div class="menu-item">
                        <a href="{{ route('client.notifications.index') }}" class="menu-link {{ request()->routeIs('client.notifications.*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </span>
                            <span class="menu-title">Notifications</span>
                        </a>
                    </div>
                </div>

                <!-- Footer Sidebar -->
                <div style="padding: 16px 26px; border-top: 1px dashed #2b2b40; flex-shrink: 0;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#50cd89,#20d489); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:white; flex-shrink:0;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:12px; font-weight:600; color:white; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
                            <div style="font-size:10px; color:#565674; font-weight:500; text-transform:uppercase; letter-spacing:0.5px; margin-top:1px;">Client</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Déconnexion" style="background:none; border:none; cursor:pointer; color:#565674; padding:4px; border-radius:4px; display:flex; align-items:center; justify-content:center; transition:color 0.2s;" onmouseover="this.style.color='#f1416c'" onmouseout="this.style.color='#565674'">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- ===== MAIN CONTENT ===== -->
            <div class="kt-content">

                <!-- HEADER -->
                <header class="kt-header">
                    <div class="kt-header-left">
                        <button class="kt-mobile-toggle" @click="sidebarOpen = true">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="kt-breadcrumb">
                            <div class="kt-breadcrumb-nav">
                                <a href="{{ route('client.dashboard') }}">FieldFlow</a>
                                <span class="kt-breadcrumb-separator">›</span>
                                <span>Espace Client</span>
                            </div>
                            @isset($header)
                                <div class="kt-breadcrumb-title">{{ $header }}</div>
                            @else
                                <div class="kt-breadcrumb-title">Espace Client</div>
                            @endisset
                        </div>
                    </div>

                    <div class="kt-header-right">
                        <!-- User dropdown -->
                        <div class="kt-dropdown" id="userDropdownClient">
                            <div class="kt-user-menu" onclick="document.getElementById('userDropdownClient').classList.toggle('open')">
                                <div class="kt-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                                <div class="kt-user-info">
                                    <div class="kt-user-name">{{ Auth::user()->name }}</div>
                                    <div class="kt-user-role">Client</div>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#a1a5b7" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <div class="kt-dropdown-menu">
                                <a href="{{ route('client.profile.edit') }}" class="kt-dropdown-item">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Mon Profil
                                </a>
                                <div class="kt-dropdown-separator"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="kt-dropdown-item" style="color:#f1416c;">
                                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- PAGE CONTENT -->
                <main class="kt-page-wrapper">
                    {{ $slot }}
                </main>

            </div>
        </div>

        <script>
            document.addEventListener('click', function(e) {
                const dropdown = document.getElementById('userDropdownClient');
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });
        </script>
    </body>
</html>
