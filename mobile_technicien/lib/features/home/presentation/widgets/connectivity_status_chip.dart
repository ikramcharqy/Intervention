import 'package:flutter/material.dart';
import 'package:mobile_technicien/core/services/connectivity_status_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Étape 2.2 : statut "En ligne" / "Hors ligne". Pas de file d'attente de
/// synchronisation aujourd'hui (voir ConnectivityStatusService) — seul le
/// statut brut est affiché, sans inventer un compteur d'éléments en attente.
class ConnectivityStatusChip extends StatefulWidget {
  final ConnectivityStatusService service;
  const ConnectivityStatusChip({super.key, required this.service});

  @override
  State<ConnectivityStatusChip> createState() => _ConnectivityStatusChipState();
}

class _ConnectivityStatusChipState extends State<ConnectivityStatusChip> {
  ConnectivityStatus _status = ConnectivityStatus.online;

  @override
  void initState() {
    super.initState();
    widget.service.currentStatus().then((s) => mounted ? setState(() => _status = s) : null);
    widget.service.watch().listen((s) {
      if (mounted) setState(() => _status = s);
    });
  }

  @override
  Widget build(BuildContext context) {
    final online = _status == ConnectivityStatus.online;
    final color = online ? AppColors.success : AppColors.warning;
    final label = online ? 'En ligne' : 'Hors ligne';

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: color.withOpacity(0.25)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(online ? Icons.wifi : Icons.wifi_off, size: 13, color: color),
          const SizedBox(width: 4),
          Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: color)),
        ],
      ),
    );
  }
}
