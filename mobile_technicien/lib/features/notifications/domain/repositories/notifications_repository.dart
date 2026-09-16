import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/notifications/domain/entities/notifications_page.dart';

abstract class NotificationsRepository {
  Future<Result<NotificationsPage>> getNotifications({int page = 1});
  Future<Result<void>> markAsRead(String id);
  Future<Result<void>> markAllAsRead();
}
