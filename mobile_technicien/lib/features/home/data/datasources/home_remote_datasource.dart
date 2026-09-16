import 'package:mobile_technicien/core/constants/api_constants.dart';
import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/home/data/models/mission_model.dart';

class HomeRemoteDatasource {
  final ApiClient apiClient;
  HomeRemoteDatasource(this.apiClient);

  /// GET /api/interventions — liste complète des missions du technicien
  /// connecté (déjà filtrée côté serveur par `technicien_id`).
  Future<List<MissionModel>> getMissions() async {
    final response = await apiClient.dio.get(ApiConstants.interventions);
    final list = response.data['data'] as List<dynamic>;
    return list.map((e) => MissionModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  /// GET /api/notifications/unread — utilisé pour le badge de notification
  /// (Étape 6.2), pas pour le contenu détaillé des notifications ici.
  Future<int> getUnreadNotificationsCount() async {
    final response = await apiClient.dio.get(ApiConstants.notificationsUnread);
    final data = response.data['data'] as Map<String, dynamic>;
    return data['unread_count'] as int;
  }

  /// POST /api/interventions/{id}/start. Mode Manuel par défaut (action
  /// rapide d'accueil) ; passe en mode GPS dès que l'écran appelant fournit
  /// une position (écran "Détails Mission" — capture ponctuelle de la
  /// position au moment de démarrer, pas un suivi continu). Le backend
  /// (InterventionService::startIntervention) reste seul responsable de
  /// toute validation métier sur cette position (ex: rayon GPS) — l'app
  /// envoie la donnée brute telle quelle.
  Future<void> startIntervention(int id, {double? latitude, double? longitude}) async {
    final hasGps = latitude != null && longitude != null;
    await apiClient.dio.post('/interventions/$id/start', data: {
      'mode': hasGps ? 'GPS' : 'Manuel',
      if (hasGps) 'latitude': latitude,
      if (hasGps) 'longitude': longitude,
    });
  }

  /// GET /api/interventions/{id} — détail complet (InterventionController::show).
  /// Réutilise MissionModel.fromJson (mêmes clés chantier/emplacement/
  /// type_intervention que la liste) plutôt qu'un second modèle : le detail
  /// endpoint ajoute rapport/materiaux/taches/historiques, ignorés ici car
  /// non lus par MissionModel.fromJson.
  Future<MissionModel> getMissionDetail(int id) async {
    final response = await apiClient.dio.get('/interventions/$id');
    return MissionModel.fromJson(response.data['data'] as Map<String, dynamic>);
  }

  /// POST /api/interventions/{id}/accept — Planifiée/Affectée → Acceptée.
  Future<void> acceptIntervention(int id) async {
    await apiClient.dio.post('/interventions/$id/accept');
  }

  /// POST /api/interventions/{id}/refuse — motif obligatoire (min 5
  /// caractères, validé côté serveur), crée une DemandeReaffectation. Réutilise
  /// le flux "Refus Techniciens" déjà existant plutôt qu'un mécanisme distinct.
  Future<void> refuseIntervention(int id, String motif) async {
    await apiClient.dio.post('/interventions/$id/refuse', data: {'motif': motif});
  }
}
