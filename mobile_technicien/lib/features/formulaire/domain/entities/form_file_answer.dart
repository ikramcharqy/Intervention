import 'dart:convert';
import 'dart:typed_data';

/// Représentation en mémoire d'un fichier joint à une réponse (Photo/
/// Signature/Document) — des OCTETS, jamais un chemin de fichier disque.
/// Flutter Web n'a pas de `dart:io`/accès système de fichiers, et
/// image_picker/file_picker n'y renvoient pas de chemin exploitable
/// (`XFile.path` est une blob URL, `PlatformFile.path` est `null`) — un
/// chemin ne fonctionne donc que par accident sur mobile et jamais sur web,
/// ce qui empêchait les photos de s'importer. Les octets fonctionnent de
/// façon identique sur toutes les plateformes.
class FormFileAnswer {
  final String nom;
  final Uint8List bytes;

  const FormFileAnswer({required this.nom, required this.bytes});

  /// Sérialisé en une seule chaîne (JSON + base64) pour cohabiter avec les
  /// réponses `List<String>` déjà utilisées ailleurs (Checkbox) et être
  /// persisté tel quel dans le brouillon local (SharedPreferences), sans
  /// dépendre d'un répertoire de fichiers.
  String toDraftString() => jsonEncode({'nom': nom, 'data': base64Encode(bytes)});

  static FormFileAnswer fromDraftString(String value) {
    final decoded = jsonDecode(value) as Map<String, dynamic>;
    return FormFileAnswer(nom: decoded['nom'] as String, bytes: base64Decode(decoded['data'] as String));
  }
}
