import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/utils/relative_time.dart';
import 'package:mobile_technicien/core/widgets/mini_location_map.dart';
import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';
import 'package:mobile_technicien/features/chantier/domain/usecases/get_chantier_detail_usecase.dart';
import 'package:mobile_technicien/features/chantier/presentation/pages/chantier_detail_screen.dart';
import 'package:mobile_technicien/features/formulaire/presentation/pages/dynamic_form_screen.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/priority_badge.dart';
import 'package:mobile_technicien/features/missions/presentation/bloc/mission_detail_bloc.dart';
import 'package:mobile_technicien/features/missions/presentation/widgets/status_badge.dart';
import 'package:mobile_technicien/features/rapport/presentation/pages/rapport_detail_screen.dart';
import 'package:mobile_technicien/injection_container.dart';
import 'package:url_launcher/url_launcher.dart';

/// Écran de détail — Étape 2 (actions contextuelles accepter/refuser/démarrer)
/// + Étape 3-4 (formulaire dynamique et soumission du rapport). Le passage
/// "Formulaire rempli" → Terminée se fait en 2 appels distincts côté backend :
/// POST .../formulaire (DynamicFormScreen, fait passer le statut à "Formulaire
/// rempli") puis POST .../finish ("Soumettre le rapport" ci-dessous, fait
/// passer à Terminée) — cf. FormulaireController::store / InterventionController::finish.
class MissionDetailPage extends StatelessWidget {
  final Mission mission;
  const MissionDetailPage({super.key, required this.mission});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      // La Mission de la liste ne porte ni `chantier_id`, ni les coordonnées
      // d'emplacement, ni l'historique — rafraîchi immédiatement via le détail
      // complet (GET /api/interventions/{id}) pour que ces sections
      // s'affichent sans attendre une action ou un tirer-pour-rafraîchir.
      create: (_) => sl<MissionDetailBloc>(param1: mission)..add(const MissionDetailRefreshed()),
      child: const _MissionDetailView(),
    );
  }
}

class _MissionDetailView extends StatelessWidget {
  const _MissionDetailView();

