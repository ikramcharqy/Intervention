import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/notifications/domain/entities/app_notification.dart';
import 'package:mobile_technicien/features/notifications/domain/usecases/get_notifications_usecase.dart';
import 'package:mobile_technicien/features/notifications/domain/usecases/mark_all_notifications_read_usecase.dart';
import 'package:mobile_technicien/features/notifications/domain/usecases/mark_notification_read_usecase.dart';

part 'notifications_event.dart';
part 'notifications_state.dart';

class NotificationsBloc extends Bloc<NotificationsEvent, NotificationsState> {
  final GetNotificationsUsecase getNotificationsUsecase;
  final MarkNotificationReadUsecase markNotificationReadUsecase;
  final MarkAllNotificationsReadUsecase markAllNotificationsReadUsecase;

  NotificationsBloc({
    required this.getNotificationsUsecase,
    required this.markNotificationReadUsecase,
    required this.markAllNotificationsReadUsecase,
  }) : super(const NotificationsLoading()) {
    on<NotificationsRequested>(_onRequested);
    on<NotificationsMoreRequested>(_onMoreRequested);
    on<NotificationMarkReadRequested>(_onMarkRead);
    on<NotificationsMarkAllReadRequested>(_onMarkAllRead);
  }

  Future<void> _onRequested(NotificationsRequested event, Emitter<NotificationsState> emit) async {
    emit(const NotificationsLoading());
    final result = await getNotificationsUsecase(page: 1);
    result.when(
      success: (page) => emit(NotificationsLoaded(
        notifications: page.notifications,
        currentPage: page.currentPage,
        lastPage: page.lastPage,
        unreadCount: page.unreadCount,
      )),
      failure: (f) => emit(NotificationsError(f.message)),
    );
  }

  Future<void> _onMoreRequested(NotificationsMoreRequested event, Emitter<NotificationsState> emit) async {
    final current = state;
    if (current is! NotificationsLoaded || !current.aPlus || current.loadingMore) return;

    emit(current.copyWith(loadingMore: true));
    final result = await getNotificationsUsecase(page: current.currentPage + 1);
    result.when(
      success: (page) => emit(current.copyWith(
        notifications: [...current.notifications, ...page.notifications],
        currentPage: page.currentPage,
        lastPage: page.lastPage,
        unreadCount: page.unreadCount,
        loadingMore: false,
      )),
      // Échec de pagination silencieux (garde la liste déjà chargée plutôt
      // que de basculer tout l'écran en erreur) — l'utilisateur peut
      // retenter en refaisant défiler.
      failure: (f) => emit(current.copyWith(loadingMore: false)),
    );
  }

  Future<void> _onMarkRead(NotificationMarkReadRequested event, Emitter<NotificationsState> emit) async {
    final current = state;
    if (current is! NotificationsLoaded) return;

    final index = current.notifications.indexWhere((n) => n.id == event.id);
    if (index == -1 || current.notifications[index].estLue) return;

    // Mise à jour optimiste locale — l'écran ne doit pas attendre l'aller-retour
    // réseau pour refléter le tap, cohérent avec le reste de l'app.
    final updated = List<AppNotification>.from(current.notifications);
    final notif = updated[index];
    updated[index] = AppNotification(
      id: notif.id,
      type: notif.type,
      titre: notif.titre,
      message: notif.message,
      createdAt: notif.createdAt,
      readAt: DateTime.now(),
      interventionId: notif.interventionId,
      codeIntervention: notif.codeIntervention,
    );

    emit(current.copyWith(notifications: updated, unreadCount: (current.unreadCount - 1).clamp(0, 1 << 30)));
    await markNotificationReadUsecase(event.id);
  }

  Future<void> _onMarkAllRead(NotificationsMarkAllReadRequested event, Emitter<NotificationsState> emit) async {
    final current = state;
    if (current is! NotificationsLoaded) return;

    final updated = current.notifications
        .map((n) => n.estLue
            ? n
            : AppNotification(
                id: n.id,
                type: n.type,
                titre: n.titre,
                message: n.message,
                createdAt: n.createdAt,
                readAt: DateTime.now(),
                interventionId: n.interventionId,
                codeIntervention: n.codeIntervention,
              ))
        .toList();

    emit(current.copyWith(notifications: updated, unreadCount: 0));
    await markAllNotificationsReadUsecase();
  }
}
