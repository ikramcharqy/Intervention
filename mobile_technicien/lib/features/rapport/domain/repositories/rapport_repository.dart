import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/rapport/domain/entities/rapport.dart';

abstract class RapportRepository {
  Future<Result<Rapport>> getRapportByIntervention(int interventionId);
  Future<Result<List<int>>> getRapportPdfBytes(int rapportId);
}
