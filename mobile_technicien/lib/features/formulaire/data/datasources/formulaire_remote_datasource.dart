import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/formulaire/data/models/formulaire_model.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/form_file_answer.dart';

class FormulaireRemoteDatasource {
  final ApiClient apiClient;
  FormulaireRemoteDatasource(this.apiClient);

  /// GET /api/interventions/{id}/formulaire (FormulaireController::show).
  Future<FormulaireModel> getFormulaire(int interventionId) async {
    final response = await apiClient.dio.get('/interventions/$interventionId/formulaire');
    final data = response.data['data'] as Map<String, dynamic>;
    return FormulaireModel.fromJson(data['formulaire'] as Map<String, dynamic>);
  }

  /// POST /api/interventions/{id}/formulaire (FormulaireController::store),
  /// multipart : `reponses[question_id]` (scalaire) ou `reponses[question_id][]`
  /// (Checkbox multi) pour les réponses texte/nombre/choix, `fichiers[question_id]`
  /// ou `fichiers[question_id][]` pour Photo/Signature/Document — mêmes clés que
  /// lues par RemplissageFormulaireService::sauvegarderReponses côté backend.
  /// [reponses] : valeur `String` ou `List<String>` (Checkbox) par question_id.
  /// [fichiers] : entrées `FormFileAnswer.toDraftString()` (JSON+base64) par
  /// question_id — jamais des chemins de fichiers (inexploitables sur
  /// Flutter Web, seule plateforme actuellement configurée dans ce projet) ;
  /// décodées en octets ici pour construire les `MultipartFile`.
  Future<void> submitFormulaire(
    int interventionId, {
    required Map<int, dynamic> reponses,
    required Map<int, List<String>> fichiers,
  }) async {
    final formData = FormData();

    reponses.forEach((questionId, value) {
      if (value is List) {
        for (final item in value) {
          formData.fields.add(MapEntry('reponses[$questionId][]', '$item'));
        }
      } else {
        formData.fields.add(MapEntry('reponses[$questionId]', '$value'));
      }
    });

    for (final entry in fichiers.entries) {
      final questionId = entry.key;
      final draftEntries = entry.value;
      for (var i = 0; i < draftEntries.length; i++) {
        final file = FormFileAnswer.fromDraftString(draftEntries[i]);
        final key = draftEntries.length > 1 ? 'fichiers[$questionId][]' : 'fichiers[$questionId]';
        formData.files.add(MapEntry(key, MultipartFile.fromBytes(file.bytes, filename: file.nom)));
      }
    }

    await apiClient.dio.post('/interventions/$interventionId/formulaire', data: formData);
  }

  /// POST /api/interventions/{id}/finish (InterventionController::finish) —
  /// Formulaire rempli → Terminée.
  Future<void> finishIntervention(int interventionId, {String? commentaire}) async {
    await apiClient.dio.post('/interventions/$interventionId/finish', data: {
      if (commentaire != null && commentaire.trim().isNotEmpty) 'commentaire': commentaire.trim(),
    });
  }
}
