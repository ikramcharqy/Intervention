import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/features/profile/domain/entities/security_entities.dart';
import 'package:mobile_technicien/features/profile/presentation/bloc/security_settings_bloc.dart';
import 'package:mobile_technicien/injection_container.dart';

/// Étape 4 — Paramètres : changement de mot de passe (même flux que côté
/// web), Sécurité (historique de connexion + sessions actives avec
/// révocation), préférence de notification. Pas de 2FA : non obligatoire
/// pour le rôle Technicien, laissé hors scope comme demandé.
class ParametresScreen extends StatelessWidget {
  const ParametresScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<SecuritySettingsBloc>()..add(const SecuritySettingsRequested()),
      child: Scaffold(
        backgroundColor: const Color(0xFFF6F7FB),
        appBar: AppBar(title: const Text('Paramètres')),
        body: const _ParametresBody(),
      ),
    );
  }
}

class _ParametresBody extends StatelessWidget {
  const _ParametresBody();

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<SecuritySettingsBloc, SecuritySettingsState>(
      listener: (context, state) {
        if (state is SecuritySettingsLoaded && state.passwordChangeStatus == PasswordChangeStatus.success) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Mot de passe mis à jour avec succès.'), backgroundColor: AppColors.success),
          );
        }
      },
      builder: (context, state) {
        return switch (state) {
          SecuritySettingsLoading() => const Center(child: CircularProgressIndicator()),
          SecuritySettingsError(:final message) => Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Text(message, style: const TextStyle(color: AppColors.danger)),
              ),
            ),
          SecuritySettingsLoaded() => RefreshIndicator(
              onRefresh: () async => context.read<SecuritySettingsBloc>().add(const SecuritySettingsRequested()),
              child: ListView(
                padding: const EdgeInsets.all(20),
                children: [
                  const _SectionTitle('Mot de passe'),
                  _PasswordChangeCard(state: state),
                  const SizedBox(height: 24),
                  const _SectionTitle('Notifications'),
                  _NotificationPrefCard(state: state),
                  const SizedBox(height: 24),
                  const _SectionTitle('Sessions actives'),
                  _SessionsCard(state: state),
                  const SizedBox(height: 24),
                  const _SectionTitle('Historique de connexion'),
                  _LoginHistoryCard(history: state.loginHistory),
                  const SizedBox(height: 24),
                  Center(
                    // Version déclarée dans pubspec.yaml — pas de plugin
                    // package_info_plus dans le projet, valeur affichée telle
                    // quelle plutôt que d'ajouter une dépendance pour un simple
                    // libellé statique.
                    child: Text('Version 0.1.0', style: TextStyle(color: AppColors.neutral.withValues(alpha: 0.7), fontSize: 11)),
                  ),
                ],
              ),
            ),
        };
      },
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String text;
  const _SectionTitle(this.text);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Text(text, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
    );
  }
}

class _Card extends StatelessWidget {
  final Widget child;
  const _Card({required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: child,
    );
  }
}

class _PasswordChangeCard extends StatefulWidget {
  final SecuritySettingsLoaded state;
  const _PasswordChangeCard({required this.state});

  @override
  State<_PasswordChangeCard> createState() => _PasswordChangeCardState();
}

class _PasswordChangeCardState extends State<_PasswordChangeCard> {
  final _formKey = GlobalKey<FormState>();
  final _currentCtrl = TextEditingController();
  final _newCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();
  bool _obscure = true;

  @override
  void dispose() {
    _currentCtrl.dispose();
    _newCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  void _submit() {
    if (!_formKey.currentState!.validate()) return;
    context.read<SecuritySettingsBloc>().add(PasswordChangeSubmitted(
          currentPassword: _currentCtrl.text,
          newPassword: _newCtrl.text,
          confirmation: _confirmCtrl.text,
        ));
  }

  @override
  void didUpdateWidget(covariant _PasswordChangeCard oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.state.passwordChangeStatus == PasswordChangeStatus.success &&
        oldWidget.state.passwordChangeStatus != PasswordChangeStatus.success) {
      _currentCtrl.clear();
      _newCtrl.clear();
      _confirmCtrl.clear();
    }
  }

