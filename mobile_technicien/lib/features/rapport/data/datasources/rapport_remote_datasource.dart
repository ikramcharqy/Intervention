import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/rapport/data/models/rapport_model.dart';

class RapportRemoteDatasource {
  final ApiClient apiClient;
  RapportRemoteDatasource(this.apiClient);

  /// GET /api/interventions/{id}/rapport (Api\RapportController::showByIntervention).
  Future<RapportModel> getRapportByIntervention(int interventionId) async {
    final response = await apiClient.dio.get('/interventions/$interventionId/rapport');
    return RapportModel.fromJson(response.data['data'] as Map<String, dynamic>);
  }

  /// GET /api/rapports/{id}/pdf (Api\RapportController::downloadPdf) — le PDF
  /// exige la même auth Bearer que le reste de l'API, donc pas d'URL publique
  /// à ouvrir directement dans un navigateur externe : on récupère les octets
  /// via Dio (déjà authentifié) puis on les ouvre côté appelant.
  Future<List<int>> getRapportPdfBytes(int rapportId) async {
    final response = await apiClient.dio.get<List<int>>(
      '/rapports/$rapportId/pdf',
      options: Options(responseType: ResponseType.bytes),
    );
    return response.data!;
  }
}
