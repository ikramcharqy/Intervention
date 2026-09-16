import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';
import 'package:mobile_technicien/features/formulaire/domain/repositories/formulaire_repository.dart';

class GetFormulaireUsecase {
  final FormulaireRepository repository;
  GetFormulaireUsecase(this.repository);

  Future<Result<Formulaire>> call(int interventionId) => repository.getFormulaire(interventionId);
}
