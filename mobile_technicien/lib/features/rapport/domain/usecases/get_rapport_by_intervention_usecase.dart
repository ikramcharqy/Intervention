import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/rapport/domain/entities/rapport.dart';
import 'package:mobile_technicien/features/rapport/domain/repositories/rapport_repository.dart';

class GetRapportByInterventionUsecase {
  final RapportRepository repository;
  GetRapportByInterventionUsecase(this.repository);

  Future<Result<Rapport>> call(int interventionId) => repository.getRapportByIntervention(interventionId);
}
