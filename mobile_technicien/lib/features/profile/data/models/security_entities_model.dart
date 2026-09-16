import 'package:mobile_technicien/features/profile/domain/entities/security_entities.dart';

class LoginHistoryEntryModel extends LoginHistoryEntry {
  const LoginHistoryEntryModel({required super.loggedInAt, required super.ipAddress, required super.userAgent});

  factory LoginHistoryEntryModel.fromJson(Map<String, dynamic> json) {
    return LoginHistoryEntryModel(
      loggedInAt: DateTime.tryParse(json['logged_in_at'] as String? ?? '') ?? DateTime.now(),
      ipAddress: json['ip_address'] as String?,
      userAgent: json['user_agent'] as String?,
    );
  }
}

class ActiveSessionModel extends ActiveSession {
  const ActiveSessionModel({
    required super.id,
    required super.nom,
    required super.derniereUtilisation,
    required super.creeLe,
    required super.estActuel,
  });

  factory ActiveSessionModel.fromJson(Map<String, dynamic> json) {
    return ActiveSessionModel(
      id: json['id'] as int,
      nom: json['nom'] as String? ?? 'Appareil',
      derniereUtilisation: json['derniere_utilisation'] != null ? DateTime.tryParse(json['derniere_utilisation'] as String) : null,
      creeLe: DateTime.tryParse(json['cree_le'] as String? ?? '') ?? DateTime.now(),
      estActuel: json['est_actuel'] as bool? ?? false,
    );
  }
}
