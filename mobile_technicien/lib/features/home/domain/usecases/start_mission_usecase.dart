import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/domain/repositories/home_repository.dart';

class StartMissionUsecase {
  final HomeRepository repository;
  StartMissionUsecase(this.repository);

  Future<Result<void>> call(int missionId) => repository.startMission(missionId);
}
