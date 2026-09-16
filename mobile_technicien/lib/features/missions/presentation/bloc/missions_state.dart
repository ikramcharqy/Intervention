part of 'missions_bloc.dart';

sealed class MissionsState extends Equatable {
  const MissionsState();
  @override
  List<Object?> get props => [];
}

class MissionsLoading extends MissionsState {
  const MissionsLoading();
}

/// Étape 2.1 : distinct d'une liste vide réelle (cf. MissionsEmpty) — jamais
/// un compteur/liste à 0 silencieux en cas d'échec réseau.
class MissionsError extends MissionsState {
  final String message;
  const MissionsError(this.message);

  @override
  List<Object?> get props => [message];
}

/// Résultat non vide pour le filtre/la recherche courants.
class MissionsLoaded extends MissionsState {
  final List<Mission> allMissions;
  final List<Mission> filteredMissions;
  final MissionStatusFilter filter;
  final String searchQuery;

  const MissionsLoaded({
    required this.allMissions,
    required this.filteredMissions,
    required this.filter,
    required this.searchQuery,
  });

  @override
  List<Object?> get props => [allMissions, filteredMissions, filter, searchQuery];
}

/// Liste réellement vide pour le filtre/la recherche courants (Étape 4.1) —
/// `allMissions` reste porté pour que les chips de filtre restent affichés et
/// interactifs même quand le filtre actif ne matche rien.
class MissionsEmpty extends MissionsState {
  final List<Mission> allMissions;
  final MissionStatusFilter filter;
  final String searchQuery;

  const MissionsEmpty({
    required this.allMissions,
    required this.filter,
    required this.searchQuery,
  });

  @override
  List<Object?> get props => [allMissions, filter, searchQuery];
}
