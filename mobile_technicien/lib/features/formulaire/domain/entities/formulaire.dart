import 'package:equatable/equatable.dart';

/// Miroir de app/Models/Formulaire.php — uniquement les champs lus par l'app.
/// `Formulaire::TYPES_CHAMPS` définit les valeurs exactes de [Question.type] :
/// Texte, TexteLong, Nombre, Date, Heure, DateHeure, OuiNon, Liste, Checkbox,
/// Radio, Photo, Signature, Document, GPS, QRCode, Materiaux (ce dernier est
/// ignoré ici — géré par les endpoints matériaux dédiés, cf.
/// RemplissageFormulaireService::sauvegarderReponses qui le skip aussi).
class Formulaire extends Equatable {
  final int id;
  final String nom;
  final String? description;
  final List<Question> questions;

  const Formulaire({required this.id, required this.nom, required this.description, required this.questions});

  @override
  List<Object?> get props => [id, nom, description, questions];
}

class Question extends Equatable {
  final int id;
  final String question;
  final String type;
  final bool obligatoire;
  final int ordre;
  final String? placeholder;
  final String? valeurParDefaut;
  final double? nombreMin;
  final double? nombreMax;
  final String? nombreUnite;
  final int fichiersMax;
  final List<ChoixQuestion> choix;

  const Question({
    required this.id,
    required this.question,
    required this.type,
    required this.obligatoire,
    required this.ordre,
    required this.placeholder,
    required this.valeurParDefaut,
    required this.nombreMin,
    required this.nombreMax,
    required this.nombreUnite,
    required this.fichiersMax,
    required this.choix,
  });

  static const _typesAvecChoix = ['Liste', 'Checkbox', 'Radio'];
  static const _typesFichier = ['Photo', 'Signature', 'Document'];

  bool get necessiteChoix => _typesAvecChoix.contains(type);

  bool get estTypeFichier => _typesFichier.contains(type);

  @override
  List<Object?> get props => [id, question, type, obligatoire, ordre, choix];
}

class ChoixQuestion extends Equatable {
  final int id;
  final String libelle;
  final String valeur;

  const ChoixQuestion({required this.id, required this.libelle, required this.valeur});

  @override
  List<Object?> get props => [id, libelle, valeur];
}
