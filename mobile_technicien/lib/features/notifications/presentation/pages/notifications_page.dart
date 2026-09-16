import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/utils/time_ago.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/home/presentation/bloc/home_bloc.dart';
import 'package:mobile_technicien/features/missions/presentation/pages/mission_detail_page.dart';
import 'package:mobile_technicien/features/notifications/domain/entities/app_notification.dart';
import 'package:mobile_technicien/features/notifications/presentation/bloc/notifications_bloc.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Écran "Notifs" — consomme GET/POST /api/notifications déjà exposés
/// (Api\NotificationController), même mécanisme lu/non-lu que le portail
/// Client (Illuminate\Notifications natif). Regroupement temporel
/// Aujourd'hui/Hier/Plus tôt : même logique que
/// resources/views/client/notifications/index.blade.php, reproduite ici
/// côté Dart plutôt qu'inventée.
class NotificationsPage extends StatelessWidget {
  const NotificationsPage({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<NotificationsBloc>()..add(const NotificationsRequested()),
      child: const _NotificationsView(),
    );
  }
}

class _NotificationsView extends StatefulWidget {
  const _NotificationsView();

  @override
  State<_NotificationsView> createState() => _NotificationsViewState();
}

class _NotificationsViewState extends State<_NotificationsView> {
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.removeListener(_onScroll);
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      context.read<NotificationsBloc>().add(const NotificationsMoreRequested());
    }
  }

  /// Marque comme lue puis, si liée à une intervention, ouvre son détail —
  /// une Mission "stub" (juste l'id connu) suffit : MissionDetailPage
  /// rafraîchit systématiquement le détail complet à l'ouverture.
  void _onTapNotification(BuildContext context, AppNotification notification) {
    if (!notification.estLue) {
      context.read<NotificationsBloc>().add(NotificationMarkReadRequested(notification.id));
      // Le badge de la barre de navigation (HomeBloc, ancêtre partagé de tous
      // les onglets) doit refléter la baisse du compteur non-lues.
      context.read<HomeBloc>().add(const HomeRefreshed());
    }

    if (notification.interventionId != null) {
      Navigator.of(context).push(MaterialPageRoute(
        builder: (_) => MissionDetailPage(
          mission: Mission(
            id: notification.interventionId!,
            codeIntervention: notification.codeIntervention ?? '…',
            statut: '',
            priorite: 'Normale',
            datePrevueDebut: null,
            description: '',
            chantierNom: 'Chargement…',
            emplacementNom: null,
            typeInterventionNom: '',
          ),
        ),
      ));
    }
  }

  void _onMarkAllRead(BuildContext context) {
    context.read<NotificationsBloc>().add(const NotificationsMarkAllReadRequested());
    context.read<HomeBloc>().add(const HomeRefreshed());
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
            child: Row(
              children: [
                Text('Notifications', style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold)),
                const Spacer(),
                BlocBuilder<NotificationsBloc, NotificationsState>(
                  builder: (context, state) {
                    if (state is! NotificationsLoaded || state.unreadCount == 0) return const SizedBox.shrink();
                    return TextButton(
                      onPressed: () => _onMarkAllRead(context),
                      child: const Text('Tout marquer comme lu'),
                    );
                  },
                ),
              ],
            ),
          ),
          Expanded(
            child: BlocBuilder<NotificationsBloc, NotificationsState>(
              builder: (context, state) {
                return switch (state) {
                  NotificationsLoading() => const Center(child: CircularProgressIndicator()),
                  NotificationsError(:final message) => _ErrorView(
                      message: message,
                      onRetry: () => context.read<NotificationsBloc>().add(const NotificationsRequested()),
                    ),
                  NotificationsLoaded(notifications: []) => RefreshIndicator(
                      onRefresh: () async => context.read<NotificationsBloc>().add(const NotificationsRequested()),
                      child: ListView(
                        children: const [
                          SizedBox(height: 100),
                          Icon(Icons.notifications_none, size: 48, color: AppColors.neutral),
                          SizedBox(height: 12),
                          Center(child: Text('Aucune notification pour le moment.', style: TextStyle(color: AppColors.neutral))),
                        ],
                      ),
                    ),
                  NotificationsLoaded() => RefreshIndicator(
                      onRefresh: () async => context.read<NotificationsBloc>().add(const NotificationsRequested()),
                      child: _GroupedList(
                        scrollController: _scrollController,
                        notifications: state.notifications,
                        loadingMore: state.loadingMore,
                        onTap: (n) => _onTapNotification(context, n),
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

class _GroupedList extends StatelessWidget {
  final ScrollController scrollController;
  final List<AppNotification> notifications;
  final bool loadingMore;
  final void Function(AppNotification) onTap;

  const _GroupedList({
    required this.scrollController,
    required this.notifications,
    required this.loadingMore,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    // Même règle que la vue Blade Client : isToday / isYesterday / reste,
    // ordre d'affichage fixe (Aujourd'hui, Hier, Plus tôt).
    final aujourdHui = <AppNotification>[];
    final hier = <AppNotification>[];
    final plusTot = <AppNotification>[];
    final now = DateTime.now();

    for (final n in notifications) {
      final local = n.createdAt.toLocal();
      final isToday = local.year == now.year && local.month == now.month && local.day == now.day;
      final yesterday = now.subtract(const Duration(days: 1));
      final isYesterday = local.year == yesterday.year && local.month == yesterday.month && local.day == yesterday.day;
      if (isToday) {
        aujourdHui.add(n);
      } else if (isYesterday) {
        hier.add(n);
      } else {
        plusTot.add(n);
      }
    }

    final groupes = [
      ("Aujourd'hui", aujourdHui),
      ('Hier', hier),
      ('Plus tôt', plusTot),
    ].where((g) => g.$2.isNotEmpty).toList();

    return ListView.builder(
      controller: scrollController,
      padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
      itemCount: groupes.fold<int>(0, (sum, g) => sum + 1 + g.$2.length) + (loadingMore ? 1 : 0),
      itemBuilder: (context, index) {
        var remaining = index;
        for (final groupe in groupes) {
          if (remaining == 0) {
            return Padding(
              padding: const EdgeInsets.only(top: 16, bottom: 8),
              child: Text(groupe.$1, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.neutral)),
            );
          }
          remaining--;
          if (remaining < groupe.$2.length) {
            return _NotificationTile(notification: groupe.$2[remaining], onTap: () => onTap(groupe.$2[remaining]));
          }
          remaining -= groupe.$2.length;
        }
        return const Padding(
          padding: EdgeInsets.symmetric(vertical: 16),
          child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
        );
      },
    );
  }
}

class _NotificationTile extends StatelessWidget {
  final AppNotification notification;
  final VoidCallback onTap;
  const _NotificationTile({required this.notification, required this.onTap});

  static const _icones = {
    'intervention_planifiee': Icons.assignment_outlined,
    'intervention_modifiee': Icons.edit_outlined,
    'intervention_acceptee': Icons.check_circle_outline,
    'intervention_reaffectee': Icons.swap_horiz,
    'intervention_demarree': Icons.play_circle_outline,
    'intervention_suspendue': Icons.pause_circle_outline,
    'formulaire_soumis': Icons.assignment_turned_in_outlined,
    'intervention_validee': Icons.verified_outlined,
    'intervention_rejetee': Icons.report_problem_outlined,
    'intervention_retard': Icons.alarm_outlined,
  };

  @override
  Widget build(BuildContext context) {
    final icon = _icones[notification.type] ?? Icons.notifications_outlined;

    return Material(
      color: notification.estLue ? Colors.white : AppColors.brandLight,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: notification.estNavigable || !notification.estLue ? onTap : null,
        child: Container(
          margin: const EdgeInsets.only(bottom: 8),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), border: Border.all(color: const Color(0xFFE6E9F4))),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CircleAvatar(
                radius: 18,
                backgroundColor: notification.estLue ? const Color(0xFFF1F5F9) : AppColors.brand.withOpacity(0.15),
                child: Icon(icon, size: 18, color: notification.estLue ? AppColors.neutral : AppColors.brand),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            notification.titre,
                            style: TextStyle(fontSize: 13, fontWeight: notification.estLue ? FontWeight.w500 : FontWeight.w700),
                          ),
                        ),
                        if (!notification.estLue)
                          Container(width: 8, height: 8, margin: const EdgeInsets.only(left: 6, top: 4), decoration: const BoxDecoration(color: AppColors.brand, shape: BoxShape.circle)),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      notification.message,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12, color: AppColors.neutral),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      timeAgoLabel(notification.createdAt),
                      style: const TextStyle(fontSize: 10, color: AppColors.neutral, fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
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
            Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.neutral)),
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
