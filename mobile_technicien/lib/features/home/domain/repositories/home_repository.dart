import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/domain/entities/home_summary.dart';

abstract class HomeRepository {
  Future<Result<HomeSummary>> getHomeSummary();

  /// POST /api/interventions/{id}/start — même règle de transition que le
  /// backend (Intervention::peutEtreDemarree()), pas un simple changement
  /// d'écran : la mission doit réellement changer de statut côté serveur.
  Future<Result<void>> startMission(int missionId);
}
