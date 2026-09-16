import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';

class LoginUsecase {
  final AuthRepository repository;
  LoginUsecase(this.repository);

  Future<Result<Technicien>> call({required String email, required String password}) {
    return repository.login(email: email, password: password);
  }
}
