import 'package:equatable/equatable.dart';

/// Miroir de app/Models/Rapport.php — même structure que celle déjà
/// normalisée côté web (Super Admin/Admin/Commercial/Client, via
/// <x-rapport-formulaire>) : réponses groupées par Question avec leur type,
/// photos, documents, signatures, coordonnées GPS réelles. Consommé ici via
/// GET /api/interventions/{id}/rapport (Api\RapportController::showByIntervention),
/// pas une structure reconstruite différemment pour l'app Technicien.
class Rapport extends Equatable {
  final int id;
  final String? travauxEffectues;
  final String? observations;
  final String? recommandations;
  final String? commentaire;
  final String? statutEquipement;
  final String? qrcodeScanne;
  final int? dureeReelle;
  final double? gpsLatitude;
  final double? gpsLongitude;
  final String? signatureTechnicienUrl;
  final String? signatureClientUrl;
  final List<RapportReponse> reponses;
  final List<RapportPhoto> photos;
  final List<RapportDocument> documents;

  const Rapport({
    required this.id,
    required this.travauxEffectues,
    required this.observations,
    required this.recommandations,
    required this.commentaire,
    required this.statutEquipement,
    required this.qrcodeScanne,
    required this.dureeReelle,
    required this.gpsLatitude,
    required this.gpsLongitude,
    required this.signatureTechnicienUrl,
    required this.signatureClientUrl,
    required this.reponses,
    required this.photos,
    required this.documents,
  });

  bool get aPosition => gpsLatitude != null && gpsLongitude != null;

  @override
  List<Object?> get props => [id];
}

/// Miroir d'une ligne `App\Models\Reponse`, avec sa Question associée — le
/// rendu par type (texte, choix résolu via ChoixQuestion, fichier) est
/// calculé ici (`valeurAffichee`), pas côté widget, pour rester la seule
/// définition du "comment afficher une réponse selon son type" côté mobile,
/// à l'image de x-rapport-reponse-valeur.blade.php côté web.
class RapportReponse extends Equatable {
  final String question;
  final String type;
  final String? reponseTexte;
  final num? reponseNombre;
  final String? reponseFichierUrl;
  final String? choixLibelle;

  /// Checkbox à sélection multiple : `App\Models\Reponse::choix_labels`
  /// (accesseur backend) — libellés déjà résolus, jamais les identifiants
  /// bruts que `reponse_texte` contient dans ce cas (ex: "3,7,9").
  final List<String>? choixLibelles;

  const RapportReponse({
    required this.question,
    required this.type,
    required this.reponseTexte,
    required this.reponseNombre,
    required this.reponseFichierUrl,
    required this.choixLibelle,
    this.choixLibelles,
  });

  bool get estTypeFichier => const ['Photo', 'Signature', 'Document'].contains(type);

  String get valeurAffichee {
    if (type == 'OuiNon') {
      return reponseTexte == '1' || reponseTexte?.toLowerCase() == 'oui' ? 'Oui' : 'Non';
    }
    if (choixLibelles != null && choixLibelles!.isNotEmpty) return choixLibelles!.join(', ');
    if (choixLibelle != null && choixLibelle!.isNotEmpty) return choixLibelle!;
    if (reponseTexte != null && reponseTexte!.isNotEmpty) return reponseTexte!;
    if (reponseNombre != null) return '$reponseNombre';
    return '—';
  }

  @override
  List<Object?> get props =>
      [question, type, reponseTexte, reponseNombre, reponseFichierUrl, choixLibelle, choixLibelles];
}

class RapportPhoto extends Equatable {
  final String url;
  final String? typePhoto;
  final String? description;

  const RapportPhoto({required this.url, required this.typePhoto, required this.description});

  @override
  List<Object?> get props => [url];
}

class RapportDocument extends Equatable {
  final String nomOriginal;
  final String url;

  const RapportDocument({required this.nomOriginal, required this.url});

  @override
  List<Object?> get props => [url];
}
