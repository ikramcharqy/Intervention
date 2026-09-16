import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/services/location_status_service.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/formulaire/domain/usecases/finish_intervention_usecase.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/accept_mission_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/get_mission_detail_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/refuse_mission_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/start_mission_usecase.dart';

part 'mission_detail_event.dart';
part 'mission_detail_state.dart';

/// Écran de détail mission — actions contextuelles selon le statut (Étape 2).
/// Chaque action (accepter/refuser/démarrer) rappelle systématiquement
/// GetMissionDetailUsecase après succès plutôt que de reconstruire l'entité
/// Mission à partir de la réponse de l'action elle-même : les trois endpoints
/// (accept/refuse/start) ne renvoient pas la même forme (refuse renvoie
/// {intervention, demande}), donc une seule source de vérité pour l'état
/// affiché évite toute divergence entre routes.
class MissionDetailBloc extends Bloc<MissionDetailEvent, MissionDetailState> {
  final GetMissionDetailUsecase getMissionDetailUsecase;
  final AcceptMissionUsecase acceptMissionUsecase;
  final RefuseMissionUsecase refuseMissionUsecase;
  final StartMissionUsecase startMissionUsecase;
  final FinishInterventionUsecase finishInterventionUsecase;
  final LocationStatusService locationService;

  MissionDetailBloc({
    required Mission initialMission,
    required this.getMissionDetailUsecase,
    required this.acceptMissionUsecase,
    required this.refuseMissionUsecase,
    required this.startMissionUsecase,
    required this.finishInterventionUsecase,
    LocationStatusService? locationService,
  })  : locationService = locationService ?? LocationStatusService(),
        super(MissionDetailState(mission: initialMission)) {
    on<MissionDetailRefreshed>(_onRefreshed);
    on<MissionAcceptRequested>(_onAccept);
    on<MissionRefuseRequested>(_onRefuse);
    on<MissionStartRequested>(_onStart);
    on<MissionFinishRequested>(_onFinish);
  }

  Future<void> _onRefreshed(MissionDetailRefreshed event, Emitter<MissionDetailState> emit) async {
    emit(state.copyWith(processing: true, errorMessage: null));
    final result = await getMissionDetailUsecase(state.mission.id);
    result.when(
      success: (mission) => emit(state.copyWith(mission: mission, processing: false)),
      failure: (f) => emit(state.copyWith(processing: false, errorMessage: f.message)),
    );
  }

  Future<void> _onAccept(MissionAcceptRequested event, Emitter<MissionDetailState> emit) =>
      _runAction(emit, () => acceptMissionUsecase(state.mission.id));

  Future<void> _onRefuse(MissionRefuseRequested event, Emitter<MissionDetailState> emit) =>
      _runAction(emit, () => refuseMissionUsecase(state.mission.id, event.motif));

  /// Capture ponctuelle de la position au moment de démarrer (pas le statut
  /// GPS permanent de l'accueil) — si indisponible/refusée, démarre quand
  /// même en mode Manuel plutôt que de bloquer l'action.
  Future<void> _onStart(MissionStartRequested event, Emitter<MissionDetailState> emit) async {
    final position = await locationService.getCurrentPosition();
    await _runAction(
      emit,
      () => startMissionUsecase(state.mission.id, latitude: position?.latitude, longitude: position?.longitude),
    );
  }

  Future<void> _onFinish(MissionFinishRequested event, Emitter<MissionDetailState> emit) =>
      _runAction(emit, () => finishInterventionUsecase(state.mission.id));

  Future<void> _runAction(Emitter<MissionDetailState> emit, Future<Result<void>> Function() action) async {
    emit(state.copyWith(processing: true, errorMessage: null));
    final result = await action();
    await result.when(
      success: (_) async {
        final refreshed = await getMissionDetailUsecase(state.mission.id);
        refreshed.when(
          success: (mission) => emit(state.copyWith(mission: mission, processing: false)),
          failure: (f) => emit(state.copyWith(processing: false, errorMessage: f.message)),
        );
      },
      failure: (f) async => emit(state.copyWith(processing: false, errorMessage: f.message)),
    );
  }
}
