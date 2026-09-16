import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/profile/domain/entities/profile_data.dart';
import 'package:mobile_technicien/features/profile/domain/entities/security_entities.dart';
import 'package:mobile_technicien/features/profile/domain/repositories/profile_repository.dart';

class GetProfileUsecase {
  final ProfileRepository repository;
  GetProfileUsecase(this.repository);
  Future<Result<ProfileData>> call() => repository.getProfile();
}

class UpdatePasswordUsecase {
  final ProfileRepository repository;
  UpdatePasswordUsecase(this.repository);
  Future<Result<void>> call({required String currentPassword, required String newPassword, required String confirmation}) =>
      repository.updatePassword(currentPassword: currentPassword, newPassword: newPassword, confirmation: confirmation);
}

class UpdateNotificationPreferencesUsecase {
  final ProfileRepository repository;
  UpdateNotificationPreferencesUsecase(this.repository);
  Future<Result<void>> call({required bool interventionUpdates}) =>
      repository.updateNotificationPreferences(interventionUpdates: interventionUpdates);
}

class GetLoginHistoryUsecase {
  final ProfileRepository repository;
  GetLoginHistoryUsecase(this.repository);
  Future<Result<List<LoginHistoryEntry>>> call() => repository.getLoginHistory();
}

class GetSessionsUsecase {
  final ProfileRepository repository;
  GetSessionsUsecase(this.repository);
  Future<Result<List<ActiveSession>>> call() => repository.getSessions();
}

class RevokeSessionUsecase {
  final ProfileRepository repository;
  RevokeSessionUsecase(this.repository);
  Future<Result<void>> call(int tokenId) => repository.revokeSession(tokenId);
}
