import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/home/presentation/bloc/home_bloc.dart';
import 'package:mobile_technicien/features/home/presentation/pages/home_page.dart';
import 'package:mobile_technicien/features/home/presentation/pages/qr_scan_page.dart';
import 'package:mobile_technicien/features/missions/presentation/pages/missions_page.dart';
import 'package:mobile_technicien/features/notifications/presentation/pages/notifications_page.dart';
import 'package:mobile_technicien/features/shell/presentation/pages/profile_page.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Coquille commune aux 4 onglets (Accueil, Missions, Notifs, Profil) : sans
/// elle, la barre de navigation basse de la maquette n'existait nulle part
/// dans le code — HomePage rendait directement son propre Scaffold sans
/// aucune BottomNavigationBar. Les onglets Missions/Notifs restent des
/// écrans "à venir" (aucune donnée/bloc dédié n'existe encore côté API pour
/// eux), Profil regroupe les réglages et la déconnexion.
class MainShell extends StatefulWidget {
  const MainShell({super.key});

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  int _index = 0;

  @override
  Widget build(BuildContext context) {
    final tabs = [
      HomePage(
        onSeeAllMissions: () => setState(() => _index = 1),
        onOpenNotifications: () => setState(() => _index = 2),
      ),
      const MissionsPage(),
      const NotificationsPage(),
      const ProfilePage(),
    ];

    // HomeBloc vit ici (au-dessus de HomePage) car la barre de navigation a
    // elle aussi besoin du compteur "non lues" pour son propre badge sur
    // l'onglet Notifs (Étape 7.1), pas seulement la cloche d'en-tête.
    return BlocProvider(
      create: (_) => sl<HomeBloc>()..add(const HomeRequested()),
      child: Scaffold(
        backgroundColor: AppColors.background,
        body: IndexedStack(index: _index, children: tabs),
        floatingActionButton: _index == 0
            ? FloatingActionButton(
                backgroundColor: AppColors.brand,
                onPressed: () async {
                  final code = await Navigator.of(context).push<String>(
                    MaterialPageRoute(builder: (_) => const QrScanPage()),
                  );
                  if (code != null && context.mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('QR scanné : $code')),
                    );
                  }
                },
                child: const Icon(Icons.qr_code_scanner, color: Colors.white),
              )
            : null,
        bottomNavigationBar: BlocBuilder<HomeBloc, HomeState>(
          builder: (context, state) {
            final unread = state is HomeLoaded ? state.summary.notificationsNonLues : 0;
            return NavigationBar(
              selectedIndex: _index,
              onDestinationSelected: (i) => setState(() => _index = i),
              destinations: [
                const NavigationDestination(
                    icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Accueil'),
                const NavigationDestination(
                    icon: Icon(Icons.assignment_outlined),
                    selectedIcon: Icon(Icons.assignment),
                    label: 'Missions'),
                NavigationDestination(
                  icon: Badge(
                    isLabelVisible: unread > 0,
                    label: Text('$unread'),
                    child: const Icon(Icons.notifications_outlined),
                  ),
                  selectedIcon: Badge(
                    isLabelVisible: unread > 0,
                    label: Text('$unread'),
                    child: const Icon(Icons.notifications),
                  ),
                  label: 'Notifs',
                ),
                const NavigationDestination(
                    icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Profil'),
              ],
            );
          },
        ),
      ),
    );
  }
}
