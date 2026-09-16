import 'package:equatable/equatable.dart';

/// Miroir de app/Models/Chantier.php — mêmes champs que ceux déjà exposés côté
/// portail Client (ClientModule\ChantierController::show()), via le nouvel
/// endpoint technicien GET /api/chantiers/{id} (Api\ChantierController) :
/// aucune fiche différente construite, la même donnée est simplement rendue
/// pour l'app Technicien.
class Chantier extends Equatable {
  final int id;
  final String nom;
  final String? adresse;
  final String? ville;
  final double? latitude;
  final double? longitude;
  final String? responsable;
  final String? telephoneResponsable;
  final String? emailResponsable;
  final List<ChantierEmplacement> emplacements;
  final List<ChantierDocument> documents;
  final List<ChantierIntervention> interventionsTerminees;

  const Chantier({
    required this.id,
    required this.nom,
    required this.adresse,
    required this.ville,
    required this.latitude,
    required this.longitude,
    required this.responsable,
    required this.telephoneResponsable,
    required this.emailResponsable,
    required this.emplacements,
    required this.documents,
    this.interventionsTerminees = const [],
  });

  bool get aPosition => latitude != null && longitude != null;

  bool get aContact => telephoneResponsable != null && telephoneResponsable!.trim().isNotEmpty;

  @override
  List<Object?> get props => [id, nom, latitude, longitude, responsable, telephoneResponsable];
}

class ChantierEmplacement extends Equatable {
  final int id;
  final String nom;
  final double? latitude;
  final double? longitude;

  const ChantierEmplacement({required this.id, required this.nom, required this.latitude, required this.longitude});

  @override
  List<Object?> get props => [id, nom, latitude, longitude];
}

class ChantierDocument extends Equatable {
  final int id;
  final String nomOriginal;
  final String? typeDocument;
  final String url;

  const ChantierDocument({required this.id, required this.nomOriginal, required this.typeDocument, required this.url});

  @override
  List<Object?> get props => [id, nomOriginal, url];
}

/// Miroir compact d'une Intervention Terminée/Validée sur ce chantier — même
/// source que la fiche chantier web (InterventionService::pourChantiers), pas
/// une requête reconstruite différemment (Étape 6 du prompt "design de
/// référence").
class ChantierIntervention extends Equatable {
  final int id;
  final String codeIntervention;
  final String statut;
  final String? typeInterventionNom;
  final DateTime? dateFin;

  const ChantierIntervention({
    required this.id,
    required this.codeIntervention,
    required this.statut,
    required this.typeInterventionNom,
    required this.dateFin,
  });

  @override
  List<Object?> get props => [id, codeIntervention, statut, dateFin];
}
