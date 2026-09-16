import 'package:mobile_technicien/features/home/domain/entities/mission.dart';

/// Filtres de l'écran Missions — Étape 3.1 du prompt "Construction de
/// l'écran Missions" : "Toutes, Planifiées, Acceptées, En cours, Formulaire
/// rempli, Terminées, Annulées". Volontairement PAS de filtre masqué pour
/// "Formulaire rempli" (contrairement au portail Client) : le technicien a
/// besoin de savoir précisément qu'il doit encore soumettre son rapport.
///
/// D'autres statuts backend existent (Affectee, Suspendue, Reportee, Client
/// absent, etc.) — ils restent visibles sous "Toutes" mais n'ont pas de
/// filtre dédié, cohérent avec la liste demandée plutôt qu'un filtre par
/// valeur d'enum backend brute.
enum MissionStatusFilter {
  toutes('Toutes'),
  planifiee('Planifiées'),
  acceptee('Acceptées'),
  enCours('En cours'),
  formulaireRempli('Formulaire rempli'),
  terminee('Terminées'),
  annulee('Annulées');

  final String label;
  const MissionStatusFilter(this.label);

  bool matches(Mission mission) {
    return switch (this) {
      MissionStatusFilter.toutes => true,
      MissionStatusFilter.planifiee => mission.statut == 'Planifiee' || mission.statut == 'Affectee',
      MissionStatusFilter.acceptee => mission.statut == 'Acceptee',
      MissionStatusFilter.enCours => mission.statut == 'En cours' || mission.statut == 'Suspendue',
      MissionStatusFilter.formulaireRempli => mission.statut == 'Formulaire rempli',
      MissionStatusFilter.terminee => mission.statut == 'Terminee' || mission.statut == 'Validee',
      MissionStatusFilter.annulee => mission.statut == 'Annulee',
    };
  }

  /// Texte d'état vide contextuel (Étape 4.1) — jamais le même message
  /// générique pour tous les filtres.
  String get emptyMessage {
    return switch (this) {
      MissionStatusFilter.toutes => 'Aucune mission ne vous est assignée pour le moment.',
      MissionStatusFilter.planifiee => 'Aucune mission planifiée.',
      MissionStatusFilter.acceptee => 'Aucune mission acceptée en attente de démarrage.',
      MissionStatusFilter.enCours => 'Aucune mission en cours.',
      MissionStatusFilter.formulaireRempli => 'Aucun rapport en attente de finalisation.',
      MissionStatusFilter.terminee => 'Aucune mission terminée pour l\'instant.',
      MissionStatusFilter.annulee => 'Aucune mission annulée.',
    };
  }
}
