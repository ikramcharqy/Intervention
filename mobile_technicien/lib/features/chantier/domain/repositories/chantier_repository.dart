import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';

abstract class ChantierRepository {
  Future<Result<Chantier>> getChantierDetail(int chantierId);
}
