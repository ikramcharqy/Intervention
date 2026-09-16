import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';

/// Reflète exactement la charge utile `data.user` retournée par
/// AuthController::login / AuthController::me (vérifié en direct via
/// `POST /api/login` et `GET /api/me` sur l'API existante).
class TechnicienModel extends Technicien {
  const TechnicienModel({
    required super.id,
    super.prenom,
    required super.name,
    required super.email,
    required super.roles,
  });

  factory TechnicienModel.fromJson(Map<String, dynamic> json) {
    return TechnicienModel(
      id: json['id'] as int,
      name: json['name'] as String,
      prenom: json['prenom'] as String?,
      email: json['email'] as String,
      roles: (json['roles'] as List<dynamic>? ?? []).map((r) => r.toString()).toList(),
    );
  }
}
