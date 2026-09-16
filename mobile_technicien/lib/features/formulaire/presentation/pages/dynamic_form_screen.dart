import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/form_file_answer.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';
import 'package:mobile_technicien/features/formulaire/presentation/bloc/dynamic_form_bloc.dart';
import 'package:mobile_technicien/features/formulaire/presentation/widgets/dynamic_form_field.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Écran "Remplir le formulaire" (Étape 3, amélioré Étape "Amélioration et
/// vérification du formulaire dynamique") — rendu dynamique du Formulaire
/// associé au TypeIntervention de la mission, jamais codé en dur pour un type
/// donné. À la soumission, POST /interventions/{id}/formulaire (via
/// FormulaireController::store) enregistre les réponses ET fait passer le
/// statut de l'intervention à "Formulaire rempli" côté serveur — cet écran ne
/// déclenche donc PAS lui-même de transition de statut, il retourne juste
/// `true` à l'appelant (MissionDetailPage) pour qu'il rafraîchisse le détail.
///
/// Regroupement en sections (Étape 2 du prompt d'amélioration) : NON
/// implémenté ici intentionnellement — `Question` (app/Models/Question.php)
/// n'a aucun champ de catégorie/section en base (fillable : formulaire_id,
/// question, type_reponse, obligatoire, ordre, placeholder, valeur_par_defaut,
/// condition_affichage, nombre_min, nombre_max, nombre_unite, fichiers_max).
/// Inventer un regroupement visuel côté Flutter sans donnée de section réelle
/// reviendrait à afficher des titres arbitraires non fournis par le backend.
/// À traiter comme une évolution de schéma séparée (ajout d'un champ
/// catégorie/section à Question) si validée côté projet.
class DynamicFormScreen extends StatelessWidget {
  final int interventionId;
  const DynamicFormScreen({super.key, required this.interventionId});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<DynamicFormBloc>(param1: interventionId)..add(const DynamicFormRequested()),
      child: const _DynamicFormView(),
    );
  }
}

class _DynamicFormView extends StatefulWidget {
  const _DynamicFormView();

  @override
  State<_DynamicFormView> createState() => _DynamicFormViewState();
}

class _DynamicFormViewState extends State<_DynamicFormView> {
  /// Une GlobalKey stable par question, créée une seule fois (indépendamment
  /// des reconstructions du BlocBuilder) pour pouvoir faire défiler l'écran
  /// jusqu'à un champ précis après une tentative de soumission invalide.
  final Map<int, GlobalKey> _fieldKeys = {};

  GlobalKey _keyFor(int questionId) => _fieldKeys.putIfAbsent(questionId, () => GlobalKey());

  Future<void> _scrollToFirstMissing(List<int> missingQuestionIds) async {
    if (missingQuestionIds.isEmpty) return;
    final key = _fieldKeys[missingQuestionIds.first];
    final targetContext = key?.currentContext;
    if (targetContext == null) return;
    await Scrollable.ensureVisible(
      targetContext,
      duration: const Duration(milliseconds: 300),
      curve: Curves.easeInOut,
      alignment: 0.1,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Formulaire d\'intervention')),
      body: BlocConsumer<DynamicFormBloc, DynamicFormState>(
        listenWhen: (previous, current) {
          if (current is! DynamicFormLoaded) return false;
          final previousMissing = previous is DynamicFormLoaded ? previous.missingQuestionIds : const <int>[];
          final newErrorMessage = current.errorMessage != null &&
              current.errorMessage != (previous is DynamicFormLoaded ? previous.errorMessage : null);
          final newMissing = current.missingQuestionIds.isNotEmpty && current.missingQuestionIds != previousMissing;
          return newErrorMessage || newMissing;
        },
        listener: (context, state) {
          if (state is! DynamicFormLoaded) return;
          if (state.errorMessage != null) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.errorMessage!), backgroundColor: AppColors.danger),
            );
          }
          if (state.missingQuestionIds.isNotEmpty) {
            _scrollToFirstMissing(state.missingQuestionIds);
          }
        },
        builder: (context, state) {
          return switch (state) {
            DynamicFormLoading() => const Center(child: CircularProgressIndicator()),
            DynamicFormLoadError(:final message) => _ErrorView(message: message),
            DynamicFormLoaded() when state.submitSuccess => _SuccessView(onDone: () => Navigator.of(context).pop(true)),
            DynamicFormLoaded() => _FormBody(state: state, keyFor: _keyFor),
          };
        },
      ),
    );
  }
}