  @override
  Widget build(BuildContext context) {
    final submitting = widget.state.passwordChangeStatus == PasswordChangeStatus.submitting;
    return _Card(
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            TextFormField(
              controller: _currentCtrl,
              obscureText: _obscure,
              decoration: const InputDecoration(labelText: 'Mot de passe actuel'),
              validator: (v) => (v == null || v.isEmpty) ? 'Champ requis' : null,
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _newCtrl,
              obscureText: _obscure,
              decoration: const InputDecoration(labelText: 'Nouveau mot de passe'),
              validator: (v) => (v == null || v.length < 8) ? 'Au moins 8 caractères' : null,
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _confirmCtrl,
              obscureText: _obscure,
              decoration: const InputDecoration(labelText: 'Confirmation'),
              validator: (v) => v != _newCtrl.text ? 'Ne correspond pas' : null,
            ),
            Row(
              children: [
                Checkbox(value: !_obscure, onChanged: (v) => setState(() => _obscure = !(v ?? false))),
                const Text('Afficher les mots de passe', style: TextStyle(fontSize: 12)),
              ],
            ),
            if (widget.state.passwordChangeStatus == PasswordChangeStatus.error && widget.state.passwordChangeError != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: Text(widget.state.passwordChangeError!, style: const TextStyle(color: AppColors.danger, fontSize: 12)),
              ),
            SizedBox(
              height: 46,
              child: ElevatedButton(
                onPressed: submitting ? null : _submit,
                child: submitting
                    ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Text('Mettre à jour le mot de passe'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _NotificationPrefCard extends StatelessWidget {
  final SecuritySettingsLoaded state;
  const _NotificationPrefCard({required this.state});

  @override
  Widget build(BuildContext context) {
    return _Card(
      child: Row(
        children: [
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Notifications de mission', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                SizedBox(height: 2),
                Text(
                  'Nouvelles missions, mises à jour et rappels liés à vos interventions.',
                  style: TextStyle(color: AppColors.neutral, fontSize: 12),
                ),
              ],
            ),
          ),
          Switch(
            value: state.notificationInterventionUpdates,
            onChanged: (v) => context.read<SecuritySettingsBloc>().add(NotificationPreferenceToggled(v)),
          ),
        ],
      ),
    );
  }
}

class _SessionsCard extends StatelessWidget {
  final SecuritySettingsLoaded state;
  const _SessionsCard({required this.state});

  @override
  Widget build(BuildContext context) {
    if (state.sessions.isEmpty) {
      return const _Card(child: Text('Aucune session active.', style: TextStyle(color: AppColors.neutral, fontSize: 13)));
    }
    return _Card(
      child: Column(
        children: [
          for (var i = 0; i < state.sessions.length; i++) ...[
            if (i > 0) const Divider(height: 20),
            _SessionRow(session: state.sessions[i], revoking: state.revokingSessionId == state.sessions[i].id),
          ],
        ],
      ),
    );
  }
}

class _SessionRow extends StatelessWidget {
  final ActiveSession session;
  final bool revoking;
  const _SessionRow({required this.session, required this.revoking});

  @override
  Widget build(BuildContext context) {
    final df = DateFormat('dd/MM/yyyy HH:mm');
    return Row(
      children: [
        Icon(session.estActuel ? Icons.smartphone : Icons.devices_other, size: 20, color: AppColors.brand),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Flexible(child: Text(session.nom, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13), overflow: TextOverflow.ellipsis)),
                  if (session.estActuel) ...[
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(color: AppColors.brandLight, borderRadius: BorderRadius.circular(6)),
                      child: const Text('Cet appareil', style: TextStyle(fontSize: 10, color: AppColors.brand)),
                    ),
                  ],
                ],
              ),
              Text(
                session.derniereUtilisation != null ? 'Utilisée le ${df.format(session.derniereUtilisation!)}' : 'Créée le ${df.format(session.creeLe)}',
                style: const TextStyle(color: AppColors.neutral, fontSize: 11),
              ),
            ],
          ),
        ),
        if (!session.estActuel)
          revoking
              ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2))
              : IconButton(
                  icon: const Icon(Icons.logout, size: 18, color: AppColors.danger),
                  tooltip: 'Révoquer',
                  onPressed: () => context.read<SecuritySettingsBloc>().add(SessionRevokeRequested(session.id)),
                ),
      ],
    );
  }
}

class _LoginHistoryCard extends StatelessWidget {
  final List<LoginHistoryEntry> history;
  const _LoginHistoryCard({required this.history});

  @override
  Widget build(BuildContext context) {
    if (history.isEmpty) {
      return const _Card(child: Text('Aucun historique disponible.', style: TextStyle(color: AppColors.neutral, fontSize: 13)));
    }
    final df = DateFormat('dd/MM/yyyy HH:mm');
    return _Card(
      child: Column(
        children: [
          for (var i = 0; i < history.length; i++) ...[
            if (i > 0) const Divider(height: 20),
            Row(
              children: [
                const Icon(Icons.history, size: 18, color: AppColors.neutral),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(df.format(history[i].loggedInAt), style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                      if (history[i].ipAddress != null)
                        Text(history[i].ipAddress!, style: const TextStyle(color: AppColors.neutral, fontSize: 11)),
                    ],
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }
}
