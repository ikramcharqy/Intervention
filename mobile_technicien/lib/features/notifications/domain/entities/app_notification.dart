import 'package:equatable/equatable.dart';

/// Miroir d'une ligne de la table `notifications` (Illuminate\Notifications) —
/// même mécanisme déjà utilisé côté portail Client
/// (ClientModule\NotificationController) et déjà exposé via
/// GET /api/notifications (Api\NotificationController), pas une structure
/// reconstruite pour l'app Technicien.
class AppNotification extends Equatable {
  final String id;

  /// Valeur sémantique (`data.type`, ex: 'intervention_planifiee') — PAS le
  /// nom de classe PHP complet (`type` au niveau racine de la ligne),
  /// utilisée pour choisir l'icône et le libellé de catégorie.
  final String type;
  final String titre;
  final String message;
  final DateTime createdAt;
  final DateTime? readAt;

  /// Présents uniquement sur les notifications liées à une Intervention
  /// (classes dédiées par événement) — absents sur les notifications
  /// génériques du portail (ClientPortalNotification, `lien_route` à la
  /// place), donc jamais garantis.
  final int? interventionId;
  final String? codeIntervention;

  const AppNotification({
    required this.id,
    required this.type,
    required this.titre,
    required this.message,
    required this.createdAt,
    required this.readAt,
    required this.interventionId,
    required this.codeIntervention,
  });

  bool get estLue => readAt != null;

  bool get estNavigable => interventionId != null;

  @override
  List<Object?> get props => [id, type, titre, message, createdAt, readAt, interventionId, codeIntervention];
}
