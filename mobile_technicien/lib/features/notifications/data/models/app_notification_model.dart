import 'package:mobile_technicien/features/notifications/domain/entities/app_notification.dart';

/// Reflète une ligne brute de `GET /api/notifications` — sérialisation
/// standard Laravel d'une `DatabaseNotification` : `{id, type (FQCN PHP),
/// data: {...}, read_at, created_at}`. Le `type` sémantique utile côté app
/// (ex: 'intervention_planifiee') vit dans `data.type`, pas au niveau racine.
class AppNotificationModel extends AppNotification {
  const AppNotificationModel({
    required super.id,
    required super.type,
    required super.titre,
    required super.message,
    required super.createdAt,
    required super.readAt,
    required super.interventionId,
    required super.codeIntervention,
  });

  factory AppNotificationModel.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};

    return AppNotificationModel(
      id: json['id'] as String,
      type: data['type'] as String? ?? 'inconnu',
      titre: data['titre'] as String? ?? 'Notification',
      message: data['message'] as String? ?? '',
      createdAt: DateTime.tryParse(json['created_at'] as String? ?? '') ?? DateTime.now(),
      readAt: json['read_at'] != null ? DateTime.tryParse(json['read_at'] as String) : null,
      interventionId: data['intervention_id'] as int?,
      codeIntervention: data['code_intervention'] as String?,
    );
  }
}
