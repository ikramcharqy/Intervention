# TechniTrack — App Technicien (Flutter)

Nouveau projet Flutter séparé, distinct de la PWA Blade/JS existante
(`resources/views/mobile/app.blade.php`), consommant l'API Laravel/Sanctum
existante (`routes/api.php`) **telle quelle — aucune route ni contrôleur
backend n'a été modifié pour ce projet.**

La PWA reste en place et fonctionnelle en parallèle. Ce projet démarre
volontairement petit : **seul l'écran d'accueil est implémenté** (cf. prompt
"Refonte complète de l'écran d'accueil Technicien"). Missions, Notifs, Profil
suivront écran par écran.

## ⚠️ Limite de vérification dans cet environnement

Aucun SDK Flutter/Dart fonctionnel n'est disponible dans cet environnement
(le dossier `Dev mobile/flutter/` référencé dans le PATH ne contient pas les
binaires `flutter`/`dart`, seulement `cache/` et `internal/`). Le code n'a
donc **pas pu être compilé, analysé (`flutter analyze`) ni testé
(`flutter test`) ici** — seule une relecture manuelle a été faite.

Ce qui A été vérifié en direct, côté API (avec un compte technicien
jetable créé puis supprimé) :
- `POST /api/login` → jeton Sanctum + objet `user` (forme exacte reprise dans
  `TechnicienModel`, avec test de régression sur la charge utile réelle).
- `GET /api/me`, `GET /api/interventions` (avec relations `chantier`,
  `emplacement`, `type_intervention` chargées), `GET /api/notifications/unread`.

**À faire par vous avant toute chose :**
```
cd mobile_technicien
flutter pub get
flutter analyze
flutter test
flutter run --dart-define=API_BASE_URL=http://<votre-ip-locale>:8000/api
```
(`10.0.2.2` par défaut ne fonctionne que depuis un émulateur Android ; sur
appareil physique ou iOS, passez l'IP réelle de la machine qui sert Laravel.)

## Ce qui est implémenté

- **Auth** (Clean Architecture complète : datasource → repository → usecases
  → BLoC) : login, `/me` au démarrage, logout, jeton persisté via
  `flutter_secure_storage`.
- **Accueil** :
  - Carte "Prochaine mission" (Étape 1) avec badge de priorité reprenant
    EXACTEMENT le mapping couleur de `resources/views/components/
    soft-badge.blade.php` (Faible/Normale/Haute/Urgente), état vide orienté
    action.
  - 4 KPI conservés sous la carte (À réaliser / En cours / Terminées / Non
    lues), avec skeleton loader au premier chargement et état d'échec distinct
    d'un 0 réel (Étapes 2.3/2.4).
  - Statut GPS et statut de connectivité, tirer-pour-rafraîchir (Étape 2).
  - Toggle "En service / Hors service" (Étape 3.1) — **purement local, non
    synchronisé, ne pilote aucun tracking GPS**, voir avertissement de
    conformité ci-dessous (Étape 3.2).
  - FAB de scan QR (Étape 4), fonctionnel (camera réelle via `mobile_scanner`)
    mais sans destination métier tant que l'écran Missions n'existe pas.
  - Verrouillage biométrique + PIN de repli à chaque démarrage (Étape 5.1).
  - Emoji retiré, badge de notification sur la cloche (Étapes 6.1/6.2).
  - Couleur rose/magenta documentée comme choix de design system assumé par
    rôle, pas une incohérence (Étape 6.4, voir `core/theme/app_theme.dart`).

## Signalé, non tranché (comme demandé)

- **Étape 3.2 — conformité GPS/présence** : le statut "En service" n'est lié à
  AUCUN déclenchement de tracking GPS. Avant de le faire, une décision
  validée est nécessaire sur la question du droit du travail marocain
  évoquée dans le prompt. Voir le commentaire en tête de
  `core/services/presence_service.dart`.
- **Étape 2.2 — file d'attente hors-ligne** : aucune persistance locale de
  requêtes en attente n'existe (ni dans la PWA, ni ici). Le statut
  "En ligne/Hors ligne" est affiché, mais le compteur "X élément(s) en
  attente" nécessite une file d'attente locale + rejeu automatique à
  concevoir — prérequis à cadrer séparément, pas improvisé ici.
- **Étape 5.2 — révocation de session mobile à distance** : `AuthController::
  logout` révoque bien le jeton Sanctum courant, et chaque jeton est une ligne
  dans `personal_access_tokens` — un Super Admin qui supprimerait cette ligne
  (ou tous les jetons de l'utilisateur) invaliderait donc bien la session
  mobile au prochain appel API. **Mais** le module web "Sessions actives"
  actuel (Super Admin) n'a pas été vérifié comme agissant sur
  `personal_access_tokens` spécifiquement — à confirmer côté backend avant de
  considérer ce point comme couvert.
- **Étape 6.3 — icône engrenage vs onglet Profil** : aucun écran Profil ni
  Réglages n'existe encore dans ce nouveau projet, donc rien à fusionner pour
  l'instant. Aucune icône engrenage n'a été ajoutée à l'accueil pour éviter de
  recréer la même ambiguïté prématurément — à trancher au moment de
  construire l'écran Profil.

## Structure (Clean Architecture + BLoC)

```
lib/
  core/            constants, network (Dio + intercepteur token), storage
                   (secure storage), theme, services (GPS/connectivité/
                   présence/biométrie), error, usecase (Result)
  features/
    auth/          data / domain / presentation (login, session, verrouillage)
    home/          data / domain / presentation (accueil, prochaine mission,
                   KPI, scan QR)
  injection_container.dart   câblage GetIt
  main.dart                  Splash → Lock → Home / Login
```
