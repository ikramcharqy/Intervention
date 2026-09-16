import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';

/// Reflète la charge utile réelle de `GET /api/chantiers/{id}`
/// (Api\ChantierController::show) : `{chantier: {...Chantier, emplacements:
/// [...]}, documents: [...]}`.
class ChantierModel extends Chantier {
  const ChantierModel({
    required super.id,
    required super.nom,
    required super.adresse,
    required super.ville,
    required super.latitude,
    required super.longitude,
    required super.responsable,
    required super.telephoneResponsable,
    required super.emailResponsable,
    required super.emplacements,
    required super.documents,
    super.interventionsTerminees,
  });

  factory ChantierModel.fromJson(Map<String, dynamic> json) {
    final chantierJson = json['chantier'] as Map<String, dynamic>;
    final emplacementsJson = chantierJson['emplacements'] as List<dynamic>? ?? [];
    final documentsJson = json['documents'] as List<dynamic>? ?? [];
    final interventionsJson = json['interventions_terminees'] as List<dynamic>? ?? [];

    return ChantierModel(
      id: chantierJson['id'] as int,
      nom: chantierJson['nom'] as String? ?? 'Chantier',
      adresse: chantierJson['adresse'] as String?,
      ville: chantierJson['ville'] as String?,
      // decimal:7 côté Laravel est sérialisé en JSON comme une chaîne.
      latitude: double.tryParse('${chantierJson['latitude'] ?? ''}'),
      longitude: double.tryParse('${chantierJson['longitude'] ?? ''}'),
      responsable: chantierJson['responsable'] as String?,
      telephoneResponsable: chantierJson['telephone_responsable'] as String?,
      emailResponsable: chantierJson['email_responsable'] as String?,
      emplacements: emplacementsJson
          .map((e) => ChantierEmplacement(
                id: e['id'] as int,
                nom: e['nom'] as String? ?? 'Emplacement',
                latitude: double.tryParse('${e['latitude'] ?? ''}'),
                longitude: double.tryParse('${e['longitude'] ?? ''}'),
              ))
          .toList(),
      documents: documentsJson
          .map((d) => ChantierDocument(
                id: d['id'] as int,
                nomOriginal: d['nom_original'] as String? ?? 'Document',
                typeDocument: d['type_document'] as String?,
                url: d['url'] as String,
              ))
          .toList(),
      interventionsTerminees: interventionsJson
          .map((i) => ChantierIntervention(
                id: i['id'] as int,
                codeIntervention: i['code_intervention'] as String? ?? '—',
                statut: i['statut'] as String? ?? '',
                typeInterventionNom: i['type_intervention_nom'] as String?,
                dateFin: i['date_fin'] != null ? DateTime.tryParse(i['date_fin'] as String) : null,
              ))
          .toList(),
    );
  }
}
