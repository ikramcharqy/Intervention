import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Persistance du jeton Sanctum (`personal_access_tokens`, créé par
/// AuthController::login côté Laravel) — Keychain/Keystore natif, jamais
/// SharedPreferences en clair pour un secret d'authentification.
class SecureStorageService {
  final FlutterSecureStorage _storage;

  SecureStorageService({FlutterSecureStorage? storage})
      : _storage = storage ?? const FlutterSecureStorage();

  static const _tokenKey = 'sanctum_token';

  Future<void> saveToken(String token) => _storage.write(key: _tokenKey, value: token);

  Future<String?> readToken() => _storage.read(key: _tokenKey);

  Future<void> deleteToken() => _storage.delete(key: _tokenKey);

  Future<bool> hasToken() async => (await readToken()) != null;
}
