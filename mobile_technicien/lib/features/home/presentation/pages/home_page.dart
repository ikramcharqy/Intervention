import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:geolocator/geolocator.dart';
import 'package:mobile_technicien/core/services/connectivity_status_service.dart';
import 'package:mobile_technicien/core/services/location_status_service.dart';
import 'package:mobile_technicien/core/services/presence_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/home/domain/usecases/start_mission_usecase.dart';
import 'package:mobile_technicien/features/home/presentation/bloc/home_bloc.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/connectivity_status_chip.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/gps_status_chip.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/kpi_grid.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/next_mission_card.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/presence_toggle.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/recent_missions_section.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Contenu de l'onglet "Accueil". Ne possède plus son propre Scaffold/FAB
/// depuis l'introduction de MainShell : c'est la coquille commune aux 4
/// onglets qui porte la BottomNavigationBar et le FAB de scan QR.
///
/// [onSeeAllMissions]/[onOpenNotifications] permettent à MainShell de
/// basculer vers l'onglet Missions/Notifs correspondant (lien "Voir tout",
/// cloche d'en-tête) sans que HomePage n'ait besoin de connaître l'index des
/// onglets lui-même.
/// HomeBloc est fourni par MainShell (pas ici) : la barre de navigation basse
/// a aussi besoin du compteur de notifications non lues pour son propre badge
/// (Étape 7.1), donc le bloc doit vivre au-dessus des deux.
class HomePage extends StatelessWidget {
  final VoidCallback? onSeeAllMissions;
  final VoidCallback? onOpenNotifications;

  const HomePage({super.key, this.onSeeAllMissions, this.onOpenNotifications});

  @override
  Widget build(BuildContext context) {
    return _HomeView(onSeeAllMissions: onSeeAllMissions, onOpenNotifications: onOpenNotifications);
  }
}

class _HomeView extends StatefulWidget {
  final VoidCallback? onSeeAllMissions;
  final VoidCallback? onOpenNotifications;

  const _HomeView({this.onSeeAllMissions, this.onOpenNotifications});

  @override
  State<_HomeView> createState() => _HomeViewState();
}

class _HomeViewState extends State<_HomeView> {
  // Une seule instance partagée : passer la localisation "En service" via
  // PresenceToggle doit se refléter immédiatement sur GpsStatusChip, pas
  // seulement au prochain rafraîchissement.
  final _locationService = LocationStatusService();
  DateTime? _lastSynced;
  double? _distanceKm;
  int? _distanceForMissionId;
  bool _startingMission = false;

  String get _lastSyncedLabel {
    final last = _lastSynced;
    if (last == null) return '';
    final diff = DateTime.now().difference(last);
    if (diff.inSeconds < 30) return 'Mis à jour à l\'instant';
    if (diff.inMinutes < 1) return 'Mis à jour il y a ${diff.inSeconds}s';
    if (diff.inHours < 1) return 'Mis à jour il y a ${diff.inMinutes} min';
    return 'Mis à jour il y a ${diff.inHours} h';
  }

  /// Étape 5 : distance à vol d'oiseau jusqu'au chantier de la prochaine
  /// mission, seulement si le GPS est activé et les coordonnées connues —
  /// jamais de valeur par défaut si l'une des deux conditions manque.
  Future<void> _maybeComputeDistance(Mission? mission) async {
    if (mission == null || mission.chantierLatitude == null || mission.chantierLongitude == null) {
      if (_distanceKm != null) setState(() => _distanceKm = null);
      return;
    }
    if (_distanceForMissionId == mission.id) return; // déjà calculée pour cette mission

    final position = await _locationService.getCurrentPosition();
    if (!mounted) return;
    if (position == null) {
      setState(() {
        _distanceKm = null;
        _distanceForMissionId = mission.id;
      });
      return;
    }

    final meters = Geolocator.distanceBetween(
      position.latitude,
      position.longitude,
      mission.chantierLatitude!,
      mission.chantierLongitude!,
    );
    setState(() {
      _distanceKm = meters / 1000;
      _distanceForMissionId = mission.id;
    });
  }

