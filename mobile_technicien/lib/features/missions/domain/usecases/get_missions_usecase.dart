import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';

class GetMissionsUsecase {
  final MissionsRepository repository;
  GetMissionsUsecase(this.repository);

  Future<Result<List<Mission>>> call() => repository.getMissions();
}
