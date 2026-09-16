import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/storage/secure_storage_service.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/auth/data/datasources/auth_remote_datasource.dart';
import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';

class AuthRepositoryImpl implements AuthRepository {
  final AuthRemoteDatasource remote;
  final SecureStorageService storage;

  AuthRepositoryImpl({required this.remote, required this.storage});

  @override
  Future<Result<Technicien>> login({required String email, required String password}) async {
    try {
      final result = await remote.login(email: email, password: password);
      await storage.saveToken(result.token);
      return Success(result.user);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<Technicien>> getCurrentUser() async {
    try {
      final user = await remote.me();
      return Success(user);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> logout() async {
    try {
      await remote.logout();
    } on DioException {
      // Voir commentaire dans AuthRemoteDatasource.logout : non bloquant.
    } finally {
      await storage.deleteToken();
    }
    return const Success(null);
  }

  @override
  Future<bool> isAuthenticated() => storage.hasToken();
}
