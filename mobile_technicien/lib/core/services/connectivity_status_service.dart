import 'package:connectivity_plus/connectivity_plus.dart';

enum ConnectivityStatus { online, offline }

/// Étape 2.2 : statut de connectivité. L'app n'a PAS de file d'attente de
/// synchronisation hors-ligne à ce jour (aucune persistance locale de requêtes
/// en attente n'existe ni côté PWA Blade/JS ni ici) — ce service expose donc
/// uniquement "En ligne" / "Hors ligne" pour l'instant. Le compteur
/// "X élément(s) en attente de synchronisation" mentionné dans le prompt est un
/// PRÉREQUIS à documenter (file d'attente locale + rejeu automatique), pas à
/// improviser dans cette passe — voir README.md du projet.
class ConnectivityStatusService {
  final Connectivity _connectivity;

  ConnectivityStatusService({Connectivity? connectivity})
      : _connectivity = connectivity ?? Connectivity();

  Future<ConnectivityStatus> currentStatus() async {
    try {
      final results = await _connectivity.checkConnectivity();
      return _map(results);
    } catch (_) {
      // connectivity_plus a une implémentation web, mais certains navigateurs
      // restreignent l'API sous-jacente : on reste "en ligne" par défaut
      // plutôt que de planter l'app pour un simple indicateur.
      return ConnectivityStatus.online;
    }
  }

  Stream<ConnectivityStatus> watch() {
    return _connectivity.onConnectivityChanged.map(_map).handleError((_) {});
  }

  ConnectivityStatus _map(List<ConnectivityResult> results) {
    final hasConnection = results.any((r) => r != ConnectivityResult.none);
    return hasConnection ? ConnectivityStatus.online : ConnectivityStatus.offline;
  }
}