  /// Reproduit la modale de confirmation du design de référence (icône dans
  /// un cercle, titre, description, bouton principal plein largeur, annuler
  /// en dessous) — habille les confirmations déjà construites (Accepter,
  /// Démarrer, Soumettre) sans changer leur logique. Pas d'illustration
  /// dessinée : aucune bibliothèque de ce type n'existe dans le projet, une
  /// icône stylisée suffit (Étape 4.2 du prompt design de référence).
  Future<bool> _showConfirmationDialog(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String description,
    required String confirmLabel,
    Color confirmColor = AppColors.brand,
  }) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (dialogContext) => Dialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 32, 24, 20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 72,
                height: 72,
                decoration: const BoxDecoration(color: AppColors.brandLight, shape: BoxShape.circle),
                child: Icon(icon, color: AppColors.brand, size: 34),
              ),
              const SizedBox(height: 18),
              Text(title, textAlign: TextAlign.center, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              Text(
                description,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.neutral, fontSize: 13, height: 1.4),
              ),
              const SizedBox(height: 22),
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  style: FilledButton.styleFrom(
                    backgroundColor: confirmColor,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () => Navigator.of(dialogContext).pop(true),
                  child: Text(confirmLabel, style: const TextStyle(fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(height: 4),
              TextButton(
                onPressed: () => Navigator.of(dialogContext).pop(false),
                child: const Text('Annuler'),
              ),
            ],
          ),
        ),
      ),
    );
    return confirmed ?? false;
  }

  Future<void> _confirmAccept(BuildContext context) async {
    final confirmed = await _showConfirmationDialog(
      context,
      icon: Icons.check_circle_outline,
      title: 'Accepter cette mission ?',
      description: 'Confirmez-vous l\'acceptation de cette intervention ?',
      confirmLabel: 'OUI, ACCEPTER',
    );
    if (confirmed && context.mounted) {
      context.read<MissionDetailBloc>().add(const MissionAcceptRequested());
    }
  }

  Future<void> _confirmStart(BuildContext context) async {
    final confirmed = await _showConfirmationDialog(
      context,
      icon: Icons.play_circle_outline,
      title: 'Démarrer l\'intervention ?',
      description: 'La position actuelle sera transmise si le GPS est activé. Démarrer maintenant ?',
      confirmLabel: 'DÉMARRER',
    );
    if (confirmed && context.mounted) {
      context.read<MissionDetailBloc>().add(const MissionStartRequested());
    }
  }

  Future<void> _promptRefuse(BuildContext context) async {
    final controller = TextEditingController();
    final formKey = GlobalKey<FormState>();

    final motif = await showDialog<String>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('Refuser la mission'),
        content: Form(
          key: formKey,
          child: TextFormField(
            controller: controller,
            autofocus: true,
            maxLines: 3,
            decoration: const InputDecoration(
              labelText: 'Motif du refus',
              hintText: 'Expliquez pourquoi vous refusez cette mission…',
              border: OutlineInputBorder(),
            ),
            // Miroir de la règle serveur (InterventionController::refuse,
            // `motif` requis, min 5 caractères) — validée ici seulement pour
            // éviter un aller-retour réseau inutile, la validation finale
            // reste côté serveur.
            validator: (value) {
              if (value == null || value.trim().length < 5) {
                return 'Le motif doit contenir au moins 5 caractères.';
              }
              return null;
            },
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.of(dialogContext).pop(), child: const Text('Annuler')),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: AppColors.danger),
            onPressed: () {
              if (formKey.currentState?.validate() ?? false) {
                Navigator.of(dialogContext).pop(controller.text.trim());
              }
            },
            child: const Text('Envoyer le refus'),
          ),
        ],
      ),
    );

    if (motif != null && context.mounted) {
      context.read<MissionDetailBloc>().add(MissionRefuseRequested(motif));
    }
  }

  Future<void> _openFormulaire(BuildContext context, int interventionId) async {
    final submitted = await Navigator.of(context).push<bool>(
      MaterialPageRoute(builder: (_) => DynamicFormScreen(interventionId: interventionId)),
    );
    // Le statut ("Formulaire rempli") a déjà été mis à jour côté serveur par
    // FormulaireController::store au moment de la soumission — on ne fait
    // que rafraîchir l'affichage local pour le refléter.
    if (submitted == true && context.mounted) {
      context.read<MissionDetailBloc>().add(const MissionDetailRefreshed());
    }
  }

  Future<void> _confirmFinish(BuildContext context) async {
    final confirmed = await _showConfirmationDialog(
      context,
      icon: Icons.send_outlined,
      title: 'Soumettre le rapport ?',
      description: 'Cette action clôture définitivement l\'intervention (statut Terminée). Confirmez-vous ?',
      confirmLabel: 'SOUMETTRE',
    );
    if (confirmed && context.mounted) {
      context.read<MissionDetailBloc>().add(const MissionFinishRequested());
    }
  }

  void _openChantier(BuildContext context, int chantierId) {
    Navigator.of(context).push(MaterialPageRoute(builder: (_) => ChantierDetailScreen(chantierId: chantierId)));
  }

  void _openRapport(BuildContext context, int interventionId) {
    Navigator.of(context).push(MaterialPageRoute(builder: (_) => RapportDetailScreen(interventionId: interventionId)));
  }

  /// Étape 7.2 : contact rapide — récupère la fiche chantier à la volée (même
  /// endpoint que l'Étape 4, pas de champ téléphone dupliqué sur cet écran)
  /// pour lancer l'appel dès que le numéro est connu.
  Future<void> _callChantier(BuildContext context, int chantierId) async {
    final messenger = ScaffoldMessenger.of(context);
    final result = await sl<GetChantierDetailUsecase>()(chantierId);
    result.when(
      success: (chantier) async {
        if (!chantier.aContact) {
          messenger.showSnackBar(const SnackBar(content: Text('Aucun numéro de contact enregistré pour ce chantier.')));
          return;
        }
        final uri = Uri(scheme: 'tel', path: chantier.telephoneResponsable!);
        if (await canLaunchUrl(uri)) {
          await launchUrl(uri);
        }
      },
      failure: (f) => messenger.showSnackBar(SnackBar(content: Text(f.message), backgroundColor: AppColors.danger)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<MissionDetailBloc, MissionDetailState>(
      listenWhen: (previous, current) => current.errorMessage != null && current.errorMessage != previous.errorMessage,
      listener: (context, state) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(state.errorMessage!), backgroundColor: AppColors.danger),
        );
      },
      builder: (context, state) {
        final mission = state.mission;
        final date = mission.datePrevueDebut;
        final dateLabel = date != null ? DateFormat('dd/MM/yyyy · HH:mm').format(date.toLocal()) : '—';

        return Scaffold(
          backgroundColor: AppColors.background,
          appBar: AppBar(title: Text(mission.codeIntervention)),
          body: RefreshIndicator(
            onRefresh: () async {
              context.read<MissionDetailBloc>().add(const MissionDetailRefreshed());
              await context.read<MissionDetailBloc>().stream.firstWhere((s) => !s.processing);
            },
            child: ListView(
              padding: const EdgeInsets.all(20),
              children: [
                Row(
                  children: [
                    StatusBadge(statut: mission.statut),
                    const SizedBox(width: 8),
                    PriorityBadge(priorite: mission.priorite),
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        // Étape 4 : nom du chantier cliquable → fiche complète
                        // (même donnée que le portail Client), seulement une
                        // fois `chantierId` connu (arrive avec le détail
                        // complet, pas avec la Mission seedée depuis la liste).
                        onTap: mission.chantierId != null ? () => _openChantier(context, mission.chantierId!) : null,
                        child: Row(
                          children: [
                            Flexible(
                              child: Text(
                                mission.chantierNom,
                                style: TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: mission.chantierId != null ? AppColors.brand : null,
                                  decoration: mission.chantierId != null ? TextDecoration.underline : null,
                                ),
                              ),
                            ),
                            if (mission.chantierId != null) ...[
                              const SizedBox(width: 4),
                              const Icon(Icons.chevron_right, size: 20, color: AppColors.brand),
                            ],
                          ],
                        ),
                      ),
                    ),
                    if (mission.chantierId != null)
                      IconButton(
                        // Étape 5 : précise explicitement le destinataire de
                        // l'appel (le responsable du chantier, pas une ligne
                        // générale du chantier lui-même) avant que le
                        // technicien ne clique.
                        tooltip: 'Appeler le responsable du chantier',
                        onPressed: () => _callChantier(context, mission.chantierId!),
                        icon: const Icon(Icons.call_outlined, color: AppColors.brand),
                      ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(mission.typeInterventionNom, style: const TextStyle(color: AppColors.neutral)),
                const SizedBox(height: 20),
                const Text('Informations', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFFE6E9F4)),
                  ),
                  child: Column(
                    children: [
                      const SizedBox(height: 10),
                      _InfoRow(
                        icon: Icons.event_outlined,
                        label: 'Prévue le',
                        value: relativeTimeLabel(date) != null ? '$dateLabel (${relativeTimeLabel(date)})' : dateLabel,
                      ),
                      if (mission.emplacementNom != null)
                        _InfoRow(icon: Icons.place_outlined, label: 'Emplacement', value: mission.emplacementNom!),
                      if (mission.description.isNotEmpty)
                        _InfoRow(icon: Icons.description_outlined, label: 'Description', value: mission.description),
                    ],
                  ),
                ),
                if (mission.chantierId != null) ...[
                  const SizedBox(height: 20),
                  _ChantierContactCard(chantierId: mission.chantierId!),
                ],
                const SizedBox(height: 24),
                _ActionsSection(
                  mission: mission,
                  processing: state.processing,
                  onAccept: () => _confirmAccept(context),
                  onRefuse: () => _promptRefuse(context),
                  onStart: () => _confirmStart(context),
                  onFillForm: () => _openFormulaire(context, mission.id),
                  onFinish: () => _confirmFinish(context),
                  onViewRapport: () => _openRapport(context, mission.id),
                ),
                if (mission.historiques.isNotEmpty) ...[
                  const SizedBox(height: 28),
                  _HistoriqueSection(historiques: mission.historiques),
                ],
              ],
            ),
          ),
        );
      },
    );
  }
}

