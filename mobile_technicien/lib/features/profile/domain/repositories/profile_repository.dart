import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/profile/domain/entities/profile_data.dart';
import 'package:mobile_technicien/features/profile/domain/entities/security_entities.dart';

abstract class ProfileRepository {
  Future<Result<ProfileData>> getProfile();

  Future<Result<void>> updatePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmation,
  });

  Future<Result<void>> updateNotificationPreferences({required bool interventionUpdates});

  Future<Result<List<LoginHistoryEntry>>> getLoginHistory();

  Future<Result<List<ActiveSession>>> getSessions();

  Future<Result<void>> revokeSession(int tokenId);
}
