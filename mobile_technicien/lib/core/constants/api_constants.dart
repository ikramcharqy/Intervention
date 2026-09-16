import 'package:flutter/foundation.dart' show kIsWeb;

/// Base URL de l'API Laravel/Sanctum existante (routes/api.php), inchangée.
/// À adapter par environnement (--dart-define=API_BASE_URL=...) plutôt que codée
/// en dur, car l'app tournera aussi sur un appareil physique, pas seulement sur
/// l'hôte du serveur.
class ApiConstants {
  ApiConstants._();

  static const String _defineUrl = String.fromEnvironment('API_BASE_URL');

  /// 10.0.2.2 est l'alias de l'hôte vu depuis l'émulateur Android — injoignable
  /// depuis Chrome (flutter run -d chrome), qui doit viser localhost directement.
  static final String baseUrl = _defineUrl.isNotEmpty
      ? _defineUrl
      : (kIsWeb ? 'http://localhost:8000/api' : 'http://10.0.2.2:8000/api');

  static const String login = '/login';
  static const String logout = '/logout';
  static const String me = '/me';
  static const String interventions = '/interventions';
  static const String notificationsUnread = '/notifications/unread';
  static const String profilePresence = '/profile/presence';
}
