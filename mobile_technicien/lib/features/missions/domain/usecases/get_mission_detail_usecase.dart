import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';

class GetMissionDetailUsecase {
  final MissionsRepository repository;
  GetMissionDetailUsecase(this.repository);

  Future<Result<Mission>> call(int missionId) => repository.getMissionDetail(missionId);
}
