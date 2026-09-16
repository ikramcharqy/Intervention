import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:geolocator/geolocator.dart';

enum LocationStatus { enabled, disabled, permissionDenied, permissionDeniedForever, notAvailable }

/// Étape 2.1 : statut GPS visible en permanence sur l'accueil. Lecture seule du
/// statut système — ne démarre AUCUN suivi de position en continu ici (le
/// tracking GPS lié aux interventions reste géré par TrackingController /
/// api/interventions/{id}/tracking/*, inchangé). Voir aussi la question de
/// conformité Étape 3.2 sur le lien statut de présence ↔ tracking GPS.
class LocationStatusService {
  Future<LocationStatus> currentStatus() async {
    try {
      final serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) return LocationStatus.disabled;

      final permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        return LocationStatus.permissionDenied;
      }
      if (permission == LocationPermission.deniedForever) {
        return LocationStatus.permissionDeniedForever;
      }
      return LocationStatus.enabled;
    } catch (_) {
      // Plateforme sans implémentation complète (ex: navigateurs qui ne
      // supportent pas l'API Permissions pour la géolocalisation) : on
      // affiche un état neutre plutôt que de planter.
      return LocationStatus.notAvailable;
    }
  }

  Future<LocationStatus> requestPermission() async {
    try {
      final permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) return LocationStatus.permissionDenied;
      if (permission == LocationPermission.deniedForever) {
        return LocationStatus.permissionDeniedForever;
      }
      return await currentStatus();
    } catch (_) {
      return LocationStatus.notAvailable;
    }
  }

  /// Position actuelle si le statut le permet — utilisée uniquement pour un
  /// calcul de distance ponctuel (Étape 5, carte "Prochaine mission"), jamais
  /// pour un suivi continu. Retourne null plutôt que de lever une exception
  /// dès que le GPS n'est pas disponible/autorisé, pour que l'appelant se
  /// contente de ne rien afficher.
  Future<Position?> getCurrentPosition() async {
    if (await currentStatus() != LocationStatus.enabled) return null;
    try {
      return await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.medium);
    } catch (_) {
      return null;
    }
  }

  /// Non implémentées côté web par geolocator_web (pas de "réglages système"
  /// dans un navigateur) : no-op sur web plutôt qu'une exception non gérée.
  Future<void> openLocationSettings() async {
    if (kIsWeb) return;
    await Geolocator.openLocationSettings();
  }

  Future<void> openAppSettings() async {
    if (kIsWeb) return;
    await Geolocator.openAppSettings();
  }
}
