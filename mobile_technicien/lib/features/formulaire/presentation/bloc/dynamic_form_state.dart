part of 'dynamic_form_bloc.dart';

sealed class DynamicFormState extends Equatable {
  const DynamicFormState();

  @override
  List<Object?> get props => [];
}

class DynamicFormLoading extends DynamicFormState {
  const DynamicFormLoading();
}

class DynamicFormLoadError extends DynamicFormState {
  final String message;
  const DynamicFormLoadError(this.message);

  @override
  List<Object?> get props => [message];
}

class DynamicFormLoaded extends DynamicFormState {
  final Formulaire formulaire;

  /// Par question_id : `String`/`List<String>` (Checkbox) pour les réponses
  /// simples, `List<String>` de chemins de fichiers locaux pour
  /// Photo/Signature/Document.
  final Map<int, dynamic> answers;
  final bool submitting;
  final String? errorMessage;
  final bool submitSuccess;

  /// Questions obligatoires identifiées comme manquantes lors de la dernière
  /// tentative de soumission (Étape 3) — pilote le surlignage rouge par champ
  /// et le défilement vers le premier champ concerné. Vidé au fur et à mesure
  /// que chaque champ est complété, pas seulement en relançant une soumission.
  final List<int> missingQuestionIds;

  const DynamicFormLoaded({
    required this.formulaire,
    required this.answers,
    this.submitting = false,
    this.errorMessage,
    this.submitSuccess = false,
    this.missingQuestionIds = const [],
  });

  /// Questions obligatoires du Formulaire (hors Materiaux, géré séparément) —
  /// base du dénominateur de l'indicateur de progression (Étape 1).
  List<Question> get questionsObligatoires =>
      formulaire.questions.where((q) => q.obligatoire && q.type != 'Materiaux').toList();

  int get totalObligatoire => questionsObligatoires.length;

  int get repondusObligatoire => questionsObligatoires.where((q) => isQuestionAnswered(answers[q.id])).length;

  DynamicFormLoaded copyWith({
    Map<int, dynamic>? answers,
    bool? submitting,
    String? errorMessage,
    bool? submitSuccess,
    List<int>? missingQuestionIds,
  }) {
    return DynamicFormLoaded(
      formulaire: formulaire,
      answers: answers ?? this.answers,
      submitting: submitting ?? this.submitting,
      errorMessage: errorMessage,
      submitSuccess: submitSuccess ?? this.submitSuccess,
      missingQuestionIds: missingQuestionIds ?? this.missingQuestionIds,
    );
  }

  @override
  List<Object?> get props => [formulaire, answers, submitting, errorMessage, submitSuccess, missingQuestionIds];
}
