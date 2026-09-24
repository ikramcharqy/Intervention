import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/services/location_status_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/form_file_answer.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';
import 'package:mobile_technicien/features/formulaire/presentation/bloc/dynamic_form_bloc.dart';
import 'package:mobile_technicien/features/home/presentation/pages/qr_scan_page.dart';
import 'package:signature/signature.dart';

/// Un champ par [Question.type] — Étape 3.2 du prompt formulaire dynamique.
/// `condition_affichage` (affichage conditionnel entre questions) n'est PAS
/// évalué ici : aucune règle de ce type n'est encore exploitée ailleurs dans
/// l'app (champ réservé côté backend, cf. Question::condition_affichage,
/// commenté "réservé pour conditions futures") — toutes les questions du
/// formulaire sont donc affichées, dans l'ordre déjà trié par `ordre`.
class DynamicFormField extends StatelessWidget {
  final Question question;
  final dynamic value;
  final bool invalid;
  const DynamicFormField({super.key, required this.question, required this.value, this.invalid = false});

  String get _label => question.obligatoire ? '${question.question} *' : question.question;

  @override
  Widget build(BuildContext context) {
    switch (question.type) {
      case 'Texte':
        return _TextAnswerField(question: question, value: value as String?, label: _label, maxLines: 1, invalid: invalid);
      case 'TexteLong':
        return _TextAnswerField(question: question, value: value as String?, label: _label, maxLines: 4, invalid: invalid);
      case 'Nombre':
        return _NumberAnswerField(question: question, value: value as String?, label: _label, invalid: invalid);
      case 'Date':
        return _DateAnswerField(question: question, value: value as String?, label: _label, mode: _DateMode.date, invalid: invalid);
      case 'Heure':
        return _DateAnswerField(question: question, value: value as String?, label: _label, mode: _DateMode.time, invalid: invalid);
      case 'DateHeure':
        return _DateAnswerField(question: question, value: value as String?, label: _label, mode: _DateMode.dateTime, invalid: invalid);
      case 'Liste':
        return _DropdownAnswerField(question: question, value: value as String?, label: _label, invalid: invalid);
      case 'Radio':
        return _RadioAnswerField(question: question, value: value as String?, label: _label, invalid: invalid);
      case 'Checkbox':
        return _CheckboxAnswerField(
          question: question,
          value: (value as List?)?.cast<String>() ?? const [],
          label: _label,
          invalid: invalid,
        );
      case 'OuiNon':
        return _OuiNonAnswerField(question: question, value: value as String?, label: _label, invalid: invalid);
      case 'Photo':
        return _FileAnswerField(
          question: question,
          draftEntries: (value as List?)?.cast<String>() ?? const [],
          label: _label,
          allowCamera: true,
          invalid: invalid,
        );
      case 'Document':
        return _FileAnswerField(
          question: question,
          draftEntries: (value as List?)?.cast<String>() ?? const [],
          label: _label,
          allowCamera: false,
          invalid: invalid,
        );
      case 'Signature':
        return _SignatureAnswerField(
          question: question,
          draftEntries: (value as List?)?.cast<String>() ?? const [],
          label: _label,
          invalid: invalid,
        );
      case 'GPS':
        return _GpsAnswerField(question: question, value: value as String?, label: _label, invalid: invalid);
      case 'QRCode':
        return _QrAnswerField(question: question, value: value as String?, label: _label, invalid: invalid);
      default:
        return _TextAnswerField(question: question, value: value as String?, label: _label, maxLines: 1, invalid: invalid);
    }
  }
}

/// Bordure/fill nettement visibles sur fond `AppColors.background` (très
/// proche du blanc) — Étape 4 : un simple `OutlineInputBorder()` par défaut
/// (couleur `outline` du ColorScheme généré) contrastait trop peu avec le
/// fond de l'écran et se lisait comme un champ désactivé.
const _fieldBorderColor = Color(0xFFCBD5E1);