class _ActionsSection extends StatelessWidget {
  final Mission mission;
  final bool processing;
  final VoidCallback onAccept;
  final VoidCallback onRefuse;
  final VoidCallback onStart;
  final VoidCallback onFillForm;
  final VoidCallback onFinish;
  final VoidCallback onViewRapport;

  const _ActionsSection({
    required this.mission,
    required this.processing,
    required this.onAccept,
    required this.onRefuse,
    required this.onStart,
    required this.onFillForm,
    required this.onFinish,
    required this.onViewRapport,
  });

  @override
  Widget build(BuildContext context) {
    if (mission.peutEtreAccepteeOuRefusee) {
      return Column(
        children: [
          SizedBox(
            width: double.infinity,
            child: FilledButton.icon(
              style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
              onPressed: processing ? null : onAccept,
              icon: processing
                  ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Icon(Icons.check_circle_outline),
              label: const Text('Accepter la mission'),
            ),
          ),
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton.icon(
              style: OutlinedButton.styleFrom(foregroundColor: AppColors.danger, side: const BorderSide(color: AppColors.danger)),
              onPressed: processing ? null : onRefuse,
              icon: const Icon(Icons.cancel_outlined),
              label: const Text('Refuser la mission'),
            ),
          ),
        ],
      );
    }

