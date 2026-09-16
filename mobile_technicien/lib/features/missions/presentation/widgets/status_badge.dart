import 'package:flutter/material.dart';

/// Reprend le mapping couleur de resources/views/components/soft-badge.blade.php
/// (mêmes clés de statut, mêmes teintes Tailwind approximées) — cohérence
/// visuelle avec le web Admin/Commercial plutôt qu'une palette inventée ici.
class StatusBadge extends StatelessWidget {
  final String statut;
  const StatusBadge({super.key, required this.statut});

  static const _colors = {
    'Planifiee': Color(0xFFB45309), // amber-700
    'Affectee': Color(0xFF6D28D9), // violet-700
    'Acceptee': Color(0xFF4338CA), // indigo-700
    'En cours': Color(0xFF047857), // emerald-700
    'Suspendue': Color(0xFF92400E), // amber-800
    'Reportee': Color(0xFF334155), // slate-700
    'Formulaire rempli': Color(0xFF0F766E), // teal-700
    'En attente validation': Color(0xFF6D28D9), // purple-700
    'Rejetee': Color(0xFFBE123C), // rose-700
    'Terminee': Color(0xFF047857), // emerald-700
    'Validee': Color(0xFF065F46), // emerald-800
    'Annulee': Color(0xFF475569), // slate-600
  };

  static const _backgrounds = {
    'Planifiee': Color(0xFFFFFBEB),
    'Affectee': Color(0xFFF5F3FF),
    'Acceptee': Color(0xFFEEF2FF),
    'En cours': Color(0xFFECFDF5),
    'Suspendue': Color(0xFFFFFBEB),
    'Reportee': Color(0xFFF1F5F9),
    'Formulaire rempli': Color(0xFFF0FDFA),
    'En attente validation': Color(0xFFF5F3FF),
    'Rejetee': Color(0xFFFFF1F2),
    'Terminee': Color(0xFFECFDF5),
    'Validee': Color(0xFFD1FAE5),
    'Annulee': Color(0xFFF1F5F9),
  };

  static const _labels = {
    'Planifiee': 'Planifiée',
    'Affectee': 'Affectée',
    'Acceptee': 'Acceptée',
    'Suspendue': 'Suspendue',
    'Reportee': 'Reportée',
    'En attente validation': 'En validation',
    'Rejetee': 'Rejetée',
    'Terminee': 'Terminée',
    'Validee': 'Validée',
    'Annulee': 'Annulée',
  };

  /// Source centralisée du libellé accentué d'un statut brut (ex : 'Terminee'
  /// → 'Terminée') — à réutiliser partout où un statut est affiché en texte
  /// (ex : l'historique des transitions), pas seulement dans ce badge, pour
  /// qu'une correction de libellé se propage automatiquement partout.
  static String labelFor(String statut) => _labels[statut] ?? statut;

  @override
  Widget build(BuildContext context) {
    final color = _colors[statut] ?? const Color(0xFF475569);
    final bg = _backgrounds[statut] ?? const Color(0xFFF1F5F9);
    final label = labelFor(statut);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: color.withOpacity(0.25)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(width: 6, height: 6, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
          const SizedBox(width: 6),
          Text(label, style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
