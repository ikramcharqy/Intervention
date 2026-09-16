import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:mobile_technicien/features/profile/domain/entities/profile_data.dart';
import 'package:mobile_technicien/features/profile/presentation/bloc/profile_bloc.dart';
import 'package:mobile_technicien/features/profile/presentation/pages/parametres_screen.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Onglet "Profil" — Étape 3 : section principale (avatar/initiales, nom,
/// email, téléphone) + statistiques déjà exposées par
/// Api\ProfileController::show() (pas recalculées côté app), et accès à
/// l'écran Paramètres. La note de satisfaction client moyenne n'existe pas
/// côté backend (aucun champ agrégé) — volontairement absente plutôt
/// qu'inventée.
class ProfilePage extends StatelessWidget {
  const ProfilePage({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<ProfileBloc>()..add(const ProfileRequested()),
      child: const _ProfileView(),
    );
  }
}

class _ProfileView extends StatelessWidget {
  const _ProfileView();

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: BlocBuilder<ProfileBloc, ProfileState>(
        builder: (context, state) {
          return RefreshIndicator(
            onRefresh: () async => context.read<ProfileBloc>().add(const ProfileRequested()),
            child: ListView(
              padding: const EdgeInsets.all(20),
              children: [
                switch (state) {
                  ProfileLoading() => const Padding(
                      padding: EdgeInsets.symmetric(vertical: 40),
                      child: Center(child: CircularProgressIndicator()),
                    ),
                  ProfileError(:final message) => _ErrorHeader(message: message),
                  ProfileLoaded(:final data) => _ProfileHeader(data: data),
                },
                const SizedBox(height: 24),
                if (state is ProfileLoaded) ...[
                  _StatsSection(stats: state.data.stats),
                  const SizedBox(height: 24),
                ],
                _ProfileTile(
                  icon: Icons.settings_outlined,
                  label: 'Paramètres',
                  onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ParametresScreen())),
                ),
                const SizedBox(height: 8),
                _ProfileTile(
                  icon: Icons.logout,
                  label: 'Se déconnecter',
                  color: AppColors.danger,
                  onTap: () => context.read<AuthBloc>().add(const AuthLogoutRequested()),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

class _ProfileHeader extends StatelessWidget {
  final ProfileData data;
  const _ProfileHeader({required this.data});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CircleAvatar(
          radius: 28,
          backgroundColor: AppColors.brandLight,
          backgroundImage: data.photoUrl != null ? NetworkImage(data.photoUrl!) : null,
          child: data.photoUrl == null
              ? Text(data.initiales, style: const TextStyle(color: AppColors.brand, fontWeight: FontWeight.bold, fontSize: 18))
              : null,
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(data.displayName, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold)),
              Text(data.email, style: const TextStyle(color: AppColors.neutral, fontSize: 13)),
              if (data.telephone != null && data.telephone!.isNotEmpty) ...[
                const SizedBox(height: 2),
                Row(
                  children: [
                    const Icon(Icons.phone_outlined, size: 13, color: AppColors.neutral),
                    const SizedBox(width: 4),
                    Text(data.telephone!, style: const TextStyle(color: AppColors.neutral, fontSize: 12)),
                  ],
                ),
              ],
            ],
          ),
        ),
      ],
    );
  }
}

class _ErrorHeader extends StatelessWidget {
  final String message;
  const _ErrorHeader({required this.message});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const CircleAvatar(
          radius: 28,
          backgroundColor: AppColors.brandLight,
          child: Icon(Icons.person, color: AppColors.brand, size: 28),
        ),
        const SizedBox(width: 14),
        Expanded(child: Text(message, style: const TextStyle(color: AppColors.danger, fontSize: 13))),
      ],
    );
  }
}

class _StatsSection extends StatelessWidget {
  final ProfileStats stats;
  const _StatsSection({required this.stats});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Mes statistiques', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(child: _StatCard(label: 'Terminées', value: '${stats.terminees}', color: AppColors.success)),
            const SizedBox(width: 10),
            Expanded(child: _StatCard(label: 'En cours', value: '${stats.enCours}', color: AppColors.info)),
            const SizedBox(width: 10),
            Expanded(child: _StatCard(label: 'Total', value: '${stats.totalInterventions}', color: AppColors.brand)),
          ],
        ),
      ],
    );
  }
}

class _StatCard extends StatelessWidget {
  final String label;
  final String value;
  final Color color;
  const _StatCard({required this.label, required this.value, required this.color});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 8),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: const Color(0xFFE6E9F4))),
      child: Column(
        children: [
          Text(value, style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: color)),
          const SizedBox(height: 2),
          Text(label, style: const TextStyle(fontSize: 11, color: AppColors.neutral), textAlign: TextAlign.center),
        ],
      ),
    );
  }
}

class _ProfileTile extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color? color;
  final VoidCallback onTap;
  const _ProfileTile({required this.icon, required this.label, required this.onTap, this.color});

  @override
  Widget build(BuildContext context) {
    final tileColor = color ?? AppColors.neutral;
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: const Color(0xFFE6E9F4)),
          ),
          child: Row(
            children: [
              Icon(icon, color: tileColor, size: 20),
              const SizedBox(width: 12),
              Text(label, style: TextStyle(color: tileColor, fontWeight: FontWeight.w600)),
              const Spacer(),
              const Icon(Icons.chevron_right, color: AppColors.neutral, size: 18),
            ],
          ),
        ),
      ),
    );
  }
}
