import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/notifications/data/datasources/notifications_remote_datasource.dart';
import 'package:mobile_technicien/features/notifications/domain/entities/notifications_page.dart';
import 'package:mobile_technicien/features/notifications/domain/repositories/notifications_repository.dart';

class NotificationsRepositoryImpl implements NotificationsRepository {
  final NotificationsRemoteDatasource remote;
  NotificationsRepositoryImpl(this.remote);

  @override
  Future<Result<NotificationsPage>> getNotifications({int page = 1}) async {
    try {
      final result = await remote.getNotifications(page: page);
      return Success(NotificationsPage(
        notifications: result.notifications,
        currentPage: result.currentPage,
        lastPage: result.lastPage,
        unreadCount: result.unreadCount,
      ));
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> markAsRead(String id) async {
    try {
      await remote.markAsRead(id);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> markAllAsRead() async {
    try {
      await remote.markAllAsRead();
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
