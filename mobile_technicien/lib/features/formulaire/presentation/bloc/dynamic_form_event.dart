part of 'dynamic_form_bloc.dart';

sealed class DynamicFormEvent extends Equatable {
  const DynamicFormEvent();

  @override
  List<Object?> get props => [];
}

class DynamicFormRequested extends DynamicFormEvent {
  const DynamicFormRequested();
}

/// Générique : Texte/TexteLong/Nombre/Date/Heure/DateHeure/OuiNon/Liste/
/// Radio/GPS/QRCode (valeur `String`) et Checkbox (valeur `List<String>`
/// d'identifiants de choix).
class DynamicFormAnswerChanged extends DynamicFormEvent {
  final int questionId;
  final dynamic value;
  const DynamicFormAnswerChanged(this.questionId, this.value);

  @override
  List<Object?> get props => [questionId, value];
}

/// Photo/Document : octets déjà lus depuis l'`XFile`/`PlatformFile` rendu par
/// image_picker/file_picker (jamais un chemin — inexploitable sur Flutter
/// Web, seule plateforme actuellement configurée dans ce projet).
class DynamicFormFileAdded extends DynamicFormEvent {
  final int questionId;
  final FormFileAnswer file;
  const DynamicFormFileAdded(this.questionId, this.file);

  @override
  List<Object?> get props => [questionId, file];
}

/// Signature : octets PNG déjà rendus par `SignatureController.toPngBytes()`.
class DynamicFormSignatureSaved extends DynamicFormEvent {
  final int questionId;
  final Uint8List bytes;
  const DynamicFormSignatureSaved(this.questionId, this.bytes);

  @override
  List<Object?> get props => [questionId, bytes];
}

/// [draftEntry] : la chaîne exacte telle que stockée dans `answers`
/// (`FormFileAnswer.toDraftString()`) — retirée par égalité de chaîne.
class DynamicFormFileRemoved extends DynamicFormEvent {
  final int questionId;
  final String draftEntry;
  const DynamicFormFileRemoved(this.questionId, this.draftEntry);

  @override
  List<Object?> get props => [questionId, draftEntry];
}

class DynamicFormSubmitted extends DynamicFormEvent {
  const DynamicFormSubmitted();
}
