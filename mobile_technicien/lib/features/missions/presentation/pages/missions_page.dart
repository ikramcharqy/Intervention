import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/missions/domain/entities/mission_status_filter.dart';
import 'package:mobile_technicien/features/missions/presentation/bloc/missions_bloc.dart';
import 'package:mobile_technicien/features/missions/presentation/pages/mission_detail_page.dart';
import 'package:mobile_technicien/features/missions/presentation/widgets/mission_list_card.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Écran "Missions" — remplace le placeholder statique
/// (MissionsPlaceholderPage). Consomme MissionsBloc, lui-même branché sur
/// GET /api/interventions via HomeRemoteDatasource (même source que les
/// compteurs de l'accueil, Étape 5).
class MissionsPage extends StatelessWidget {
  const MissionsPage({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<MissionsBloc>()..add(const MissionsRequested()),
      child: const _MissionsView(),
    );
  }
}

class _MissionsView extends StatefulWidget {
  const _MissionsView();

  @override
  State<_MissionsView> createState() => _MissionsViewState();
}

class _MissionsViewState extends State<_MissionsView> {
  final _searchController = TextEditingController();

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
            child: Text('Mes missions', style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold)),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: TextField(
              controller: _searchController,
              onChanged: (value) => context.read<MissionsBloc>().add(MissionsSearchChanged(value)),
              decoration: InputDecoration(
                hintText: 'Rechercher par chantier ou référence…',
                prefixIcon: const Icon(Icons.search, size: 20),
                isDense: true,
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                contentPadding: const EdgeInsets.symmetric(vertical: 12),
              ),
            ),
          ),
          const SizedBox(height: 10),
          _FilterChips(),
          const SizedBox(height: 8),
          Expanded(
            child: BlocBuilder<MissionsBloc, MissionsState>(
              builder: (context, state) {
                return switch (state) {
                  MissionsLoading() => const Center(child: CircularProgressIndicator()),
                  MissionsError(:final message) => _ErrorView(
                      message: message,
                      onRetry: () => context.read<MissionsBloc>().add(const MissionsRequested()),
                    ),
                  MissionsEmpty(:final filter, :final searchQuery) => _EmptyView(
                      filter: filter,
                      searchQuery: searchQuery,
                      onRefresh: () async {
                        context.read<MissionsBloc>().add(const MissionsRefreshed());
                      },
                    ),
                  MissionsLoaded(:final filteredMissions) => RefreshIndicator(
                      onRefresh: () async {
                        context.read<MissionsBloc>().add(const MissionsRefreshed());
                      },
                      child: ListView.separated(
                        padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
                        itemCount: filteredMissions.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 10),
                        itemBuilder: (context, index) {
                          final mission = filteredMissions[index];
                          return MissionListCard(
                            mission: mission,
                            onTap: () => Navigator.of(context).push(
                              MaterialPageRoute(builder: (_) => MissionDetailPage(mission: mission)),
                            ),
                          );
                        },
                      ),
                    ),
                };
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _FilterChips extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final state = context.watch<MissionsBloc>().state;
    final activeFilter = switch (state) {
      MissionsLoaded(:final filter) => filter,
      MissionsEmpty(:final filter) => filter,
      _ => MissionStatusFilter.toutes,
    };

    return SizedBox(
      height: 36,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 20),
        itemCount: MissionStatusFilter.values.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final filter = MissionStatusFilter.values[index];
          final selected = filter == activeFilter;
          return ChoiceChip(
            label: Text(filter.label),
            selected: selected,
            onSelected: (_) => context.read<MissionsBloc>().add(MissionsFilterChanged(filter)),
            selectedColor: AppColors.brand,
            labelStyle: TextStyle(
              color: selected ? Colors.white : AppColors.neutral,
              fontWeight: FontWeight.w600,
              fontSize: 12,
            ),
            backgroundColor: Colors.white,
            side: BorderSide(color: selected ? AppColors.brand : const Color(0xFFE6E9F4)),
          );
        },
      ),
    );
  }
}

class _EmptyView extends StatelessWidget {
  final MissionStatusFilter filter;
  final String searchQuery;
  final Future<void> Function() onRefresh;
  const _EmptyView({required this.filter, required this.searchQuery, required this.onRefresh});

  @override
  Widget build(BuildContext context) {
    final message = searchQuery.trim().isNotEmpty
        ? 'Aucun résultat pour "$searchQuery".'
        : filter.emptyMessage;

    return RefreshIndicator(
      onRefresh: onRefresh,
      // ListView (pas juste Center) pour que le tirer-pour-rafraîchir reste
      // possible même quand la liste filtrée est vide.
      child: ListView(
        children: [
          const SizedBox(height: 80),
          Icon(Icons.inbox_outlined, size: 48, color: AppColors.neutral.withOpacity(0.5)),
          const SizedBox(height: 12),
          Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32),
              child: Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.neutral)),
            ),
          ),
        ],
      ),
    );
  }
}

class _ErrorView extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;
  const _ErrorView({required this.message, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.error_outline, color: AppColors.danger, size: 40),
            const SizedBox(height: 12),
            Text('Impossible de charger vos missions', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 6),
            Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.neutral, fontSize: 13)),
            const SizedBox(height: 16),
            FilledButton(
              style: FilledButton.styleFrom(backgroundColor: AppColors.brand),
              onPressed: onRetry,
              child: const Text('Réessayer'),
            ),
          ],
        ),
      ),
    );
  }
}
