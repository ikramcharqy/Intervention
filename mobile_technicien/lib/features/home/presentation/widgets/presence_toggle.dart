import 'package:flutter/material.dart';
import 'package:mobile_technicien/core/services/location_status_service.dart';
import 'package:mobile_technicien/core/services/presence_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Étape 3.1 : statut "En service" / "Hors service" — élément central de
/// l'écran (conditionne potentiellement la disponibilité vue par le
/// Commercial/Super Admin), d'où le traitement visuel plein plutôt qu'une
/// carte neutre parmi d'autres.
///
/// ⚠️ Question de conformité SIGNALÉE, PAS TRANCHÉE (cf. PresenceService) :
/// passer "En service" invite seulement l'utilisateur à activer sa
/// localisation (paramètre confort/utilité), ça ne déclenche AUCUN tracking
/// GPS — le lien "tracking actif uniquement pendant le service" nécessite une
/// décision produit/droit du travail avant d'être codé.
///
/// ⚠️ Persistance SIGNALÉE, PAS ENCORE FAITE : ce statut reste local à
/// l'appareil (SharedPreferences) — aucun endpoint backend n'existe
/// aujourd'hui pour le persister côté serveur (cf. PresenceService). L'ajouter
/// nécessite une colonne sur `users` (changement de schéma) : à confirmer
/// avant implémentation plutôt que décidé unilatéralement ici.
class PresenceToggle extends StatefulWidget {
  final PresenceService service;
  final LocationStatusService? locationService;

  /// Nombre d'interventions "En cours" assignées à ce technicien — garde-fou
  /// avant de passer "Hors service" (Étape 3.3 : avertissement fort plutôt
  /// qu'un blocage strict, choix par défaut demandé quand les deux options
  /// sont possibles).
  final int missionsEnCours;

  const PresenceToggle({
    super.key,
    required this.service,
    this.locationService,
    this.missionsEnCours = 0,
  });

  @override
  State<PresenceToggle> createState() => _PresenceToggleState();
}

class _PresenceToggleState extends State<PresenceToggle> {
  bool _enService = false;

  @override
  void initState() {
    super.initState();
    widget.service.isEnService().then((v) => mounted ? setState(() => _enService = v) : null);
  }

  Future<void> _toggle() async {
    final next = !_enService;

    if (!next) {
      final confirme = await _confirmerHorsService();
      if (!confirme) return;
    }

    try {
      await widget.service.setEnService(next);
    } catch (_) {
      // Écriture locale déjà faite (cf. PresenceService), mais la
      // synchronisation serveur a échoué : on prévient plutôt que de laisser
      // croire à une mise à jour silencieusement réussie.
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Statut enregistré localement, mais pas synchronisé avec le serveur.')),
        );
      }
    }
    if (mounted) setState(() => _enService = next);

    if (next) await _checkLocationOnStart();
  }

  /// Étape 3.2 : confirmation avant "Hors service" uniquement (pas nécessaire
  /// pour "En service", moins risqué) — texte renforcé et explicite sur le
  /// nombre de missions en cours quand il y en a.
  Future<bool> _confirmerHorsService() async {
    final hasActiveMissions = widget.missionsEnCours > 0;

    final confirme = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Passer hors service ?'),
        content: Text(
          hasActiveMissions
              ? 'Vous avez ${widget.missionsEnCours} intervention(s) en cours — êtes-vous sûr de vouloir passer hors service ?'
              : 'Vous ne serez plus visible comme disponible pour de nouvelles missions.',
        ),
        actions: [
          TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Annuler')),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: hasActiveMissions ? AppColors.danger : AppColors.brand),
            onPressed: () => Navigator.of(context).pop(true),
            child: const Text('Confirmer'),
          ),
        ],
      ),
    );

    return confirme ?? false;
  }

  Future<void> _checkLocationOnStart() async {
    final locationService = widget.locationService;
    if (locationService == null) return;

    final status = await locationService.currentStatus();
    if (status == LocationStatus.enabled || status == LocationStatus.notAvailable) return;
    if (!mounted) return;

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Activez votre localisation pour démarrer votre service.')),
    );
    await locationService.requestPermission();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: _enService ? AppColors.success : AppColors.neutral,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(
        children: [
          Icon(_enService ? Icons.work : Icons.work_off_outlined, color: Colors.white, size: 20),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              _enService ? 'En service' : 'Hors service',
              style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 14),
            ),
          ),
          Switch(
            value: _enService,
            activeColor: Colors.white,
            activeTrackColor: Colors.white.withOpacity(0.4),
            inactiveThumbColor: Colors.white,
            inactiveTrackColor: Colors.white.withOpacity(0.3),
            onChanged: (_) => _toggle(),
          ),
        ],
      ),
    );
  }
}
