import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/utils/relative_time.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/priority_badge.dart';
import 'package:mobile_technicien/features/missions/presentation/widgets/status_badge.dart';

/// Carte de la liste Missions — réutilise PriorityBadge (accueil) et
/// StatusBadge plutôt que d'improviser une mise en forme par écran.
class MissionListCard extends StatelessWidget {
  final Mission mission;
  final VoidCallback onTap;

  const MissionListCard({super.key, required this.mission, required this.onTap});

  /// En retard : date prévue dépassée sans que la mission soit clôturée
  /// (Étape 3.6 : mise en évidence visuelle, pas seulement un tri).
  bool get _enRetard {
    final date = mission.datePrevueDebut;
    if (date == null || mission.estCloturee) return false;
    return date.isBefore(DateTime.now());
  }

  @override
  Widget build(BuildContext context) {
    final date = mission.datePrevueDebut;
    final dateLabel = date != null ? DateFormat('dd/MM/yyyy · HH:mm').format(date.toLocal()) : 'Date non définie';
    final relatif = relativeTimeLabel(date);
    final enRetard = _enRetard;

    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: enRetard ? AppColors.danger.withOpacity(0.4) : const Color(0xFFE6E9F4)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(mission.codeIntervention,
                        style: const TextStyle(fontSize: 11, color: AppColors.neutral, fontWeight: FontWeight.w600)),
                  ),
                  PriorityBadge(priorite: mission.priorite),
                ],
              ),
              const SizedBox(height: 6),
              Text(mission.chantierNom, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
              const SizedBox(height: 2),
              Text(mission.typeInterventionNom, style: const TextStyle(fontSize: 13, color: AppColors.neutral)),
              const SizedBox(height: 10),
              Row(
                children: [
                  StatusBadge(statut: mission.statut),
                  const SizedBox(width: 8),
                  if (enRetard)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: AppColors.danger.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(999),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: const [
                          Icon(Icons.warning_amber_rounded, size: 12, color: AppColors.danger),
                          SizedBox(width: 3),
                          Text('En retard',
                              style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: AppColors.danger)),
                        ],
                      ),
                    ),
                  const Spacer(),
                  Icon(Icons.chevron_right, size: 18, color: AppColors.neutral.withOpacity(0.6)),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                relatif != null ? '$dateLabel · $relatif' : dateLabel,
                style: const TextStyle(fontSize: 11, color: AppColors.neutral),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
