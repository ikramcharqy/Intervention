import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';

abstract class FormulaireRepository {
  Future<Result<Formulaire>> getFormulaire(int interventionId);

  Future<Result<void>> submitFormulaire(
    int interventionId, {
    required Map<int, dynamic> reponses,
    required Map<int, List<String>> fichiers,
  });

  Future<Result<void>> finishIntervention(int interventionId, {String? commentaire});
}