InputBorder _fieldBorder([Color? color]) =>
    OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide(color: color ?? _fieldBorderColor));

class _FieldCard extends StatelessWidget {
  final String label;
  final Widget child;
  final String? helperText;
  final bool invalid;
  const _FieldCard({required this.label, required this.child, this.helperText, this.invalid = false});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 18),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14, color: invalid ? AppColors.danger : Colors.black87),
          ),
          const SizedBox(height: 8),
          if (invalid)
            Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(border: Border.all(color: AppColors.danger, width: 1.5), borderRadius: BorderRadius.circular(10)),
              child: child,
            )
          else
            child,
          if (invalid) ...[
            const SizedBox(height: 4),
            const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(Icons.error_outline, size: 14, color: AppColors.danger),
                SizedBox(width: 4),
                Text('Ce champ est obligatoire.', style: TextStyle(fontSize: 11, color: AppColors.danger)),
              ],
            ),
          ] else if (helperText != null) ...[
            const SizedBox(height: 4),
            Text(helperText!, style: const TextStyle(fontSize: 11, color: AppColors.neutral)),
          ],
        ],
      ),
    );
  }
}

class _TextAnswerField extends StatefulWidget {
  final Question question;
  final String? value;
  final String label;
  final int maxLines;
  final bool invalid;
  const _TextAnswerField({
    required this.question,
    required this.value,
    required this.label,
    required this.maxLines,
    this.invalid = false,
  });

  @override
  State<_TextAnswerField> createState() => _TextAnswerFieldState();
}

class _TextAnswerFieldState extends State<_TextAnswerField> {
  late final TextEditingController _controller = TextEditingController(text: widget.value ?? '');

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return _FieldCard(
      label: widget.label,
      invalid: widget.invalid,
      child: TextFormField(
        controller: _controller,
        maxLines: widget.maxLines,
        decoration: InputDecoration(
          hintText: widget.question.placeholder,
          border: _fieldBorder(),
          enabledBorder: _fieldBorder(),
          focusedBorder: _fieldBorder(AppColors.brand),
          filled: true,
          fillColor: Colors.white,
        ),
        onChanged: (v) => context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(widget.question.id, v)),
      ),
    );
  }
}

class _NumberAnswerField extends StatefulWidget {
  final Question question;
  final String? value;
  final String label;
  final bool invalid;
  const _NumberAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  @override
  State<_NumberAnswerField> createState() => _NumberAnswerFieldState();
}

class _NumberAnswerFieldState extends State<_NumberAnswerField> {
  late final TextEditingController _controller = TextEditingController(text: widget.value ?? '');

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final min = widget.question.nombreMin;
    final max = widget.question.nombreMax;
    final unite = widget.question.nombreUnite;
    final bounds = [
      if (min != null) 'min ${_trimZero(min)}',
      if (max != null) 'max ${_trimZero(max)}',
      if (unite != null && unite.isNotEmpty) unite,
    ].join(' · ');

    return _FieldCard(
      label: widget.label,
      invalid: widget.invalid,
      helperText: bounds.isEmpty ? null : bounds,
      child: TextFormField(
        controller: _controller,
        keyboardType: const TextInputType.numberWithOptions(decimal: true, signed: true),
        decoration: InputDecoration(
          hintText: widget.question.placeholder,
          suffixText: unite,
          border: _fieldBorder(),
          enabledBorder: _fieldBorder(),
          focusedBorder: _fieldBorder(AppColors.brand),
          filled: true,
          fillColor: Colors.white,
        ),
        onChanged: (v) => context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(widget.question.id, v)),
      ),
    );
  }

  String _trimZero(double v) => v == v.roundToDouble() ? v.toInt().toString() : v.toString();
}

enum _DateMode { date, time, dateTime }

