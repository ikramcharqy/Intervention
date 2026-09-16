import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/chantier/data/models/chantier_model.dart';

class ChantierRemoteDatasource {
  final ApiClient apiClient;
  ChantierRemoteDatasource(this.apiClient);

  /// GET /api/chantiers/{id} (Api\ChantierController::show) — restreint aux
  /// chantiers où le technicien connecté a au moins une intervention assignée.
  Future<ChantierModel> getChantierDetail(int chantierId) async {
    final response = await apiClient.dio.get('/chantiers/$chantierId');
    return ChantierModel.fromJson(response.data['data'] as Map<String, dynamic>);
  }
}