class _FormBody extends StatelessWidget {
  final DynamicFormLoaded state;
  final GlobalKey Function(int questionId) keyFor;
  const _FormBody({required this.state, required this.keyFor});

  @override
  Widget build(BuildContext context) {
    // Materiaux est géré par les endpoints matériaux dédiés (onglet séparé,
    // déjà utilisé ailleurs dans l'app) — RemplissageFormulaireService le
    // skip aussi côté backend, donc pas de champ à générer ici pour ce type.
    final questions = state.formulaire.questions.where((q) => q.type != 'Materiaux').toList();

    return Column(
      children: [
        _ProgressHeader(total: state.totalObligatoire, repondus: state.repondusObligatoire),
        Expanded(
          child: ListView(
            padding: const EdgeInsets.all(20),
            children: [
              Text(state.formulaire.nom, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              if (state.formulaire.description?.isNotEmpty == true) ...[
                const SizedBox(height: 4),
                Text(state.formulaire.description!, style: const TextStyle(color: AppColors.neutral, fontSize: 13)),
              ],
              const SizedBox(height: 16),
              for (final question in questions)
                DynamicFormField(
                  key: keyFor(question.id),
                  question: question,
                  value: state.answers[question.id],
                  invalid: state.missingQuestionIds.contains(question.id),
                ),
            ],
          ),
        ),
        SafeArea(
          top: false,
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 8, 20, 16),
            child: SizedBox(
              width: double.infinity,
              child: FilledButton(
                style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
                onPressed: state.submitting ? null : () => _openRecap(context),
                child: state.submitting
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Text('Vérifier et envoyer'),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

/// Étape 1 : progression calculée sur les Questions OBLIGATOIRES répondues —
/// pas sur le total de questions, pour que la barre atteigne 100% dès que le
/// formulaire est réellement soumissible, cohérent avec la validation.
class _ProgressHeader extends StatelessWidget {
  final int total;
  final int repondus;
  const _ProgressHeader({required this.total, required this.repondus});

  @override
  Widget build(BuildContext context) {
    final ratio = total == 0 ? 1.0 : repondus / total;

    return Container(
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 12),
      color: Colors.white,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Progression', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.neutral)),
              Text(
                '$repondus/$total questions obligatoires',
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.neutral),
              ),
            ],
          ),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(6),
            child: LinearProgressIndicator(
              value: ratio,
              minHeight: 8,
              backgroundColor: const Color(0xFFE6E9F4),
              valueColor: AlwaysStoppedAnimation(ratio >= 1.0 ? AppColors.success : AppColors.brand),
            ),
          ),
        ],
      ),
    );
  }
}

class _ErrorView extends StatelessWidget {
  final String message;
  const _ErrorView({required this.message});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.error_outline, color: AppColors.danger, size: 40),
            const SizedBox(height: 12),
            Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.neutral)),
          ],
        ),
      ),
    );
  }
}

/// Étape 5.2 (design de référence) : récapitulatif avant soumission finale —
/// reprend le style visuel de la modale de complétion du mockup (carte
/// blanche, coche verte par item) SANS remplacer le moteur de formulaire
/// dynamique par une checklist générique : chaque ligne est une vraie
/// question/réponse du Formulaire réellement rempli, pas "Task Completion/
/// Cleanliness/Customer Approval". La soumission réelle (POST
/// /interventions/{id}/formulaire) ne part que depuis ce récapitulatif.
void _openRecap(BuildContext context) {
  final bloc = context.read<DynamicFormBloc>();
  Navigator.of(context).push(MaterialPageRoute(
    builder: (_) => BlocProvider.value(value: bloc, child: const _FormRecapScreen()),
  ));
}