class _DateAnswerField extends StatelessWidget {
  final Question question;
  final String? value;
  final String label;
  final _DateMode mode;
  final bool invalid;
  const _DateAnswerField({
    required this.question,
    required this.value,
    required this.label,
    required this.mode,
    this.invalid = false,
  });

  static const _dateFmt = 'yyyy-MM-dd';
  static const _timeFmt = 'HH:mm';
  static const _dateTimeFmt = 'yyyy-MM-dd HH:mm';

  String get _pattern => switch (mode) {
        _DateMode.date => _dateFmt,
        _DateMode.time => _timeFmt,
        _DateMode.dateTime => _dateTimeFmt,
      };

  Future<void> _pick(BuildContext context) async {
    DateTime base = DateTime.now();
    if (value != null && value!.isNotEmpty) {
      try {
        base = DateFormat(_pattern).parse(value!);
      } catch (_) {}
    }

    DateTime? pickedDate = base;
    TimeOfDay? pickedTime = TimeOfDay.fromDateTime(base);

    if (mode == _DateMode.date || mode == _DateMode.dateTime) {
      pickedDate = await showDatePicker(
        context: context,
        initialDate: base,
        firstDate: DateTime(2020),
        lastDate: DateTime(2100),
      );
      if (pickedDate == null) return;
    }

    if (mode == _DateMode.time || mode == _DateMode.dateTime) {
      if (!context.mounted) return;
      pickedTime = await showTimePicker(context: context, initialTime: TimeOfDay.fromDateTime(base));
      if (pickedTime == null) return;
    }

    final result = DateTime(
      pickedDate.year,
      pickedDate.month,
      pickedDate.day,
      pickedTime.hour,
      pickedTime.minute,
    );

    if (!context.mounted) return;
    context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, DateFormat(_pattern).format(result)));
  }

  @override
  Widget build(BuildContext context) {
    final icon = switch (mode) {
      _DateMode.date => Icons.calendar_today_outlined,
      _DateMode.time => Icons.access_time_outlined,
      _DateMode.dateTime => Icons.event_outlined,
    };

    return _FieldCard(
      label: label,
      invalid: invalid,
      child: InkWell(
        onTap: () => _pick(context),
        child: InputDecorator(
          decoration: InputDecoration(border: _fieldBorder(), filled: true, fillColor: Colors.white),
          child: Row(
            children: [
              Icon(icon, size: 18, color: AppColors.neutral),
              const SizedBox(width: 10),
              Text(value?.isNotEmpty == true ? value! : 'Sélectionner…'),
            ],
          ),
        ),
      ),
    );
  }
}

class _DropdownAnswerField extends StatelessWidget {
  final Question question;
  final String? value;
  final String label;
  final bool invalid;
  const _DropdownAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  @override
  Widget build(BuildContext context) {
    return _FieldCard(
      label: label,
      invalid: invalid,
      child: DropdownButtonFormField<String>(
        value: question.choix.any((c) => '${c.id}' == value) ? value : null,
        decoration: InputDecoration(border: _fieldBorder(), filled: true, fillColor: Colors.white),
        hint: const Text('Sélectionner…'),
        items: question.choix.map((c) => DropdownMenuItem(value: '${c.id}', child: Text(c.libelle))).toList(),
        onChanged: (v) => context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, v)),
      ),
    );
  }
}

class _RadioAnswerField extends StatelessWidget {
  final Question question;
  final String? value;
  final String label;
  final bool invalid;
  const _RadioAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  @override
  Widget build(BuildContext context) {
    return _FieldCard(
      label: label,
      invalid: invalid,
      child: Container(
        decoration: BoxDecoration(border: Border.all(color: AppColors.border), borderRadius: BorderRadius.circular(8)),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: question.choix
              .map((c) => RadioListTile<String>(
                    value: '${c.id}',
                    groupValue: value,
                    dense: true,
                    title: Text(c.libelle),
                    activeColor: AppColors.brand,
                    onChanged: (v) => context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, v)),
                  ))
              .toList(),
        ),
      ),
    );
  }
}

