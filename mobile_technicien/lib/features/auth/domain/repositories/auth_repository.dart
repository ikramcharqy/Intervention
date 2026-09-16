import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';

abstract class AuthRepository {
  Future<Result<Technicien>> login({required String email, required String password});
  Future<Result<Technicien>> getCurrentUser();
  Future<Result<void>> logout();
  Future<bool> isAuthenticated();
}