    if (mission.peutEtreDemarreeStrict) {
      return SizedBox(
        width: double.infinity,
        child: FilledButton.icon(
          style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
          onPressed: processing ? null : onStart,
          icon: processing
              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : const Icon(Icons.play_circle_outline),
          label: const Text('Démarrer l\'intervention'),
        ),
      );
    }

    if (mission.statut == 'En cours') {
      return SizedBox(
        width: double.infinity,
        child: FilledButton.icon(
          style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
          onPressed: onFillForm,
          icon: const Icon(Icons.assignment_outlined),
          label: const Text('Remplir le formulaire'),
        ),
      );
    }

    if (mission.statut == 'Formulaire rempli') {
      return SizedBox(
        width: double.infinity,
        child: FilledButton.icon(
          style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
          onPressed: processing ? null : onFinish,
          icon: processing
              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : const Icon(Icons.send_outlined),
          label: const Text('Soumettre le rapport'),
        ),
      );
    }

    // Étape 1 : le rapport (structure déjà normalisée Super Admin/Admin/
    // Client) est consultable dès que l'intervention est réellement Terminée
    // — remplace l'ancien message générique qui masquait cet accès.
    //
    // Garde-fou (prompt "Cohérence seed/rapport", Étape 2) : certaines
    // interventions Terminée peuvent, par bug ou par incohérence de données,
    // n'avoir aucun Rapport réel en base — proposer "Voir le rapport" mènerait
    // systématiquement à une impasse (404 "Aucun rapport enregistré"). La
    // relation `rapport` est déjà chargée par GET /api/interventions/{id} ;
    // `mission.rapportExiste` reste `null` tant que ce détail n'a pas encore
    // été rafraîchi (Mission seedée depuis la liste) — dans ce cas on attend
    // plutôt que d'afficher un état probablement faux.
    if (mission.statut == 'Terminee') {
      if (mission.rapportExiste == null) {
        return const SizedBox(
          width: double.infinity,
          child: Center(child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(strokeWidth: 2))),
        );
      }

      if (mission.rapportExiste == false) {
        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: AppColors.brandLight, borderRadius: BorderRadius.circular(14)),
          child: const Text(
            'Cette intervention est terminée mais aucun rapport n\'a été enregistré — contactez votre responsable.',
            style: TextStyle(color: AppColors.brandDark, fontSize: 13, height: 1.4),
          ),
        );
      }

      return SizedBox(
        width: double.infinity,
        child: FilledButton.icon(
          style: FilledButton.styleFrom(backgroundColor: AppColors.brand, padding: const EdgeInsets.symmetric(vertical: 14)),
          onPressed: onViewRapport,
          icon: const Icon(Icons.description_outlined),
          label: const Text('Voir le rapport'),
        ),
      );
    }

    // Étape 1.3 : cas authentiquement "en attente de traitement
    // administratif" (Intervention::STATUT_EN_ATTENTE_REAFFECTATION, après un
    // refus technicien) — message distinct, pas le même texte générique que
    // pour un statut clôturé.
    if (mission.statut == 'En attente reafectation') {
      return Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: AppColors.brandLight, borderRadius: BorderRadius.circular(14)),
        child: const Text(
          'Votre refus a été transmis à l\'administration, qui va réaffecter cette mission. Aucune action de votre part n\'est nécessaire pour le moment.',
          style: TextStyle(color: AppColors.brandDark, fontSize: 13, height: 1.4),
        ),
      );
    }

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: AppColors.brandLight, borderRadius: BorderRadius.circular(14)),
      child: const Text(
        'Aucune action disponible pour cette intervention dans son état actuel.',
        style: TextStyle(color: AppColors.brandDark, fontSize: 13, height: 1.4),
      ),
    );
  }
}

