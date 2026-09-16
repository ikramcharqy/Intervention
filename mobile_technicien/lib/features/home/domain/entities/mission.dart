import 'package:equatable/equatable.dart';

/// Miroir du modèle Intervention (app/Models/Intervention.php) — uniquement
/// les champs consommés par l'écran d'accueil. Valeurs de `statut`/`priorite`
/// vérifiées en direct via `GET /api/interventions` (constantes
/// Intervention::STATUT_* / PRIORITE_*), pas inventées.
class Mission extends Equatable {
  final int id;
  final String codeIntervention;
  final String statut;
  final String priorite;
  final DateTime? datePrevueDebut;
  final String description;
  final String chantierNom;
  final String? emplacementNom;
  final String typeInterventionNom;
  final double? chantierLatitude;
  final double? chantierLongitude;

  /// Champs supplémentaires lus uniquement par le détail (GET
  /// /api/interventions/{id}) — absents/vides sur les Mission construites à
  /// partir de la liste (GET /api/interventions, sans `historiques`), remplis
  /// dès que l'écran de détail rafraîchit via GetMissionDetailUsecase.
  final int? chantierId;
  final double? emplacementLatitude;
  final double? emplacementLongitude;
  final List<HistoriqueEntry> historiques;

  /// `null` tant que non déterminé (Mission seedée depuis la liste, avant le
  /// premier rafraîchissement détail) ; `true`/`false` une fois connu, à
  /// partir de la relation `rapport` déjà chargée par
  /// `GET /api/interventions/{id}` — garde-fou pour ne jamais proposer "Voir
  /// le rapport" quand aucun Rapport n'existe réellement (statut Terminée
  /// incohérent, cf. Étape 2 du prompt "Cohérence seed/rapport").
  final bool? rapportExiste;

  const Mission({
    required this.id,
    required this.codeIntervention,
    required this.statut,
    required this.priorite,
    required this.datePrevueDebut,
    required this.description,
    required this.chantierNom,
    required this.emplacementNom,
    required this.typeInterventionNom,
    this.chantierLatitude,
    this.chantierLongitude,
    this.chantierId,
    this.emplacementLatitude,
    this.emplacementLongitude,
    this.historiques = const [],
    this.rapportExiste,
  });

  static const _statutsClotures = ['Terminee', 'Validee', 'Annulee', 'Rejetee'];

  bool get estCloturee => _statutsClotures.contains(statut);

  /// Miroir de `Intervention::peutEtreDemarree()` (app/Models/Intervention.php) :
  /// le backend n'autorise POST /interventions/{id}/start que depuis ces
  /// statuts. Sert à choisir "Démarrer" vs "Itinéraire" sur la carte
  /// "Prochaine mission" sans dupliquer une règle métier différente côté app.
  static const _statutsDemarrables = ['Acceptee', 'Suspendue', 'Reportee', 'Rejetee', 'Rouverte'];

  bool get peutDemarrer => statut == 'En cours' || _statutsDemarrables.contains(statut);

  bool get formulaireRempli => statut == 'Formulaire rempli';

  static const _statutsAcceptablesOuRefusables = ['Planifiee', 'Affectee'];

  /// Miroir de `Intervention::peutEtreAcceptee()` ET `peutEtreRefusee()`
  /// (app/Models/Intervention.php) — les deux méthodes backend testent
  /// exactement la même liste de statuts. Sert à l'écran de détail (bouton
  /// "Accepter"/"Refuser") : contrairement à `peutDemarrer` ci-dessus (pensé
  /// pour l'accueil, volontairement plus large), ce getter reflète EXACTEMENT
  /// la garde serveur pour n'afficher un bouton que s'il fonctionnera.
  bool get peutEtreAccepteeOuRefusee => _statutsAcceptablesOuRefusables.contains(statut);

  /// Miroir strict de `Intervention::peutEtreDemarree()` — sans le cas
  /// supplémentaire 'En cours' qu'ajoute `peutDemarrer` pour l'accueil.
  bool get peutEtreDemarreeStrict => _statutsDemarrables.contains(statut);

  @override
  List<Object?> get props => [id, codeIntervention, statut, priorite, datePrevueDebut];
}

/// Miroir d'une ligne `App\Models\InterventionHistorique` — horodatage réel
/// d'une transition de statut, tel qu'enregistré par
/// `InterventionService::enregistrerHistorique()` à chaque action (accepter,
/// démarrer, refuser, terminer, ...). Pas de placeholder : n'existe que si le
/// backend l'a réellement journalisé.
class HistoriqueEntry extends Equatable {
  final String? statutAvant;
  final String statutApres;
  final String? commentaire;
  final DateTime? date;
  final String? auteur;

  const HistoriqueEntry({
    required this.statutAvant,
    required this.statutApres,
    required this.commentaire,
    required this.date,
    required this.auteur,
  });

  @override
  List<Object?> get props => [statutAvant, statutApres, commentaire, date, auteur];
}
