/// Estimation grossière temps de trajet à partir d'une distance à vol
/// d'oiseau — AUCUNE API de routing (Google Directions/Mapbox/OSRM) n'existe
/// dans ce projet, seule `Geolocator.distanceBetween` (déjà utilisée pour la
/// carte "Prochaine mission" de l'accueil) est disponible. Vitesse moyenne
/// forfaitaire, à afficher explicitement comme une estimation côté UI, jamais
/// comme un temps de trajet routier exact.
class EtaEstimate {
  final double distanceKm;
  final Duration duree;
  const EtaEstimate({required this.distanceKm, required this.duree});

  String get distanceLabel => '${distanceKm.toStringAsFixed(1)} km';

  String get dureeLabel {
    final minutes = duree.inMinutes;
    if (minutes < 60) return '~$minutes min';
    final heures = minutes ~/ 60;
    final reste = minutes % 60;
    return reste == 0 ? '~${heures}h' : '~${heures}h$reste';
  }
}

EtaEstimate estimateEta(double distanceKm, {double vitesseMoyenneKmh = 35}) {
  final heures = distanceKm / vitesseMoyenneKmh;
  return EtaEstimate(distanceKm: distanceKm, duree: Duration(minutes: (heures * 60).round().clamp(1, 24 * 60)));
}
