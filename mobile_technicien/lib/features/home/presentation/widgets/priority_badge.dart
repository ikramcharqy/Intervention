import 'package:flutter/material.dart';

/// Étape 1.1 : "réutiliser le composant déjà utilisé côté portail Client" —
/// reprend exactement le mapping couleur de resources/views/components/
/// soft-badge.blade.php (clés 'Faible'/'Normale'/'Haute'/'Urgente') pour que
/// la même priorité ait la même couleur sur le web Client et l'app Technicien.
class PriorityBadge extends StatelessWidget {
  final String priorite;
  const PriorityBadge({super.key, required this.priorite});

  static const _colors = {
    'Faible': Color(0xFF64748B),
    'Normale': Color(0xFF4F46E5),
    'Haute': Color(0xFFD97706),
    'Urgente': Color(0xFFE11D48),
  };

  static const _backgrounds = {
    'Faible': Color(0xFFF1F5F9),
    'Normale': Color(0xFFEEF2FF),
    'Haute': Color(0xFFFFFBEB),
    'Urgente': Color(0xFFFFF1F2),
  };

  @override
  Widget build(BuildContext context) {
    final color = _colors[priorite] ?? _colors['Normale']!;
    final bg = _backgrounds[priorite] ?? _backgrounds['Normale']!;

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
          Text(priorite, style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
