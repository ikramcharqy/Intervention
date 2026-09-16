/// "Dans Xh/min" si dans les prochaines heures, "aujourd'hui"/"demain" sinon,
/// "en retard" si la date est dépassée — factorisé depuis NextMissionCard
/// (écran d'accueil) pour être réutilisé tel quel sur l'écran Missions
/// (Étape 3.2 du prompt Missions : "cohérent avec la correction déjà
/// demandée sur l'accueil"), plutôt que dupliqué.
String? relativeTimeLabel(DateTime? date, {DateTime? now}) {
  if (date == null) return null;
  final local = date.toLocal();
  final reference = now ?? DateTime.now();
  final diff = local.difference(reference);

  if (diff.inMinutes.abs() < 1) return 'maintenant';

  if (diff.inMinutes > 0 && diff.inHours < 6) {
    final h = diff.inHours;
    final min = diff.inMinutes % 60;
    if (h > 0) return 'dans ${h}h${min.toString().padLeft(2, '0')}';
    return 'dans $min min';
  }

  final sameDay = local.year == reference.year && local.month == reference.month && local.day == reference.day;
  if (sameDay) return "aujourd'hui";

  final tomorrow = reference.add(const Duration(days: 1));
  final isTomorrow = local.year == tomorrow.year && local.month == tomorrow.month && local.day == tomorrow.day;
  if (isTomorrow) return 'demain';

  if (diff.isNegative) return 'en retard';

  return null;
}
