import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';

class LogoutUsecase {
  final AuthRepository repository;
  LogoutUsecase(this.repository);

  Future<Result<void>> call() => repository.logout();
}
