part of 'notifications_bloc.dart';

sealed class NotificationsState extends Equatable {
  const NotificationsState();

  @override
  List<Object?> get props => [];
}

class NotificationsLoading extends NotificationsState {
  const NotificationsLoading();
}

class NotificationsError extends NotificationsState {
  final String message;
  const NotificationsError(this.message);

  @override
  List<Object?> get props => [message];
}

class NotificationsLoaded extends NotificationsState {
  final List<AppNotification> notifications;
  final int currentPage;
  final int lastPage;
  final int unreadCount;
  final bool loadingMore;

  const NotificationsLoaded({
    required this.notifications,
    required this.currentPage,
    required this.lastPage,
    required this.unreadCount,
    this.loadingMore = false,
  });

  bool get aPlus => currentPage < lastPage;

  NotificationsLoaded copyWith({
    List<AppNotification>? notifications,
    int? currentPage,
    int? lastPage,
    int? unreadCount,
    bool? loadingMore,
  }) {
    return NotificationsLoaded(
      notifications: notifications ?? this.notifications,
      currentPage: currentPage ?? this.currentPage,
      lastPage: lastPage ?? this.lastPage,
      unreadCount: unreadCount ?? this.unreadCount,
      loadingMore: loadingMore ?? this.loadingMore,
    );
  }

  @override
  List<Object?> get props => [notifications, currentPage, lastPage, unreadCount, loadingMore];
}
