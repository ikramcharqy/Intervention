import 'package:flutter/material.dart';
import 'package:mobile_technicien/core/services/biometric_lock_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Étape 5.1 : verrouillage biométrique (ou PIN de repli) affiché à chaque
/// ouverture/reprise de l'app, après une session Sanctum déjà valide — c'est
/// une couche supplémentaire locale, pas un remplacement de l'authentification
/// serveur.
class LockPage extends StatefulWidget {
  final BiometricLockService lockService;
  final VoidCallback onUnlocked;

  const LockPage({super.key, required this.lockService, required this.onUnlocked});

  @override
  State<LockPage> createState() => _LockPageState();
}

class _LockPageState extends State<LockPage> {
  bool _biometricAvailable = false;
  bool _checking = true;
  bool _showPinFallback = false;
  String? _pinError;
  final _pinController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _bootstrap();
  }

  Future<void> _bootstrap() async {
    _biometricAvailable = await widget.lockService.isBiometricAvailable();
    setState(() => _checking = false);
    if (_biometricAvailable) {
      _tryBiometric();
    } else {
      setState(() => _showPinFallback = true);
    }
  }

  Future<void> _tryBiometric() async {
    final ok = await widget.lockService.authenticateWithBiometrics();
    if (ok) {
      widget.onUnlocked();
    } else {
      setState(() => _showPinFallback = true);
    }
  }

  Future<void> _verifyPin() async {
    final hasPin = await widget.lockService.hasFallbackPin();
    if (!hasPin) {
      // Premier lancement : on définit le PIN de repli plutôt que de bloquer
      // l'utilisateur sans biométrie disponible.
      await widget.lockService.setFallbackPin(_pinController.text);
      widget.onUnlocked();
      return;
    }
    final ok = await widget.lockService.verifyFallbackPin(_pinController.text);
    if (ok) {
      widget.onUnlocked();
    } else {
      setState(() => _pinError = 'Code incorrect');
    }
  }

  @override
  void dispose() {
    _pinController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (_checking) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.lock_outline, size: 48, color: AppColors.brand),
              const SizedBox(height: 16),
              const Text('Application verrouillée', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              const Text(
                'Déverrouillez pour accéder aux données de vos interventions.',
                textAlign: TextAlign.center,
                style: TextStyle(color: AppColors.neutral),
              ),
              const SizedBox(height: 24),
              if (_biometricAvailable)
                OutlinedButton.icon(
                  onPressed: _tryBiometric,
                  icon: const Icon(Icons.fingerprint),
                  label: const Text('Réessayer la biométrie'),
                ),
              if (_showPinFallback) ...[
                const SizedBox(height: 16),
                TextField(
                  controller: _pinController,
                  keyboardType: TextInputType.number,
                  obscureText: true,
                  maxLength: 6,
                  decoration: InputDecoration(
                    labelText: 'Code PIN',
                    border: const OutlineInputBorder(),
                    errorText: _pinError,
                  ),
                ),
                FilledButton(
                  style: FilledButton.styleFrom(backgroundColor: AppColors.brand),
                  onPressed: _verifyPin,
                  child: const Text('Valider'),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
