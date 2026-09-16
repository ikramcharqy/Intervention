import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';

abstract class MissionsRepository {
  /// Même source que l'écran d'accueil (Étape 5 : garantir que les compteurs
  /// de l'accueil et la liste Missions ne divergent jamais, contrairement à
  /// l'incident déjà rencontré côté web entre deux vues d'une même donnée).
  Future<Result<List<Mission>>> getMissions();

  /// Détail à jour d'une mission — utilisé après chaque action (accepter,
  /// refuser, démarrer) pour refléter le nouveau statut sans supposer la
  /// forme de la réponse de l'action elle-même.
  Future<Result<Mission>> getMissionDetail(int missionId);

  Future<Result<void>> acceptMission(int missionId);

  Future<Result<void>> refuseMission(int missionId, String motif);

  /// [latitude]/[longitude] optionnels : capture ponctuelle de la position au
  /// moment de démarrer, envoyée telle quelle (aucune validation de rayon
  /// côté app, cf. RemplissageFormulaireService/InterventionService côté
  /// backend qui restent seuls responsables de cette validation).
  Future<Result<void>> startMission(int missionId, {double? latitude, double? longitude});
}
