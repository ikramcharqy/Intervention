import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/formulaire/domain/repositories/formulaire_repository.dart';

class SubmitFormulaireUsecase {
  final FormulaireRepository repository;
  SubmitFormulaireUsecase(this.repository);

  Future<Result<void>> call(
    int interventionId, {
    required Map<int, dynamic> reponses,
    required Map<int, List<String>> fichiers,
  }) =>
      repository.submitFormulaire(interventionId, reponses: reponses, fichiers: fichiers);
}
