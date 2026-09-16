import 'dart:typed_data';

import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/formulaire/data/local/formulaire_draft_service.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/form_file_answer.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';
import 'package:mobile_technicien/features/formulaire/domain/usecases/get_formulaire_usecase.dart';
import 'package:mobile_technicien/features/formulaire/domain/usecases/submit_formulaire_usecase.dart';

part 'dynamic_form_event.dart';
part 'dynamic_form_state.dart';

/// Une question obligatoire est considérée répondue si sa valeur est une
/// chaîne non vide (Texte/Nombre/Date/.../GPS/QRCode) ou une liste non vide
/// (Checkbox, ou fichiers Photo/Signature/Document) — partagé entre la
/// validation de soumission et le calcul de progression (Étape 1/3) pour ne
/// pas avoir deux définitions de "répondu" qui divergent.
bool isQuestionAnswered(dynamic value) {
  if (value == null) return false;
  if (value is String) return value.trim().isNotEmpty;
  if (value is List) return value.isNotEmpty;
  return true;
}

/// Moteur de formulaire dynamique (Étape 3) : charge le Formulaire associé au
/// TypeIntervention, restaure un brouillon local s'il existe, persiste chaque
/// saisie au fur et à mesure (résilience réseau/fermeture accidentelle), puis
/// soumet l'ensemble à POST /interventions/{id}/formulaire.
class DynamicFormBloc extends Bloc<DynamicFormEvent, DynamicFormState> {
  final int interventionId;
  final GetFormulaireUsecase getFormulaireUsecase;
  final SubmitFormulaireUsecase submitFormulaireUsecase;
  final FormulaireDraftService draftService;

  DynamicFormBloc({
    required this.interventionId,
    required this.getFormulaireUsecase,
    required this.submitFormulaireUsecase,
    required this.draftService,
  }) : super(const DynamicFormLoading()) {
    on<DynamicFormRequested>(_onRequested);
    on<DynamicFormAnswerChanged>(_onAnswerChanged);
    on<DynamicFormFileAdded>(_onFileAdded);
    on<DynamicFormSignatureSaved>(_onSignatureSaved);
    on<DynamicFormFileRemoved>(_onFileRemoved);
    on<DynamicFormSubmitted>(_onSubmitted);
  }

  Future<void> _onRequested(DynamicFormRequested event, Emitter<DynamicFormState> emit) async {
    emit(const DynamicFormLoading());
    final result = await getFormulaireUsecase(interventionId);
    await result.when(
      success: (formulaire) async {
        final draft = await draftService.loadAnswers(interventionId) ?? {};
        // Pré-remplit avec valeur_par_defaut les questions sans réponse de
        // brouillon, plutôt que de laisser un champ requis vide inutilement.
        final answers = <int, dynamic>{...draft};
        for (final question in formulaire.questions) {
          if (!answers.containsKey(question.id) && question.valeurParDefaut != null && question.valeurParDefaut!.isNotEmpty) {
            answers[question.id] = question.valeurParDefaut;
          }
        }
        emit(DynamicFormLoaded(formulaire: formulaire, answers: answers));
      },
      failure: (f) async => emit(DynamicFormLoadError(f.message)),
    );
  }

  Future<void> _onAnswerChanged(DynamicFormAnswerChanged event, Emitter<DynamicFormState> emit) async {
    final current = state;
    if (current is! DynamicFormLoaded) return;

    final answers = Map<int, dynamic>.from(current.answers);
    if (event.value == null || (event.value is String && (event.value as String).isEmpty)) {
      answers.remove(event.questionId);
    } else {
      answers[event.questionId] = event.value;
    }

    emit(current.copyWith(answers: answers, errorMessage: null, missingQuestionIds: _clearIfAnswered(current, event.questionId, answers)));
    await draftService.saveAnswers(interventionId, _serializableAnswers(answers));
  }

  Future<void> _onFileAdded(DynamicFormFileAdded event, Emitter<DynamicFormState> emit) async {
    final current = state;
    if (current is! DynamicFormLoaded) return;

    final question = current.formulaire.questions.firstWhere((q) => q.id == event.questionId);

    final answers = Map<int, dynamic>.from(current.answers);
    final existing = List<String>.from(answers[event.questionId] as List? ?? const []);
    existing.add(event.file.toDraftString());
    // Respecte fichiers_max plutôt que d'accumuler indéfiniment : retire les
    // plus anciens fichiers en excès (le plus récent gagne).
    while (existing.length > question.fichiersMax) {
      existing.removeAt(0);
    }
    answers[event.questionId] = existing;

    emit(current.copyWith(answers: answers, errorMessage: null, missingQuestionIds: _clearIfAnswered(current, event.questionId, answers)));
    await draftService.saveAnswers(interventionId, _serializableAnswers(answers));
  }

