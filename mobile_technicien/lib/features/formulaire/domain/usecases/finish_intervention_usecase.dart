import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/formulaire/domain/repositories/formulaire_repository.dart';

class FinishInterventionUsecase {
  final FormulaireRepository repository;
  FinishInterventionUsecase(this.repository);

  Future<Result<void>> call(int interventionId, {String? commentaire}) =>
      repository.finishIntervention(interventionId, commentaire: commentaire);
}
