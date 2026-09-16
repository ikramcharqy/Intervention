import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/notifications/domain/entities/notifications_page.dart';
import 'package:mobile_technicien/features/notifications/domain/repositories/notifications_repository.dart';

class GetNotificationsUsecase {
  final NotificationsRepository repository;
  GetNotificationsUsecase(this.repository);

  Future<Result<NotificationsPage>> call({int page = 1}) => repository.getNotifications(page: page);
}
