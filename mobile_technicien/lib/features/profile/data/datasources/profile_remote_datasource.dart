import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/features/profile/data/models/profile_data_model.dart';
import 'package:mobile_technicien/features/profile/data/models/security_entities_model.dart';

class ProfileRemoteDatasource {
  final ApiClient apiClient;
  ProfileRemoteDatasource(this.apiClient);

  Future<ProfileDataModel> getProfile() async {
    final response = await apiClient.dio.get('/profile');
    return ProfileDataModel.fromJson(response.data['data'] as Map<String, dynamic>);
  }

  Future<void> updatePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmation,
  }) async {
    await apiClient.dio.put('/profile/password', data: {
      'current_password': currentPassword,
      'password': newPassword,
      'password_confirmation': confirmation,
    });
  }

  Future<void> updateNotificationPreferences({required bool interventionUpdates}) async {
    await apiClient.dio.put('/profile/notification-preferences', data: {
      'intervention_updates': interventionUpdates,
    });
  }

  Future<List<LoginHistoryEntryModel>> getLoginHistory() async {
    final response = await apiClient.dio.get('/profile/login-history');
    final items = response.data['data'] as List<dynamic>;
    return items.map((e) => LoginHistoryEntryModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<List<ActiveSessionModel>> getSessions() async {
    final response = await apiClient.dio.get('/profile/sessions');
    final items = response.data['data'] as List<dynamic>;
    return items.map((e) => ActiveSessionModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<void> revokeSession(int tokenId) async {
    await apiClient.dio.delete('/profile/sessions/$tokenId');
  }
}
