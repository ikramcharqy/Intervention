import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';

class AcceptMissionUsecase {
  final MissionsRepository repository;
  AcceptMissionUsecase(this.repository);

  Future<Result<void>> call(int missionId) => repository.acceptMission(missionId);
}
