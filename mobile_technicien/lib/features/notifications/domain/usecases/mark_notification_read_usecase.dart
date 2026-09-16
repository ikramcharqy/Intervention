import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/notifications/domain/repositories/notifications_repository.dart';

class MarkNotificationReadUsecase {
  final NotificationsRepository repository;
  MarkNotificationReadUsecase(this.repository);

  Future<Result<void>> call(String id) => repository.markAsRead(id);
}