  /// Étape 2 : l'action "Démarrer" déclenche un vrai changement de statut
  /// côté API (POST /interventions/{id}/start), pas seulement une navigation
  /// — puis recharge le résumé pour refléter le nouveau statut.
  Future<void> _onStart(Mission mission) async {
    if (_startingMission) return;
    setState(() => _startingMission = true);

    final result = await sl<StartMissionUsecase>()(mission.id);

    if (!mounted) return;
    setState(() => _startingMission = false);

    result.when(
      success: (_) => context.read<HomeBloc>().add(const HomeRequested()),
      failure: (f) => ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(f.message), backgroundColor: AppColors.danger),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authState = context.watch<AuthBloc>().state;
    final firstName = authState is AuthAuthenticated
        ? (authState.user.prenom?.isNotEmpty == true ? authState.user.prenom! : authState.user.name)
        : '';
    final homeState = context.watch<HomeBloc>().state;
    final missionsEnCours = homeState is HomeLoaded ? homeState.summary.enCours : 0;

    return SafeArea(
      child: BlocListener<HomeBloc, HomeState>(
        listener: (context, state) {
          if (state is HomeLoaded) setState(() => _lastSynced = DateTime.now());
        },
        child: RefreshIndicator(
          // Étape 2.5 : tirer-pour-rafraîchir, en complément du futur bouton
          // de rafraîchissement explicite.
          onRefresh: () async {
            context.read<HomeBloc>().add(const HomeRefreshed());
            await context.read<HomeBloc>().stream.firstWhere((s) => s is HomeLoaded || s is HomeError);
          },
          child: ListView(
            padding: const EdgeInsets.all(20),
            children: [
              _Header(firstName: firstName, onOpenNotifications: widget.onOpenNotifications),
              if (_lastSynced != null) ...[
                const SizedBox(height: 4),
                Text(_lastSyncedLabel, style: const TextStyle(color: AppColors.neutral, fontSize: 11)),
              ],
              const SizedBox(height: 16),
              // Étape 4 : le statut de présence est l'élément central de
              // l'écran (conditionne potentiellement la disponibilité vue
              // par le Commercial/Super Admin) — placé au-dessus des badges
              // GPS/connectivité, avec un traitement visuel plein dédié.
              PresenceToggle(
                service: sl<PresenceService>(),
                locationService: _locationService,
                missionsEnCours: missionsEnCours,
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  GpsStatusChip(service: _locationService),
                  const SizedBox(width: 8),
                  ConnectivityStatusChip(service: ConnectivityStatusService()),
                ],
              ),
              const SizedBox(height: 20),
              BlocBuilder<HomeBloc, HomeState>(
                builder: (context, state) {
                  return switch (state) {
                    HomeLoading() => Column(
                        children: [
                          Container(
                            height: 180,
                            decoration: BoxDecoration(
                              color: AppColors.surfaceMuted,
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                          const SizedBox(height: 20),
                          const KpiGridSkeleton(),
                        ],
                      ),
                    HomeLoaded(summary: final summary) => Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Builder(builder: (context) {
                            // Calcul lancé au build plutôt que dans le
                            // BlocListener plus haut : ne dépend pas de l'état
                            // Auth/Home global, seulement de la mission
                            // affichée ici.
                            WidgetsBinding.instance.addPostFrameCallback(
                              (_) => _maybeComputeDistance(summary.prochaineMission),
                            );
                            return NextMissionCard(
                              mission: summary.prochaineMission,
                              distanceKm: _distanceForMissionId == summary.prochaineMission?.id ? _distanceKm : null,
                              isActionLoading: _startingMission,
                              onStart: () {
                                final mission = summary.prochaineMission;
                                if (mission != null) _onStart(mission);
                              },
                              onItineraire: () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Itinéraire à venir.')),
                                );
                              },
                              onContinue: () {
                                // Écran "Détails Mission" (formulaire dynamique
                                // en cours de remplissage) à venir — ne doit
                                // surtout pas rappeler l'API "start" sur une
                                // mission déjà "En cours" (rejeté par le
                                // backend, cf. Intervention::peutEtreDemarree()).
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Écran de suivi de mission à venir.')),
                                );
                              },
                            );
                          }),
                          const SizedBox(height: 20),
                          KpiGrid(summary: summary),
                          if (summary.missionsRecentes.isNotEmpty) ...[
                            const SizedBox(height: 20),
                            RecentMissionsSection(
                              missions: summary.missionsRecentes,
                              onSeeAll: () => widget.onSeeAllMissions?.call(),
                            ),
                          ],
                        ],
                      ),
                    HomeError() => KpiGridError(
                        onRetry: () => context.read<HomeBloc>().add(const HomeRequested()),
                      ),
                  };
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _Header extends StatelessWidget {
  final String firstName;
  final VoidCallback? onOpenNotifications;
  const _Header({required this.firstName, this.onOpenNotifications});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Étape 6.1 : emoji retiré, cohérent avec la décision déjà prise
              // côté portail Client. Aucune différence de ton assumée n'a été
              // validée pour l'app terrain — à documenter séparément si elle
              // l'est un jour.
              Text('Bienvenue, $firstName',
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              const Text('Voici votre journée', style: TextStyle(color: AppColors.neutral, fontSize: 13)),
            ],
          ),
        ),
        // Étape 6.2 : badge de notification numérique, cohérent avec celui
        // déjà implémenté sur la cloche du portail Client desktop.
        BlocBuilder<HomeBloc, HomeState>(
          builder: (context, state) {
            final unread = state is HomeLoaded ? state.summary.notificationsNonLues : 0;
            return Stack(
              clipBehavior: Clip.none,
              children: [
                IconButton(
                  onPressed: () => onOpenNotifications?.call(),
                  icon: const Icon(Icons.notifications_outlined),
                ),
                if (unread > 0)
                  Positioned(
                    right: 4,
                    top: 4,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1),
                      decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(999)),
                      child: Text(
                        unread > 9 ? '9+' : '$unread',
                        style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ),
                  ),
              ],
            );
          },
        ),
      ],
    );
  }
}