  Future<void> _onSignatureSaved(DynamicFormSignatureSaved event, Emitter<DynamicFormState> emit) async {
    final current = state;
    if (current is! DynamicFormLoaded) return;

    // Une seule signature par question — remplace, ne s'accumule pas.
    final answers = Map<int, dynamic>.from(current.answers);
    answers[event.questionId] = [FormFileAnswer(nom: 'signature.png', bytes: event.bytes).toDraftString()];

    emit(current.copyWith(answers: answers, errorMessage: null, missingQuestionIds: _clearIfAnswered(current, event.questionId, answers)));
    await draftService.saveAnswers(interventionId, _serializableAnswers(answers));
  }

  Future<void> _onFileRemoved(DynamicFormFileRemoved event, Emitter<DynamicFormState> emit) async {
    final current = state;
    if (current is! DynamicFormLoaded) return;

    final answers = Map<int, dynamic>.from(current.answers);
    final existing = List<String>.from(answers[event.questionId] as List? ?? const []);
    existing.remove(event.draftEntry);
    if (existing.isEmpty) {
      answers.remove(event.questionId);
    } else {
      answers[event.questionId] = existing;
    }

    emit(current.copyWith(answers: answers, errorMessage: null, missingQuestionIds: _clearIfAnswered(current, event.questionId, answers)));
    await draftService.saveAnswers(interventionId, _serializableAnswers(answers));
  }

  /// Retire [questionId] de `missingQuestionIds` dès qu'il redevient répondu,
  /// pour que le surlignage rouge disparaisse au fur et à mesure de la
  /// saisie plutôt qu'uniquement à la prochaine tentative de soumission.
  List<int> _clearIfAnswered(DynamicFormLoaded current, int questionId, Map<int, dynamic> answers) {
    if (!current.missingQuestionIds.contains(questionId)) return current.missingQuestionIds;
    if (!isQuestionAnswered(answers[questionId])) return current.missingQuestionIds;
    return current.missingQuestionIds.where((id) => id != questionId).toList();
  }

  Future<void> _onSubmitted(DynamicFormSubmitted event, Emitter<DynamicFormState> emit) async {
    final current = state;
    if (current is! DynamicFormLoaded) return;

    final missing = current.formulaire.questions.where((q) => q.obligatoire && !isQuestionAnswered(current.answers[q.id])).toList();

    if (missing.isNotEmpty) {
      emit(current.copyWith(
        errorMessage: 'Champ(s) obligatoire(s) manquant(s) — vérifiez les champs surlignés en rouge.',
        missingQuestionIds: missing.map((q) => q.id).toList(),
      ));
      return;
    }

    emit(current.copyWith(submitting: true, errorMessage: null, missingQuestionIds: const []));

    final reponses = <int, dynamic>{};
    final fichiers = <int, List<String>>{};
    for (final question in current.formulaire.questions) {
      final value = current.answers[question.id];
      if (value == null) continue;
      if (question.estTypeFichier) {
        fichiers[question.id] = List<String>.from(value as List);
      } else {
        reponses[question.id] = value;
      }
    }

    final result = await submitFormulaireUsecase(interventionId, reponses: reponses, fichiers: fichiers);
    await result.when(
      success: (_) async {
        await draftService.clear(interventionId);
        emit(current.copyWith(submitting: false, submitSuccess: true));
      },
      failure: (f) async => emit(current.copyWith(submitting: false, errorMessage: f.message)),
    );
  }

  /// `List<String>` (fichiers/Checkbox) et `String` sont déjà JSON-sérialisables
  /// tels quels ; ce filtre existe pour ne jamais tenter de sérialiser un type
  /// inattendu si l'un des `on<...>Changed` évolue plus tard.
  Map<int, dynamic> _serializableAnswers(Map<int, dynamic> answers) {
    return answers.map((key, value) => MapEntry(key, value is List ? List<String>.from(value) : value));
  }
}
