import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';

class RefuseMissionUsecase {
  final MissionsRepository repository;
  RefuseMissionUsecase(this.repository);

  Future<Result<void>> call(int missionId, String motif) => repository.refuseMission(missionId, motif);
}
