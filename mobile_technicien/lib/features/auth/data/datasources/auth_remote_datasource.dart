import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/constants/api_constants.dart';
import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/auth/data/models/technicien_model.dart';

class AuthRemoteDatasource {
  final ApiClient apiClient;

  AuthRemoteDatasource(this.apiClient);

  /// POST /api/login — enveloppe {success, message, data: {token, user}}
  /// (App\Http\Controllers\Api\AuthController::login, vérifié en direct).
  Future<({String token, TechnicienModel user})> login({
    required String email,
    required String password,
  }) async {
    final response = await apiClient.dio.post(
      ApiConstants.login,
      data: {'email': email, 'password': password},
    );

    final data = response.data['data'] as Map<String, dynamic>;
    return (
      token: data['token'] as String,
      user: TechnicienModel.fromJson(data['user'] as Map<String, dynamic>),
    );
  }

  /// GET /api/me
  Future<TechnicienModel> me() async {
    final response = await apiClient.dio.get(ApiConstants.me);
    final data = response.data['data'] as Map<String, dynamic>;
    return TechnicienModel.fromJson(data);
  }

  /// POST /api/logout — révoque le token courant côté serveur
  /// (`$request->user()->currentAccessToken()->delete()`).
  Future<void> logout() async {
    try {
      await apiClient.dio.post(ApiConstants.logout);
    } on DioException {
      // Le token peut déjà être invalide (session déjà révoquée à distance,
      // cf. Étape 5.2) — la déconnexion locale doit réussir dans tous les cas.
    }
  }
}