class _CheckboxAnswerField extends StatelessWidget {
  final Question question;
  final List<String> value;
  final String label;
  final bool invalid;
  const _CheckboxAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  @override
  Widget build(BuildContext context) {
    return _FieldCard(
      label: label,
      invalid: invalid,
      child: Container(
        decoration: BoxDecoration(border: Border.all(color: AppColors.border), borderRadius: BorderRadius.circular(8)),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: question.choix.map((c) {
            final id = '${c.id}';
            final checked = value.contains(id);
            return CheckboxListTile(
              value: checked,
              dense: true,
              title: Text(c.libelle),
              activeColor: AppColors.brand,
              onChanged: (v) {
                final next = List<String>.from(value);
                if (v == true) {
                  next.add(id);
                } else {
                  next.remove(id);
                }
                context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, next));
              },
            );
          }).toList(),
        ),
      ),
    );
  }
}

class _OuiNonAnswerField extends StatelessWidget {
  final Question question;
  final String? value;
  final String label;
  final bool invalid;
  const _OuiNonAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  @override
  Widget build(BuildContext context) {
    return _FieldCard(
      label: label,
      invalid: invalid,
      child: Row(
        children: [
          Expanded(
            child: ChoiceChip(
              label: const Text('Oui'),
              selected: value == 'Oui',
              selectedColor: AppColors.brand,
              labelStyle: TextStyle(color: value == 'Oui' ? Colors.white : Colors.black87),
              onSelected: (_) => context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, 'Oui')),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: ChoiceChip(
              label: const Text('Non'),
              selected: value == 'Non',
              selectedColor: AppColors.brand,
              labelStyle: TextStyle(color: value == 'Non' ? Colors.white : Colors.black87),
              onSelected: (_) => context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, 'Non')),
            ),
          ),
        ],
      ),
    );
  }
}

class _FileAnswerField extends StatelessWidget {
  final Question question;

  /// Entrées `FormFileAnswer.toDraftString()` — jamais des chemins de
  /// fichiers (voir form_file_answer.dart : `XFile.path`/`PlatformFile.path`
  /// ne sont pas exploitables sur Flutter Web).
  final List<String> draftEntries;
  final String label;
  final bool allowCamera;
  final bool invalid;
  const _FileAnswerField({
    required this.question,
    required this.draftEntries,
    required this.label,
    required this.allowCamera,
    this.invalid = false,
  });

  Future<void> _addPhoto(BuildContext context, ImageSource source) async {
    final picked = await ImagePicker().pickImage(source: source, imageQuality: 85);
    if (picked == null || !context.mounted) return;
    final bytes = await picked.readAsBytes();
    if (!context.mounted) return;
    context.read<DynamicFormBloc>().add(DynamicFormFileAdded(question.id, FormFileAnswer(nom: picked.name, bytes: bytes)));
  }

