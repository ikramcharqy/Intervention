import 'package:mobile_technicien/core/constants/api_constants.dart';

/// Construit l'URL absolue d'un fichier stocké sur le disque `public` Laravel
/// à partir d'un chemin relatif renvoyé tel quel par l'API (ex :
/// `signatures/xxx.png`, `photos/rapports/xxx.jpg`).
///
/// Passe par `/api/media/{path}` (Api\MediaController) — PAS `/storage/{path}`
/// (lien symbolique) : ce dernier est servi statiquement par le serveur web
/// sans jamais passer par Laravel, donc jamais soumis au middleware CORS
/// (`config/cors.php` ne couvre que `api/*`). Le rendu CanvasKit de Flutter
/// Web récupère les images via `fetch()`, soumis aux règles CORS du
/// navigateur, contrairement à une balise `<img>` HTML classique.
String? absoluteStorageUrl(String? relativePath) {
  if (relativePath == null || relativePath.isEmpty) return null;
  if (relativePath.startsWith('http://') || relativePath.startsWith('https://')) return relativePath;
  final apiBase = ApiConstants.baseUrl.replaceFirst(RegExp(r'/api/?$'), '');
  final cleanPath = relativePath.startsWith('/') ? relativePath.substring(1) : relativePath;
  return '$apiBase/api/media/$cleanPath';
}
