import 'package:intl/intl.dart';

/// Équivalent Dart de Carbon::diffForHumans() (utilisé côté web dans
/// resources/views/client/notifications/index.blade.php) pour un horodatage
/// passé : "à l'instant" / "il y a Xmin" / "il y a Xh" / "il y a Xj", puis
/// bascule sur une date absolue au-delà d'une semaine.
String timeAgoLabel(DateTime date) {
  final local = date.toLocal();
  final diff = DateTime.now().difference(local);

  if (diff.inSeconds < 60) return "à l'instant";
  if (diff.inMinutes < 60) return 'il y a ${diff.inMinutes} min';
  if (diff.inHours < 24) return 'il y a ${diff.inHours} h';
  if (diff.inDays < 7) return 'il y a ${diff.inDays} j';

  return DateFormat('dd/MM/yyyy').format(local);
}
