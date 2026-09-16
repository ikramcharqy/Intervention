import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/rapport/domain/repositories/rapport_repository.dart';

class GetRapportPdfBytesUsecase {
  final RapportRepository repository;
  GetRapportPdfBytesUsecase(this.repository);

  Future<Result<List<int>>> call(int rapportId) => repository.getRapportPdfBytes(rapportId);
}
