import 'package:mobile_technicien/core/constants/api_constants.dart';
import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Étape 3.1 : statut de présence "En service" / "Hors service".
///
/// ⚠️ Étape 3.2 — question de conformité SIGNALÉE, PAS TRANCHÉE :
/// ce service NE DÉCLENCHE ET N'ARRÊTE AUCUN TRACKING GPS. Il ne fait que
/// mémoriser un statut déclaratif.
///
/// Persisté côté serveur depuis PUT /api/profile/presence (users.en_service +
/// en_service_maj_le) — SharedPreferences ne sert plus que de cache
/// d'affichage immédiat/hors-ligne : `setEnService` écrit les deux, en
/// remontant l'erreur serveur à l'appelant (pas de fallback silencieux qui
/// ferait croire à une synchronisation réussie alors qu'elle a échoué).
///
/// Avant de lier ce statut au tracking GPS live (module Super Admin
/// "Tracking GPS Live" / TrackingController), il faut une décision validée sur
/// la question suivante, potentiellement couverte par le droit du travail
/// marocain sur la géolocalisation des salariés : le tracking GPS doit-il être
/// actif UNIQUEMENT pendant les heures déclarées "En service" ? Cette décision
/// n'a pas été prise dans le cadre de cette passe.
class PresenceService {
  final ApiClient _apiClient;
  PresenceService(this._apiClient);

  static const _key = 'presence_en_service';

  Future<bool> isEnService() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_key) ?? false;
  }

  /// Lance à la fois l'écriture locale immédiate (affichage réactif) et
  /// l'appel serveur. Lève une [DioException] si la synchronisation serveur
  /// échoue — à l'appelant de décider comment l'afficher (le cache local
  /// reste écrit pour ne pas perdre le geste utilisateur en cas de coupure
  /// réseau ponctuelle, mais la source de vérité reste le serveur au
  /// prochain chargement).
  Future<void> setEnService(bool value) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_key, value);

    await _apiClient.dio.put(ApiConstants.profilePresence, data: {'en_service': value});
  }
}
