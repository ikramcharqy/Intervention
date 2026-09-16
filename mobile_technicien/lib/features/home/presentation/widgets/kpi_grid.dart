import 'package:flutter/material.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/home/domain/entities/home_summary.dart';

class KpiGrid extends StatelessWidget {
  final HomeSummary summary;
  const KpiGrid({super.key, required this.summary});

  @override
  Widget build(BuildContext context) {
    final items = [
      _KpiItem('À réaliser', summary.aRealiser, Icons.assignment_outlined, AppColors.info),
      _KpiItem('En cours', summary.enCours, Icons.autorenew, AppColors.warning),
      _KpiItem('Terminées', summary.terminees, Icons.check_circle_outline, AppColors.success),
      _KpiItem('Non lues', summary.notificationsNonLues, Icons.notifications_outlined, AppColors.brand),
    ];

    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 1.6,
      children: items.map((i) => _KpiCard(item: i)).toList(),
    );
  }
}

class _KpiItem {
  final String label;
  final int value;
  final IconData icon;
  final Color color;
  _KpiItem(this.label, this.value, this.icon, this.color);
}

class _KpiCard extends StatelessWidget {
  final _KpiItem item;
  const _KpiCard({required this.item});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE6E9F4)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Icon(item.icon, color: item.color, size: 20),
          Text('${item.value}', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          Text(item.label, style: const TextStyle(fontSize: 12, color: AppColors.neutral)),
        ],
      ),
    );
  }
}

/// Étape 2.4 : skeleton loader pendant le chargement initial, au lieu d'un
/// écran figé.
class KpiGridSkeleton extends StatelessWidget {
  const KpiGridSkeleton({super.key});

  @override
  Widget build(BuildContext context) {
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 1.6,
      children: List.generate(4, (_) => const _SkeletonBox()),
    );
  }
}

class _SkeletonBox extends StatelessWidget {
  const _SkeletonBox();

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFEDEFF5),
        borderRadius: BorderRadius.circular(14),
      ),
    );
  }
}

/// Étape 2.3 : distinction visuelle explicite entre un échec de chargement des
/// KPI et un compteur à 0 réellement vide.
class KpiGridError extends StatelessWidget {
  final VoidCallback onRetry;
  const KpiGridError({super.key, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.danger.withOpacity(0.06),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.danger.withOpacity(0.25)),
      ),
      child: Column(
        children: [
          const Icon(Icons.error_outline, color: AppColors.danger),
          const SizedBox(height: 8),
          const Text('Impossible de charger vos statistiques', style: TextStyle(fontWeight: FontWeight.w600)),
          TextButton(onPressed: onRetry, child: const Text('Appuyez pour réessayer')),
        ],
      ),
    );
  }
}
