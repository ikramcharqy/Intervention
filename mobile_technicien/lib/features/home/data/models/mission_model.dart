import 'package:mobile_technicien/features/home/domain/entities/mission.dart';

/// Reflète la charge utile réelle de `GET /api/interventions`
/// (InterventionController::index, `with(['chantier', 'emplacement',
/// 'typeIntervention'])`) — vérifié en direct sur l'API. `historiques` n'est
/// présent que sur `GET /api/interventions/{id}` (détail) — absent sur la
/// liste, donc toujours vide pour une Mission construite depuis
/// getMissions().
class MissionModel extends Mission {
  const MissionModel({
    required super.id,
    required super.codeIntervention,
    required super.statut,
    required super.priorite,
    required super.datePrevueDebut,
    required super.description,
    required super.chantierNom,
    required super.emplacementNom,
    required super.typeInterventionNom,
    super.chantierLatitude,
    super.chantierLongitude,
    super.chantierId,
    super.emplacementLatitude,
    super.emplacementLongitude,
    super.historiques,
    super.rapportExiste,
  });

  factory MissionModel.fromJson(Map<String, dynamic> json) {
    final chantier = json['chantier'] as Map<String, dynamic>?;
    final emplacement = json['emplacement'] as Map<String, dynamic>?;
    final typeIntervention = json['type_intervention'] as Map<String, dynamic>?;
    final historiquesJson = json['historiques'] as List<dynamic>?;

    return MissionModel(
      id: json['id'] as int,
      codeIntervention: json['code_intervention'] as String? ?? '—',
      statut: json['statut'] as String? ?? '',
      priorite: json['priorite'] as String? ?? 'Normale',
      datePrevueDebut: json['date_prevue_debut'] != null
          ? DateTime.tryParse(json['date_prevue_debut'] as String)
          : null,
      description: json['description'] as String? ?? '',
      chantierNom: chantier?['nom'] as String? ?? 'Chantier inconnu',
      emplacementNom: emplacement?['nom'] as String?,
      typeInterventionNom: typeIntervention?['nom'] as String? ?? 'Intervention',
      // decimal:7 côté Laravel est sérialisé en JSON comme une chaîne, pas un nombre.
      chantierLatitude: double.tryParse('${chantier?['latitude'] ?? ''}'),
      chantierLongitude: double.tryParse('${chantier?['longitude'] ?? ''}'),
      chantierId: chantier?['id'] as int?,
      emplacementLatitude: double.tryParse('${emplacement?['latitude'] ?? ''}'),
      emplacementLongitude: double.tryParse('${emplacement?['longitude'] ?? ''}'),
      historiques: historiquesJson == null
          ? const []
          : (historiquesJson
              .map((h) => HistoriqueEntry(
                    statutAvant: h['statut_avant'] as String?,
                    statutApres: h['statut_apres'] as String? ?? '',
                    commentaire: h['commentaire'] as String?,
                    date: h['created_at'] != null ? DateTime.tryParse(h['created_at'] as String) : null,
                    auteur: (h['user'] as Map<String, dynamic>?)?['name'] as String?,
                  ))
              .toList()
            ..sort((a, b) => (b.date ?? DateTime(0)).compareTo(a.date ?? DateTime(0)))),
      // `rapport` n'est présent (même à `null`) que sur GET
      // /api/interventions/{id} (détail) — absent sur GET /api/interventions
      // (liste), d'où la distinction via containsKey plutôt qu'un simple `!= null`.
      rapportExiste: json.containsKey('rapport') ? json['rapport'] != null : null,
    );
  }
}