  Future<void> _pickPhotoSource(BuildContext context) async {
    final source = await showModalBottomSheet<ImageSource>(
      context: context,
      builder: (sheetContext) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              leading: const Icon(Icons.camera_alt_outlined),
              title: const Text('Prendre une photo'),
              onTap: () => Navigator.of(sheetContext).pop(ImageSource.camera),
            ),
            ListTile(
              leading: const Icon(Icons.photo_library_outlined),
              title: const Text('Choisir dans la galerie'),
              onTap: () => Navigator.of(sheetContext).pop(ImageSource.gallery),
            ),
          ],
        ),
      ),
    );
    if (source != null && context.mounted) {
      await _addPhoto(context, source);
    }
  }

  Future<void> _pickDocument(BuildContext context) async {
    // withData: true — sur Flutter Web, PlatformFile.path est toujours null ;
    // seuls les octets sont disponibles.
    final result = await FilePicker.platform.pickFiles(withData: true);
    final picked = result?.files.single;
    final bytes = picked?.bytes;
    if (picked == null || bytes == null || !context.mounted) return;
    context.read<DynamicFormBloc>().add(DynamicFormFileAdded(question.id, FormFileAnswer(nom: picked.name, bytes: bytes)));
  }

  @override
  Widget build(BuildContext context) {
    final canAddMore = draftEntries.length < question.fichiersMax;
    final files = draftEntries.map(FormFileAnswer.fromDraftString).toList();

    return _FieldCard(
      label: label,
      invalid: invalid,
      helperText: question.fichiersMax > 1 ? '${draftEntries.length}/${question.fichiersMax} fichier(s)' : null,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (allowCamera)
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: List.generate(files.length, (i) {
                final file = files[i];
                return Stack(
                  alignment: Alignment.topRight,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.memory(file.bytes, width: 84, height: 84, fit: BoxFit.cover),
                    ),
                    _RemoveBadge(
                      onTap: () => context.read<DynamicFormBloc>().add(DynamicFormFileRemoved(question.id, draftEntries[i])),
                    ),
                  ],
                );
              }),
            )
          else
            Column(
              children: List.generate(files.length, (i) {
                final file = files[i];
                return Card(
                  margin: const EdgeInsets.only(bottom: 6),
                  child: ListTile(
                    dense: true,
                    leading: const Icon(Icons.insert_drive_file_outlined),
                    title: Text(file.nom, overflow: TextOverflow.ellipsis),
                    trailing: IconButton(
                      icon: const Icon(Icons.close, size: 18),
                      onPressed: () => context.read<DynamicFormBloc>().add(DynamicFormFileRemoved(question.id, draftEntries[i])),
                    ),
                  ),
                );
              }),
            ),
          if (canAddMore) ...[
            const SizedBox(height: 8),
            OutlinedButton.icon(
              onPressed: () => allowCamera ? _pickPhotoSource(context) : _pickDocument(context),
              icon: Icon(allowCamera ? Icons.add_a_photo_outlined : Icons.attach_file),
              label: Text(allowCamera ? 'Ajouter une photo' : 'Ajouter un document'),
            ),
          ],
        ],
      ),
    );
  }
}

class _RemoveBadge extends StatelessWidget {
  final VoidCallback onTap;
  const _RemoveBadge({required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.all(2),
        padding: const EdgeInsets.all(2),
        decoration: const BoxDecoration(color: Colors.black54, shape: BoxShape.circle),
        child: const Icon(Icons.close, size: 14, color: Colors.white),
      ),
    );
  }
}

class _SignatureAnswerField extends StatefulWidget {
  final Question question;

  /// Entrée `FormFileAnswer.toDraftString()` — jamais un chemin de fichier.
  final List<String> draftEntries;
  final String label;
  final bool invalid;
  const _SignatureAnswerField({required this.question, required this.draftEntries, required this.label, this.invalid = false});

  @override
  State<_SignatureAnswerField> createState() => _SignatureAnswerFieldState();
}

class _SignatureAnswerFieldState extends State<_SignatureAnswerField> {
  final SignatureController _controller = SignatureController(penStrokeWidth: 3, penColor: Colors.black);

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _save(BuildContext context) async {
    if (_controller.isEmpty) return;
    final bytes = await _controller.toPngBytes();
    if (bytes == null || !context.mounted) return;
    context.read<DynamicFormBloc>().add(DynamicFormSignatureSaved(widget.question.id, bytes));
    _controller.clear();
  }

