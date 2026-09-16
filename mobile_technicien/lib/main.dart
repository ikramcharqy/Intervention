import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/services/biometric_lock_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:mobile_technicien/features/auth/presentation/pages/lock_page.dart';
import 'package:mobile_technicien/features/auth/presentation/pages/login_page.dart';
import 'package:mobile_technicien/features/shell/presentation/pages/main_shell.dart';
import 'package:mobile_technicien/injection_container.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initDependencies();
  runApp(const TechniTrackTechnicienApp());
}

class TechniTrackTechnicienApp extends StatelessWidget {
  const TechniTrackTechnicienApp({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<AuthBloc>()..add(const AuthCheckRequested()),
      child: MaterialApp(
        title: 'TechniTrack Technicien',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.light(),
        home: const _RootRouter(),
      ),
    );
  }
}

/// Étape 5.1 : un utilisateur déjà authentifié (token Sanctum valide) passe
/// systématiquement par le verrouillage biométrique/PIN avant d'atteindre
/// l'accueil, à chaque démarrage de l'app.
class _RootRouter extends StatefulWidget {
  const _RootRouter();

  @override
  State<_RootRouter> createState() => _RootRouterState();
}

class _RootRouterState extends State<_RootRouter> {
  // Sur le web, ce verrouillage n'a pas de sens (pas de biométrie, usage
  // "aperçu design" en développement, pas terrain) : on démarre déverrouillé.
  bool _unlocked = kIsWeb;

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthBloc, AuthState>(
      builder: (context, state) {
        if (state is AuthLoading || state is AuthInitial) {
          return const Scaffold(body: Center(child: CircularProgressIndicator()));
        }

        if (state is AuthAuthenticated) {
          if (!_unlocked) {
            return LockPage(
              lockService: sl<BiometricLockService>(),
              onUnlocked: () => setState(() => _unlocked = true),
            );
          }
          return const MainShell();
        }

        return const LoginPage();
      },
    );
  }
}
