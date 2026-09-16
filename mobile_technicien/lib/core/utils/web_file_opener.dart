import 'dart:html' as html;
import 'dart:typed_data';

/// Déclenche un téléchargement natif d'octets binaires (PDF, etc.) via une
/// URL Blob + un élément `<a download>` simulé — PAS `window.open()` : ce
/// dernier n'est autorisé sans blocage popup que s'il est appelé de façon
/// SYNCHRONE dans le même call stack qu'un geste utilisateur direct. Ici,
/// l'ouverture survient après un `await` (la récupération des octets du PDF
/// via Dio), donc après un aller-retour réseau — la plupart des navigateurs
/// ne considèrent alors plus l'appel comme directement déclenché par
/// l'utilisateur et bloquent silencieusement la popup, ce qui donnait
/// l'impression que "le téléchargement ne s'effectue pas". Un clic simulé
/// sur un `<a download>` n'ouvre pas de fenêtre/onglet et n'est pas soumis à
/// ce blocage. Web-only (`dart:html`) : ce projet ne cible actuellement QUE
/// Flutter Web (aucun dossier android/ ni ios/) — à remplacer par une
/// implémentation conditionnelle par plateforme si des cibles natives sont
/// ajoutées un jour.
void downloadBytes(Uint8List bytes, {required String mimeType, required String filename}) {
  final blob = html.Blob([bytes], mimeType);
  final url = html.Url.createObjectUrlFromBlob(blob);
  html.AnchorElement(href: url)
    ..setAttribute('download', filename)
    ..click();
  // Révoqué après un délai plutôt qu'immédiatement, pour laisser le temps au
  // navigateur d'amorcer réellement le téléchargement avant que l'URL ne
  // devienne invalide.
  Future.delayed(const Duration(seconds: 30), () => html.Url.revokeObjectUrl(url));
}
