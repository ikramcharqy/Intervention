part of 'missions_bloc.dart';

sealed class MissionsEvent extends Equatable {
  const MissionsEvent();
  @override
  List<Object?> get props => [];
}

class MissionsRequested extends MissionsEvent {
  const MissionsRequested();
}

class MissionsRefreshed extends MissionsEvent {
  const MissionsRefreshed();
}

/// Recalcule uniquement la liste filtrée à partir du cache déjà chargé — pas
/// de nouvel appel réseau (Étape 2.2 : "sans recharger inutilement toute la
/// liste si déjà en cache").
class MissionsFilterChanged extends MissionsEvent {
  final MissionStatusFilter filter;
  const MissionsFilterChanged(this.filter);

  @override
  List<Object?> get props => [filter];
}

class MissionsSearchChanged extends MissionsEvent {
  final String query;
  const MissionsSearchChanged(this.query);

  @override
  List<Object?> get props => [query];
}
