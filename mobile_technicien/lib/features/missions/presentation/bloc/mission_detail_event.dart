part of 'mission_detail_bloc.dart';

sealed class MissionDetailEvent extends Equatable {
  const MissionDetailEvent();

  @override
  List<Object?> get props => [];
}

class MissionDetailRefreshed extends MissionDetailEvent {
  const MissionDetailRefreshed();
}

class MissionAcceptRequested extends MissionDetailEvent {
  const MissionAcceptRequested();
}

class MissionRefuseRequested extends MissionDetailEvent {
  final String motif;
  const MissionRefuseRequested(this.motif);

  @override
  List<Object?> get props => [motif];
}

class MissionStartRequested extends MissionDetailEvent {
  const MissionStartRequested();
}

/// POST /interventions/{id}/finish — Formulaire rempli → Terminée.
class MissionFinishRequested extends MissionDetailEvent {
  const MissionFinishRequested();
}
