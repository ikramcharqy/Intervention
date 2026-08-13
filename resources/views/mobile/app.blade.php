<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Technicien Mobile — Intervention App</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .glass-panel { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.04); }
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
                    <!-- Accept Button -->
                    <button id="btn-action-accept" onclick="executeAcceptIntervention()" class="hidden w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-check-circle text-xs"></i>
                        <span>Accepter la mission</span>
                    </button>

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
                        <span>Gérer le Rapport</span>
                    </button>

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
                    <div id="tab-content-materiaux" class="detail-tab-pane hidden glass-panel p-4 rounded-xl text-xs space-y-2 bg-white border border-slate-200">
                        <div id="detail-materiaux-list" class="space-y-2">
                            <!-- Materials list -->
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

                    <!-- Section : Photos du rapport -->
                    <div class="glass-panel p-4 rounded-2xl space-y-3 bg-white border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-camera"></i><span>Photos d'Intervention</span>
                        </h3>
                        <p class="text-[11px] text-slate-500">Prenez des photos en direct via l'API Caméra WebRTC ou importez depuis la galerie.</p>

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
                        <p class="text-[11px] text-slate-500">Enregistrez une vidéo de démonstration (test connexion, validation système, etc.).</p>

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
            } else {
                navigateTo('login');
            }
        });

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
                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${getStatusClass(item.statut)}">${item.statut}</span>
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
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${getStatusClass(item.statut)}">${item.statut}</span>
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
            statusPill.innerText = item.statut;
            statusPill.className = `px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase ${getStatusClass(item.statut)}`;

            document.getElementById('detail-priorite-badge').innerText = item.priorite || 'Normale';
            document.getElementById('detail-date-prevue').innerText = formatDateDisplay(item.date_prevue_debut);
            document.getElementById('detail-desc-text').innerText = item.description || 'Aucune description disponible.';
            document.getElementById('detail-mode-suivi-badge').innerText = item.mode_suivi || 'MANUEL';

            // ACTION BUTTONS CONTROLS
            const btnAccept = document.getElementById('btn-action-accept');
            const btnStart = document.getElementById('btn-action-start');
            const btnForm = document.getElementById('btn-action-form');
            const btnRapport = document.getElementById('btn-action-rapport');
            const btnValidate = document.getElementById('btn-action-validate');

            btnAccept.classList.add('hidden');
            btnStart.classList.add('hidden');
            btnForm.classList.add('hidden');
            btnRapport.classList.add('hidden');
            btnValidate.classList.add('hidden');

            if (item.statut === 'Planifiee') {
                btnAccept.classList.remove('hidden');
            } else if (item.statut === 'Acceptee') {
                btnStart.classList.remove('hidden');
            } else if (item.statut === 'En cours') {
                btnForm.classList.remove('hidden');
                btnRapport.classList.remove('hidden');
            } else if (item.statut === 'Formulaire rempli') {
                btnForm.classList.remove('hidden');
                btnRapport.classList.remove('hidden');
                btnValidate.classList.remove('hidden');
            }

            // Render sub-tabs content
            renderDetailTabs();
        }

        function renderDetailTabs() {
            const item = selectedIntervention;
            if (!item) return;

            // Taches
            const containerTaches = document.getElementById('detail-taches-list');
            if (item.taches && item.taches.length > 0) {
                containerTaches.innerHTML = item.taches.map(t => `
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="font-medium text-slate-800">${t.tache ? t.tache.nom : 'Tâche'}</span>
                        <span class="text-[10px] font-bold ${t.statut === 'Terminee' ? 'text-emerald-600' : 'text-slate-500'}">${t.statut || 'En attente'}</span>
                    </div>
                `).join('');
            } else {
                containerTaches.innerHTML = `<p class="text-slate-400 italic">Aucune tâche assignée.</p>`;
            }

            // Materiaux
            const containerMateriaux = document.getElementById('detail-materiaux-list');
            if (item.materiaux && item.materiaux.length > 0) {
                containerMateriaux.innerHTML = item.materiaux.map(m => `
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="font-medium text-slate-800">${m.materiau ? m.materiau.nom : 'Matériau'}</span>
                        <span class="text-xs font-bold text-indigo-600">Qté: ${m.quantite}</span>
                    </div>
                `).join('');
            } else {
                containerMateriaux.innerHTML = `<p class="text-slate-400 italic">Aucun matériau enregistré.</p>`;
            }

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

        // ACTIONS EXECUTION
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

                    // Send position point every 30s
                    if (trackingSecondsCounter % 30 === 0 && activeGpsSessionId) {
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

            container.innerHTML = formulaire.questions.map(q => {
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
                        inputHtml = `<input type="number" step="any" name="q_${q.id}" ${req} placeholder="${ph || '0'}" value="${def}" class="${BASE}">`;
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
                            <div class="grid grid-cols-2 gap-2.5 sm:gap-3 w-full pt-1">
                                <label class="flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-emerald-500/30 bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 cursor-pointer transition shadow-sm text-xs group">
                                    <input type="radio" name="q_${q.id}" value="Oui" ${req} class="hidden">
                                    <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                                    <span>Oui</span>
                                </label>
                                <label class="flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-red-500/30 bg-red-50 text-red-700 font-bold hover:bg-red-100 cursor-pointer transition shadow-sm text-xs group">
                                    <input type="radio" name="q_${q.id}" value="Non" class="hidden">
                                    <i class="fa-solid fa-xmark text-red-600 text-xs"></i>
                                    <span>Non</span>
                                </label>
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
                                    <input type="file" id="q_photo_gal_${q.id}" name="q_${q.id}" accept="image/*" multiple class="hidden" onchange="previewFormPhoto(event, ${q.id})">
                                </div>
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

                return `
                    <div class="glass-panel p-4 rounded-xl flex flex-col space-y-2 border border-slate-200 bg-white shadow-sm">
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
        }

        async function handleFormulaireSubmit(e) {
            e.preventDefault();
            if (!selectedIntervention) return;

            const formEl = document.getElementById('dynamic-form-element');
            const formData = new FormData(formEl);
            const reponses = {};

            formData.forEach((val, key) => {
                if (key.startsWith('q_')) {
                    const qId = key.replace('q_', '');
                    reponses[qId] = val;
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

        async function openRapportScreen() {
            if (!selectedIntervention) return;
            navigateTo('rapport');

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
                document.getElementById('btn-download-pdf').classList.remove('hidden');

                // Show existing photos as thumbnails (read-only)
                if (r.photos && r.photos.length > 0) {
                    const preview = document.getElementById('rapport-photos-preview');
                    r.photos.forEach(p => {
                        const img = document.createElement('img');
                        img.src = `/storage/${p.chemin}`;
                        img.className = 'w-full aspect-square object-cover rounded-lg border border-slate-700';
                        preview.appendChild(img);
                    });
                }

                // Show existing videos list
                if (r.videos && r.videos.length > 0) {
                    const vidPrev = document.getElementById('rapport-videos-preview');
                    r.videos.forEach(v => {
                        const div = document.createElement('div');
                        div.className = 'flex items-center space-x-2 p-2 rounded-lg bg-slate-800/60 border border-slate-700 text-xs text-slate-300';
                        div.innerHTML = `<i class="fa-solid fa-video text-blue-400"></i><span>${v.nom_original || v.chemin}</span>`;
                        vidPrev.appendChild(div);
                    });
                }
            } else {
                ['rapport-travaux','rapport-observations','rapport-recommandations','rapport-commentaire','rapport-qrcode'].forEach(id => {
                    const el = document.getElementById(id); if(el) el.value = '';
                });
                document.getElementById('rapport-statut-equipement').value = 'Conforme';
                document.getElementById('btn-download-pdf').classList.add('hidden');
            }
        }

        function addRapportPhotos(e) {
            const files = Array.from(e.target.files);
            rapportPhotoFiles.push(...files);
            const preview = document.getElementById('rapport-photos-preview');
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = ev => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    wrapper.innerHTML = `
                        <img src="${ev.target.result}" class="w-full aspect-square object-cover rounded-lg border border-emerald-500/40">
                        <button type="button" onclick="removeRapportPhoto(this, '${file.name}')" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-600 text-white rounded-full text-[9px] flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-xmark"></i>
                        </button>`;
                    preview.appendChild(wrapper);
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

            rapportPhotoFiles.forEach(f => fd.append('photos[]', f));
            rapportVideoFiles.forEach(f => fd.append('videos[]', f));

            const res = await apiFetch(`/interventions/${selectedIntervention.id}/rapport`, 'POST', fd, true);

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up text-xs"></i><span>Enregistrer le Rapport</span>';

            if (res.success) {
                showToast('Rapport enregistré avec succès !', 'success');
                rapportPhotoFiles = [];
                rapportVideoFiles = [];
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
            const files = Array.from(e.target.files);
            const preview = document.getElementById(`photo-preview-${qId}`);
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
                case 'Planifiee': return 'bg-amber-100 text-amber-800 border border-amber-300';
                case 'Acceptee': return 'bg-blue-100 text-blue-800 border border-blue-300';
                case 'En cours': return 'bg-indigo-100 text-indigo-800 border border-indigo-300';
                case 'Formulaire rempli': return 'bg-purple-100 text-purple-800 border border-purple-300';
                case 'Terminee': return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
                case 'Annulee': return 'bg-red-100 text-red-800 border border-red-300';
                default: return 'bg-slate-200 text-slate-700 border border-slate-300';
            }
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
</body>
</html>