class _FormRecapScreen extends StatelessWidget {
  const _FormRecapScreen();

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<DynamicFormBloc, DynamicFormState>(
      listenWhen: (previous, current) => current is DynamicFormLoaded && current.submitSuccess,
      // La soumission a réussi : on referme le récapitulatif pour révéler
      // l'écran de succès de DynamicFormScreen en dessous, plutôt que d'en
      // dupliquer un second ici.
      listener: (context, state) => Navigator.of(context).pop(),
      builder: (context, state) {
        if (state is! DynamicFormLoaded) return const SizedBox.shrink();
        final questions = state.formulaire.questions.where((q) => q.type != 'Materiaux').toList();

        return Scaffold(
          backgroundColor: AppColors.background,
          appBar: AppBar(title: const Text('Récapitulatif')),
          body: Column(
            children: [
              Expanded(
                child: ListView(
                  padding: const EdgeInsets.all(20),
                  children: [
                    Text(state.formulaire.nom, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    const Text(
                      'Vérifiez vos réponses avant l\'envoi définitif du rapport.',
                      style: TextStyle(color: AppColors.neutral, fontSize: 12),
                    ),
                    const SizedBox(height: 16),
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: const Color(0xFFE6E9F4)),
                      ),
                      child: Column(
                        children: [
                          for (var i = 0; i < questions.length; i++) ...[
                            if (i > 0) const Divider(height: 1),
                            _RecapRow(question: questions[i], value: state.answers[questions[i].id]),
                          ],
                        ],
                      ),
                    ),
                    if (state.errorMessage != null) ...[
                      const SizedBox(height: 12),
                      Text(state.errorMessage!, style: const TextStyle(color: AppColors.danger, fontSize: 12)),
                    ],
                  ],
                ),
              ),
              SafeArea(
                top: false,
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 8, 20, 16),
                  child: SizedBox(
                    width: double.infinity,
                    child: FilledButton(
                      style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
                      onPressed: state.submitting ? null : () => context.read<DynamicFormBloc>().add(const DynamicFormSubmitted()),
                      child: state.submitting
                          ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                          : const Text('Confirmer l\'envoi'),
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

class _RecapRow extends StatelessWidget {
  final Question question;
  final dynamic value;
  const _RecapRow({required this.question, required this.value});

  String _preview() {
    if (value == null) return 'Non renseigné';
    if (value is FormFileAnswer) return '1 fichier joint';
    if (value is List) {
      if (value.isEmpty) return 'Non renseigné';
      if (value.first is FormFileAnswer) return '${value.length} fichier(s) joint(s)';
      return value.join(', ');
    }
    final text = value.toString().trim();
    return text.isEmpty ? 'Non renseigné' : text;
  }

  @override
  Widget build(BuildContext context) {
    final answered = isQuestionAnswered(value);
    return Padding(
      padding: const EdgeInsets.all(14),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(
            answered ? Icons.check_circle : Icons.radio_button_unchecked,
            color: answered ? AppColors.success : AppColors.neutral,
            size: 20,
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(question.question, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                const SizedBox(height: 2),
                Text(
                  _preview(),
                  style: TextStyle(fontSize: 12, color: answered ? AppColors.neutral : AppColors.danger),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _SuccessView extends StatelessWidget {
  final VoidCallback onDone;
  const _SuccessView({required this.onDone});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.check_circle_outline, color: AppColors.success, size: 48),
            const SizedBox(height: 12),
            const Text('Formulaire enregistré avec succès.', textAlign: TextAlign.center, style: TextStyle(fontSize: 15)),
            const SizedBox(height: 20),
            FilledButton(
              style: FilledButton.styleFrom(backgroundColor: AppColors.brand),
              onPressed: onDone,
              child: const Text('Retour à la mission'),
            ),
          ],
        ),
      ),
    );
  }
}
