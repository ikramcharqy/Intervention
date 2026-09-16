import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';

/// Distinct de home/domain/usecases/start_mission_usecase.dart (celui-ci passe
/// par MissionsRepository, pas HomeRepository) — même appel API sous-jacent
/// (HomeRemoteDatasource.startIntervention), juste un point d'entrée par
/// écran, cohérent avec le reste de ce module (get_missions_usecase.dart).
class StartMissionUsecase {
  final MissionsRepository repository;
  StartMissionUsecase(this.repository);

  Future<Result<void>> call(int missionId, {double? latitude, double? longitude}) =>
      repository.startMission(missionId, latitude: latitude, longitude: longitude);
}
