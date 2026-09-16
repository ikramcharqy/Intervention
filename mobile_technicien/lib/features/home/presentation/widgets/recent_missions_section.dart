import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/presentation/widgets/status_badge.dart';

/// Étape 4 : section "Missions récentes" — les 2-3 dernières missions
/// clôturées (ou "En cours" en complément, cf. HomeRepositoryImpl), avec un
/// lien "Voir tout" vers l'onglet Missions complet.
class RecentMissionsSection extends StatelessWidget {
  final List<Mission> missions;
  final VoidCallback onSeeAll;

  const RecentMissionsSection({super.key, required this.missions, required this.onSeeAll});

  @override
  Widget build(BuildContext context) {
    if (missions.isEmpty) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('Missions récentes', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
            TextButton(onPressed: onSeeAll, child: const Text('Voir tout')),
          ],
        ),
        ...missions.map((m) => _RecentMissionTile(mission: m)),
      ],
    );
  }
}

class _RecentMissionTile extends StatelessWidget {
  final Mission mission;
  const _RecentMissionTile({required this.mission});

  @override
  Widget build(BuildContext context) {
    final date = mission.datePrevueDebut;
    final dateLabel = date != null ? DateFormat('dd/MM · HH:mm').format(date.toLocal()) : '—';
    final termine = mission.statut == 'Terminee' || mission.statut == 'Validee';

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE6E9F4)),
      ),
      child: Row(
        children: [
          Icon(
            termine ? Icons.check_circle_outline : Icons.autorenew,
            color: termine ? AppColors.success : AppColors.warning,
            size: 22,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(mission.chantierNom,
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                    overflow: TextOverflow.ellipsis),
                const SizedBox(height: 2),
                Text('${mission.typeInterventionNom} · $dateLabel',
                    style: const TextStyle(color: AppColors.neutral, fontSize: 11)),
              ],
            ),
          ),
          const SizedBox(width: 8),
          StatusBadge(statut: mission.statut),
        ],
      ),
    );
  }
}
