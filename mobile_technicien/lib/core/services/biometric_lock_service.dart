import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:local_auth/local_auth.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Étape 5.1 : verrouillage biométrique (Face ID/empreinte) avec code PIN de
/// repli, requis à l'ouverture de l'app en plus du verrouillage du téléphone
/// — cohérent avec la sensibilité des données clients manipulées (adresses de
/// chantiers, contacts).
class BiometricLockService {
  final LocalAuthentication _auth;
  final FlutterSecureStorage _storage;

  BiometricLockService({LocalAuthentication? auth, FlutterSecureStorage? storage})
      : _auth = auth ?? LocalAuthentication(),
        _storage = storage ?? const FlutterSecureStorage();

  static const _pinKey = 'fallback_pin';

  /// local_auth n'a aucune implémentation web (federated plugin sans package
  /// `_web`) : tout appel à ses méthodes sur le web lève une
  /// MissingPluginException. La biométrie n'a de toute façon de sens que sur
  /// un vrai appareil, donc on la désactive purement et simplement sur web.
  Future<bool> isBiometricAvailable() async {
    if (kIsWeb) return false;
    final canCheck = await _auth.canCheckBiometrics;
    final isSupported = await _auth.isDeviceSupported();
    return canCheck && isSupported;
  }

  Future<bool> authenticateWithBiometrics() async {
    if (kIsWeb) return false;
    try {
      return await _auth.authenticate(
        localizedReason: 'Authentifiez-vous pour accéder à TechniTrack',
        options: const AuthenticationOptions(
          biometricOnly: true,
          stickyAuth: true,
        ),
      );
    } catch (_) {
      return false;
    }
  }

  Future<bool> hasFallbackPin() async => (await _storage.read(key: _pinKey)) != null;

  Future<void> setFallbackPin(String pin) => _storage.write(key: _pinKey, value: pin);

  Future<bool> verifyFallbackPin(String pin) async {
    final stored = await _storage.read(key: _pinKey);
    return stored != null && stored == pin;
  }
}
