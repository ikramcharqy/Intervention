import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/notifications/domain/repositories/notifications_repository.dart';

class MarkAllNotificationsReadUsecase {
  final NotificationsRepository repository;
  MarkAllNotificationsReadUsecase(this.repository);

  Future<Result<void>> call() => repository.markAllAsRead();
}
