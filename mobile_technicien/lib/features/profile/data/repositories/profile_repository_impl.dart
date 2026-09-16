import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/profile/data/datasources/profile_remote_datasource.dart';
import 'package:mobile_technicien/features/profile/domain/entities/profile_data.dart';
import 'package:mobile_technicien/features/profile/domain/entities/security_entities.dart';
import 'package:mobile_technicien/features/profile/domain/repositories/profile_repository.dart';

class ProfileRepositoryImpl implements ProfileRepository {
  final ProfileRemoteDatasource remote;
  ProfileRepositoryImpl(this.remote);

  @override
  Future<Result<ProfileData>> getProfile() async {
    try {
      return Success(await remote.getProfile());
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> updatePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmation,
  }) async {
    try {
      await remote.updatePassword(currentPassword: currentPassword, newPassword: newPassword, confirmation: confirmation);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> updateNotificationPreferences({required bool interventionUpdates}) async {
    try {
      await remote.updateNotificationPreferences(interventionUpdates: interventionUpdates);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<List<LoginHistoryEntry>>> getLoginHistory() async {
    try {
      return Success(await remote.getLoginHistory());
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<List<ActiveSession>>> getSessions() async {
    try {
      return Success(await remote.getSessions());
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> revokeSession(int tokenId) async {
    try {
      await remote.revokeSession(tokenId);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
