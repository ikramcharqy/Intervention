import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';

/// Reflète la charge utile réelle de `GET /api/interventions/{id}/formulaire`
/// (FormulaireController::show → `Formulaire::with(['questions.choix'])`).
class FormulaireModel extends Formulaire {
  const FormulaireModel({required super.id, required super.nom, required super.description, required super.questions});

  factory FormulaireModel.fromJson(Map<String, dynamic> json) {
    final questionsJson = json['questions'] as List<dynamic>? ?? [];
    final questions = questionsJson.map((q) => QuestionModel.fromJson(q as Map<String, dynamic>)).toList()
      ..sort((a, b) => a.ordre.compareTo(b.ordre));

    return FormulaireModel(
      id: json['id'] as int,
      nom: json['nom'] as String? ?? 'Formulaire',
      description: json['description'] as String?,
      questions: questions,
    );
  }
}

class QuestionModel extends Question {
  const QuestionModel({
    required super.id,
    required super.question,
    required super.type,
    required super.obligatoire,
    required super.ordre,
    required super.placeholder,
    required super.valeurParDefaut,
    required super.nombreMin,
    required super.nombreMax,
    required super.nombreUnite,
    required super.fichiersMax,
    required super.choix,
  });

  factory QuestionModel.fromJson(Map<String, dynamic> json) {
    final choixJson = json['choix'] as List<dynamic>? ?? [];
    final choix = choixJson.map((c) => ChoixQuestionModel.fromJson(c as Map<String, dynamic>)).toList();

    return QuestionModel(
      id: json['id'] as int,
      question: json['question'] as String? ?? '',
      type: json['type_reponse'] as String? ?? 'Texte',
      obligatoire: json['obligatoire'] as bool? ?? false,
      ordre: json['ordre'] as int? ?? 0,
      placeholder: json['placeholder'] as String?,
      valeurParDefaut: json['valeur_par_defaut'] as String?,
      nombreMin: (json['nombre_min'] as num?)?.toDouble(),
      nombreMax: (json['nombre_max'] as num?)?.toDouble(),
      nombreUnite: json['nombre_unite'] as String?,
      // Absent/nul côté serveur = un seul fichier accepté (comportement par
      // défaut de RemplissageFormulaireService, qui stocke un seul chemin
      // tant qu'un tableau de fichiers n'est pas explicitement envoyé).
      fichiersMax: (json['fichiers_max'] as num?)?.toInt() ?? 1,
      choix: choix,
    );
  }
}

class ChoixQuestionModel extends ChoixQuestion {
  const ChoixQuestionModel({required super.id, required super.libelle, required super.valeur});

  factory ChoixQuestionModel.fromJson(Map<String, dynamic> json) {
    return ChoixQuestionModel(
      id: json['id'] as int,
      libelle: json['libelle'] as String? ?? '',
      valeur: json['valeur'] as String? ?? '',
    );
  }
}
