import 'package:equatable/equatable.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';

/// Agrégat calculé pour l'écran d'accueil : prochaine mission + compteurs KPI
/// + nombre de notifications non lues (pour le badge de navigation, Étape 6.2).
/// Assemblé dans GetHomeSummaryUsecase à partir des DEUX seules données
/// réellement exposées par l'API aujourd'hui (interventions, notifications) —
/// aucun champ n'est inventé.
class HomeSummary extends Equatable {
  final Mission? prochaineMission;
  final int aRealiser;
  final int enCours;
  final int terminees;
  final int notificationsNonLues;
  final List<Mission> missionsRecentes;

  const HomeSummary({
    required this.prochaineMission,
    required this.aRealiser,
    required this.enCours,
    required this.terminees,
    required this.notificationsNonLues,
    required this.missionsRecentes,
  });

  @override
  List<Object?> get props =>
      [prochaineMission, aRealiser, enCours, terminees, notificationsNonLues, missionsRecentes];
}
