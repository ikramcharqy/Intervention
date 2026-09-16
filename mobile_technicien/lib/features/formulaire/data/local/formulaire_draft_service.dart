import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

/// Persistance locale du brouillon de formulaire (Étape 3.4) — pour ne pas
/// perdre la saisie terrain en cas de fermeture accidentelle de l'app ou de
/// perte de connexion. Tout (texte/nombre/date/choix ET fichiers) est stocké
/// dans SharedPreferences ; les fichiers (Photo/Signature/Document) sont
/// encodés en base64 via FormFileAnswer.toDraftString() plutôt que copiés
/// dans un répertoire de l'app — cette dernière approche (dart:io
/// File/Directory + path_provider) ne fonctionne pas sur Flutter Web
/// (aucun système de fichiers), qui est la seule plateforme actuellement
/// configurée dans ce projet (pas de dossier android/ ni ios/).
class FormulaireDraftService {
  static const _prefsKeyPrefix = 'form_draft_intervention_';

  Future<void> saveAnswers(int interventionId, Map<int, dynamic> answers) async {
    final prefs = await SharedPreferences.getInstance();
    final encoded = jsonEncode(answers.map((key, value) => MapEntry(key.toString(), value)));
    await prefs.setString('$_prefsKeyPrefix$interventionId', encoded);
  }

  Future<Map<int, dynamic>?> loadAnswers(int interventionId) async {
    final prefs = await SharedPreferences.getInstance();
    final raw = prefs.getString('$_prefsKeyPrefix$interventionId');
    if (raw == null) return null;
    final decoded = jsonDecode(raw) as Map<String, dynamic>;
    return decoded.map((key, value) => MapEntry(int.parse(key), value));
  }

  Future<void> clear(int interventionId) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('$_prefsKeyPrefix$interventionId');
  }
}
