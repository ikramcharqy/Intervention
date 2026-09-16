import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/constants/api_constants.dart';
import 'package:mobile_technicien/core/storage/secure_storage_service.dart';

/// Client HTTP unique vers l'API Sanctum existante. Toute la couche data des
/// features (auth, home, ...) passe par ce client — aucune feature n'appelle
/// Dio directement, pour garder un seul endroit qui connaît le token/l'enveloppe
/// de réponse {success, message, data} définie côté Laravel
/// (App\Traits\ApiResponseTrait).
class ApiClient {
  final Dio dio;
  final SecureStorageService storage;

  ApiClient({required this.storage})
      : dio = Dio(BaseOptions(
          baseUrl: ApiConstants.baseUrl,
          connectTimeout: const Duration(seconds: 15),
          receiveTimeout: const Duration(seconds: 15),
          headers: {'Accept': 'application/json'},
        )) {
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await storage.readToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
    ));
  }
}
