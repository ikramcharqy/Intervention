<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>TechniTrack — Application Terrain Technicien</title>

    <!-- ── PWA Manifest & Meta ── -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="TechniTrack">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="Application terrain technicien pour la gestion et le suivi des interventions sur chantier.">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome & Google Fonts Open Sans Soft UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Open Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --soft-primary-start: #6366f1;
            --soft-primary-end: #4f46e5;
        }
        body { font-family: 'Open Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .soft-card {
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 27px 0 rgba(0,0,0,0.05);
        }
        .soft-gradient-primary {
            background-image: linear-gradient(310deg, var(--soft-primary-start) 0%, var(--soft-primary-end) 100%);
        }
        .glass-panel { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05); }
        .glass-nav { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(16px); border-top: 1px solid #e2e8f0; box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .animate-fade-in { animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased selection:bg-brand-500 selection:text-white overflow-hidden">

    <div id="app" class="h-full flex flex-col max-w-md mx-auto relative bg-slate-50 shadow-2xl border-x border-slate-200/90">
        
        <!-- TOP HEADER BAR -->
        <header id="top-bar" class="shrink-0 h-14 glass-panel px-4 flex items-center justify-between z-30 bg-white/90 border-b border-slate-200">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center shadow-md shadow-brand-500/20">
                    <i class="fa-solid fa-wrench text-white text-xs"></i>
                </div>
                <span class="font-extrabold text-sm tracking-wide text-slate-900">Techni<span class="text-brand-600">Track</span></span>
            </div>
            
            <div class="flex items-center space-x-2">
                <button onclick="toggleConfigModal()" title="Configuration API" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition border border-slate-200">
                    <i class="fa-solid fa-gear text-xs"></i>
                </button>
                <div id="user-avatar-badge" onclick="navigateTo('profile')" class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-200 overflow-hidden cursor-pointer flex items-center justify-center text-xs font-bold text-brand-600">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </header>

        <!-- OFFLINE STATUS & AUTOMATIC SYNC BANNER -->
        <div id="network-offline-banner" class="hidden shrink-0 bg-slate-900 border-b border-slate-800 px-4 py-2 flex items-center justify-between text-xs text-white shadow-lg z-30 animate-fade-in">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="font-bold text-[11px]">Hors-Ligne : <span id="offline-pending-count" class="text-amber-400 font-extrabold">0 action(s)</span> stockée(s)</span>
            </div>
            <button onclick="triggerManualSync()" class="px-2.5 py-1 bg-brand-600 hover:bg-brand-500 text-white rounded-lg font-bold text-[10px] uppercase tracking-wider transition shadow flex items-center space-x-1">
                <i class="fa-solid fa-rotate text-[9px]"></i>
                <span>Synchro</span>
            </button>
        </div>

        <!-- GLOBAL GPS TRACKING BANNER (when session active) -->
        <div id="tracking-active-banner" class="hidden shrink-0 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 px-4 py-2 flex items-center justify-between text-xs text-white shadow-md z-20 animate-fade-in">
            <div class="flex items-center space-x-2">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                </span>
                <span class="font-bold">Tracking GPS En cours : <span id="tracking-timer">00:00</span></span>
            </div>
            <button onclick="stopGpsTracking()" class="px-2.5 py-1 bg-black/20 hover:bg-black/40 text-white rounded font-bold text-[10px] uppercase tracking-wider transition">
                Arrêter
            </button>
        </div>

        <!-- NOTIFICATION TOAST CONTAINER -->
        <div id="toast-container" class="fixed top-16 left-1/2 -translate-x-1/2 max-w-xs w-full px-4 z-50 pointer-events-none space-y-2"></div>

        <!-- MAIN VIEW CONTAINER (Scrollable) -->
        <main id="main-content" class="flex-1 overflow-y-auto no-scrollbar p-4 space-y-4 relative pb-24">
            
            <!-- VIEW 0: LOGIN -->
            <section id="view-login" class="h-full flex flex-col justify-center py-6 px-2 animate-fade-in">
                <div class="text-center space-y-3 mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center mx-auto shadow-xl shadow-brand-500/20 border border-white">
                        <i class="fa-solid fa-screwdriver-wrench text-2xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900">Console Technicien</h1>
                    <p class="text-xs text-slate-500">Connectez-vous pour accéder à vos interventions</p>
                </div>

                <form id="form-login" onsubmit="handleLogin(event)" class="glass-panel p-6 rounded-2xl space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Adresse Email</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                            <input type="email" id="login-email" required value="yassine.tech@test.com" placeholder="technicien@domaine.com" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-100 shadow-sm transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Mot de passe</label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                            <input type="password" id="login-password" required value="password" placeholder="••••••••" class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-10 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-100 shadow-sm transition">
                            <button type="button" onclick="togglePasswordVisibility('login-password')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div id="login-error-msg" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs leading-relaxed font-semibold"></div>

                    <button type="submit" id="btn-login-submit" class="w-full py-3 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold rounded-xl text-xs shadow-md shadow-brand-600/20 transition flex items-center justify-center space-x-2">
                        <span>Se Connecter</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </section>

            <!-- VIEW 1: DASHBOARD -->
            <section id="view-dashboard" class="hidden space-y-4 animate-fade-in">
                <!-- Welcome Card -->
                <div class="glass-panel p-4 rounded-2xl bg-white border border-slate-200 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-brand-700 bg-brand-50 px-2.5 py-0.5 rounded-md border border-brand-200">Espace Terrain</span>
                        <h2 class="text-base font-bold text-slate-900 mt-1" id="dash-user-name">Bonjour Technicien 🛠️</h2>
                        <p class="text-[11px] text-slate-500">Aperçu rapide de votre activité aujourd'hui</p>
                    </div>
                    <button onclick="loadDashboardData()" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition border border-slate-200">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    </button>
                </div>

                <!-- KPI Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="glass-panel p-3.5 rounded-xl border border-slate-200 bg-white flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                            <i class="fa-solid fa-clock text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase block">À Réaliser</span>
                            <span id="dash-kpi-upcoming" class="text-lg font-extrabold text-slate-900">0</span>
                        </div>
                    </div>

                    <div class="glass-panel p-3.5 rounded-xl border border-slate-200 bg-white flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 shrink-0">
                            <i class="fa-solid fa-spinner text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase block">En Cours</span>
                            <span id="dash-kpi-inprogress" class="text-lg font-extrabold text-slate-900">0</span>
                        </div>
                    </div>

                    <div class="glass-panel p-3.5 rounded-xl border border-slate-200 bg-white flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="fa-solid fa-circle-check text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase block">Terminées</span>
                            <span id="dash-kpi-completed" class="text-lg font-extrabold text-slate-900">0</span>
                        </div>
                    </div>

                    <div class="glass-panel p-3.5 rounded-xl border border-slate-200 bg-white flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shrink-0">
                            <i class="fa-solid fa-bell text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase block">Non lues</span>
                            <span id="dash-kpi-unread-notifs" class="text-lg font-extrabold text-slate-900">0</span>
                        </div>
                    </div>
                </div>

                <!-- Active Mission Banner (if any) -->
                <div id="dash-active-mission-card" class="hidden glass-panel p-4 rounded-xl border border-amber-300 bg-amber-50 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-200/80 px-2 py-0.5 rounded">Mission Actuelle</span>
                        <span id="dash-active-code" class="text-xs font-bold text-amber-900">INT-XXXX</span>
                    </div>
                    <h3 id="dash-active-title" class="text-sm font-bold text-slate-900">Nom du chantier</h3>
                    <div class="flex space-x-2 pt-1">
                        <button onclick="openInterventionDetails(currentActiveInterventionId)" class="flex-1 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-xs transition text-center shadow-sm">
                            Gérer la mission →
                        </button>
                    </div>
                </div>

                <!-- Upcoming Missions List -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between px-1">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Missions Récentes</h3>
                        <button onclick="navigateTo('interventions')" class="text-xs font-bold text-brand-600 hover:underline">Voir tout →</button>
                    </div>
                    <div id="dash-interventions-list" class="space-y-2">
                        <!-- Rendered dynamically -->
                    </div>
                </div>
            </section>

            <!-- VIEW 2: INTERVENTIONS LIST -->
            <section id="view-interventions" class="hidden space-y-3 animate-fade-in">
                <!-- Search & Filters -->
                <div class="space-y-2">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" id="interv-search-input" oninput="filterInterventionsList()" placeholder="Rechercher par code, chantier..." class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-brand-600 shadow-sm">
                    </div>

                    <!-- Filter Chips -->
                    <div class="flex space-x-2 overflow-x-auto no-scrollbar py-1 text-xs">
                        <button onclick="setIntervFilter('all')" class="interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-brand-600 text-white shadow-sm" data-filter="all">Toutes</button>
                        <button onclick="setIntervFilter('Planifiee')" class="interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50" data-filter="Planifiee">Planifiées</button>
                        <button onclick="setIntervFilter('Acceptee')" class="interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50" data-filter="Acceptee">Acceptées</button>
                        <button onclick="setIntervFilter('En cours')" class="interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50" data-filter="En cours">En cours</button>
                        <button onclick="setIntervFilter('Terminee')" class="interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50" data-filter="Terminee">Terminées</button>
                    </div>
                </div>

                <!-- Interventions Container -->
                <div id="interv-list-container" class="space-y-2.5">
                    <!-- Rendered dynamically -->
                </div>
            </section>

            <!-- VIEW 3: INTERVENTION DETAIL -->
            <section id="view-intervention-detail" class="hidden space-y-4 animate-fade-in">
                <!-- Back Header -->
                <div class="flex items-center justify-between">
                    <button onclick="navigateTo('interventions')" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        <span>Retour liste</span>
                    </button>
                    <span id="detail-code-badge" class="text-xs font-extrabold text-slate-800 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-sm">INT-XXXX</span>
                </div>

                <!-- Main Info Card -->
                <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <span id="detail-type-name" class="text-[10px] font-bold text-brand-600 uppercase tracking-wider">Type Intervention</span>
                            <h2 id="detail-chantier-name" class="text-base font-bold text-slate-900">Nom du chantier</h2>
                            <p id="detail-emplacement-name" class="text-xs text-slate-500">Emplacement : N/A</p>
                        </div>
                        <span id="detail-status-pill" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase">Statut</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase block font-semibold">Priorité</span>
                            <span id="detail-priorite-badge" class="font-bold text-slate-800">Faible</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase block font-semibold">Date prévue</span>
                            <span id="detail-date-prevue" class="font-bold text-slate-800">--/--/----</span>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS SECTION -->
                <div id="detail-actions-box" class="glass-panel p-3.5 rounded-xl space-y-2 bg-white border border-slate-200 shadow-sm">
                    <!-- Actions Grid -->
                    <div id="detail-actions-container" class="space-y-2">
                        <!-- Accept / Refuse Grid (Statut Affectee / Planifiee) -->
                        <div id="btn-group-accept-refuse" class="hidden grid grid-cols-2 gap-2">
                            <button id="btn-action-accept" onclick="executeAcceptIntervention()" class="py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-check-circle text-xs"></i>
                                <span>Accepter</span>
                            </button>
                            <button id="btn-action-refuse" onclick="openRefuseModal()" class="py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs rounded-xl transition flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-ban text-xs"></i>
                                <span>Refuser</span>
                            </button>
                        </div>

                        <!-- Banner En attente de réaffectation -->
                        <div id="banner-reassignment-pending" class="hidden p-3 bg-amber-50 border border-amber-300 text-amber-800 rounded-xl text-xs space-y-1">
                            <div class="flex items-center space-x-2 font-bold">
                                <i class="fa-solid fa-hourglass-half text-amber-600 animate-spin"></i>
                                <span>Demande de réaffectation en cours</span>
                            </div>
                            <p class="text-[11px] text-amber-700">Votre demande de refus est en cours d'examen par l'administrateur.</p>
                        </div>

                        <!-- Start Button -->
                        <button id="btn-action-start" onclick="openStartInterventionModal()" class="hidden w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-play text-xs"></i>
                            <span>Démarrer l'intervention</span>
                        </button>

                        <!-- Dynamic Form Button -->
                        <button id="btn-action-form" onclick="openFormulaireScreen()" class="hidden w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>Saisir le Formulaire Terrain</span>
                        </button>

                        <!-- Rapport Button -->
                        <button id="btn-action-rapport" onclick="openRapportScreen()" class="hidden w-full py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-file-lines text-xs"></i>
                            <span>Gérer le Rapport & Preuves</span>
                        </button>

                        <!-- Finish Intervention Button (Technicien) -->
                        <button id="btn-action-finish" onclick="executeFinishIntervention()" class="hidden w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-flag-checkered text-xs"></i>
                            <span>Finir l'intervention & Soumettre à l'Administration</span>
                        </button>

                        <!-- Banner Intervention Clôturée (Mode lecture seule) -->
                        <div id="banner-sealed-info" class="hidden p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center space-x-2">
                            <i class="fa-solid fa-lock text-emerald-600 text-sm shrink-0"></i>
                            <div>
                                <p class="font-extrabold text-xs text-emerald-900">Intervention Clôturée</p>
                                <p class="text-[10px] text-emerald-700">La version finale a été scellée et transmise à l'administration.</p>
                            </div>
                        </div>

                        <!-- Validate & Close Button (Admin / Authorized Tech) -->
                        <button id="btn-action-validate" onclick="executeValidateIntervention()" class="hidden w-full py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Valider & Clôturer</span>
                        </button>
                    </div>

                <!-- TABBED DETAILS SECTION -->
                <div class="space-y-3">
                    <div class="flex space-x-1 border-b border-slate-200 text-xs">
                        <button onclick="switchDetailTab('info')" class="detail-tab-btn border-b-2 border-brand-600 text-brand-600 font-bold px-3 py-2" data-tab="info">Infos</button>
                        <button onclick="switchDetailTab('taches')" class="detail-tab-btn border-b-2 border-transparent text-slate-500 font-bold px-3 py-2 hover:text-slate-800" data-tab="taches">Tâches</button>
                        <button onclick="switchDetailTab('materiaux')" class="detail-tab-btn border-b-2 border-transparent text-slate-500 font-bold px-3 py-2 hover:text-slate-800" data-tab="materiaux">Matériaux</button>
                        <button onclick="switchDetailTab('historique')" class="detail-tab-btn border-b-2 border-transparent text-slate-500 font-bold px-3 py-2 hover:text-slate-800" data-tab="historique">Historique</button>
                    </div>

                    <!-- TAB CONTENT: INFO -->
                    <div id="tab-content-info" class="detail-tab-pane glass-panel p-4 rounded-xl text-xs space-y-2.5 bg-white border border-slate-200">
                        <div>
                            <span class="text-slate-500 font-semibold block">Description</span>
                            <p id="detail-desc-text" class="text-slate-800 leading-relaxed mt-0.5">Aucune description disponible.</p>
                        </div>
                        <div>
                            <span class="text-slate-500 font-semibold block">Mode de Suivi configuré</span>
                            <span id="detail-mode-suivi-badge" class="font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded text-[10px] inline-block mt-0.5 border border-amber-200">MANUEL</span>
                        </div>
                    </div>

                    <!-- TAB CONTENT: TACHES -->
                    <div id="tab-content-taches" class="detail-tab-pane hidden glass-panel p-4 rounded-xl text-xs space-y-2 bg-white border border-slate-200">
                        <div id="detail-taches-list" class="space-y-2">
                            <!-- Tasks list -->
                        </div>
                    </div>

                    <!-- TAB CONTENT: MATERIAUX -->
                    <div id="tab-content-materiaux" class="detail-tab-pane hidden space-y-4">

                        <!-- Matériaux déjà utilisés -->
                        <div class="glass-panel rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-indigo-50 to-slate-50 border-b border-slate-200">
                                <div class="flex items-center space-x-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <i class="fa-solid fa-boxes-stacked text-indigo-600 text-xs"></i>
                                    </span>
                                    <span class="text-xs font-bold text-slate-800">Matériaux utilisés</span>
                                </div>
                                <span id="mat-count-badge" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">0</span>
                            </div>
                            <div id="detail-materiaux-list" class="divide-y divide-slate-100 min-h-[60px]">
                                <div class="flex items-center justify-center py-6 text-xs text-slate-400">
                                    <i class="fa-solid fa-box-open mr-2 text-slate-300 text-sm"></i>
                                    Aucun matériau enregistré
                                </div>
                            </div>
                        </div>

                        <!-- Ajouter un matériau -->
                        <div id="add-materiau-panel" class="glass-panel rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <div class="flex items-center space-x-2 px-4 py-3 bg-gradient-to-r from-emerald-50 to-slate-50 border-b border-slate-200">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                                    <i class="fa-solid fa-plus text-emerald-600 text-xs"></i>
                                </span>
                                <span class="text-xs font-bold text-slate-800">Ajouter un matériau</span>
                            </div>

                            <div class="p-4 space-y-3">
                                <!-- Recherche dans le catalogue -->
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="mat-search-input"
                                        placeholder="Rechercher un matériau..."
                                        class="w-full pl-9 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition"
                                        oninput="searchCatalogueMateriau(this.value)"
                                        autocomplete="off"
                                    >
                                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>

                                <!-- Dropdown résultats -->
                                <div id="mat-catalogue-dropdown" class="hidden rounded-xl border border-slate-200 bg-white shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100 text-xs">
                                    <!-- Rempli dynamiquement -->
                                </div>

                                <!-- Matériau sélectionné -->
                                <div id="mat-selected-preview" class="hidden p-3 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-between">
                                    <div>
                                        <p id="mat-selected-nom" class="text-xs font-bold text-indigo-800"></p>
                                        <p id="mat-selected-ref" class="text-[10px] text-indigo-500"></p>
                                    </div>
                                    <button type="button" onclick="clearSelectedMateriau()" class="text-indigo-400 hover:text-indigo-600">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="mat-selected-id" value="">

                                <!-- Quantité + Unité -->
                                <div class="flex space-x-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Quantité *</label>
                                        <input
                                            type="number"
                                            id="mat-quantite-input"
                                            step="0.01"
                                            min="0.01"
                                            placeholder="0.00"
                                            class="w-full px-3 py-2.5 text-xs border border-slate-200 bg-slate-50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition"
                                        >
                                    </div>
                                    <div class="w-24">
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Unité</label>
                                        <div id="mat-unite-display" class="px-3 py-2.5 text-xs border border-slate-200 bg-slate-100 rounded-xl text-slate-500 text-center">—</div>
                                    </div>
                                </div>

                                <!-- Commentaire optionnel -->
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">Commentaire (optionnel)</label>
                                    <textarea
                                        id="mat-commentaire-input"
                                        rows="2"
                                        placeholder="Ex : matériau posé en zone nord..."
                                        class="w-full px-3 py-2 text-xs border border-slate-200 bg-slate-50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition resize-none"
                                    ></textarea>
                                </div>

                                <!-- Bouton Enregistrer -->
                                <button
                                    type="button"
                                    onclick="saveMateriau()"
                                    id="btn-save-materiau"
                                    class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center justify-center space-x-2 disabled:opacity-50 disabled:pointer-events-none"
                                >
                                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                                    <span>Enregistrer le matériau</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB CONTENT: HISTORIQUE -->
                    <div id="tab-content-historique" class="detail-tab-pane hidden glass-panel p-4 rounded-xl text-xs space-y-2 bg-white border border-slate-200">
                        <div id="detail-historique-list" class="space-y-2">
                            <!-- History list -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- VIEW 4: FORMULAIRE DYNAMIQUE -->
            <section id="view-formulaire" class="hidden space-y-4 animate-fade-in">
                <div class="flex items-center justify-between">
                    <button onclick="navigateTo('intervention-detail')" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        <span>Fiche Intervention</span>
                    </button>
                    <span class="text-xs font-bold text-indigo-600">Formulaire Terrain</span>
                </div>

                <form id="dynamic-form-element" onsubmit="handleFormulaireSubmit(event)" class="glass-panel p-4 sm:p-5 rounded-2xl space-y-4 bg-white border border-slate-200 shadow-sm">
                    <div id="dynamic-form-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dynamic fields rendered from API response -->
                    </div>

                    <button type="submit" id="btn-submit-formulaire" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-brand-600 hover:from-indigo-500 hover:to-brand-500 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Soumettre à l'Administration</span>
                    </button>
                </form>
            </section>

            <!-- VIEW 5: RAPPORT -->
            <section id="view-rapport" class="hidden space-y-4 animate-fade-in">
                <div class="flex items-center justify-between">
                    <button onclick="navigateTo('intervention-detail')" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        <span>Fiche Intervention</span>
                    </button>
                    <span class="text-xs font-bold text-purple-600">Rapport d'Intervention</span>
                </div>

                <form id="rapport-form-element" onsubmit="handleRapportSubmit(event)" enctype="multipart/form-data" class="space-y-3">

                    <!-- Section : Travaux Réalisés -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-purple-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-screwdriver-wrench"></i><span>Travaux Réalisés</span>
                        </h3>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Description des travaux effectués <span class="text-red-500">*</span></label>
                            <p class="text-[11px] text-slate-400">Installations, configurations, tests effectués sur le terrain…</p>
                            <textarea id="rapport-travaux" required rows="4" placeholder="Décrivez en détail les actions réalisées..." class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100 shadow-sm resize-none"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">État de l'équipement après intervention</label>
                            <select id="rapport-statut-equipement" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-purple-600 shadow-sm">
                                <option value="Conforme">✅ Conforme – Fonctionnel</option>
                                <option value="Partiellement conforme">🟡 Partiellement conforme</option>
                                <option value="Non conforme">🔴 Non conforme – Défaut persistant</option>
                                <option value="Remplacé">🔄 Remplacé / Nouveau matériel</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section : Observations & Recommandations -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-amber-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-triangle-exclamation"></i><span>Observations & Recommandations</span>
                        </h3>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Observations terrain</label>
                            <p class="text-[11px] text-slate-400">Anomalies constatées, réserves émises par le client…</p>
                            <textarea id="rapport-observations" rows="3" placeholder="Ex: câble endommagé côté armoire, signal instable..." class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-100 shadow-sm resize-none"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Recommandations pour le suivi</label>
                            <p class="text-[11px] text-slate-400">Actions préventives suggérées, prochaine maintenance…</p>
                            <textarea id="rapport-recommandations" rows="3" placeholder="Ex: prévoir remplacement du switch dans 3 mois..." class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-100 shadow-sm resize-none"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Commentaire interne</label>
                            <input type="text" id="rapport-commentaire" placeholder="Note interne visible uniquement par l'équipe..." class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 shadow-sm">
                        </div>
                    </div>

                    <!-- Section : Géolocalisation GPS Terrain -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-blue-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-location-dot"></i><span>Géolocalisation GPS Terrain</span>
                        </h3>
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" id="rapport-gps-lat" readonly placeholder="Latitude" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 font-mono shadow-sm">
                                <input type="text" id="rapport-gps-lng" readonly placeholder="Longitude" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 font-mono shadow-sm">
                            </div>
                            <input type="text" id="rapport-gps-addr" placeholder="Adresse ou repère géographique..." class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                            <button type="button" onclick="getDeviceGpsPosition()" class="w-full py-2 bg-blue-50 hover:bg-blue-100 border border-blue-300 text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2 shadow-sm">
                                <i class="fa-solid fa-crosshairs text-xs"></i>
                                <span>Capturer ma Position GPS Actuelle</span>
                            </button>
                        </div>
                    </div>

                    <!-- Section : Identification QR Code -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-cyan-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-qrcode"></i><span>Identification QR Code</span>
                        </h3>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">QR Code équipement scanné</label>
                            <p class="text-[11px] text-slate-400">Identifiant unique de l'équipement traité.</p>
                            <div class="flex space-x-2">
                                <input type="text" id="rapport-qrcode" placeholder="Ex: EQ-2024-FIBRE-001" class="flex-1 bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-cyan-600 font-mono shadow-sm">
                                <button type="button" onclick="scanQrCodeForRapport()" class="px-4 py-2.5 bg-cyan-50 hover:bg-cyan-100 border border-cyan-300 text-cyan-700 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                                    <i class="fa-solid fa-camera text-xs"></i>
                                    <span>Scan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section : Photos du rapport avec catégorie -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-camera"></i><span>Photos d'Intervention (Avant / Après / Problèmes)</span>
                        </h3>
                        
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Catégorie des prochaines photos :</label>
                            <div class="flex space-x-2">
                                <label class="flex-1 text-center py-1.5 bg-emerald-50 border border-emerald-300 rounded-lg text-xs font-bold text-emerald-800 cursor-pointer">
                                    <input type="radio" name="photo-category-select" value="avant" checked class="hidden" onchange="updatePhotoCategoryLabel('Avant')">
                                    <span>📷 Avant</span>
                                </label>
                                <label class="flex-1 text-center py-1.5 bg-blue-50 border border-blue-300 rounded-lg text-xs font-bold text-blue-800 cursor-pointer">
                                    <input type="radio" name="photo-category-select" value="apres" class="hidden" onchange="updatePhotoCategoryLabel('Après')">
                                    <span>📸 Après</span>
                                </label>
                                <label class="flex-1 text-center py-1.5 bg-amber-50 border border-amber-300 rounded-lg text-xs font-bold text-amber-800 cursor-pointer">
                                    <input type="radio" name="photo-category-select" value="probleme" class="hidden" onchange="updatePhotoCategoryLabel('Problème')">
                                    <span>⚠️ Problème</span>
                                </label>
                            </div>
                        </div>

                        <!-- Aperçu photos -->
                        <div id="rapport-photos-preview" class="grid grid-cols-3 gap-2"></div>

                        <!-- Boutons photo -->
                        <div class="flex space-x-2">
                            <button type="button" onclick="openLiveCameraModal(null)" class="flex-1 flex items-center justify-center space-x-2 py-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-700 rounded-xl text-xs font-bold transition shadow-sm">
                                <i class="fa-solid fa-camera text-xs"></i>
                                <span>Caméra Live</span>
                            </button>
                            <label for="rapport-photo-gallery" class="flex-1 flex items-center justify-center space-x-2 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold cursor-pointer transition shadow-sm">
                                <i class="fa-solid fa-images text-xs"></i>
                                <span>Galerie</span>
                            </label>
                            <input type="file" id="rapport-photo-gallery" accept="image/*" multiple class="hidden" onchange="addRapportPhotos(event, 'gallery')">
                        </div>
                        <div id="rapport-photos-count" class="text-[10px] text-slate-400 text-center font-medium">0 photo(s) sélectionnée(s)</div>
                    </div>

                    <!-- Section : Vidéos du rapport -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-blue-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-video"></i><span>Vidéos de Démonstration</span>
                        </h3>
                        <div id="rapport-videos-preview" class="space-y-2"></div>
                        <div class="flex space-x-2">
                            <label for="rapport-video-record" class="flex-1 flex items-center justify-center space-x-2 py-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-300 text-blue-700 rounded-xl text-xs font-bold cursor-pointer transition shadow-sm">
                                <i class="fa-solid fa-circle-dot text-xs"></i>
                                <span>Enregistrer</span>
                            </label>
                            <input type="file" id="rapport-video-record" accept="video/*" capture="environment" class="hidden" onchange="addRapportVideo(event)">
                            <label for="rapport-video-file" class="flex-1 flex items-center justify-center space-x-2 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold cursor-pointer transition shadow-sm">
                                <i class="fa-solid fa-folder-open text-xs"></i>
                                <span>Choisir fichier</span>
                            </label>
                            <input type="file" id="rapport-video-file" accept="video/mp4,video/mov,video/webm" class="hidden" onchange="addRapportVideo(event)">
                        </div>
                        <div id="rapport-videos-count" class="text-[10px] text-slate-400 text-center font-medium">0 vidéo(s) sélectionnée(s)</div>
                    </div>

                    <!-- Section : Signatures (Technicien & Client) -->
                    <div class="glass-panel p-4 rounded-2xl space-y-4 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-indigo-900 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-signature"></i><span>Visas & Signatures Numériques</span>
                        </h3>

                        <!-- Signature Technicien -->
                        <div class="space-y-2 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-extrabold text-indigo-900">Signature du Technicien <span class="text-red-500">*</span></label>
                                <button type="button" onclick="clearSignatureTech()" class="text-[10px] font-bold text-rose-600 hover:underline">Effacer</button>
                            </div>

                            <!-- Prévisualisation signature enregistrée ou importée -->
                            <div id="sig-tech-preview-container" class="hidden bg-white p-2 border border-emerald-300 rounded-lg text-center shadow-inner">
                                <span class="text-[9px] font-bold text-emerald-600 block mb-1">✓ Signature Technicien enregistrée :</span>
                                <img id="sig-tech-preview" src="" class="max-h-20 mx-auto object-contain">
                            </div>

                            <canvas id="canvas-sig-tech" width="300" height="120" class="w-full h-28 bg-white border border-slate-300 rounded-lg touch-none cursor-crosshair shadow-sm"></canvas>
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>Dessinez votre signature avec le doigt</span>
                                <label for="sig-tech-file-input" class="text-indigo-600 font-bold cursor-pointer hover:underline">Ou photo signature papier</label>
                                <input type="file" id="sig-tech-file-input" accept="image/*" class="hidden" onchange="previewPaperSignature(this, 'sig-tech-preview', 'sig-tech-preview-container')">
                            </div>
                        </div>

                        <!-- Signature Client -->
                        <div class="space-y-2 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-extrabold text-indigo-900">Signature du Client (Accusé de Réception)</label>
                                <button type="button" onclick="clearSignatureClient()" class="text-[10px] font-bold text-rose-600 hover:underline">Effacer</button>
                            </div>

                            <!-- Prévisualisation signature enregistrée ou importée -->
                            <div id="sig-client-preview-container" class="hidden bg-white p-2 border border-emerald-300 rounded-lg text-center shadow-inner">
                                <span class="text-[9px] font-bold text-emerald-600 block mb-1">✓ Signature Client enregistrée :</span>
                                <img id="sig-client-preview" src="" class="max-h-20 mx-auto object-contain">
                            </div>

                            <canvas id="canvas-sig-client" width="300" height="120" class="w-full h-28 bg-white border border-slate-300 rounded-lg touch-none cursor-crosshair shadow-sm"></canvas>
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>Le client peut signer avec le doigt</span>
                                <label for="sig-client-file-input" class="text-indigo-600 font-bold cursor-pointer hover:underline">Ou photo signature papier</label>
                                <input type="file" id="sig-client-file-input" accept="image/*" class="hidden" onchange="previewPaperSignature(this, 'sig-client-preview', 'sig-client-preview-container')">
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action finaux -->
                    <div class="flex space-x-2 pb-4">
                        <button type="submit" id="btn-submit-rapport" class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <span>Enregistrer le Rapport</span>
                        </button>
                        <button type="button" id="btn-download-pdf" onclick="downloadRapportPdf()" class="hidden px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs border border-slate-300 transition flex items-center space-x-1 shadow-sm">
                            <i class="fa-solid fa-file-pdf text-red-600"></i>
                            <span>PDF</span>
                        </button>
                    </div>
                </form>
            </section>


            <!-- MODAL LIVE CAMERA WEBRTC -->
            <div id="modal-live-camera" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="glass-panel max-w-md w-full p-4 sm:p-6 rounded-3xl space-y-4 animate-fade-in bg-white border border-slate-200 shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center space-x-2">
                                <i class="fa-solid fa-camera text-emerald-600"></i>
                                <span>Caméra Live API</span>
                            </h3>
                        </div>
                        <button type="button" onclick="closeLiveCameraModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center border border-slate-200">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Viewfinder -->
                    <div class="relative bg-black rounded-2xl overflow-hidden border border-slate-800 shadow-inner flex items-center justify-center min-h-[260px]">
                        <video id="live-camera-feed" class="w-full h-64 sm:h-72 object-cover" autoplay playsinline muted></video>
                        <canvas id="live-camera-canvas" class="hidden"></canvas>
                        
                        <!-- Overlay -->
                        <div class="absolute inset-4 border-2 border-white/20 rounded-xl pointer-events-none flex items-center justify-center">
                            <div class="w-10 h-10 border-t-2 border-l-2 border-emerald-400 absolute top-0 left-0"></div>
                            <div class="w-10 h-10 border-t-2 border-r-2 border-emerald-400 absolute top-0 right-0"></div>
                            <div class="w-10 h-10 border-b-2 border-l-2 border-emerald-400 absolute bottom-0 left-0"></div>
                            <div class="w-10 h-10 border-b-2 border-r-2 border-emerald-400 absolute bottom-0 right-0"></div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center justify-around pt-2">
                        <button type="button" onclick="switchCameraFacingMode()" class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 flex items-center justify-center transition shadow-sm" title="Changer de caméra">
                            <i class="fa-solid fa-camera-rotate text-lg"></i>
                        </button>

                        <!-- Shutter button -->
                        <button type="button" onclick="captureLiveCameraPhoto()" class="w-16 h-16 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-500 hover:scale-105 active:scale-95 text-white flex items-center justify-center shadow-xl shadow-emerald-500/20 border-4 border-white transition">
                            <div class="w-12 h-12 rounded-full border-2 border-white/80"></div>
                        </button>

                        <label for="fallback-file-input" class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 flex items-center justify-center cursor-pointer transition shadow-sm" title="Galerie">
                            <i class="fa-solid fa-images text-lg"></i>
                        </label>
                        <input type="file" id="fallback-file-input" accept="image/*" multiple class="hidden" onchange="addRapportPhotos(event, 'gallery'); closeLiveCameraModal();">
                    </div>
                </div>
            </div>

            <!-- MODAL QR SCAN RAPPORT -->
            <div id="modal-qr-scan" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="glass-panel max-w-sm w-full p-5 rounded-2xl space-y-4 animate-fade-in bg-white border border-slate-200 shadow-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-qrcode mr-2 text-cyan-600"></i>Scanner QR Code</h3>
                        <button onclick="closeQrScanModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="bg-black rounded-xl overflow-hidden" style="height:200px;">
                        <video id="qr-video-preview" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    </div>
                    <p class="text-[11px] text-slate-500 text-center">Pointez la caméra vers le QR Code de l'équipement</p>
                    <div class="flex space-x-2">
                        <input type="text" id="qr-manual-input" placeholder="Ou saisissez manuellement..." class="flex-1 bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono shadow-sm">
                        <button onclick="confirmQrManualEntry()" class="px-3 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs shadow-sm">OK</button>
                    </div>
                </div>
            </div>

            <!-- VIEW 6: NOTIFICATIONS -->
            <section id="view-notifications" class="hidden space-y-3 animate-fade-in">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-sm font-bold text-slate-900">Notifications</h2>
                    <button onclick="markAllNotificationsAsRead()" class="text-xs font-bold text-brand-600 hover:underline">
                        Tout marquer comme lu
                    </button>
                </div>

                <div id="notifications-list-container" class="space-y-2">
                    <!-- Notifications items -->
                </div>
            </section>

            <!-- VIEW 7: PROFIL -->
            <section id="view-profile" class="hidden space-y-4 animate-fade-in">
                <div class="glass-panel p-4 rounded-2xl text-center space-y-3 bg-white border border-slate-200 shadow-sm">
                    <div class="relative w-20 h-20 mx-auto">
                        <div id="profile-avatar-display" class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white text-2xl font-bold shadow-md border-2 border-white overflow-hidden">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <label for="photo-upload-input" class="absolute -bottom-1 -right-1 w-7 h-7 bg-brand-600 hover:bg-brand-500 text-white rounded-full flex items-center justify-center shadow-md cursor-pointer border border-white">
                            <i class="fa-solid fa-camera text-xs"></i>
                        </label>
                        <input type="file" id="photo-upload-input" accept="image/*" onchange="uploadProfilePhoto(event)" class="hidden">
                    </div>

                    <div>
                        <h2 id="profile-name-text" class="text-base font-bold text-slate-900">Nom du Technicien</h2>
                        <span id="profile-role-text" class="text-[10px] font-extrabold uppercase tracking-wider text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">Rôle Technicien</span>
                    </div>
                </div>

                <!-- Update Info Form -->
                <form id="profile-info-form" onsubmit="handleProfileUpdate(event)" class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Informations Personnelles</h3>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] text-slate-600 font-semibold mb-1">Nom</label>
                            <input type="text" id="prof-name" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-600 font-semibold mb-1">Prénom</label>
                            <input type="text" id="prof-prenom" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Email</label>
                        <input type="email" id="prof-email" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] text-slate-600 font-semibold mb-1">Téléphone</label>
                            <input type="text" id="prof-phone" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-600 font-semibold mb-1">Adresse</label>
                            <input type="text" id="prof-address" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                        Sauvegarder les modifications
                    </button>
                </form>

                <!-- Update Password Form -->
                <form id="profile-password-form" onsubmit="handlePasswordUpdate(event)" class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Changer le Mot de passe</h3>
                    
                    <div>
                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Mot de passe actuel</label>
                        <input type="password" id="pass-current" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Nouveau mot de passe</label>
                        <input type="password" id="pass-new" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Confirmation</label>
                        <input type="password" id="pass-confirm" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 shadow-sm">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
                        Mettre à jour le mot de passe
                    </button>
                </form>

                <!-- Logout Button -->
                <button onclick="handleLogout()" class="w-full py-3 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-2xl text-xs border border-red-200 transition flex items-center justify-center space-x-2 shadow-sm">
                    <i class="fa-solid fa-power-off text-xs"></i>
                    <span>Déconnexion</span>
                </button>
            </section>

        </main>

        <!-- START INTERVENTION MODAL -->
        <div id="modal-start-intervention" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="glass-panel max-w-sm w-full p-5 rounded-2xl space-y-4 animate-fade-in bg-white border border-slate-200 shadow-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Démarrage Intervention</h3>
                    <button onclick="closeStartModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                
                <p id="modal-start-mode-desc" class="text-xs text-slate-600 leading-relaxed">Mode de présence configuré : MANUEL</p>

                <div id="modal-start-inputs" class="space-y-3 text-xs">
                    <!-- Mode specific inputs -->
                </div>

                <div class="flex space-x-2 pt-2">
                    <button onclick="closeStartModal()" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-200">Annuler</button>
                    <button onclick="confirmStartIntervention()" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-sm">Confirmer</button>
                </div>
            </div>
        </div>

        <!-- MODAL REFUSE INTERVENTION (DEMANDE DE REAFFECTATION) -->
        <div id="modal-refuse-intervention" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="glass-panel max-w-sm w-full p-5 rounded-2xl space-y-4 animate-fade-in bg-white border border-slate-200 shadow-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2 text-rose-600">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Refuser la Mission</span>
                    </h3>
                    <button onclick="closeRefuseModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                
                <p class="text-xs text-slate-600 leading-relaxed">Veuillez indiquer le motif obligatoire de votre refus. Une demande de réaffectation sera automatiquement transmise à l'administrateur pour arbitrage.</p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Motif du refus <span class="text-red-500">*</span></label>
                    <textarea id="refuse-motif-input" rows="3" placeholder="Ex: Indisponibilité planning, compétence spécifique manquante, problème de transport..." class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-600 shadow-sm resize-none"></textarea>
                </div>

                <div class="flex space-x-2 pt-1">
                    <button onclick="closeRefuseModal()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-200">Annuler</button>
                    <button onclick="submitRefuseIntervention()" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md">Transmettre le refus</button>
                </div>
            </div>
        </div>

        <!-- CONFIG API BASE URL MODAL -->
        <div id="modal-config-api" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="glass-panel max-w-sm w-full p-5 rounded-2xl space-y-4 animate-fade-in bg-white border border-slate-200 shadow-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Configuration API Base URL</h3>
                    <button onclick="toggleConfigModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">Modifiez l'URL de l'API pour s'adapter à votre environnement.</p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">API Base URL</label>
                    <input type="text" id="config-api-url" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono shadow-sm">
                </div>

                <div class="flex space-x-2">
                    <button onclick="resetApiUrlDefault()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-200">Réinitialiser</button>
                    <button onclick="saveApiUrlConfig()" class="flex-1 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs shadow-sm">Enregistrer</button>
                </div>
            </div>
        </div>

        <!-- UPLOAD PROGRESS & SYNC MODAL -->
        <div id="modal-upload-progress" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center space-y-4 border border-slate-100 animate-fade-in">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-brand-600 flex items-center justify-center mx-auto shadow-inner">
                    <i id="upload-icon" class="fa-solid fa-cloud-arrow-up text-2xl animate-bounce"></i>
                </div>
                <div>
                    <h3 id="upload-title" class="text-sm font-extrabold text-slate-900">Envoi des Preuves Terrain...</h3>
                    <p id="upload-subtitle" class="text-xs text-slate-500 mt-1">Compression photos &amp; transfert sécurisé</p>
                </div>
                <div class="space-y-1.5">
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
                        <div id="upload-progress-bar" class="bg-gradient-to-r from-brand-600 to-indigo-500 h-full rounded-full transition-all duration-150" style="width: 0%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500">
                        <span id="upload-status-text">Transfert réseau...</span>
                        <span id="upload-percentage" class="text-brand-600 font-extrabold">0%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CAMERA & GPS PERMISSIONS GUIDE MODAL -->
        <div id="modal-permissions-guide" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl space-y-4 border border-slate-100 text-center animate-fade-in">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-shield-halved text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Autorisations Téléphone Requises</h3>
                    <p class="text-xs text-slate-500 mt-1">L'application nécessite l'accès à la Caméra et à la Géolocalisation GPS pour valider les interventions sur le chantier.</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-left text-xs space-y-2 text-slate-700">
                    <div class="flex items-center space-x-2 font-bold text-slate-900">
                        <i class="fa-solid fa-camera text-brand-600"></i>
                        <span>1. Caméra WebRTC &amp; Scan QR</span>
                    </div>
                    <p class="text-[11px] text-slate-500 pl-5">Nécessaire pour photographier les équipements et scanner les QR codes.</p>
                    
                    <div class="flex items-center space-x-2 font-bold text-slate-900 pt-1">
                        <i class="fa-solid fa-location-dot text-emerald-600"></i>
                        <span>2. Geolocation GPS Haute Précision</span>
                    </div>
                    <p class="text-[11px] text-slate-500 pl-5">Nécessaire pour valider votre présence physique sur le lieu du chantier.</p>
                </div>
                <button onclick="closePermissionsGuide()" class="w-full py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                    J'ai compris – Réessayer
                </button>
            </div>
        </div>

        <!-- BOTTOM NAVIGATION BAR (Main Tabs) -->
        <nav id="bottom-nav" class="hidden shrink-0 h-16 glass-nav px-2 grid grid-cols-4 items-center z-30 bg-white/95 border-t border-slate-200">
            <button onclick="navigateTo('dashboard')" class="nav-tab-btn flex flex-col items-center justify-center py-1 text-brand-600 font-bold" data-view="dashboard">
                <i class="fa-solid fa-table-cells-large text-base"></i>
                <span class="text-[10px] font-bold mt-1">Accueil</span>
            </button>

            <button onclick="navigateTo('interventions')" class="nav-tab-btn flex flex-col items-center justify-center py-1 text-slate-400 hover:text-slate-700" data-view="interventions">
                <i class="fa-solid fa-list-check text-base"></i>
                <span class="text-[10px] font-bold mt-1">Missions</span>
            </button>

            <button onclick="navigateTo('notifications')" class="nav-tab-btn flex flex-col items-center justify-center py-1 text-slate-400 hover:text-slate-700 relative" data-view="notifications">
                <i class="fa-solid fa-bell text-base"></i>
                <span id="nav-unread-badge" class="hidden absolute top-1 right-5 w-4 h-4 bg-red-500 text-white text-[9px] font-extrabold rounded-full flex items-center justify-center">0</span>
                <span class="text-[10px] font-bold mt-1">Notifs</span>
            </button>

            <button onclick="navigateTo('profile')" class="nav-tab-btn flex flex-col items-center justify-center py-1 text-slate-400 hover:text-slate-700" data-view="profile">
                <i class="fa-solid fa-user text-base"></i>
                <span class="text-[10px] font-bold mt-1">Profil</span>
            </button>
        </nav>

    </div>

    <!-- JAVASCRIPT APPLICATION LOGIC -->
    <script>
        // CONFIGURATION & GLOBAL STATE
        let API_BASE_URL = localStorage.getItem('API_BASE_URL') || '/api';
        let authToken = localStorage.getItem('auth_token') || null;
        let currentUser = null;
        let currentView = 'login';

        let globalInterventions = [];
        let currentIntervFilter = 'all';
        let selectedIntervention = null;
        let currentActiveInterventionId = null;

        let trackingWatchId = null;
        let trackingIntervalTimer = null;
        let trackingSecondsCounter = 0;
        let activeGpsSessionId = null;

        // INITIALIZATION
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('config-api-url').value = API_BASE_URL;
            if (authToken) {
                verifyTokenAndInitialize();
                registerServiceWorkerAndPush();
            } else {
                navigateTo('login');
            }
        });

        // PWA SERVICE WORKER & WEB PUSH SUBSCRIPTION ENGINE
        async function registerServiceWorkerAndPush() {
            if (!('serviceWorker' in navigator)) {
                console.warn('Service Worker non supporté par ce navigateur.');
                return;
            }

            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('[PWA] Service Worker actif:', registration.scope);

                if ('Notification' in window && Notification.permission === 'default') {
                    setTimeout(async () => {
                        const permission = await Notification.requestPermission();
                        if (permission === 'granted') {
                            showToast('🔔 Notifications Push OS activées sur votre mobile !', 'success');
                            if (authToken) subscribeUserToPush(registration);
                        }
                    }, 3000);
                } else if ('Notification' in window && Notification.permission === 'granted' && authToken) {
                    subscribeUserToPush(registration);
                }
            } catch (err) {
                console.error('[PWA] Erreur enregistrement Service Worker:', err);
            }
        }

        async function subscribeUserToPush(registration) {
            try {
                let subscription = null;
                if (registration.pushManager) {
                    subscription = await registration.pushManager.getSubscription();
                    if (!subscription) {
                        subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array('BEl62iUYgUivxIkv69yViEuiBIa40yYVR0epw0L9HIna5z5nN3Lz96m3z_9nK91z432')
                        }).catch(() => null);
                    }
                }

                const endpoint = subscription ? subscription.endpoint : `https://push.technitrack.ma/devices/${Date.now()}`;
                
                await apiFetch('/push/subscribe', 'POST', {
                    endpoint: endpoint,
                    device_name: navigator.userAgent
                });
            } catch (e) {
                console.warn('[Push] Souscription enregistrée:', e);
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        // HTTP CLIENT WRAPPER (Axios-like Fetch Wrapper)
        async function apiFetch(endpoint, method = 'GET', body = null, isFormData = false) {
            const url = `${API_BASE_URL}${endpoint}`;
            const headers = { 'Accept': 'application/json' };
            
            if (authToken) {
                headers['Authorization'] = `Bearer ${authToken}`;
            }

            if (body && !isFormData) {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(body);
            }

            try {
                const response = await fetch(url, { method, headers, body });
                const json = await response.json().catch(() => ({ success: false, message: 'Erreur parsing JSON' }));

                if (response.status === 401) {
                    showToast('Session expirée, veuillez vous reconnecter.', 'error');
                    handleLogout();
                    return { success: false, message: 'Non authentifié' };
                }

                if (!response.ok) {
                    return {
                        success: false,
                        status: response.status,
                        message: json.message || 'Une erreur est survenue.',
                        errors: json.errors || null
                    };
                }

                return { success: true, status: response.status, ...json };
            } catch (err) {
                return { success: false, message: 'Erreur réseau : impossible de joindre le serveur API.' };
            }
        }

        // ── MODULE HORS-LIGNE & SYNCHRONISATION AUTOMATIQUE (IndexedDB / LocalStorage) ──
        const OFFLINE_QUEUE_KEY = 'technitrack_sync_queue';

        function getOfflineQueue() {
            try {
                return JSON.parse(localStorage.getItem(OFFLINE_QUEUE_KEY) || '[]');
            } catch (e) {
                return [];
            }
        }

        function saveOfflineAction(endpoint, method, data, label = 'Action Terrain') {
            const queue = getOfflineQueue();
            const payload = data instanceof FormData ? serializeFormData(data) : data;
            const item = {
                id: 'sync_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7),
                endpoint,
                method,
                label,
                payload,
                timestamp: new Date().toISOString()
            };
            queue.push(item);
            localStorage.setItem(OFFLINE_QUEUE_KEY, JSON.stringify(queue));
            updateOfflineUI();
            showToast(`💾 Mode Hors-Ligne : Action "${label}" enregistrée localement.`, 'info');
        }

        function serializeFormData(formData) {
            const obj = {};
            for (let [k, v] of formData.entries()) {
                if (!(v instanceof File)) {
                    obj[k] = v;
                }
            }
            return obj;
        }

        function updateOfflineUI() {
            const queue = getOfflineQueue();
            const banner = document.getElementById('network-offline-banner');
            const countEl = document.getElementById('offline-pending-count');

            if (countEl) countEl.innerText = `${queue.length} action(s)`;

            if (!navigator.onLine || queue.length > 0) {
                if (banner) banner.classList.remove('hidden');
            } else {
                if (banner) banner.classList.add('hidden');
            }
        }

        async function processOfflineSyncQueue() {
            if (!navigator.onLine) return;
            const queue = getOfflineQueue();
            if (queue.length === 0) return;

            showToast(`🔄 Synchronisation de ${queue.length} action(s) hors-ligne...`, 'info');
            const remaining = [];

            for (let item of queue) {
                try {
                    const res = await apiFetch(item.endpoint, item.method, item.payload);
                    if (res.success) {
                        showToast(`✓ Synchro réussie : ${item.label}`, 'success');
                    } else {
                        remaining.push(item);
                    }
                } catch (e) {
                    remaining.push(item);
                }
            }

            localStorage.setItem(OFFLINE_QUEUE_KEY, JSON.stringify(remaining));
            updateOfflineUI();

            if (remaining.length === 0) {
                showToast('🎉 Toutes les données hors-ligne ont été synchronisées !', 'success');
                if (selectedIntervention) openInterventionDetails(selectedIntervention.id);
            }
        }

        function triggerManualSync() {
            if (!navigator.onLine) {
                showToast('Aucune connexion Internet disponible.', 'error');
                return;
            }
            processOfflineSyncQueue();
        }

        window.addEventListener('online', () => {
            updateOfflineUI();
            showToast('🟢 Connexion Internet rétablie ! Synchronisation automatique...', 'success');
            processOfflineSyncQueue();
        });

        window.addEventListener('offline', () => {
            updateOfflineUI();
            showToast('🔴 Mode Hors-Ligne activé. Les données seront stockées localement.', 'error');
        });

        // ── COMPRESSION PHOTO CLIENT (HTML5 Canvas) ──
        function compressPhotoFile(file, maxDimension = 1600, quality = 0.8) {
            return new Promise((resolve) => {
                if (!file || !file.type.startsWith('image/')) {
                    resolve(file);
                    return;
                }
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxDimension || height > maxDimension) {
                            if (width > height) {
                                height = Math.round((height * maxDimension) / width);
                                width = maxDimension;
                            } else {
                                width = Math.round((width * maxDimension) / height);
                                height = maxDimension;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob) {
                                const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                resolve(file);
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                };
                reader.onerror = () => resolve(file);
            });
        }

        // ── UPLOAD AVEC PROGRESSION & RETRY AUTOMATIQUE ──
        async function apiFetchWithProgress(endpoint, method = 'POST', formData = null, maxRetries = 3) {
            const url = `${API_BASE_URL}${endpoint}`;
            const progressBar = document.getElementById('upload-progress-bar');
            const progressPct = document.getElementById('upload-percentage');
            const progressModal = document.getElementById('modal-upload-progress');

            if (progressModal) progressModal.classList.remove('hidden');
            if (progressBar) progressBar.style.width = '0%';
            if (progressPct) progressPct.innerText = '0%';

            // Compress photos in formData before transfer
            if (formData && formData.has('photos[]')) {
                const photos = formData.getAll('photos[]');
                const types = formData.getAll('photos_types[]');
                formData.delete('photos[]');
                formData.delete('photos_types[]');

                for (let i = 0; i < photos.length; i++) {
                    const comp = await compressPhotoFile(photos[i]);
                    formData.append('photos[]', comp);
                    if (types[i]) formData.append('photos_types[]', types[i]);
                }
            }

            let attempt = 0;
            while (attempt < maxRetries) {
                attempt++;
                try {
                    const result = await new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open(method, url, true);
                        xhr.setRequestHeader('Accept', 'application/json');
                        if (authToken) {
                            xhr.setRequestHeader('Authorization', `Bearer ${authToken}`);
                        }

                        xhr.upload.onprogress = (e) => {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                if (progressBar) progressBar.style.width = `${percent}%`;
                                if (progressPct) progressPct.innerText = `${percent}%`;
                            }
                        };

                        xhr.onload = () => {
                            if (progressModal) progressModal.classList.add('hidden');
                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    resolve({ success: true, status: xhr.status, ...json });
                                } else {
                                    resolve({ success: false, status: xhr.status, message: json.message || 'Erreur serveur', errors: json.errors || null });
                                }
                            } catch (e) {
                                resolve({ success: false, message: 'Erreur réponse serveur' });
                            }
                        };

                        xhr.onerror = () => {
                            reject(new Error('Réseau interrompu'));
                        };

                        xhr.send(formData);
                    });

                    return result;

                } catch (err) {
                    console.warn(`Tentative d'upload ${attempt}/${maxRetries} échouée...`);
                    if (attempt < maxRetries) {
                        await new Promise(r => setTimeout(r, attempt * 1500));
                    } else {
                        if (progressModal) progressModal.classList.add('hidden');
                        saveOfflineAction(endpoint, method, formData, 'Rapport & Preuves Terrain');
                        return { success: false, offline: true, message: 'Erreur réseau. Données sauvegardées en mode Hors-Ligne.' };
                    }
                }
            }
        }

        // ── GESTION DES PERMISSIONS TÉLÉPHONE ──
        function showPermissionsGuide() {
            document.getElementById('modal-permissions-guide')?.classList.remove('hidden');
        }
        function closePermissionsGuide() {
            document.getElementById('modal-permissions-guide')?.classList.add('hidden');
        }

        // NAVIGATION ROUTER
        function navigateTo(viewName) {
            currentView = viewName;
            
            const views = ['login', 'dashboard', 'interventions', 'intervention-detail', 'formulaire', 'rapport', 'notifications', 'profile'];
            views.forEach(v => {
                const el = document.getElementById(`view-${v}`);
                if (el) el.classList.add('hidden');
            });

            const targetEl = document.getElementById(`view-${viewName}`);
            if (targetEl) targetEl.classList.remove('hidden');

            const navBar = document.getElementById('bottom-nav');
            if (viewName === 'login') {
                navBar.classList.add('hidden');
            } else {
                navBar.classList.remove('hidden');
                updateNavActiveState(viewName);
            }

            // View-specific trigger
            if (viewName === 'dashboard') loadDashboardData();
            if (viewName === 'interventions') loadInterventionsData();
            if (viewName === 'notifications') loadNotificationsData();
            if (viewName === 'profile') loadProfileData();
        }

        function updateNavActiveState(viewName) {
            document.querySelectorAll('.nav-tab-btn').forEach(btn => {
                const target = btn.getAttribute('data-view');
                if (target === viewName || (viewName === 'intervention-detail' && target === 'interventions')) {
                    btn.className = 'nav-tab-btn flex flex-col items-center justify-center py-1 text-brand-500';
                } else {
                    btn.className = 'nav-tab-btn flex flex-col items-center justify-center py-1 text-slate-500 hover:text-slate-300';
                }
            });
        }

        // MODULE 1: AUTHENTICATION
        async function verifyTokenAndInitialize() {
            const res = await apiFetch('/me');
            if (res.success && res.data) {
                currentUser = res.data;
                updateUserHeaderBadge();
                navigateTo('dashboard');
            } else {
                handleLogout();
            }
        }

        async function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;
            const errBox = document.getElementById('login-error-msg');
            const submitBtn = document.getElementById('btn-login-submit');

            errBox.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Connexion...</span>`;

            const res = await apiFetch('/login', 'POST', { email, password });
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span>Se Connecter</span> <i class="fa-solid fa-arrow-right text-xs"></i>`;

            if (res.success && res.data && res.data.token) {
                authToken = res.data.token;
                localStorage.setItem('auth_token', authToken);
                currentUser = res.data.user;
                updateUserHeaderBadge();
                showToast('Connexion réussie !', 'success');
                navigateTo('dashboard');
            } else {
                errBox.innerText = res.message || 'Identifiants incorrects.';
                errBox.classList.remove('hidden');
            }
        }

        async function handleLogout() {
            if (authToken) {
                await apiFetch('/logout', 'POST');
            }
            authToken = null;
            currentUser = null;
            localStorage.removeItem('auth_token');
            stopGpsTracking();
            navigateTo('login');
        }

        function updateUserHeaderBadge() {
            if (currentUser && currentUser.name) {
                const initial = currentUser.name.charAt(0).toUpperCase();
                document.getElementById('user-avatar-badge').innerText = initial;
            }
        }

        // MODULE 2: DASHBOARD
        async function loadDashboardData() {
            if (currentUser) {
                document.getElementById('dash-user-name').innerText = `Bonjour, ${currentUser.name} 🛠️`;
            }

            const resInterv = await apiFetch('/interventions');
            if (resInterv.success && Array.isArray(resInterv.data)) {
                globalInterventions = resInterv.data;
                const total = globalInterventions.length;
                const upcoming = globalInterventions.filter(i => i.statut === 'Planifiee' || i.statut === 'Acceptee').length;
                const inprogress = globalInterventions.filter(i => i.statut === 'En cours').length;
                const completed = globalInterventions.filter(i => i.statut === 'Terminee').length;

                document.getElementById('dash-kpi-upcoming').innerText = upcoming;
                document.getElementById('dash-kpi-inprogress').innerText = inprogress;
                document.getElementById('dash-kpi-completed').innerText = completed;

                // Active intervention check
                const activeInterv = globalInterventions.find(i => i.statut === 'En cours');
                const activeCard = document.getElementById('dash-active-mission-card');
                if (activeInterv) {
                    currentActiveInterventionId = activeInterv.id;
                    document.getElementById('dash-active-code').innerText = activeInterv.code_intervention;
                    document.getElementById('dash-active-title').innerText = activeInterv.chantier ? activeInterv.chantier.nom : 'Chantier en cours';
                    activeCard.classList.remove('hidden');
                } else {
                    activeCard.classList.add('hidden');
                }

                // Render recent list
                renderDashboardRecentList(globalInterventions.slice(0, 4));
            }

            // Load notifications unread count
            const resNotif = await apiFetch('/notifications/unread');
            if (resNotif.success && resNotif.data) {
                const count = resNotif.data.unread_count || 0;
                document.getElementById('dash-kpi-unread-notifs').innerText = count;
                const badge = document.getElementById('nav-unread-badge');
                if (count > 0) {
                    badge.innerText = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }

        function renderDashboardRecentList(items) {
            const container = document.getElementById('dash-interventions-list');
            if (!items || items.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-500 py-3 italic text-center">Aucune intervention récente.</p>`;
                return;
            }

            container.innerHTML = items.map(item => `
                <div onclick="openInterventionDetails(${item.id})" class="glass-panel p-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 transition flex items-center justify-between cursor-pointer">
                    <div class="space-y-0.5">
                        <span class="text-xs font-bold text-slate-900 block">${item.code_intervention}</span>
                        <span class="text-[11px] text-slate-500 block">${item.chantier ? item.chantier.nom : 'Chantier'}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${getStatusClass(item.statut)}">${getStatusLabel(item.statut)}</span>
                </div>
            `).join('');
        }

        // MODULE 3: INTERVENTIONS LIST
        async function loadInterventionsData() {
            const container = document.getElementById('interv-list-container');
            container.innerHTML = `<div class="py-8 text-center"><i class="fa-solid fa-circle-notch fa-spin text-brand-600 text-lg"></i><p class="text-xs text-slate-500 mt-2">Chargement des missions...</p></div>`;

            const res = await apiFetch('/interventions');
            if (res.success && Array.isArray(res.data)) {
                globalInterventions = res.data;
                filterInterventionsList();
            } else {
                container.innerHTML = `
                    <div class="glass-panel p-6 rounded-2xl text-center space-y-2 border border-red-200 bg-red-50/50">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        <p class="text-xs text-slate-700 font-medium">${res.message || 'Erreur lors de la récupération.'}</p>
                        <button onclick="loadInterventionsData()" class="px-3 py-1.5 bg-white text-xs font-bold text-slate-700 rounded-lg border border-slate-300 shadow-sm hover:bg-slate-50">Réessayer</button>
                    </div>
                `;
            }
        }

        function setIntervFilter(filter) {
            currentIntervFilter = filter;
            document.querySelectorAll('.interv-filter-btn').forEach(btn => {
                if (btn.getAttribute('data-filter') === filter) {
                    btn.className = 'interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-brand-600 text-white shadow-sm';
                } else {
                    btn.className = 'interv-filter-btn shrink-0 px-3 py-1.5 rounded-lg font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-100';
                }
            });
            filterInterventionsList();
        }

        function filterInterventionsList() {
            const query = (document.getElementById('interv-search-input').value || '').toLowerCase();
            const container = document.getElementById('interv-list-container');

            let filtered = globalInterventions.filter(item => {
                const matchesFilter = (currentIntervFilter === 'all') || (item.statut === currentIntervFilter);
                const matchesSearch = item.code_intervention.toLowerCase().includes(query) ||
                    (item.chantier && item.chantier.nom.toLowerCase().includes(query));
                return matchesFilter && matchesSearch;
            });

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="glass-panel p-8 rounded-2xl text-center space-y-2 border border-slate-200 bg-white shadow-sm">
                        <i class="fa-solid fa-folder-open text-slate-400 text-2xl"></i>
                        <p class="text-xs text-slate-500">Aucune intervention trouvée.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filtered.map(item => `
                <div onclick="openInterventionDetails(${item.id})" class="glass-panel p-4 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-brand-300 hover:shadow-md transition cursor-pointer space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-brand-600 uppercase tracking-wider">${item.type_intervention ? item.type_intervention.nom : 'Intervention'}</span>
                            <h3 class="text-xs font-bold text-slate-900 mt-0.5">${item.code_intervention} — ${item.chantier ? item.chantier.nom : 'N/A'}</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${getStatusClass(item.statut)}">${getStatusLabel(item.statut)}</span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                        <span><i class="fa-solid fa-clock mr-1 text-slate-400"></i> ${formatDateDisplay(item.date_prevue_debut)}</span>
                        <span class="font-bold ${getPrioriteClass(item.priorite)}">${item.priorite || 'Normale'}</span>
                    </div>
                </div>
            `).join('');
        }

        // MODULE 4, 5, 6: INTERVENTION DETAILS & ACTIONS
        async function openInterventionDetails(id) {
            navigateTo('intervention-detail');
            const res = await apiFetch(`/interventions/${id}`);

            if (res.success && res.data) {
                selectedIntervention = res.data;
                renderInterventionDetailScreen();
            } else {
                showToast(res.message || 'Erreur chargement de l\'intervention', 'error');
            }
        }

        function renderInterventionDetailScreen() {
            const item = selectedIntervention;
            if (!item) return;

            document.getElementById('detail-code-badge').innerText = item.code_intervention;
            document.getElementById('detail-type-name').innerText = item.type_intervention ? item.type_intervention.nom : 'Intervention';
            document.getElementById('detail-chantier-name').innerText = item.chantier ? item.chantier.nom : 'Chantier';
            document.getElementById('detail-emplacement-name').innerText = `Emplacement : ${item.emplacement ? item.emplacement.nom : 'N/A'}`;
            
            const statusPill = document.getElementById('detail-status-pill');
            statusPill.innerText = getStatusLabel(item.statut);
            statusPill.className = `px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase ${getStatusClass(item.statut)}`;

            document.getElementById('detail-priorite-badge').innerText = item.priorite || 'Normale';
            document.getElementById('detail-date-prevue').innerText = formatDateDisplay(item.date_prevue_debut);
            document.getElementById('detail-desc-text').innerText = item.description || 'Aucune description disponible.';
            document.getElementById('detail-mode-suivi-badge').innerText = item.mode_suivi || 'MANUEL';

            // ACTION BUTTONS CONTROLS
            const btnGroupAcceptRefuse = document.getElementById('btn-group-accept-refuse');
            const bannerReassignment   = document.getElementById('banner-reassignment-pending');
            const btnStart              = document.getElementById('btn-action-start');
            const btnForm               = document.getElementById('btn-action-form');
            const btnRapport            = document.getElementById('btn-action-rapport');
            const btnFinish             = document.getElementById('btn-action-finish');
            const bannerSealed          = document.getElementById('banner-sealed-info');
            const btnValidate           = document.getElementById('btn-action-validate');

            if (btnGroupAcceptRefuse) btnGroupAcceptRefuse.classList.add('hidden');
            if (bannerReassignment)   bannerReassignment.classList.add('hidden');
            if (btnStart)              btnStart.classList.add('hidden');
            if (btnForm)               btnForm.classList.add('hidden');
            if (btnRapport)            btnRapport.classList.add('hidden');
            if (btnFinish)             btnFinish.classList.add('hidden');
            if (bannerSealed)          bannerSealed.classList.add('hidden');
            if (btnValidate)           btnValidate.classList.add('hidden');

            if (item.statut === 'Planifiee' || item.statut === 'Affectee') {
                if (btnGroupAcceptRefuse) btnGroupAcceptRefuse.classList.remove('hidden');
            } else if (item.statut === 'En attente reafectation') {
                if (bannerReassignment) bannerReassignment.classList.remove('hidden');
            } else if (['Acceptee', 'Suspendue', 'Reportee', 'Rejetee', 'Rouverte'].includes(item.statut)) {
                if (btnStart) btnStart.classList.remove('hidden');
            } else if (item.statut === 'En cours' || item.statut === 'Formulaire rempli') {
                if (btnForm) btnForm.classList.remove('hidden');
                if (btnRapport) btnRapport.classList.remove('hidden');
                if (btnFinish) btnFinish.classList.remove('hidden');
            } else if (item.statut === 'Terminee' || item.statut === 'Validee') {
                if (bannerSealed) bannerSealed.classList.remove('hidden');
                if (btnForm) btnForm.classList.remove('hidden');
                if (btnRapport) btnRapport.classList.remove('hidden');
            }

            // Masquer la zone d'ajout de matériau si l'intervention est clôturée
            const addMatPanel = document.getElementById('add-materiau-panel');
            if (addMatPanel) {
                if (['Terminee', 'Validee', 'Annulee'].includes(item.statut)) {
                    addMatPanel.classList.add('hidden');
                } else {
                    addMatPanel.classList.remove('hidden');
                }
            }

            // Render sub-tabs content
            renderDetailTabs();
        }

        function openRefuseModal() {
            document.getElementById('refuse-motif-input').value = '';
            document.getElementById('modal-refuse-intervention').classList.remove('hidden');
        }

        function closeRefuseModal() {
            document.getElementById('modal-refuse-intervention').classList.add('hidden');
        }

        async function submitRefuseIntervention() {
            if (!selectedIntervention) return;

            const motif = document.getElementById('refuse-motif-input').value.trim();
            if (!motif || motif.length < 5) {
                showToast('Veuillez préciser un motif de refus d\'au moins 5 caractères.', 'error');
                return;
            }

            const res = await apiFetch(`/interventions/${selectedIntervention.id}/refuse`, 'POST', { motif });

            if (res.success) {
                showToast('Demande de réaffectation transmise à l\'administrateur !', 'success');
                closeRefuseModal();
                openInterventionDetails(selectedIntervention.id);
            } else {
                showToast(res.message || 'Erreur lors de la transmission du refus.', 'error');
            }
        }

        function renderMobileTaskCard(t, isParent = false) {
            const pct = t.pourcentage || 0;
            const statut = t.statut || 'Non commencee';
            const isDone = statut === 'Terminee' || pct >= 100;
            const isStarted = statut === 'En cours' || pct > 0;
            const taskNom = t.tache ? t.tache.nom : 'Tâche';

            return `
                <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-sm space-y-2" id="mobile-task-card-${t.id}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-bold text-slate-800 text-xs">${taskNom}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded ${isDone ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : (isStarted ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200')}">
                            ${statut} (${pct}%)
                        </span>
                    </div>

                    <!-- Barre de progression de la tâche -->
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full ${isDone ? 'bg-emerald-500' : 'bg-indigo-600'} transition-all duration-300" style="width: ${pct}%"></div>
                    </div>

                    <!-- Actions Technicien sur la tâche -->
                    <div class="flex flex-wrap items-center justify-between gap-1 pt-1 border-t border-slate-50">
                        <div class="flex items-center gap-1">
                            ${!isStarted ? `
                                <button onclick="mobileStartTask(${t.id})" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] rounded-lg shadow-sm transition flex items-center gap-1">
                                    ▶ Démarrer
                                </button>
                            ` : ''}
                            ${!isDone ? `
                                <button onclick="mobileFinishTask(${t.id})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg shadow-sm transition flex items-center gap-1">
                                    ✓ Terminer
                                </button>
                            ` : ''}
                        </div>
                        <div class="flex items-center gap-1">
                            <button onclick="mobileSetTaskProgressPrompt(${t.id}, ${pct})" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold text-[10px] rounded-lg transition">
                                📊 Prog: ${pct}%
                            </button>
                            <button onclick="mobileTaskCommentPrompt(${t.id}, '${(t.commentaire || '').replace(/'/g, "\\'")}')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] rounded-lg transition">
                                💬 Note
                            </button>
                        </div>
                    </div>
                    ${t.commentaire ? `<p class="text-[10px] text-slate-600 italic bg-amber-50/70 p-1.5 rounded-lg border border-amber-100">💬 "${t.commentaire}"</p>` : ''}
                </div>
            `;
        }

        function renderDetailTabs() {
            const item = selectedIntervention;
            if (!item) return;

            // Taches avec hiérarchie Grandes Tâches -> Sous-tâches
            const containerTaches = document.getElementById('detail-taches-list');
            if (item.taches && item.taches.length > 0) {
                const grandes = item.taches.filter(t => !t.tache || !t.tache.parent_id);
                const subTasks = item.taches.filter(t => t.tache && t.tache.parent_id);

                let html = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-indigo-50 border border-indigo-100 mb-2">
                            <span class="font-bold text-slate-700 text-xs">Progression globale calculée</span>
                            <span class="text-xs font-extrabold text-indigo-700 bg-white px-2.5 py-1 rounded-lg border border-indigo-200 shadow-sm" id="mobile-live-global-pct">
                                ${item.pourcentage_global || 0}%
                            </span>
                        </div>
                `;

                if (grandes.length === 0 && subTasks.length > 0) {
                    html += subTasks.map(t => renderMobileTaskCard(t)).join('');
                } else {
                    html += grandes.map(g => {
                        const subs = subTasks.filter(s => s.tache && s.tache.parent_id === g.tache_id);
                        return `
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-slate-900 text-xs">📌 ${g.tache ? g.tache.nom : 'Grande Tâche'}</span>
                                    <span class="text-[11px] font-extrabold text-indigo-600 bg-white px-2 py-0.5 rounded border border-indigo-100">${g.pourcentage || 0}%</span>
                                </div>
                                ${renderMobileTaskCard(g, true)}
                                ${subs.length > 0 ? `
                                    <div class="ml-3 pl-2 border-l-2 border-indigo-300 space-y-2 mt-2">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sous-tâches</span>
                                        ${subs.map(s => renderMobileTaskCard(s, false)).join('')}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }).join('');
                }
                html += `</div>`;
                containerTaches.innerHTML = html;
            } else {
                containerTaches.innerHTML = `<p class="text-slate-400 italic">Aucune tâche assignée à cette intervention.</p>`;
            }

            // Materiaux - mise à jour de la liste
            renderMateriauList(item.materiaux || []);

            // Historique
            const containerHist = document.getElementById('detail-historique-list');
            if (item.historiques && item.historiques.length > 0) {
                containerHist.innerHTML = item.historiques.map(h => `
                    <div class="border-l-2 border-brand-600 pl-3 py-1 space-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-[11px]">${h.statut_apres}</span>
                            <span class="text-[9px] text-slate-400">${formatDateDisplay(h.created_at)}</span>
                        </div>
                        <p class="text-slate-600 text-[11px]">${h.commentaire || 'Changement de statut'}</p>
                    </div>
                `).join('');
            } else {
                containerHist.innerHTML = `<p class="text-slate-400 italic">Aucun historique enregistré.</p>`;
            }
        }

        function switchDetailTab(tabName) {
            document.querySelectorAll('.detail-tab-btn').forEach(btn => {
                if (btn.getAttribute('data-tab') === tabName) {
                    btn.className = 'detail-tab-btn border-b-2 border-brand-600 text-brand-600 font-bold px-3 py-2';
                } else {
                    btn.className = 'detail-tab-btn border-b-2 border-transparent text-slate-500 font-bold px-3 py-2 hover:text-slate-800';
                }
            });

            document.querySelectorAll('.detail-tab-pane').forEach(pane => pane.classList.add('hidden'));
            document.getElementById(`tab-content-${tabName}`).classList.remove('hidden');
        }

        // ACTIONS CLIENTS & TECHNICIENS SUR LES TÂCHES ET MATÉRIAUX
        async function mobileStartTask(tachePivotId) {
            if (!selectedIntervention) return;
            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/taches/${tachePivotId}/start`, 'POST', {});
                if (res.success || res.data) {
                    showToast('Tâche démarrée !', 'success');
                    openInterventionDetails(selectedIntervention.id);
                } else {
                    showToast(res.message || 'Erreur au démarrage de la tâche', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur lors du démarrage de la tâche', 'error');
            }
        }

        async function mobileFinishTask(tachePivotId) {
            if (!selectedIntervention) return;
            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/taches/${tachePivotId}/finish`, 'POST', {});
                if (res.success || res.data) {
                    showToast('Tâche marquée comme terminée ! Pourcentage global recalculé.', 'success');
                    openInterventionDetails(selectedIntervention.id);
                } else {
                    showToast(res.message || 'Erreur lors de la fin de tâche', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur lors de la fin de la tâche', 'error');
            }
        }

        // --- Modal Progression Tâche ---
        let _progressTachePivotId = null;

        function mobileSetTaskProgressPrompt(tachePivotId, currentPct) {
            _progressTachePivotId = tachePivotId;
            const slider = document.getElementById('modal-progress-slider');
            const display = document.getElementById('modal-progress-value');
            slider.value = currentPct;
            display.textContent = currentPct + '%';
            slider.oninput = () => { display.textContent = slider.value + '%'; };
            document.getElementById('modal-task-progress').classList.remove('hidden');
            document.getElementById('modal-task-progress').classList.add('flex');
        }

        function closeProgressModal() {
            document.getElementById('modal-task-progress').classList.add('hidden');
            document.getElementById('modal-task-progress').classList.remove('flex');
            _progressTachePivotId = null;
        }

        async function submitTaskProgress() {
            if (!selectedIntervention || _progressTachePivotId === null) return;
            const pct = parseInt(document.getElementById('modal-progress-slider').value, 10);
            const btn = document.getElementById('btn-submit-progress');
            btn.disabled = true;
            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/taches/${_progressTachePivotId}/progress`, 'POST', {
                    pourcentage: pct
                });
                if (res.success || res.data) {
                    showToast(`Progression enregistrée : ${pct}%`, 'success');
                    closeProgressModal();
                    openInterventionDetails(selectedIntervention.id);
                } else {
                    showToast(res.message || 'Erreur mise à jour progression', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur enregistrement progression', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        // --- Modal Commentaire Tâche ---
        let _commentTachePivotId = null;
        let _commentCurrentPct = 0;

        function mobileTaskCommentPrompt(tachePivotId, currentComment) {
            _commentTachePivotId = tachePivotId;
            const taskObj = (selectedIntervention?.taches || []).find(t => t.id === tachePivotId);
            _commentCurrentPct = taskObj ? (taskObj.pourcentage || 0) : 0;
            document.getElementById('modal-comment-textarea').value = currentComment || '';
            document.getElementById('modal-task-comment').classList.remove('hidden');
            document.getElementById('modal-task-comment').classList.add('flex');
            setTimeout(() => document.getElementById('modal-comment-textarea').focus(), 100);
        }

        function closeCommentModal() {
            document.getElementById('modal-task-comment').classList.add('hidden');
            document.getElementById('modal-task-comment').classList.remove('flex');
            _commentTachePivotId = null;
        }

        async function submitTaskComment() {
            if (!selectedIntervention || _commentTachePivotId === null) return;
            const comment = document.getElementById('modal-comment-textarea').value.trim();
            const btn = document.getElementById('btn-submit-comment');
            btn.disabled = true;
            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/taches/${_commentTachePivotId}/progress`, 'POST', {
                    pourcentage: _commentCurrentPct,
                    commentaire: comment
                });
                if (res.success || res.data) {
                    showToast('Commentaire enregistré !', 'success');
                    closeCommentModal();
                    openInterventionDetails(selectedIntervention.id);
                } else {
                    showToast(res.message || 'Erreur enregistrement commentaire', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur enregistrement commentaire', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        /* ==========================================
           GESTION DES MATÉRIAUX - INTERFACE NATIVE
           ========================================== */

        let _catalogueMateriaux = [];
        let _searchDebounce = null;

        /** Rend la liste des matériaux utilisés dans la carte intervention */
        function renderMateriauList(materiaux) {
            const container = document.getElementById('detail-materiaux-list');
            const badge = document.getElementById('mat-count-badge');
            if (!container) return;

            if (badge) badge.textContent = materiaux.length;

            if (materiaux.length === 0) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-8 text-xs text-slate-400 space-y-2">
                        <i class="fa-solid fa-box-open text-2xl text-slate-200"></i>
                        <span>Aucun matériau enregistré</span>
                    </div>`;
                return;
            }

            container.innerHTML = materiaux.map(m => {
                const nom  = m.materiau ? m.materiau.nom  : (m.nom  || 'Matériau');
                const ref  = m.materiau ? m.materiau.reference : '';
                const unit = m.materiau ? m.materiau.unite : '';
                const qte  = parseFloat(m.quantite || 0).toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 3});
                return `
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition group" id="mat-row-${m.id}">
                        <div class="flex items-center space-x-3 min-w-0">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-cube text-indigo-500 text-[10px]"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">${nom}</p>
                                ${ref ? `<p class="text-[10px] text-slate-400">Réf: ${ref}</p>` : ''}
                                ${m.commentaire ? `<p class="text-[10px] text-slate-500 italic truncate">${m.commentaire}</p>` : ''}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            <span class="text-xs font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-lg">
                                ${qte} ${unit || ''}
                            </span>
                            <button onclick="deleteMateriau(${m.id})" class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50" title="Supprimer">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>`;
            }).join('');
        }

        /** Recherche dans le catalogue matériaux (debounced) */
        async function searchCatalogueMateriau(query) {
            if (_searchDebounce) clearTimeout(_searchDebounce);
            const dropdown = document.getElementById('mat-catalogue-dropdown');

            if (!query || query.length < 1) {
                dropdown.classList.add('hidden');
                return;
            }

            _searchDebounce = setTimeout(async () => {
                try {
                    const res = await apiFetch(`/materiaux?q=${encodeURIComponent(query)}`, 'GET');
                    const items = res.data || res || [];
                    _catalogueMateriaux = items;

                    if (items.length === 0) {
                        dropdown.innerHTML = `<div class="px-4 py-3 text-xs text-slate-400 italic">Aucun résultat pour "${query}"</div>`;
                    } else {
                        dropdown.innerHTML = items.map(m => `
                            <button type="button"
                                onclick="selectMateriauFromCatalogue(${m.id}, '${(m.nom||'').replace(/'/g, "\\'")}', '${(m.reference||'').replace(/'/g, "\\'")}', '${(m.unite||'').replace(/'/g, "\\'")}', '${(m.description||'').replace(/'/g, "\\'")}')"
                                class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 transition flex items-center justify-between group">
                                <div>
                                    <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-800">${m.nom}</p>
                                    ${m.reference ? `<p class="text-[10px] text-slate-400">Réf: ${m.reference}</p>` : ''}
                                </div>
                                ${m.unite ? `<span class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">${m.unite}</span>` : ''}
                            </button>`).join('');
                    }
                    dropdown.classList.remove('hidden');
                } catch (e) {
                    dropdown.innerHTML = `<div class="px-4 py-3 text-xs text-red-400">Erreur de chargement du catalogue</div>`;
                    dropdown.classList.remove('hidden');
                }
            }, 300);
        }

        /** Sélectionner un matériau depuis le catalogue */
        function selectMateriauFromCatalogue(id, nom, ref, unite, desc) {
            document.getElementById('mat-selected-id').value = id;
            document.getElementById('mat-selected-nom').textContent = nom;
            document.getElementById('mat-selected-ref').textContent = [ref && `Réf: ${ref}`, desc].filter(Boolean).join(' — ');
            document.getElementById('mat-unite-display').textContent = unite || '—';
            document.getElementById('mat-selected-preview').classList.remove('hidden');
            document.getElementById('mat-catalogue-dropdown').classList.add('hidden');
            document.getElementById('mat-search-input').value = nom;
            document.getElementById('mat-quantite-input').focus();
        }

        /** Effacer la sélection de matériau */
        function clearSelectedMateriau() {
            document.getElementById('mat-selected-id').value = '';
            document.getElementById('mat-selected-preview').classList.add('hidden');
            document.getElementById('mat-search-input').value = '';
            document.getElementById('mat-unite-display').textContent = '—';
        }

        /** Enregistrer le matériau utilisé */
        async function saveMateriau() {
            if (!selectedIntervention) return;

            const materiauId = document.getElementById('mat-selected-id').value;
            const quantite   = document.getElementById('mat-quantite-input').value;
            const commentaire = document.getElementById('mat-commentaire-input').value;

            if (!materiauId) {
                showToast('Sélectionnez un matériau dans le catalogue.', 'warning');
                return;
            }
            if (!quantite || parseFloat(quantite) <= 0) {
                showToast('Saisissez une quantité valide.', 'warning');
                return;
            }

            const btn = document.getElementById('btn-save-materiau');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Enregistrement...</span>`;

            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/materiaux`, 'POST', {
                    materiau_id: parseInt(materiauId, 10),
                    quantite:    parseFloat(quantite),
                    commentaire: commentaire || null
                });

                if (res.success || res.data) {
                    showToast('Matériau enregistré avec succès !', 'success');
                    // Réinitialiser le formulaire
                    clearSelectedMateriau();
                    document.getElementById('mat-quantite-input').value = '';
                    document.getElementById('mat-commentaire-input').value = '';
                    // Rafraîchir depuis l'API
                    await refreshMateriaux();
                } else {
                    showToast(res.message || 'Erreur lors de l\'enregistrement.', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur serveur.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }

        /** Supprimer un matériau de l'intervention */
        async function deleteMateriau(pivotId) {
            if (!selectedIntervention) return;
            if (!confirm('Supprimer ce matériau de l\'intervention ?')) return;

            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/materiaux/${pivotId}`, 'DELETE');
                if (res.success || res.data === null) {
                    showToast('Matériau supprimé.', 'success');
                    // Retirer visuellement sans rechargement complet
                    const row = document.getElementById(`mat-row-${pivotId}`);
                    if (row) row.remove();
                    // Mettre à jour le badge et selectedIntervention
                    if (selectedIntervention.materiaux) {
                        selectedIntervention.materiaux = selectedIntervention.materiaux.filter(m => m.id !== pivotId);
                        const badge = document.getElementById('mat-count-badge');
                        if (badge) badge.textContent = selectedIntervention.materiaux.length;
                    }
                } else {
                    showToast(res.message || 'Erreur suppression.', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur serveur.', 'error');
            }
        }

        /** Rafraîchir la liste des matériaux depuis l'API sans recharger toute l'intervention */
        async function refreshMateriaux() {
            if (!selectedIntervention) return;
            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}`, 'GET');
                if (res.data) {
                    selectedIntervention.materiaux = res.data.materiaux || [];
                    renderMateriauList(selectedIntervention.materiaux);
                    // Mise à jour du badge dans les onglets
                    const badge = document.getElementById('mat-count-badge');
                    if (badge) badge.textContent = selectedIntervention.materiaux.length;
                }
            } catch (e) {
                // Silent fail, la liste reste en place
            }
        }


        async function executeFinishIntervention() {
            if (!selectedIntervention) return;
            if (!confirm('Confirmez-vous la fin de cette intervention ? Les éléments (formulaire et rapport) seront transmis à l\'administration et scellés.')) return;

            try {
                const res = await apiFetch(`/interventions/${selectedIntervention.id}/finish`, 'POST');
                if (res.success || res.data) {
                    stopGpsTracking();
                    showToast('Intervention terminée avec succès ! transmise à l\'administration.', 'success');
                    openInterventionDetails(selectedIntervention.id);
                } else {
                    showToast(res.message || 'Impossible de terminer l\'intervention', 'error');
                }
            } catch (e) {
                showToast(e.message || 'Erreur lors de la fin de l\'intervention', 'error');
            }
        }

        async function executeAcceptIntervention() {
            if (!selectedIntervention) return;
            const res = await apiFetch(`/interventions/${selectedIntervention.id}/accept`, 'POST');
            if (res.success) {
                showToast('Intervention acceptée avec succès !', 'success');
                openInterventionDetails(selectedIntervention.id);
            } else {
                showToast(res.message || 'Impossible d\'accepter l\'intervention', 'error');
            }
        }

        function openStartInterventionModal() {
            if (!selectedIntervention) return;
            const mode = selectedIntervention.mode_suivi || 'Manuel';
            document.getElementById('modal-start-mode-desc').innerText = `Mode de présence configuré par l'administration : ${mode}`;
            
            const inputsContainer = document.getElementById('modal-start-inputs');
            inputsContainer.innerHTML = '';

            if (mode === 'GPS') {
                inputsContainer.innerHTML = `
                    <p class="text-slate-600 font-medium"><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> Validation des coordonnées GPS en cours...</p>
                    <input type="hidden" id="start-lat" value="33.5731">
                    <input type="hidden" id="start-lng" value="-7.5898">
                `;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        document.getElementById('start-lat').value = pos.coords.latitude;
                        document.getElementById('start-lng').value = pos.coords.longitude;
                        inputsContainer.innerHTML = `<p class="text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 rounded-xl p-2.5"><i class="fa-solid fa-location-dot mr-1"></i> Coordonnées GPS capturées (${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)})</p>`;
                    }, err => {
                        inputsContainer.innerHTML = `<p class="text-red-700 font-medium bg-red-50 border border-red-200 rounded-xl p-2.5"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Géolocalisation refusée. Coordonnées par défaut utilisées.</p>`;
                    });
                }
            } else if (mode === 'QR') {
                inputsContainer.innerHTML = `
                    <label class="block text-slate-700 font-semibold mb-1">Scannez ou saisissez le QR Code :</label>
                    <input type="text" id="start-qrcode" placeholder="Ex: QR-ZONE-A1" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 shadow-sm">
                `;
            } else if (mode === 'NFC') {
                inputsContainer.innerHTML = `
                    <label class="block text-slate-700 font-semibold mb-1">Tag NFC UID :</label>
                    <input type="text" id="start-nfctag" placeholder="Ex: NFC-UID-8899" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 shadow-sm">
                `;
            } else {
                inputsContainer.innerHTML = `<p class="text-slate-600 bg-slate-50 border border-slate-200 rounded-xl p-2.5">Aucune donnée supplémentaire requise pour le mode Manuel.</p>`;
            }

            document.getElementById('modal-start-intervention').classList.remove('hidden');
        }

        function closeStartModal() {
            document.getElementById('modal-start-intervention').classList.add('hidden');
        }

        async function confirmStartIntervention() {
            if (!selectedIntervention) return;
            const mode = selectedIntervention.mode_suivi || 'Manuel';
            const body = { mode };

            if (mode === 'GPS') {
                body.latitude = document.getElementById('start-lat') ? document.getElementById('start-lat').value : 33.5731;
                body.longitude = document.getElementById('start-lng') ? document.getElementById('start-lng').value : -7.5898;
            } else if (mode === 'QR') {
                body.qr_code = document.getElementById('start-qrcode') ? document.getElementById('start-qrcode').value : '';
            } else if (mode === 'NFC') {
                body.nfc_tag = document.getElementById('start-nfctag') ? document.getElementById('start-nfctag').value : '';
            }

            const res = await apiFetch(`/interventions/${selectedIntervention.id}/start`, 'POST', body);
            closeStartModal();

            if (res.success) {
                showToast('Intervention démarrée avec succès !', 'success');
                // Automatically start GPS tracking
                startGpsTrackingSession(selectedIntervention.id);
                openInterventionDetails(selectedIntervention.id);
            } else {
                showToast(res.message || 'Erreur lors du démarrage.', 'error');
            }
        }

        async function executeValidateIntervention() {
            if (!selectedIntervention) return;
            const res = await apiFetch(`/interventions/${selectedIntervention.id}/validate`, 'POST');
            if (res.success) {
                showToast('Intervention validée et clôturée avec succès !', 'success');
                openInterventionDetails(selectedIntervention.id);
            } else {
                showToast(res.message || 'Erreur lors de la validation.', 'error');
            }
        }

        // MODULE 7: TRACKING GPS
        async function startGpsTrackingSession(interventionId) {
            const res = await apiFetch(`/interventions/${interventionId}/tracking/start`, 'POST', {
                mode: 'GPS',
                latitude: 33.5731,
                longitude: -7.5898
            });

            if (res.success && res.data) {
                activeGpsSessionId = res.data.id;
                document.getElementById('tracking-active-banner').classList.remove('hidden');
                
                // Start timer and position watch
                trackingSecondsCounter = 0;
                clearInterval(trackingIntervalTimer);
                trackingIntervalTimer = setInterval(() => {
                    trackingSecondsCounter++;
                    const mins = String(Math.floor(trackingSecondsCounter / 60)).padStart(2, '0');
                    const secs = String(trackingSecondsCounter % 60).padStart(2, '0');
                    document.getElementById('tracking-timer').innerText = `${mins}:${secs}`;

                    // Send position point every 8s (standard field-service tracking interval)
                    if (trackingSecondsCounter % 8 === 0 && activeGpsSessionId) {
                        sendGpsPositionPoint(activeGpsSessionId);
                    }
                }, 1000);
            }
        }

        async function sendGpsPositionPoint(sessionId) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async pos => {
                    await apiFetch(`/tracking/sessions/${sessionId}/points`, 'POST', {
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude
                    });
                });
            }
        }

        async function stopGpsTracking() {
            if (activeGpsSessionId) {
                await apiFetch(`/tracking/sessions/${activeGpsSessionId}/stop`, 'POST');
                activeGpsSessionId = null;
            }
            clearInterval(trackingIntervalTimer);
            document.getElementById('tracking-active-banner').classList.add('hidden');
            showToast('Tracking GPS arrêté.', 'info');
        }

        // MODULE 8: FORMULAIRE DYNAMIQUE
        async function openFormulaireScreen() {
            if (!selectedIntervention) return;
            navigateTo('formulaire');
            const container = document.getElementById('dynamic-form-fields');
            container.innerHTML = `<div class="py-8 text-center"><i class="fa-solid fa-circle-notch fa-spin text-indigo-500 text-lg"></i><p class="text-xs text-slate-400 mt-2">Chargement du formulaire...</p></div>`;

            const res = await apiFetch(`/interventions/${selectedIntervention.id}/formulaire`);
            if (res.success && res.data && res.data.formulaire) {
                renderDynamicFormFields(res.data.formulaire);
            } else {
                container.innerHTML = `<p class="text-xs text-red-400 p-4 text-center">${res.message || 'Aucun formulaire disponible.'}</p>`;
            }
        }

        function renderDynamicFormFields(formulaire) {
            const container = document.getElementById('dynamic-form-fields');
            if (!formulaire.questions || formulaire.questions.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-500 p-4 text-center">Aucune question configurée pour ce type d'intervention.</p>`;
                return;
            }

            const BASE = `w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 shadow-sm transition`;
            const BASE_AREA = `w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 shadow-sm transition resize-none`;

            container.innerHTML = formulaire.questions
                .filter(q => (q.type_reponse || q.type_champ) !== 'Materiaux')
                .map(q => {
                const req   = q.obligatoire ? 'required' : '';
                const star  = q.obligatoire ? '<span class="text-red-500 font-bold">*</span>' : '<span class="text-slate-400 text-[10px]">(optionnel)</span>';
                const ph    = q.placeholder  ? q.placeholder  : '';
                const def   = q.valeur_par_defaut ? q.valeur_par_defaut : '';
                const type  = q.type_reponse || q.type_champ || 'Texte';
                const choix = q.choix || [];

                // Icon per type
                const icons = {
                    'Texte':'fa-font','TexteLong':'fa-align-left','Nombre':'fa-hashtag',
                    'Date':'fa-calendar','Heure':'fa-clock','DateHeure':'fa-calendar-days',
                    'OuiNon':'fa-toggle-on','Oui_Non':'fa-toggle-on','Liste':'fa-list-ul',
                    'Checkbox':'fa-square-check','Radio':'fa-circle-dot','Photo':'fa-camera',
                    'Video':'fa-video','Signature':'fa-signature','Document':'fa-file-arrow-up',
                    'GPS':'fa-location-dot','QRCode':'fa-qrcode','Materiaux':'fa-boxes-stacked'
                };
                const icon = icons[type] || 'fa-circle-question';

                let inputHtml = '';
                switch(type) {
                    case 'TexteLong':
                        inputHtml = `<textarea name="q_${q.id}" ${req} placeholder="${ph}" rows="3" class="${BASE_AREA}">${def}</textarea>`;
                        break;

                    case 'Nombre':
                        const numMinAttr = (q.nombre_min !== null && q.nombre_min !== undefined) ? `min="${q.nombre_min}"` : '';
                        const numMaxAttr = (q.nombre_max !== null && q.nombre_max !== undefined) ? `max="${q.nombre_max}"` : '';
                        const numInput = `<input type="number" step="any" name="q_${q.id}" ${req} ${numMinAttr} ${numMaxAttr} placeholder="${ph || '0'}" value="${def}" class="${BASE}">`;
                        inputHtml = q.nombre_unite
                            ? `<div class="flex items-center space-x-2">${numInput}<span class="shrink-0 px-2.5 py-2 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600">${q.nombre_unite}</span></div>`
                            : numInput;
                        if (q.nombre_min !== null && q.nombre_min !== undefined || q.nombre_max !== null && q.nombre_max !== undefined) {
                            const bounds = [
                                (q.nombre_min !== null && q.nombre_min !== undefined) ? `min ${q.nombre_min}` : null,
                                (q.nombre_max !== null && q.nombre_max !== undefined) ? `max ${q.nombre_max}` : null,
                            ].filter(Boolean).join(' — ');
                            inputHtml += `<p class="text-[10px] text-slate-400 mt-1">${bounds}</p>`;
                        }
                        break;

                    case 'Date':
                        inputHtml = `<input type="date" name="q_${q.id}" ${req} class="${BASE}" value="${def}">`;
                        break;

                    case 'Heure':
                        inputHtml = `<input type="time" name="q_${q.id}" ${req} class="${BASE}">`;
                        break;

                    case 'DateHeure':
                        inputHtml = `<input type="datetime-local" name="q_${q.id}" ${req} class="${BASE}">`;
                        break;

                    case 'OuiNon':
                    case 'Oui_Non':
                        inputHtml = `
                            <div class="ouinon-container flex gap-3 w-full pt-1" data-qid="${q.id}">
                                <button type="button" data-val="Oui" onclick="selectOuiNon(this, ${q.id}, 'Oui')" class="ouinon-btn flex-1 flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 cursor-pointer transition shadow-sm text-xs group">
                                    <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                                    <span>Oui</span>
                                </button>
                                <button type="button" data-val="Non" onclick="selectOuiNon(this, ${q.id}, 'Non')" class="ouinon-btn flex-1 flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-red-300 bg-red-50 text-red-700 font-bold hover:bg-red-100 cursor-pointer transition shadow-sm text-xs group">
                                    <i class="fa-solid fa-xmark text-red-600 text-xs"></i>
                                    <span>Non</span>
                                </button>
                                <input type="hidden" id="input_q_${q.id}" name="q_${q.id}" value="" ${req}>
                            </div>`;
                        break;

                    case 'Liste':
                        inputHtml = `<select name="q_${q.id}" ${req} class="${BASE}">
                            <option value="">-- Sélectionnez une option --</option>
                            ${choix.map(c => `<option value="${c.valeur}">${c.libelle || c.valeur}</option>`).join('')}
                        </select>`;
                        break;

                    case 'Radio':
                        inputHtml = `<div class="space-y-2 pt-1">
                            ${choix.map(c => `
                            <label class="flex items-center space-x-3 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50 cursor-pointer transition shadow-sm">
                                <input type="radio" name="q_${q.id}" value="${c.valeur}" ${req} class="accent-indigo-600">
                                <span class="text-xs text-slate-800 font-medium">${c.libelle || c.valeur}</span>
                            </label>`).join('')}
                        </div>`;
                        break;

                    case 'Checkbox':
                        inputHtml = `<div class="space-y-2 pt-1">
                            ${choix.map(c => `
                            <label class="flex items-center space-x-3 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50 cursor-pointer transition shadow-sm">
                                <input type="checkbox" name="q_${q.id}[]" value="${c.valeur}" class="accent-indigo-600 rounded">
                                <span class="text-xs text-slate-800 font-medium">${c.libelle || c.valeur}</span>
                            </label>`).join('')}
                        </div>`;
                        break;

                    case 'Photo':
                        inputHtml = `
                            <div class="space-y-2">
                                <div id="photo-preview-${q.id}" class="grid grid-cols-3 gap-2"></div>
                                <div class="flex space-x-2">
                                    <button type="button" onclick="openLiveCameraModal(${q.id})" class="flex-1 flex items-center justify-center space-x-1.5 py-2.5 bg-emerald-50 border border-emerald-300 text-emerald-700 rounded-xl text-xs font-bold hover:bg-emerald-100 transition shadow-sm">
                                        <i class="fa-solid fa-camera text-xs"></i><span>Caméra Live</span>
                                    </button>
                                    <label for="q_photo_gal_${q.id}" class="flex-1 flex items-center justify-center space-x-1.5 py-2.5 bg-slate-100 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold cursor-pointer hover:bg-slate-200 transition shadow-sm">
                                        <i class="fa-solid fa-images text-xs"></i><span>Galerie</span>
                                    </label>
                                    <input type="file" id="q_photo_gal_${q.id}" name="q_${q.id}" accept="image/*" multiple class="hidden" ${q.fichiers_max ? `data-max="${q.fichiers_max}"` : ''} onchange="previewFormPhoto(event, ${q.id})">
                                </div>
                                ${q.fichiers_max ? `<p class="text-[10px] text-slate-400">Maximum ${q.fichiers_max} photo(s)</p>` : ''}
                            </div>`;
                        break;

                    case 'Video':
                        inputHtml = `
                            <div class="space-y-2">
                                <div id="video-preview-${q.id}" class="text-[11px] text-slate-500"></div>
                                <div class="flex space-x-2">
                                    <label for="q_vid_rec_${q.id}" class="flex-1 flex items-center justify-center space-x-1.5 py-2.5 bg-blue-50 border border-blue-300 text-blue-700 rounded-xl text-xs font-bold cursor-pointer hover:bg-blue-100 transition shadow-sm">
                                        <i class="fa-solid fa-circle-dot text-xs"></i><span>Enregistrer</span>
                                    </label>
                                    <input type="file" id="q_vid_rec_${q.id}" name="q_${q.id}" accept="video/*" capture="environment" class="hidden" ${req} onchange="previewFormVideo(event, ${q.id})">
                                    <label for="q_vid_fil_${q.id}" class="flex-1 flex items-center justify-center space-x-1.5 py-2.5 bg-slate-100 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold cursor-pointer hover:bg-slate-200 transition shadow-sm">
                                        <i class="fa-solid fa-folder-open text-xs"></i><span>Fichier</span>
                                    </label>
                                    <input type="file" id="q_vid_fil_${q.id}" name="q_${q.id}" accept="video/mp4,video/mov,video/webm" class="hidden" onchange="previewFormVideo(event, ${q.id})">
                                </div>
                            </div>`;
                        break;

                    case 'QRCode':
                        inputHtml = `
                            <div class="flex space-x-2">
                                <input type="text" id="q_qr_${q.id}" name="q_${q.id}" ${req} placeholder="${ph || 'Scannez ou saisissez le code...'}" class="flex-1 ${BASE} font-mono">
                                <button type="button" onclick="scanQrForField('q_qr_${q.id}')" class="px-3 py-2 bg-cyan-50 hover:bg-cyan-100 border border-cyan-300 text-cyan-700 rounded-xl text-xs font-bold transition shadow-sm">
                                    <i class="fa-solid fa-qrcode"></i>
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Pointez la caméra vers le QR Code ou saisissez manuellement.</p>`;
                        break;

                    case 'GPS':
                        inputHtml = `
                            <div class="space-y-2">
                                <div id="gps-status-${q.id}" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600">
                                    <i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> Position non capturée
                                </div>
                                <input type="hidden" id="q_gps_${q.id}" name="q_${q.id}" value="">
                                <button type="button" onclick="captureGpsForField(${q.id})" class="w-full py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2 shadow-sm">
                                    <i class="fa-solid fa-crosshairs fa-spin text-xs"></i>
                                    <span>Capturer ma position GPS</span>
                                </button>
                            </div>`;
                        break;

                    case 'Signature':
                        inputHtml = `
                            <div class="space-y-2">
                                <div class="relative rounded-xl overflow-hidden border-2 border-dashed border-slate-300 bg-white shadow-inner" style="height:100px;touch-action:none;">
                                    <canvas id="sig-canvas-${q.id}" class="w-full h-full" style="cursor:crosshair;"></canvas>
                                </div>
                                <input type="hidden" id="q_sig_${q.id}" name="q_${q.id}">
                                <div class="flex justify-between">
                                    <p class="text-[10px] text-slate-500">Signez dans la zone ci-dessus</p>
                                    <button type="button" onclick="clearSignatureCanvas(${q.id})" class="text-[10px] text-red-600 hover:text-red-700 font-bold">Effacer</button>
                                </div>
                            </div>`;
                        break;

                    case 'Document':
                        inputHtml = `
                            <label for="q_doc_${q.id}" class="flex items-center justify-center space-x-2 py-3 border-2 border-dashed border-slate-300 hover:border-brand-600 rounded-xl text-xs font-bold text-slate-600 cursor-pointer transition bg-slate-50">
                                <i class="fa-solid fa-file-arrow-up text-lg text-slate-400"></i>
                                <span>Sélectionner un fichier</span>
                            </label>
                            <input type="file" id="q_doc_${q.id}" name="q_${q.id}" accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden" ${req} onchange="showDocName(event, ${q.id})">
                            <div id="doc-name-${q.id}" class="text-[10px] text-slate-500 text-center"></div>`;
                        break;

                    case 'Materiaux':
                        inputHtml = `
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                                <i class="fa-solid fa-boxes-stacked mr-1 text-slate-400"></i>
                                Les matériaux utilisés sont gérés dans l'onglet <strong class="text-slate-800">Matériaux</strong> de la fiche intervention.
                            </div>`;
                        break;

                    default:
                        inputHtml = `<input type="text" name="q_${q.id}" ${req} placeholder="${ph}" value="${def}" class="${BASE}">`;
                }

                // Affichage conditionnel : ce champ ne s'affiche que si une réponse
                // précise a été donnée à une question précédente (ex: "Autre" coché).
                const cond = q.condition_affichage;
                const condAttr = cond && cond.depends_on
                    ? `data-depends-on="q_${cond.depends_on}" data-depends-on-value="${cond.choix_valeur}"`
                    : '';
                const condStyle = cond && cond.depends_on ? 'display:none;' : '';

                return `
                    <div class="glass-panel p-4 rounded-xl flex flex-col space-y-2 border border-slate-200 bg-white shadow-sm" id="field-card-q_${q.id}" ${condAttr} style="${condStyle}">
                        <!-- Structuration verticale du label -->
                        <div class="flex flex-col space-y-1">
                            <label class="flex items-center space-x-2 text-xs font-bold text-slate-800">
                                <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fa-solid ${icon} text-indigo-600" style="font-size:10px;"></i>
                                </span>
                                <span>${q.question} ${star}</span>
                            </label>
                            ${ph && type !== 'Photo' && type !== 'Video' && type !== 'QRCode' && type !== 'GPS' && type !== 'Signature' && type !== 'Document' && type !== 'Materiaux' ? `<p class="text-[11px] text-slate-500 font-normal pl-8">${ph}</p>` : ''}
                        </div>

                        <!-- Input Control Verticalement structuré -->
                        <div class="w-full pt-1">${inputHtml}</div>
                    </div>
                `;
            }).join('');

            // Init signature canvases after render
            formulaire.questions.filter(q => (q.type_reponse || q.type_champ) === 'Signature').forEach(q => initSignatureCanvas(q.id));

            initConditionalFields();
        }

        // Affiche/masque les champs à condition d'affichage selon la valeur cochée/sélectionnée
        // du champ parent (Checkbox, Radio ou Liste). Générique, basé sur condition_affichage.
        function initConditionalFields() {
            document.querySelectorAll('[data-depends-on]').forEach(card => {
                const parentName = card.dataset.dependsOn;
                const triggerValue = card.dataset.dependsOnValue;

                const evaluate = () => {
                    const checked = document.querySelectorAll(`input[name="${parentName}[]"]:checked, input[name="${parentName}"]:checked`);
                    const select = document.querySelector(`select[name="${parentName}"]`);
                    let show = Array.from(checked).some(el => el.value === triggerValue);
                    if (!show && select) show = select.value === triggerValue;
                    card.style.display = show ? '' : 'none';
                };

                document.querySelectorAll(`[name="${parentName}[]"], [name="${parentName}"]`).forEach(el => {
                    el.addEventListener('change', evaluate);
                });
                evaluate();
            });
        }

        function selectOuiNon(btn, questionId, val) {
            const container = btn.closest('.ouinon-container');
            if (!container) return;

            // Reset buttons styling in container
            container.querySelectorAll('.ouinon-btn').forEach(b => {
                if (b.dataset.val === 'Oui') {
                    b.className = 'ouinon-btn flex-1 flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 cursor-pointer transition shadow-sm text-xs';
                } else {
                    b.className = 'ouinon-btn flex-1 flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-red-300 bg-red-50 text-red-700 font-bold hover:bg-red-100 cursor-pointer transition shadow-sm text-xs';
                }
            });

            // Set hidden input value
            const input = document.getElementById(`input_q_${questionId}`);
            if (input) input.value = val;

            // Highlight active button
            if (val === 'Oui') {
                btn.className = 'ouinon-btn flex-1 flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border-2 border-emerald-600 bg-emerald-600 text-white font-extrabold shadow-md ring-2 ring-emerald-200 cursor-pointer transition text-xs';
            } else {
                btn.className = 'ouinon-btn flex-1 flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border-2 border-red-600 bg-red-600 text-white font-extrabold shadow-md ring-2 ring-red-200 cursor-pointer transition text-xs';
            }
        }

        async function handleFormulaireSubmit(e) {
            e.preventDefault();
            if (!selectedIntervention) return;

            const formEl = document.getElementById('dynamic-form-element');
            const formData = new FormData(formEl);
            const reponses = {};

            formData.forEach((val, key) => {
                if (key.startsWith('q_')) {
                    let cleanKey = key.replace('q_', '');
                    let isArray = false;
                    if (cleanKey.endsWith('[]')) {
                        cleanKey = cleanKey.replace('[]', '');
                        isArray = true;
                    }
                    if (isArray) {
                        if (!reponses[cleanKey]) reponses[cleanKey] = [];
                        reponses[cleanKey].push(val);
                    } else {
                        reponses[cleanKey] = val;
                    }
                }
            });

            const res = await apiFetch(`/interventions/${selectedIntervention.id}/formulaire`, 'POST', { reponses });
            if (res.success) {
                showToast('Formulaire enregistré et soumis à validation !', 'success');
                openInterventionDetails(selectedIntervention.id);
            } else {
                showToast(res.message || 'Erreur lors de la soumission.', 'error');
            }
        }

        // MODULE 9: RAPPORT
        let rapportPhotoFiles = [];
        let rapportVideoFiles = [];
        let currentPhotoCategory = 'avant';

        function updatePhotoCategoryLabel(catName) {
            const rad = document.querySelector('input[name="photo-category-select"]:checked');
            if (rad) currentPhotoCategory = rad.value;
        }

        // Canvas Signature Technicien
        let canvasTech, ctxTech, isDrawingTech = false, hasDrawnTech = false;
        function initSignatureCanvasTech() {
            canvasTech = document.getElementById('canvas-sig-tech');
            if (!canvasTech) return;

            const rect = canvasTech.getBoundingClientRect();
            if (rect.width > 0) {
                canvasTech.width = rect.width;
                canvasTech.height = 120;
            }

            ctxTech = canvasTech.getContext('2d');
            ctxTech.lineWidth = 2.5;
            ctxTech.strokeStyle = '#1e1b4b';
            ctxTech.lineCap = 'round';
            ctxTech.lineJoin = 'round';

            function getPos(e) {
                const r = canvasTech.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: clientX - r.left, y: clientY - r.top };
            }

            function startDraw(e) {
                if (e.type === 'touchstart') e.preventDefault();
                isDrawingTech = true;
                const pos = getPos(e);
                ctxTech.beginPath();
                ctxTech.moveTo(pos.x, pos.y);
            }

            function moveDraw(e) {
                if (!isDrawingTech) return;
                if (e.type === 'touchmove') e.preventDefault();
                hasDrawnTech = true;
                const pos = getPos(e);
                ctxTech.lineTo(pos.x, pos.y);
                ctxTech.stroke();
            }

            function stopDraw(e) {
                isDrawingTech = false;
            }

            canvasTech.onmousedown = startDraw;
            canvasTech.onmousemove = moveDraw;
            canvasTech.onmouseup = stopDraw;

            canvasTech.ontouchstart = startDraw;
            canvasTech.ontouchmove = moveDraw;
            canvasTech.ontouchend = stopDraw;
        }

        function clearSignatureTech() {
            if (ctxTech && canvasTech) {
                ctxTech.clearRect(0, 0, canvasTech.width, canvasTech.height);
                hasDrawnTech = false;
                const container = document.getElementById('sig-tech-preview-container');
                if (container) container.classList.add('hidden');
            }
        }

        // Canvas Signature Client
        let canvasClient, ctxClient, isDrawingClient = false, hasDrawnClient = false;
        function initSignatureCanvasClient() {
            canvasClient = document.getElementById('canvas-sig-client');
            if (!canvasClient) return;

            const rect = canvasClient.getBoundingClientRect();
            if (rect.width > 0) {
                canvasClient.width = rect.width;
                canvasClient.height = 120;
            }

            ctxClient = canvasClient.getContext('2d');
            ctxClient.lineWidth = 2.5;
            ctxClient.strokeStyle = '#1e1b4b';
            ctxClient.lineCap = 'round';
            ctxClient.lineJoin = 'round';

            function getPos(e) {
                const r = canvasClient.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: clientX - r.left, y: clientY - r.top };
            }

            function startDraw(e) {
                if (e.type === 'touchstart') e.preventDefault();
                isDrawingClient = true;
                const pos = getPos(e);
                ctxClient.beginPath();
                ctxClient.moveTo(pos.x, pos.y);
            }

            function moveDraw(e) {
                if (!isDrawingClient) return;
                if (e.type === 'touchmove') e.preventDefault();
                hasDrawnClient = true;
                const pos = getPos(e);
                ctxClient.lineTo(pos.x, pos.y);
                ctxClient.stroke();
            }

            function stopDraw(e) {
                isDrawingClient = false;
            }

            canvasClient.onmousedown = startDraw;
            canvasClient.onmousemove = moveDraw;
            canvasClient.onmouseup = stopDraw;

            canvasClient.ontouchstart = startDraw;
            canvasClient.ontouchmove = moveDraw;
            canvasClient.ontouchend = stopDraw;
        }

        function clearSignatureClient() {
            if (ctxClient && canvasClient) {
                ctxClient.clearRect(0, 0, canvasClient.width, canvasClient.height);
                hasDrawnClient = false;
                const container = document.getElementById('sig-client-preview-container');
                if (container) container.classList.add('hidden');
            }
        }

        function previewPaperSignature(input, imgId, containerId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById(imgId).src = e.target.result;
                    document.getElementById(containerId).classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // GPS Capture
        function getDeviceGpsPosition() {
            if (!navigator.geolocation) {
                showToast('La géolocalisation n\'est pas supportée par votre navigateur.', 'error');
                return;
            }
            showToast('Recherche de la position GPS en cours...', 'info');
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                document.getElementById('rapport-gps-lat').value = lat;
                document.getElementById('rapport-gps-lng').value = lng;
                document.getElementById('rapport-gps-addr').value = `GPS: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                showToast('Position GPS capturée !', 'success');
            }, err => {
                showToast('Impossible de capturer la position GPS.', 'error');
            });
        }

        async function openRapportScreen() {
            if (!selectedIntervention) return;
            navigateTo('rapport');

            setTimeout(() => {
                initSignatureCanvasTech();
                initSignatureCanvasClient();
            }, 150);

            // Reset media buffers
            rapportPhotoFiles = [];
            rapportVideoFiles = [];
            document.getElementById('rapport-photos-preview').innerHTML = '';
            document.getElementById('rapport-videos-preview').innerHTML = '';
            document.getElementById('rapport-photos-count').innerText = '0 photo(s) sélectionnée(s)';
            document.getElementById('rapport-videos-count').innerText = '0 vidéo(s) sélectionnée(s)';

            const res = await apiFetch(`/interventions/${selectedIntervention.id}/rapport`);
            if (res.success && res.data) {
                const r = res.data;
                document.getElementById('rapport-travaux').value              = r.travaux_effectues   || '';
                document.getElementById('rapport-observations').value         = r.observations        || '';
                document.getElementById('rapport-recommandations').value      = r.recommandations     || '';
                document.getElementById('rapport-commentaire').value          = r.commentaire         || '';
                document.getElementById('rapport-qrcode').value               = r.qrcode_scanne       || '';
                document.getElementById('rapport-statut-equipement').value    = r.statut_equipement   || 'Conforme';
                if (r.gps_latitude) document.getElementById('rapport-gps-lat').value  = r.gps_latitude;
                if (r.gps_longitude) document.getElementById('rapport-gps-lng').value = r.gps_longitude;
                if (r.gps_adresse) document.getElementById('rapport-gps-addr').value   = r.gps_adresse;

                // Afficher les signatures existantes si déjà enregistrées
                if (r.signature_technicien) {
                    const imgTech = document.getElementById('sig-tech-preview');
                    const contTech = document.getElementById('sig-tech-preview-container');
                    if (imgTech && contTech) {
                        imgTech.src = r.signature_technicien.startsWith('data:') ? r.signature_technicien : `/storage/${r.signature_technicien}`;
                        contTech.classList.remove('hidden');
                    }
                } else {
                    const contTech = document.getElementById('sig-tech-preview-container');
                    if (contTech) contTech.classList.add('hidden');
                }

                if (r.signature_client) {
                    const imgClient = document.getElementById('sig-client-preview');
                    const contClient = document.getElementById('sig-client-preview-container');
                    if (imgClient && contClient) {
                        imgClient.src = r.signature_client.startsWith('data:') ? r.signature_client : `/storage/${r.signature_client}`;
                        contClient.classList.remove('hidden');
                    }
                } else {
                    const contClient = document.getElementById('sig-client-preview-container');
                    if (contClient) contClient.classList.add('hidden');
                }

                document.getElementById('btn-download-pdf').classList.remove('hidden');

                if (r.photos && r.photos.length > 0) {
                    const preview = document.getElementById('rapport-photos-preview');
                    r.photos.forEach(p => {
                        const img = document.createElement('img');
                        img.src = `/storage/${p.chemin}`;
                        img.className = 'w-full aspect-square object-cover rounded-lg border border-slate-700';
                        preview.appendChild(img);
                    });
                }
            } else {
                ['rapport-travaux','rapport-observations','rapport-recommandations','rapport-commentaire','rapport-qrcode','rapport-gps-lat','rapport-gps-lng','rapport-gps-addr'].forEach(id => {
                    const el = document.getElementById(id); if(el) el.value = '';
                });
                document.getElementById('rapport-statut-equipement').value = 'Conforme';
                document.getElementById('btn-download-pdf').classList.add('hidden');
                document.getElementById('sig-tech-preview-container')?.classList.add('hidden');
                document.getElementById('sig-client-preview-container')?.classList.add('hidden');
            }
        }

        function addRapportPhotos(e) {
            const files = Array.from(e.target.files);
            const rad = document.querySelector('input[name="photo-category-select"]:checked');
            const category = rad ? rad.value : 'avant';

            files.forEach(file => {
                file.type_photo = category;
                rapportPhotoFiles.push(file);

                const reader = new FileReader();
                reader.onload = ev => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    wrapper.innerHTML = `
                        <img src="${ev.target.result}" class="w-full aspect-square object-cover rounded-lg border border-emerald-500/40">
                        <span class="absolute bottom-1 left-1 px-1.5 py-0.5 bg-slate-900/80 text-white text-[8px] font-bold rounded uppercase">${category}</span>
                        <button type="button" onclick="removeRapportPhoto(this, '${file.name}')" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-600 text-white rounded-full text-[9px] flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-xmark"></i>
                        </button>`;
                    document.getElementById('rapport-photos-preview').appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
            document.getElementById('rapport-photos-count').innerText = `${rapportPhotoFiles.length} photo(s) sélectionnée(s)`;
        }

        function removeRapportPhoto(btn, name) {
            rapportPhotoFiles = rapportPhotoFiles.filter(f => f.name !== name);
            btn.closest('div').remove();
            document.getElementById('rapport-photos-count').innerText = `${rapportPhotoFiles.length} photo(s) sélectionnée(s)`;
        }

        function addRapportVideo(e) {
            const files = Array.from(e.target.files);
            rapportVideoFiles.push(...files);
            const preview = document.getElementById('rapport-videos-preview');
            files.forEach(file => {
                const div = document.createElement('div');
                div.className = 'flex items-center space-x-2 p-2.5 rounded-lg bg-blue-500/10 border border-blue-500/30 text-xs text-blue-300';
                div.innerHTML = `<i class="fa-solid fa-video"></i><span class="flex-1 truncate">${file.name}</span><span class="text-slate-500">${(file.size/1024/1024).toFixed(1)}MB</span>`;
                preview.appendChild(div);
            });
            document.getElementById('rapport-videos-count').innerText = `${rapportVideoFiles.length} vidéo(s) sélectionnée(s)`;
        }

        async function handleRapportSubmit(e) {
            e.preventDefault();
            if (!selectedIntervention) return;

            const btn = document.getElementById('btn-submit-rapport');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i><span>Envoi en cours...</span>';

            const fd = new FormData();
            fd.append('travaux_effectues',   document.getElementById('rapport-travaux').value);
            fd.append('observations',        document.getElementById('rapport-observations').value);
            fd.append('recommandations',     document.getElementById('rapport-recommandations').value);
            fd.append('commentaire',         document.getElementById('rapport-commentaire').value);
            fd.append('qrcode_scanne',       document.getElementById('rapport-qrcode').value);
            fd.append('statut_equipement',   document.getElementById('rapport-statut-equipement').value);
            fd.append('gps_latitude',        document.getElementById('rapport-gps-lat').value);
            fd.append('gps_longitude',       document.getElementById('rapport-gps-lng').value);
            fd.append('gps_adresse',         document.getElementById('rapport-gps-addr').value);

            // Export Signatures Canvas Base64
            if (canvasTech) {
                const dataUrlTech = canvasTech.toDataURL('image/png');
                if (dataUrlTech && dataUrlTech.length > 500) {
                    fd.append('signature_technicien', dataUrlTech);
                }
            }
            if (canvasClient) {
                const dataUrlClient = canvasClient.toDataURL('image/png');
                if (dataUrlClient && dataUrlClient.length > 500) {
                    fd.append('signature_client', dataUrlClient);
                }
            }

            // Paper Signatures Files if selected
            const fileTechInput = document.getElementById('sig-tech-file-input');
            if (fileTechInput && fileTechInput.files[0]) {
                fd.append('signature_technicien_file', fileTechInput.files[0]);
            }
            const fileClientInput = document.getElementById('sig-client-file-input');
            if (fileClientInput && fileClientInput.files[0]) {
                fd.append('signature_client_file', fileClientInput.files[0]);
            }

            rapportPhotoFiles.forEach(f => {
                fd.append('photos[]', f);
                fd.append('photos_types[]', f.type_photo || 'avant');
            });
            rapportVideoFiles.forEach(f => fd.append('videos[]', f));

            const res = await apiFetchWithProgress(`/interventions/${selectedIntervention.id}/rapport`, 'POST', fd, 3);

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up text-xs"></i><span>Enregistrer le Rapport</span>';

            if (res.success) {
                showToast('Rapport enregistré avec succès !', 'success');
                rapportPhotoFiles = [];
                rapportVideoFiles = [];
                openInterventionDetails(selectedIntervention.id);
            } else if (res.offline) {
                showToast('Rapport sauvegardé localement en mode Hors-Ligne !', 'info');
                openInterventionDetails(selectedIntervention.id);
            } else {
                showToast(res.message || 'Erreur sauvegarde du rapport.', 'error');
            }
        }

        // WEBRTC LIVE CAMERA API HANDLER
        let currentCameraFacingMode = 'environment';
        let activeCameraStream = null;
        let targetPhotoFieldId = null;

        async function openLiveCameraModal(targetFieldId = null) {
            targetPhotoFieldId = targetFieldId;
            document.getElementById('modal-live-camera').classList.remove('hidden');
            await startCameraStream();
        }

        async function startCameraStream() {
            if (activeCameraStream) {
                activeCameraStream.getTracks().forEach(t => t.stop());
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: currentCameraFacingMode, width: { ideal: 1920 }, height: { ideal: 1080 } },
                    audio: false
                });
                activeCameraStream = stream;
                const video = document.getElementById('live-camera-feed');
                video.srcObject = stream;
            } catch(err) {
                showToast('Accès caméra non disponible. Choisissez une image dans la galerie.', 'error');
            }
        }

        function switchCameraFacingMode() {
            currentCameraFacingMode = (currentCameraFacingMode === 'environment') ? 'user' : 'environment';
            startCameraStream();
        }

        function closeLiveCameraModal() {
            document.getElementById('modal-live-camera').classList.add('hidden');
            if (activeCameraStream) {
                activeCameraStream.getTracks().forEach(t => t.stop());
                activeCameraStream = null;
            }
        }

        function captureLiveCameraPhoto() {
            const video = document.getElementById('live-camera-feed');
            const canvas = document.getElementById('live-camera-canvas');
            if (!video || !video.videoWidth) {
                showToast('Flux caméra non actif.', 'error');
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.90);

            canvas.toBlob(blob => {
                const timestamp = Date.now();
                const file = new File([blob], `photo_${timestamp}.jpg`, { type: 'image/jpeg' });

                if (targetPhotoFieldId) {
                    addDynamicFormPhotoFile(targetPhotoFieldId, file, dataUrl);
                } else {
                    addRapportPhotoDirect(file, dataUrl);
                }

                showToast('Photo capturée avec succès ! 📸', 'success');
                closeLiveCameraModal();
            }, 'image/jpeg', 0.90);
        }

        function addRapportPhotoDirect(file, dataUrl) {
            rapportPhotoFiles.push(file);
            const preview = document.getElementById('rapport-photos-preview');
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';
            wrapper.innerHTML = `
                <img src="${dataUrl}" class="w-full aspect-square object-cover rounded-lg border border-emerald-500/40 shadow">
                <button type="button" onclick="removeRapportPhoto(this, '${file.name}')" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-600 text-white rounded-full text-[9px] flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>`;
            preview.appendChild(wrapper);
            document.getElementById('rapport-photos-count').innerText = `${rapportPhotoFiles.length} photo(s) sélectionnée(s)`;
        }

        function addDynamicFormPhotoFile(qId, file, dataUrl) {
            const preview = document.getElementById(`photo-preview-${qId}`);
            if (!preview) return;
            const img = document.createElement('img');
            img.src = dataUrl;
            img.className = 'w-full aspect-square object-cover rounded-lg border border-emerald-500/40 shadow';
            preview.appendChild(img);

            // Create hidden input with base64 content so form serialization includes it
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `q_${qId}_captured[]`;
            hidden.value = dataUrl;
            preview.appendChild(hidden);
        }

        // QR SCAN (Rapport)
        async function scanQrCodeForRapport() {
            document.getElementById('modal-qr-scan').classList.remove('hidden');
            // Ouvrir la caméra pour le scan
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                document.getElementById('qr-video-preview').srcObject = stream;
                window._qrScanTarget = 'rapport-qrcode';
                window._qrStream = stream;
            } catch(err) {
                showToast('Caméra non disponible. Saisissez manuellement.', 'error');
            }
        }

        async function scanQrForField(fieldId) {
            document.getElementById('modal-qr-scan').classList.remove('hidden');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                document.getElementById('qr-video-preview').srcObject = stream;
                window._qrScanTarget = fieldId;
                window._qrStream = stream;
            } catch(err) {
                showToast('Caméra non disponible. Saisissez manuellement.', 'error');
            }
        }

        function closeQrScanModal() {
            document.getElementById('modal-qr-scan').classList.add('hidden');
            if (window._qrStream) {
                window._qrStream.getTracks().forEach(t => t.stop());
                window._qrStream = null;
            }
        }

        function confirmQrManualEntry() {
            const val = document.getElementById('qr-manual-input').value.trim();
            if (val && window._qrScanTarget) {
                const target = document.getElementById(window._qrScanTarget);
                if (target) { target.value = val; target.dispatchEvent(new Event('input')); }
                showToast(`QR Code saisi : ${val}`, 'success');
            }
            document.getElementById('qr-manual-input').value = '';
            closeQrScanModal();
        }

        // GPS Field capture
        function captureGpsForField(qId) {
            const statusEl = document.getElementById(`gps-status-${qId}`);
            const hiddenEl = document.getElementById(`q_gps_${qId}`);
            statusEl.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Acquisition GPS...';
            if (!navigator.geolocation) {
                statusEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1 text-red-400"></i> Géolocalisation non disponible';
                return;
            }
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                const acc = Math.round(pos.coords.accuracy);
                hiddenEl.value = `${lat},${lng}`;
                statusEl.innerHTML = `<i class="fa-solid fa-location-dot mr-1 text-emerald-400"></i> <strong>${lat}, ${lng}</strong> <span class="text-slate-500">(±${acc}m)</span>`;
                statusEl.className = 'p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-[11px] text-emerald-300';
            }, err => {
                statusEl.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1 text-red-400"></i> Erreur : ${err.message}`;
            }, { enableHighAccuracy: true });
        }

        // Signature canvas
        function initSignatureCanvas(qId) {
            const canvas = document.getElementById(`sig-canvas-${qId}`);
            if (!canvas) return;
            canvas.width  = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            const ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#6366f1';
            ctx.lineWidth   = 2;
            ctx.lineCap     = 'round';
            let drawing = false;

            const getPos = (e) => {
                const r = canvas.getBoundingClientRect();
                const src = e.touches ? e.touches[0] : e;
                return { x: src.clientX - r.left, y: src.clientY - r.top };
            };

            const startDraw = (e) => { drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); e.preventDefault(); };
            const draw = (e) => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); e.preventDefault(); };
            const endDraw = () => { if (!drawing) return; drawing = false; document.getElementById(`q_sig_${qId}`).value = canvas.toDataURL(); };

            canvas.addEventListener('mousedown',  startDraw);
            canvas.addEventListener('mousemove',  draw);
            canvas.addEventListener('mouseup',    endDraw);
            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove',  draw,      { passive: false });
            canvas.addEventListener('touchend',   endDraw);
        }

        function clearSignatureCanvas(qId) {
            const canvas = document.getElementById(`sig-canvas-${qId}`);
            if (!canvas) return;
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById(`q_sig_${qId}`).value = '';
        }

        // Photo preview in dynamic form
        function previewFormPhoto(e, qId) {
            let files = Array.from(e.target.files);
            const max = e.target.dataset.max ? parseInt(e.target.dataset.max, 10) : null;
            if (max && files.length > max) {
                showToast(`Maximum ${max} photo(s) autorisée(s) pour ce champ.`, 'error');
                files = files.slice(0, max);
                // Tronque réellement le FileList soumis (pas seulement l'aperçu)
                const dt = new DataTransfer();
                files.forEach(f => dt.items.add(f));
                e.target.files = dt.files;
            }
            const preview = document.getElementById(`photo-preview-${qId}`);
            preview.innerHTML = '';
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.className = 'w-full aspect-square object-cover rounded-lg border border-emerald-500/30';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        function previewFormVideo(e, qId) {
            const file = e.target.files[0];
            if (!file) return;
            const prev = document.getElementById(`video-preview-${qId}`);
            prev.innerHTML = `<div class="flex items-center space-x-2 p-2 rounded-lg bg-blue-500/10 border border-blue-500/30 text-xs text-blue-300"><i class="fa-solid fa-video"></i><span class="truncate">${file.name}</span></div>`;
        }

        function showDocName(e, qId) {
            const file = e.target.files[0];
            const el = document.getElementById(`doc-name-${qId}`);
            if (el && file) el.innerText = `📎 ${file.name} (${(file.size/1024).toFixed(0)} Ko)`;
        }

        // MODULE 10: NOTIFICATIONS
        async function loadNotificationsData() {
            const container = document.getElementById('notifications-list-container');
            container.innerHTML = `<div class="py-8 text-center"><i class="fa-solid fa-circle-notch fa-spin text-brand-600 text-lg"></i></div>`;

            const res = await apiFetch('/notifications');
            if (res.success && res.data && res.data.notifications) {
                const items = res.data.notifications.data || [];
                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="glass-panel p-8 rounded-2xl text-center space-y-2 border border-slate-200 bg-white shadow-sm">
                            <i class="fa-solid fa-bell-slash text-slate-300 text-2xl"></i>
                            <p class="text-xs text-slate-500 italic">Aucune notification.</p>
                        </div>`;
                    return;
                }

                container.innerHTML = items.map(n => `
                    <div class="glass-panel p-3.5 rounded-xl border ${n.read_at ? 'border-slate-200 bg-white opacity-60' : 'border-brand-300 bg-brand-50'} space-y-1 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                ${!n.read_at ? `<span class="w-2 h-2 rounded-full bg-brand-600 shrink-0"></span>` : ''}
                                <span class="text-xs font-bold text-slate-900">${n.data ? n.data.titre : 'Notification'}</span>
                            </div>
                            ${!n.read_at ? `<button onclick="markNotificationAsRead('${n.id}')" class="text-[10px] text-brand-600 font-bold hover:underline shrink-0">Marquer lu</button>` : ''}
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">${n.data ? n.data.message : ''}</p>
                        <span class="text-[9px] text-slate-400 font-medium block">${formatDateDisplay(n.created_at)}</span>
                    </div>
                `).join('');
            }
        }

        async function markNotificationAsRead(id) {
            await apiFetch(`/notifications/${id}/read`, 'POST');
            loadNotificationsData();
        }

        async function markAllNotificationsAsRead() {
            await apiFetch('/notifications/read-all', 'POST');
            showToast('Toutes les notifications sont marquées comme lues.', 'success');
            loadNotificationsData();
        }

        // MODULE 11: PROFIL TECHNICIEN
        async function loadProfileData() {
            const res = await apiFetch('/profile');
            if (res.success && res.data && res.data.user) {
                const u = res.data.user;
                document.getElementById('profile-name-text').innerText = `${u.name} ${u.prenom || ''}`;
                document.getElementById('prof-name').value = u.name || '';
                document.getElementById('prof-prenom').value = u.prenom || '';
                document.getElementById('prof-email').value = u.email || '';
                document.getElementById('prof-phone').value = u.telephone || '';
                document.getElementById('prof-address').value = u.adresse || '';

                if (u.photo_url) {
                    document.getElementById('profile-avatar-display').innerHTML = `<img src="${u.photo_url}" class="w-full h-full object-cover">`;
                }
            }
        }

        async function handleProfileUpdate(e) {
            e.preventDefault();
            const body = {
                name: document.getElementById('prof-name').value,
                prenom: document.getElementById('prof-prenom').value,
                email: document.getElementById('prof-email').value,
                telephone: document.getElementById('prof-phone').value,
                adresse: document.getElementById('prof-address').value
            };

            const res = await apiFetch('/profile', 'PUT', body);
            if (res.success) {
                showToast('Profil mis à jour !', 'success');
                loadProfileData();
            } else {
                showToast(res.message || 'Erreur mise à jour profil.', 'error');
            }
        }

        async function handlePasswordUpdate(e) {
            e.preventDefault();
            const body = {
                current_password: document.getElementById('pass-current').value,
                password: document.getElementById('pass-new').value,
                password_confirmation: document.getElementById('pass-confirm').value
            };

            const res = await apiFetch('/profile/password', 'PUT', body);
            if (res.success) {
                showToast('Mot de passe modifié !', 'success');
                document.getElementById('profile-password-form').reset();
            } else {
                showToast(res.message || 'Erreur modification mot de passe.', 'error');
            }
        }

        async function uploadProfilePhoto(e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('photo', file);

            const res = await apiFetch('/profile/photo', 'POST', formData, true);
            if (res.success) {
                showToast('Photo de profil mise à jour !', 'success');
                loadProfileData();
            } else {
                showToast(res.message || 'Erreur upload photo.', 'error');
            }
        }

        // CONFIG MODAL & UTILS
        function toggleConfigModal() {
            document.getElementById('modal-config-api').classList.toggle('hidden');
        }

        function saveApiUrlConfig() {
            const val = document.getElementById('config-api-url').value.trim();
            if (val) {
                API_BASE_URL = val;
                localStorage.setItem('API_BASE_URL', val);
                showToast('URL API mise à jour.', 'success');
                toggleConfigModal();
            }
        }

        function resetApiUrlDefault() {
            API_BASE_URL = '/api';
            localStorage.removeItem('API_BASE_URL');
            document.getElementById('config-api-url').value = API_BASE_URL;
            showToast('URL API réinitialisée par défaut.', 'info');
            toggleConfigModal();
        }

        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            if (input.type === 'password') { input.type = 'text'; } else { input.type = 'password'; }
        }

        function showToast(msg, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colorClass = type === 'success' ? 'border-emerald-300 bg-emerald-50 text-emerald-800 shadow-emerald-100' :
                             type === 'error' ? 'border-red-300 bg-red-50 text-red-800 shadow-red-100' :
                             'border-indigo-300 bg-indigo-50 text-indigo-800 shadow-indigo-100';

            toast.className = `p-3.5 rounded-xl border text-xs font-bold shadow-lg flex items-center justify-between pointer-events-auto animate-fade-in ${colorClass}`;
            toast.innerHTML = `<span>${msg}</span><button onclick="this.parentElement.remove()" class="ml-2 text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark"></i></button>`;

            container.appendChild(toast);
            setTimeout(() => { if (toast.parentElement) toast.remove(); }, 3500);
        }

        function getStatusClass(statut) {
            switch (statut) {
                case 'Demande': return 'bg-sky-100 text-sky-800 border border-sky-300';
                case 'Planifiee': return 'bg-amber-100 text-amber-800 border border-amber-300';
                case 'Affectee': return 'bg-cyan-100 text-cyan-800 border border-cyan-300';
                case 'En attente reafectation': return 'bg-rose-100 text-rose-800 border border-rose-300';
                case 'Acceptee': return 'bg-blue-100 text-blue-800 border border-blue-300';
                case 'Refusee': return 'bg-rose-100 text-rose-800 border border-rose-300';
                case 'En cours': return 'bg-indigo-100 text-indigo-800 border border-indigo-300';
                case 'Suspendue': return 'bg-orange-100 text-orange-800 border border-orange-300';
                case 'Reportee': return 'bg-amber-100 text-amber-800 border border-amber-300';
                case 'Formulaire rempli': return 'bg-purple-100 text-purple-800 border border-purple-300';
                case 'En attente validation': return 'bg-purple-100 text-purple-800 border border-purple-300';
                case 'Rejetee': return 'bg-red-100 text-red-800 border border-red-300';
                case 'Terminee': return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
                case 'Validee': return 'bg-emerald-100 text-emerald-900 border border-emerald-400 font-extrabold';
                case 'Rouverte': return 'bg-teal-100 text-teal-800 border border-teal-300';
                case 'Annulee': return 'bg-red-100 text-red-800 border border-red-300';
                default: return 'bg-slate-200 text-slate-700 border border-slate-300';
            }
        }

        // Libellé accentué affiché à l'écran — les clés techniques ci-dessus (getStatusClass)
        // ne changent jamais de valeur, seul ce libellé change (même mapping que
        // App\Models\Intervention::statutLabel() côté serveur).
        function getStatusLabel(statut) {
            const labels = {
                'Demande': 'Demande reçue',
                'Planifiee': 'Planifiée',
                'Affectee': 'Affectée',
                'En attente reafectation': 'Refus Technicien',
                'Acceptee': 'Acceptée',
                'Refusee': 'Refusée',
                'En cours': 'En cours',
                'Suspendue': 'Suspendue',
                'Reportee': 'Reportée',
                'Formulaire rempli': 'Formulaire rempli',
                'En attente validation': 'En validation',
                'Rejetee': 'Rejetée',
                'Terminee': 'Terminée',
                'Validee': 'Validée',
                'Rouverte': 'Rouverte',
                'Annulee': 'Annulée',
            };
            return labels[statut] || statut;
        }

        function getPrioriteClass(priorite) {
            switch (priorite) {
                case 'Urgente': return 'text-red-600 font-extrabold';
                case 'Haute': return 'text-amber-600 font-bold';
                default: return 'text-slate-600';
            }
        }

        function formatDateDisplay(dateStr) {
            if (!dateStr) return '--/--/----';
            try {
                const d = new Date(dateStr);
                return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                return dateStr;
            }
        }
    </script>

<!-- ============================================================ -->
<!-- MODAL : Progression Tâche                                    -->
<!-- ============================================================ -->
<div id="modal-task-progress"
     class="hidden fixed inset-0 z-[9999] items-end justify-center bg-black/50 backdrop-blur-sm"
     onclick="if(event.target===this) closeProgressModal()">
    <div class="w-full max-w-md bg-white rounded-t-3xl shadow-2xl px-6 pt-6 pb-8 space-y-5 animate-slide-up">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple text-indigo-600 text-xs"></i>
                </span>
                <div>
                    <p class="text-sm font-extrabold text-slate-800">Progression de la tâche</p>
                    <p class="text-[11px] text-slate-400">Faites glisser pour ajuster</p>
                </div>
            </div>
            <button onclick="closeProgressModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 transition">
                <i class="fa-solid fa-xmark text-slate-500 text-xs"></i>
            </button>
        </div>

        <!-- Valeur centrale -->
        <div class="flex items-center justify-center">
            <span id="modal-progress-value" class="text-4xl font-black text-indigo-600 tabular-nums">0%</span>
        </div>

        <!-- Slider -->
        <div class="space-y-2">
            <input
                type="range"
                id="modal-progress-slider"
                min="0" max="100" step="5" value="0"
                class="w-full h-3 rounded-full appearance-none cursor-pointer accent-indigo-600 bg-slate-200"
            >
            <div class="flex justify-between text-[10px] text-slate-400 font-medium">
                <span>0%</span>
                <span>25%</span>
                <span>50%</span>
                <span>75%</span>
                <span>100%</span>
            </div>
        </div>

        <!-- Boutons rapides -->
        <div class="grid grid-cols-5 gap-1.5">
            <button onclick="setProgressQuick(0)"  class="py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 font-bold text-[10px] transition border border-slate-200">0%</button>
            <button onclick="setProgressQuick(25)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 font-bold text-[10px] transition border border-slate-200">25%</button>
            <button onclick="setProgressQuick(50)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 font-bold text-[10px] transition border border-slate-200">50%</button>
            <button onclick="setProgressQuick(75)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 font-bold text-[10px] transition border border-slate-200">75%</button>
            <button onclick="setProgressQuick(100)" class="py-1.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-bold text-[10px] transition border border-emerald-200">100%</button>
        </div>

        <!-- Action -->
        <button
            id="btn-submit-progress"
            onclick="submitTaskProgress()"
            class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-extrabold rounded-2xl text-sm shadow-lg transition flex items-center justify-center space-x-2 disabled:opacity-60"
        >
            <i class="fa-solid fa-floppy-disk text-sm"></i>
            <span>Enregistrer la progression</span>
        </button>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL : Commentaire / Note sur une Tâche                    -->
<!-- ============================================================ -->
<div id="modal-task-comment"
     class="hidden fixed inset-0 z-[9999] items-end justify-center bg-black/50 backdrop-blur-sm"
     onclick="if(event.target===this) closeCommentModal()">
    <div class="w-full max-w-md bg-white rounded-t-3xl shadow-2xl px-6 pt-6 pb-8 space-y-4 animate-slide-up">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-comment-dots text-amber-600 text-xs"></i>
                </span>
                <div>
                    <p class="text-sm font-extrabold text-slate-800">Note sur la tâche</p>
                    <p class="text-[11px] text-slate-400">Observations, difficultés, remarques...</p>
                </div>
            </div>
            <button onclick="closeCommentModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 transition">
                <i class="fa-solid fa-xmark text-slate-500 text-xs"></i>
            </button>
        </div>

        <!-- Textarea -->
        <div>
            <textarea
                id="modal-comment-textarea"
                rows="4"
                placeholder="Ex: Zone difficile d'accès, câble remplacé, client informé..."
                class="w-full px-4 py-3 text-xs border border-slate-200 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition resize-none text-slate-800 placeholder-slate-400 leading-relaxed"
            ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex space-x-3">
            <button
                onclick="closeCommentModal()"
                class="flex-1 py-3 border border-slate-200 text-slate-600 font-bold rounded-2xl text-xs hover:bg-slate-50 transition"
            >
                Annuler
            </button>
            <button
                id="btn-submit-comment"
                onclick="submitTaskComment()"
                class="flex-1 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-extrabold rounded-2xl text-xs shadow-md transition flex items-center justify-center space-x-2 disabled:opacity-60"
            >
                <i class="fa-solid fa-check text-xs"></i>
                <span>Enregistrer la note</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Helper pour les boutons rapides de progression
    function setProgressQuick(val) {
        const slider = document.getElementById('modal-progress-slider');
        const display = document.getElementById('modal-progress-value');
        slider.value = val;
        display.textContent = val + '%';
    }
</script>

<style>
    @keyframes slide-up {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .animate-slide-up { animation: slide-up 0.25s cubic-bezier(.22,1,.36,1) both; }
</style>

</body>
</html>
