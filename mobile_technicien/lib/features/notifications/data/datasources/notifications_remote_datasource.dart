import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/notifications/data/models/app_notification_model.dart';

class NotificationsPageResult {
  final List<AppNotificationModel> notifications;
  final int currentPage;
  final int lastPage;
  final int unreadCount;

  const NotificationsPageResult({
    required this.notifications,
    required this.currentPage,
    required this.lastPage,
    required this.unreadCount,
  });
}

class NotificationsRemoteDatasource {
  final ApiClient apiClient;
  NotificationsRemoteDatasource(this.apiClient);

  /// GET /api/notifications (Api\NotificationController::index) — pagination
  /// standard Laravel (`{current_page, data: [...], last_page, ...}`), déjà
  /// utilisée telle quelle, pas un second format inventé côté app.
  Future<NotificationsPageResult> getNotifications({int page = 1, int perPage = 20}) async {
    final response = await apiClient.dio.get(
      '/notifications',
      queryParameters: {'page': page, 'per_page': perPage},
    );
    final data = response.data['data'] as Map<String, dynamic>;
    final paginator = data['notifications'] as Map<String, dynamic>;
    final items = paginator['data'] as List<dynamic>;

    return NotificationsPageResult(
      notifications: items.map((n) => AppNotificationModel.fromJson(n as Map<String, dynamic>)).toList(),
      currentPage: paginator['current_page'] as int? ?? 1,
      lastPage: paginator['last_page'] as int? ?? 1,
      unreadCount: data['unread_count'] as int? ?? 0,
    );
  }

  /// POST /api/notifications/{id}/read
  Future<void> markAsRead(String id) async {
    await apiClient.dio.post('/notifications/$id/read');
  }

  /// POST /api/notifications/read-all
  Future<void> markAllAsRead() async {
    await apiClient.dio.post('/notifications/read-all');
  }
}
