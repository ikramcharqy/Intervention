import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/error/failure.dart';

/// Traduit les erreurs Dio en Failure de domaine, en réutilisant le message
/// déjà fourni par l'API (`errorResponse()` / ApiResponseTrait côté Laravel)
/// plutôt que d'inventer un message générique quand un vrai message existe.
Failure mapDioError(DioException e) {
  if (e.type == DioExceptionType.connectionTimeout ||
      e.type == DioExceptionType.receiveTimeout ||
      e.type == DioExceptionType.connectionError) {
    return const NetworkFailure('Connexion impossible au serveur. Vérifiez votre réseau.');
  }

  final statusCode = e.response?.statusCode;
  final serverMessage = e.response?.data is Map ? e.response?.data['message'] as String? : null;

  if (statusCode == 401) {
    return UnauthorizedFailure(serverMessage ?? 'Session expirée, reconnectez-vous.');
  }

  return ServerFailure(serverMessage ?? 'Une erreur est survenue.');
}
