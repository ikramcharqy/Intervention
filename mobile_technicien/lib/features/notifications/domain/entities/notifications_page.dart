import 'package:equatable/equatable.dart';
import 'package:mobile_technicien/features/notifications/domain/entities/app_notification.dart';

class NotificationsPage extends Equatable {
  final List<AppNotification> notifications;
  final int currentPage;
  final int lastPage;
  final int unreadCount;

  const NotificationsPage({
    required this.notifications,
    required this.currentPage,
    required this.lastPage,
    required this.unreadCount,
  });

  bool get aPlus => currentPage < lastPage;

  @override
  List<Object?> get props => [notifications, currentPage, lastPage, unreadCount];
}
