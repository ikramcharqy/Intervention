import 'package:equatable/equatable.dart';

/// Miroir de la réponse `GET /api/profile` (Api\ProfileController::show) —
/// déjà enrichie de statistiques côté backend, pas recalculées côté app.
class ProfileData extends Equatable {
  final int id;
  final String name;
  final String prenom;
  final String email;
  final String? telephone;
  final String? adresse;
  final String? photoUrl;

  /// Catégorie unique réellement exploitée aujourd'hui côté backend
  /// (cf. InterventionService::souhaiteNotification()) — `true` par défaut
  /// tant que jamais désactivée explicitement.
  final bool notificationInterventionUpdates;

  final ProfileStats stats;

  const ProfileData({
    required this.id,
    required this.name,
    required this.prenom,
    required this.email,
    required this.telephone,
    required this.adresse,
    required this.photoUrl,
    required this.notificationInterventionUpdates,
    required this.stats,
  });

  String get displayName => prenom.isNotEmpty ? prenom : name;

  /// Initiales pour l'avatar de repli (pas de photo) — ex: "Yassine Amine" → "YA".
  String get initiales {
    final p = prenom.isNotEmpty ? prenom[0] : '';
    final n = name.isNotEmpty ? name[0] : '';
    final combined = '$p$n'.toUpperCase();
    return combined.isNotEmpty ? combined : '?';
  }

  @override
  List<Object?> get props => [id, name, prenom, email, telephone, adresse, photoUrl, notificationInterventionUpdates, stats];
}

/// `App\Models\Intervention n'a pas de note de satisfaction client agrégée —
/// champ volontairement absent ici plutôt qu'inventé (cf. Étape 3.2 du
/// prompt Notifications/Profil/Paramètres : signaler l'absence, pas
/// improviser).
class ProfileStats extends Equatable {
  final int totalInterventions;
  final int enCours;
  final int terminees;
  final int enAttente;

  const ProfileStats({
    required this.totalInterventions,
    required this.enCours,
    required this.terminees,
    required this.enAttente,
  });

  @override
  List<Object?> get props => [totalInterventions, enCours, terminees, enAttente];
}
