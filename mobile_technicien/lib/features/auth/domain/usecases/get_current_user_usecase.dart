import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';

class GetCurrentUserUsecase {
  final AuthRepository repository;
  GetCurrentUserUsecase(this.repository);

  Future<Result<Technicien>> call() => repository.getCurrentUser();
}
