import 'package:flutter/material.dart';
import 'package:mobile_technicien/core/services/location_status_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Étape 2.1 : indicateur de statut GPS visible en permanence.
class GpsStatusChip extends StatefulWidget {
  final LocationStatusService service;
  const GpsStatusChip({super.key, required this.service});

  @override
  State<GpsStatusChip> createState() => _GpsStatusChipState();
}

class _GpsStatusChipState extends State<GpsStatusChip> with WidgetsBindingObserver {
  LocationStatus? _status;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _refresh();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    // L'utilisateur peut activer la localisation depuis les réglages système
    // puis revenir dans l'app — on rafraîchit le statut à la reprise.
    if (state == AppLifecycleState.resumed) _refresh();
  }

  Future<void> _refresh() async {
    final status = await widget.service.currentStatus();
    if (mounted) setState(() => _status = status);
  }

  Future<void> _onTap() async {
    if (_status == LocationStatus.enabled || _status == LocationStatus.notAvailable) return;
    if (_status == LocationStatus.permissionDeniedForever) {
      await widget.service.openAppSettings();
    } else if (_status == LocationStatus.disabled) {
      await widget.service.openLocationSettings();
    } else {
      await widget.service.requestPermission();
    }
    _refresh();
  }

  @override
  Widget build(BuildContext context) {
    final enabled = _status == LocationStatus.enabled;
    final notAvailable = _status == LocationStatus.notAvailable;
    final color = enabled
        ? AppColors.success
        : (notAvailable ? AppColors.neutral : AppColors.danger);
    final label = enabled
        ? 'Localisation activée'
        : (notAvailable ? 'Non disponible sur cette plateforme' : 'Localisation désactivée');

    return InkWell(
      onTap: _onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
        decoration: BoxDecoration(
          color: color.withOpacity(0.08),
          borderRadius: BorderRadius.circular(999),
          border: Border.all(color: color.withOpacity(0.25)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(enabled ? Icons.location_on : Icons.location_off, size: 13, color: color),
            const SizedBox(width: 4),
            Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: color)),
          ],
        ),
      ),
    );
  }
}
