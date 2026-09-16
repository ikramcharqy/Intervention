import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';
import 'package:mobile_technicien/features/chantier/domain/repositories/chantier_repository.dart';

class GetChantierDetailUsecase {
  final ChantierRepository repository;
  GetChantierDetailUsecase(this.repository);

  Future<Result<Chantier>> call(int chantierId) => repository.getChantierDetail(chantierId);
}
