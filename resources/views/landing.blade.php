<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50 scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TechniTrack • La gestion d'interventions, simplifiée</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-technitrack-icon.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Assets Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .hero-bg {
            background-image: linear-gradient(115deg, rgba(15, 23, 42, 0.86) 0%, rgba(15, 23, 42, 0.72) 45%, rgba(79, 70, 229, 0.55) 100%), url('https://images.pexels.com/photos/35072822/pexels-photo-35072822/free-photo-of-technician-working-inside-industrial-electrical-panel.jpeg?auto=compress&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="antialiased text-slate-900 selection:bg-indigo-500 selection:text-white">

    <!-- ===================== HEADER ===================== -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex items-center">
                    <img src="{{ asset('images/logo-technitrack-full.png') }}" alt="TechniTrack" class="h-11 sm:h-12 w-auto object-contain">
                </a>

                <!-- Menu horizontal -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="#accueil" class="hover:text-indigo-600 transition">Accueil</a>
                    <a href="#services" class="hover:text-indigo-600 transition">Services</a>
                    <a href="#a-propos" class="hover:text-indigo-600 transition">À propos</a>
                    <a href="#contact" class="hover:text-indigo-600 transition">Contact</a>
                </nav>

                <!-- Espaces de connexion -->
                <div class="hidden sm:flex items-center gap-2.5">
                    <a href="{{ route('espace.client') }}" class="ui-btn ui-btn-secondary text-xs">
                        <x-icon name="user" class="w-3.5 h-3.5" />
                        <span>Espace Client</span>
                    </a>
                    <a href="{{ route('espace.employe') }}" class="ui-btn ui-btn-primary text-xs">
                        <x-icon name="briefcase" class="w-3.5 h-3.5" />
                        <span>Espace Employé</span>
                    </a>
                </div>

                <!-- Menu mobile -->
                <div x-data="{ open: false }" class="sm:hidden">
                    <button @click="open = !open" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 text-slate-600">
                        <x-icon name="x" class="w-4 h-4" x-show="open" x-cloak />
                        <x-icon name="bars" class="w-4 h-4" x-show="!open" />
                    </button>
                    <div x-cloak x-show="open" x-transition
                         class="absolute top-20 inset-x-0 bg-white border-b border-slate-200 shadow-lg p-4 space-y-3">
                        <nav class="flex flex-col gap-1 text-sm font-semibold text-slate-700">
                            <a href="#accueil" class="px-2 py-2 rounded-lg hover:bg-slate-50">Accueil</a>
                            <a href="#services" class="px-2 py-2 rounded-lg hover:bg-slate-50">Services</a>
                            <a href="#a-propos" class="px-2 py-2 rounded-lg hover:bg-slate-50">À propos</a>
                            <a href="#contact" class="px-2 py-2 rounded-lg hover:bg-slate-50">Contact</a>
                        </nav>
                        <div class="flex flex-col gap-2 pt-2 border-t border-slate-100">
                            <a href="{{ route('espace.client') }}" class="ui-btn ui-btn-secondary text-xs w-full">
                                <x-icon name="user" class="w-3.5 h-3.5" /><span>Espace Client</span>
                            </a>
                            <a href="{{ route('espace.employe') }}" class="ui-btn ui-btn-primary text-xs w-full">
                                <x-icon name="briefcase" class="w-3.5 h-3.5" /><span>Espace Employé</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===================== HERO ===================== -->
    <section id="accueil" class="hero-bg pt-32 pb-24 sm:pt-40 sm:pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-bold uppercase tracking-wider mb-5">
                    <x-icon name="bolt-solid" class="w-3 h-3 text-amber-300" />
                    Plateforme marocaine de gestion technique
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    La gestion d'interventions, simplifiée
                </h1>
                <p class="mt-5 text-sm sm:text-base text-slate-200 leading-relaxed max-w-xl">
                    TechniTrack accompagne les entreprises marocaines dans le suivi de leurs interventions techniques et chantiers, en temps réel.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="#services" class="ui-btn ui-btn-primary px-5 py-3 text-sm">
                        <span>Découvrir nos services</span>
                        <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                    </a>
                    <a href="{{ route('espace.client') }}" class="ui-btn px-5 py-3 text-sm bg-white/10 border border-white/25 text-white hover:bg-white/20">
                        <x-icon name="user" class="w-3.5 h-3.5" />
                        <span>Espace Client</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== À PROPOS ===================== -->
    <section id="a-propos" class="py-20 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Qui sommes-nous</span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Notre histoire</h2>
                    <div class="mt-5 space-y-4 text-sm sm:text-[15px] text-slate-600 leading-relaxed">
                        <p>
                            Fondée en 2016 à Meknès, TechniTrack est née d'un constat simple : les entreprises industrielles et de maintenance marocaines manquaient d'outils modernes pour piloter leurs interventions sur le terrain. Notre équipe de techniciens et de développeurs a conçu une plateforme pensée par et pour les métiers de l'intervention technique.
                        </p>
                        <p>
                            Depuis, TechniTrack accompagne des dizaines d'entreprises à travers le Royaume dans la digitalisation de leur gestion de chantiers, de leurs équipes techniques et de leur relation client. Notre mission est de rendre chaque intervention traçable, chaque équipe coordonnée et chaque client informé, en temps réel.
                        </p>
                        <p>
                            Nos valeurs : la rigueur du terrain, la transparence envers nos clients et une innovation continue au service des équipes techniques marocaines.
                        </p>
                    </div>
                </div>
                <div class="rounded-2xl overflow-hidden ui-card aspect-[4/3]">
                    <img src="https://images.pexels.com/photos/34938441/pexels-photo-34938441/free-photo-of-engineer-adjusting-industrial-pipes-in-factory.jpeg"
                         alt="Ingénieur TechniTrack sur le terrain" class="w-full h-full object-cover" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== SERVICES ===================== -->
    <section id="services" class="py-20 sm:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl mx-auto text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Ce que nous proposons</span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Nos services</h2>
                <p class="mt-3 text-sm text-slate-500">Une suite complète d'outils pour piloter vos interventions de bout en bout.</p>
            </div>

            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach([
                    ['icon' => 'wrench', 'title' => 'Gestion des interventions', 'desc' => "Planifiez, suivez et clôturez vos interventions techniques depuis une interface unique."],
                    ['icon' => 'map-pin', 'title' => 'Suivi des chantiers en temps réel', 'desc' => "Visualisez l'avancement de chaque chantier et localisez vos équipes sur le terrain."],
                    ['icon' => 'users', 'title' => 'Gestion des techniciens', 'desc' => "Affectez les bonnes compétences aux bonnes missions et suivez la charge de vos équipes."],
                    ['icon' => 'document-text', 'title' => 'Rapports et documents digitaux', 'desc' => "Générez des rapports d'intervention professionnels, signés et archivés automatiquement."],
                    ['icon' => 'bell', 'title' => 'Notifications en temps réel', 'desc' => "Vos équipes et vos clients sont informés instantanément de chaque étape clé."],
                    ['icon' => 'chart-bar', 'title' => 'Tableau de bord analytique', 'desc' => "Pilotez votre activité grâce à des indicateurs clairs sur vos interventions et chantiers."],
                ] as $service)
                    <div class="ui-card ui-card-hover p-6">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base mb-4">
                            <x-icon :name="$service['icon']" class="w-5 h-5" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $service['title'] }}</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">{{ $service['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===================== AVIS CLIENTS ===================== -->
    <section class="py-20 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl mx-auto text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Ils nous font confiance</span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Avis clients</h2>
            </div>

            <div class="mt-12 grid md:grid-cols-3 gap-6">
                @foreach([
                    ['name' => 'Yassine El Amrani', 'role' => 'Responsable Maintenance', 'company' => 'Cimenterie Atlas', 'quote' => "TechniTrack nous a permis de diviser par deux le temps de traitement de nos interventions. Un vrai gain de visibilité pour toute l'équipe."],
                    ['name' => 'Sanae Bouzidi', 'role' => 'Directrice Technique', 'company' => 'Groupe Faraday Industries', 'quote' => "Le suivi des chantiers en temps réel a transformé notre relation avec nos clients. Tout est enfin traçable et transparent."],
                    ['name' => 'Karim Moussaoui', 'role' => 'Chef de Projet', 'company' => 'Atlas Froid & Climatisation', 'quote' => "Une plateforme simple, pensée pour le terrain. Nos techniciens l'ont adoptée dès la première semaine."],
                ] as $avis)
                    <div class="ui-card p-6">
                        <div class="flex items-center gap-1 text-amber-400 text-xs mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">&ldquo;{{ $avis['quote'] }}&rdquo;</p>
                        <div class="mt-5 flex items-center gap-3 pt-5 border-t border-slate-100">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($avis['name']) }}&background=random"
                                 alt="{{ $avis['name'] }}" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $avis['name'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $avis['role'] }} • {{ $avis['company'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===================== CTA ===================== -->
    <section class="py-16 bg-indigo-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Prêt à simplifier vos interventions ?</h2>
                <p class="mt-1.5 text-sm text-indigo-100">Rejoignez les entreprises qui font confiance à TechniTrack.</p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('espace.client') }}" class="ui-btn bg-white text-indigo-700 hover:bg-indigo-50 px-5 py-3 text-sm">
                    <x-icon name="user" class="w-3.5 h-3.5" /><span>Espace Client</span>
                </a>
                <a href="{{ route('espace.employe') }}" class="ui-btn bg-slate-900 text-white hover:bg-slate-800 px-5 py-3 text-sm">
                    <x-icon name="briefcase" class="w-3.5 h-3.5" /><span>Espace Employé</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ===================== FOOTER ===================== -->
    <footer id="contact" class="bg-slate-900 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-10">

                <!-- À propos rapide -->
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('images/logo-technitrack-full.png') }}" alt="TechniTrack" class="h-11 w-auto object-contain brightness-0 invert">
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Plateforme marocaine de gestion des interventions techniques et des chantiers, pour des équipes coordonnées et des clients informés en temps réel.
                    </p>
                    <div class="flex items-center gap-3 mt-5">
                        <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 transition"><i class="fab fa-facebook-f text-xs"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 transition"><i class="fab fa-linkedin-in text-xs"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 transition"><i class="fab fa-instagram text-xs"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 transition"><i class="fab fa-x-twitter text-xs"></i></a>
                    </div>
                </div>

                <!-- Liens utiles -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Liens utiles</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#accueil" class="hover:text-white transition">Accueil</a></li>
                        <li><a href="#services" class="hover:text-white transition">Services</a></li>
                        <li><a href="#a-propos" class="hover:text-white transition">À propos</a></li>
                        <li><a href="{{ route('espace.client') }}" class="hover:text-white transition">Espace Client</a></li>
                        <li><a href="{{ route('espace.employe') }}" class="hover:text-white transition">Espace Employé</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Contact</h3>
                    <ul class="space-y-3 text-sm text-slate-300">
                        <li class="flex items-start gap-2.5">
                            <x-icon name="map-pin" class="w-3.5 h-3.5 text-indigo-400 mt-0.5 shrink-0" />
                            <span>Zone Industrielle, Meknès, Maroc</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <x-icon name="phone" class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                            <span>+212 5XX-XXXXXX</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <x-icon name="mail" class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                            <span>contact@technitrack.ma</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-white/10 text-center">
                <p class="text-[11px] text-slate-500">
                    © {{ date('Y') }} {{ config('app.name', 'TechniTrack') }} — Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
