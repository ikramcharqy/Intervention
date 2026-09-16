import 'package:mobile_technicien/features/profile/domain/entities/profile_data.dart';

class ProfileDataModel extends ProfileData {
  const ProfileDataModel({
    required super.id,
    required super.name,
    required super.prenom,
    required super.email,
    required super.telephone,
    required super.adresse,
    required super.photoUrl,
    required super.notificationInterventionUpdates,
    required super.stats,
  });

  factory ProfileDataModel.fromJson(Map<String, dynamic> json) {
    final user = json['user'] as Map<String, dynamic>;
    final stats = json['statistics'] as Map<String, dynamic>? ?? {};
    final prefs = user['notification_preferences'] as Map<String, dynamic>?;

    return ProfileDataModel(
      id: user['id'] as int,
      name: user['name'] as String? ?? '',
      prenom: user['prenom'] as String? ?? '',
      email: user['email'] as String? ?? '',
      telephone: user['telephone'] as String?,
      adresse: user['adresse'] as String?,
      photoUrl: user['photo_url'] as String?,
      notificationInterventionUpdates: (prefs?['intervention_updates'] as bool?) ?? true,
      stats: ProfileStats(
        totalInterventions: stats['total_interventions'] as int? ?? 0,
        enCours: stats['interventions_en_cours'] as int? ?? 0,
        terminees: stats['interventions_terminees'] as int? ?? 0,
        enAttente: stats['interventions_en_attente'] as int? ?? 0,
      ),
    );
  }
}