/// Étape 3 : horodatages réels des transitions déjà journalisées côté serveur
/// (InterventionHistorique via InterventionService::enregistrerHistorique) —
/// affichées telles quelles, aucune transition n'est déduite/inventée côté
/// app si elle n'a pas été journalisée.
class _HistoriqueSection extends StatelessWidget {
  final List<HistoriqueEntry> historiques;
  const _HistoriqueSection({required this.historiques});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Historique', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
        const SizedBox(height: 10),
        ...historiques.map((entry) => Padding(
              padding: const EdgeInsets.only(bottom: 14),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Padding(
                    padding: EdgeInsets.only(top: 3),
                    child: Icon(Icons.fiber_manual_record, size: 10, color: AppColors.brand),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          // Même source de libellés que StatusBadge (Étape 2) :
                          // évite de réafficher les valeurs brutes non
                          // accentuées ("Terminee") comme ici auparavant.
                          entry.statutAvant != null
                              ? '${StatusBadge.labelFor(entry.statutAvant!)} → ${StatusBadge.labelFor(entry.statutApres)}'
                              : StatusBadge.labelFor(entry.statutApres),
                          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                        ),
                        if (entry.date != null)
                          Text(
                            DateFormat('dd/MM/yyyy · HH:mm').format(entry.date!.toLocal()) +
                                (entry.auteur != null ? ' · ${entry.auteur}' : ''),
                            style: const TextStyle(fontSize: 11, color: AppColors.neutral),
                          ),
                        if (entry.commentaire != null && entry.commentaire!.isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(top: 2),
                            child: Text(entry.commentaire!, style: const TextStyle(fontSize: 12)),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
            )),
      ],
    );
  }
}

/// Étape 3 (design de référence) : carte "Chantier" affichée directement sur
/// le détail d'intervention (avatar/nom/adresse + mini-carte), en réutilisant
/// GetChantierDetailUsecase et MiniLocationMap déjà construits pour la fiche
/// chantier complète (Étape 4/5 du prompt précédent) — pas de logique de
/// géolocalisation/contact dupliquée, seulement un affichage compact en plus
/// de l'accès à la fiche complète via le nom du chantier ci-dessus.
class _ChantierContactCard extends StatefulWidget {
  final int chantierId;
  const _ChantierContactCard({required this.chantierId});

  @override
  State<_ChantierContactCard> createState() => _ChantierContactCardState();
}

class _ChantierContactCardState extends State<_ChantierContactCard> {
  Chantier? _chantier;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final result = await sl<GetChantierDetailUsecase>()(widget.chantierId);
    if (!mounted) return;
    result.when(
      success: (c) => setState(() {
        _chantier = c;
        _loading = false;
      }),
      failure: (_) => setState(() => _loading = false),
    );
  }

  Future<void> _appeler(String telephone) async {
    final uri = Uri(scheme: 'tel', path: telephone);
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Padding(
        padding: EdgeInsets.symmetric(vertical: 16),
        child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
      );
    }
    final chantier = _chantier;
    if (chantier == null) return const SizedBox.shrink();

    final nomAffiche = chantier.responsable?.isNotEmpty == true ? chantier.responsable! : chantier.nom;
    final initiale = nomAffiche.isNotEmpty ? nomAffiche[0].toUpperCase() : '?';

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Chantier', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: const Color(0xFFE6E9F4)),
          ),
          child: Row(
            children: [
              CircleAvatar(
                radius: 20,
                backgroundColor: AppColors.brandLight,
                child: Text(initiale, style: const TextStyle(color: AppColors.brand, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(nomAffiche, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                    if (chantier.adresse != null)
                      Text(
                        chantier.adresse!,
                        style: const TextStyle(color: AppColors.neutral, fontSize: 12),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                  ],
                ),
              ),
              if (chantier.aContact)
                IconButton(
                  tooltip: 'Appeler',
                  onPressed: () => _appeler(chantier.telephoneResponsable!),
                  icon: const Icon(Icons.call, color: AppColors.brand),
                ),
            ],
          ),
        ),
        if (chantier.aPosition) ...[
          const SizedBox(height: 10),
          MiniLocationMap(latitude: chantier.latitude!, longitude: chantier.longitude!, height: 140),
        ],
      ],
    );
  }
}

class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;
  const _InfoRow({required this.icon, required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 18, color: AppColors.neutral),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: const TextStyle(fontSize: 11, color: AppColors.neutral)),
                Text(value, style: const TextStyle(fontSize: 14)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
