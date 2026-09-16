import 'package:mobile_technicien/core/utils/storage_url.dart';
import 'package:mobile_technicien/features/rapport/domain/entities/rapport.dart';

/// Reflète la charge utile réelle de `GET /api/interventions/{id}/rapport`
/// (Api\RapportController::showByIntervention, `load(['photos', 'videos',
/// 'documents', 'reponses.question', 'reponses.choixQuestion'])`).
class RapportModel extends Rapport {
  const RapportModel({
    required super.id,
    required super.travauxEffectues,
    required super.observations,
    required super.recommandations,
    required super.commentaire,
    required super.statutEquipement,
    required super.qrcodeScanne,
    required super.dureeReelle,
    required super.gpsLatitude,
    required super.gpsLongitude,
    required super.signatureTechnicienUrl,
    required super.signatureClientUrl,
    required super.reponses,
    required super.photos,
    required super.documents,
  });

  factory RapportModel.fromJson(Map<String, dynamic> json) {
    final reponsesJson = json['reponses'] as List<dynamic>? ?? [];
    final photosJson = json['photos'] as List<dynamic>? ?? [];
    final documentsJson = json['documents'] as List<dynamic>? ?? [];

    return RapportModel(
      id: json['id'] as int,
      travauxEffectues: json['travaux_effectues'] as String?,
      observations: json['observations'] as String?,
      recommandations: json['recommandations'] as String?,
      commentaire: json['commentaire'] as String?,
      statutEquipement: json['statut_equipement'] as String?,
      qrcodeScanne: json['qrcode_scanne'] as String?,
      dureeReelle: (json['duree_reelle'] as num?)?.toInt(),
      gpsLatitude: double.tryParse('${json['gps_latitude'] ?? ''}'),
      gpsLongitude: double.tryParse('${json['gps_longitude'] ?? ''}'),
      signatureTechnicienUrl: absoluteStorageUrl(json['signature_technicien'] as String?),
      signatureClientUrl: absoluteStorageUrl(json['signature_client'] as String?),
      reponses: reponsesJson.map((r) {
        final question = r['question'] as Map<String, dynamic>?;
        final choix = r['choix_question'] as Map<String, dynamic>?;
        final choixLabels = r['choix_labels'] as List<dynamic>?;
        return RapportReponse(
          question: question?['question'] as String? ?? '—',
          type: question?['type_reponse'] as String? ?? 'Texte',
          reponseTexte: r['reponse_texte'] as String?,
          reponseNombre: r['reponse_nombre'] as num?,
          reponseFichierUrl: absoluteStorageUrl(r['reponse_fichier'] as String?),
          choixLibelle: choix?['valeur'] as String?,
          choixLibelles: choixLabels?.cast<String>(),
        );
      }).toList(),
      photos: photosJson
          .map((p) => RapportPhoto(
                url: absoluteStorageUrl(p['chemin'] as String?) ?? '',
                typePhoto: p['type_photo'] as String?,
                description: p['description'] as String?,
              ))
          .where((p) => p.url.isNotEmpty)
          .toList(),
      documents: documentsJson
          .map((d) => RapportDocument(
                nomOriginal: d['nom_original'] as String? ?? 'Document',
                url: absoluteStorageUrl(d['chemin'] as String?) ?? '',
              ))
          .where((d) => d.url.isNotEmpty)
          .toList(),
    );
  }
}