  @override
  Widget build(BuildContext context) {
    final hasSaved = widget.draftEntries.isNotEmpty;

    return _FieldCard(
      label: widget.label,
      invalid: widget.invalid,
      child: hasSaved
          ? Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: Container(
                    color: Colors.white,
                    child: Image.memory(FormFileAnswer.fromDraftString(widget.draftEntries.first).bytes, height: 120),
                  ),
                ),
                const SizedBox(height: 6),
                TextButton.icon(
                  onPressed: () =>
                      context.read<DynamicFormBloc>().add(DynamicFormFileRemoved(widget.question.id, widget.draftEntries.first)),
                  icon: const Icon(Icons.refresh, size: 18),
                  label: const Text('Refaire la signature'),
                ),
              ],
            )
          : Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: 160,
                  decoration: BoxDecoration(border: Border.all(color: AppColors.border), borderRadius: BorderRadius.circular(8)),
                  child: Signature(controller: _controller, backgroundColor: Colors.white),
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    TextButton(onPressed: () => _controller.clear(), child: const Text('Effacer')),
                    const Spacer(),
                    FilledButton(
                      style: FilledButton.styleFrom(backgroundColor: AppColors.brand),
                      onPressed: () => _save(context),
                      child: const Text('Valider la signature'),
                    ),
                  ],
                ),
              ],
            ),
    );
  }
}

class _GpsAnswerField extends StatefulWidget {
  final Question question;
  final String? value;
  final String label;
  final bool invalid;
  const _GpsAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  @override
  State<_GpsAnswerField> createState() => _GpsAnswerFieldState();
}

class _GpsAnswerFieldState extends State<_GpsAnswerField> {
  final _locationService = LocationStatusService();
  bool _capturing = false;

  Future<void> _capture(BuildContext context) async {
    setState(() => _capturing = true);
    // Capture ponctuelle au moment de remplir CE champ — pas le statut GPS
    // permanent de l'accueil ni un suivi continu.
    final position = await _locationService.getCurrentPosition();
    if (!mounted) return;
    setState(() => _capturing = false);

    if (position == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Position indisponible — vérifiez que le GPS est activé et autorisé.'), backgroundColor: AppColors.danger),
      );
      return;
    }

    context.read<DynamicFormBloc>().add(
          DynamicFormAnswerChanged(widget.question.id, '${position.latitude},${position.longitude}'),
        );
  }

  @override
  Widget build(BuildContext context) {
    final coords = widget.value?.split(',');
    final hasValue = coords != null && coords.length == 2;

    return _FieldCard(
      label: widget.label,
      invalid: widget.invalid,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (hasValue)
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: AppColors.brandLight, borderRadius: BorderRadius.circular(8)),
              child: Text('Lat : ${coords.first} · Lng : ${coords.last}', style: const TextStyle(fontSize: 13)),
            ),
          const SizedBox(height: 6),
          OutlinedButton.icon(
            onPressed: _capturing ? null : () => _capture(context),
            icon: _capturing
                ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))
                : const Icon(Icons.my_location),
            label: Text(hasValue ? 'Recapturer la position' : 'Capturer la position'),
          ),
        ],
      ),
    );
  }
}

class _QrAnswerField extends StatelessWidget {
  final Question question;
  final String? value;
  final String label;
  final bool invalid;
  const _QrAnswerField({required this.question, required this.value, required this.label, this.invalid = false});

  Future<void> _scan(BuildContext context) async {
    final code = await Navigator.of(context).push<String>(MaterialPageRoute(builder: (_) => const QrScanPage()));
    if (code != null && context.mounted) {
      context.read<DynamicFormBloc>().add(DynamicFormAnswerChanged(question.id, code));
    }
  }

  @override
  Widget build(BuildContext context) {
    return _FieldCard(
      label: label,
      invalid: invalid,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (value?.isNotEmpty == true)
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: AppColors.brandLight, borderRadius: BorderRadius.circular(8)),
              child: Text(value!, style: const TextStyle(fontSize: 13)),
            ),
          const SizedBox(height: 6),
          OutlinedButton.icon(
            onPressed: () => _scan(context),
            icon: const Icon(Icons.qr_code_scanner),
            label: Text(value?.isNotEmpty == true ? 'Rescanner' : 'Scanner un QR code'),
          ),
        ],
      ),
    );
  }
}
