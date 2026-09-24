import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/services/location_status_service.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/utils/eta_estimator.dart';
import 'package:mobile_technicien/core/widgets/mini_location_map.dart';
import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';
import 'package:mobile_technicien/features/chantier/presentation/bloc/chantier_detail_bloc.dart';
import 'package:mobile_technicien/features/missions/presentation/widgets/status_badge.dart';
import 'package:mobile_technicien/injection_container.dart';
import 'package:url_launcher/url_launcher.dart';

/// Fiche chantier (Étape 4) — même donnée que le portail Client
/// (ClientModule\ChantierController::show : contact, coordonnées, documents),
/// consommée ici via GET /api/chantiers/{id} (Api\ChantierController), pas une
/// fiche reconstruite différemment. Mini-carte en flutter_map + OpenStreetMap
/// pour rester cohérent avec l'intégration Leaflet/OSM déjà utilisée côté web
/// (pas de Google Maps ailleurs dans le projet).
class ChantierDetailScreen extends StatelessWidget {
  final int chantierId;
  const ChantierDetailScreen({super.key, required this.chantierId});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<ChantierDetailBloc>(param1: chantierId)..add(const ChantierDetailRequested()),
      child: const _ChantierDetailView(),
    );
  }
}

class _ChantierDetailView extends StatelessWidget {
  const _ChantierDetailView();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Fiche chantier')),
      body: BlocBuilder<ChantierDetailBloc, ChantierDetailState>(
        builder: (context, state) {
          return switch (state) {
            ChantierDetailLoading() => const Center(child: CircularProgressIndicator()),
            ChantierDetailError(:final message) => _ErrorView(
                message: message,
                onRetry: () => context.read<ChantierDetailBloc>().add(const ChantierDetailRequested()),
              ),
            ChantierDetailLoaded(:final chantier) => _ChantierBody(chantier: chantier),
          };
        },
      ),
    );
  }
}

class _ChantierBody extends StatefulWidget {
  final Chantier chantier;
  const _ChantierBody({required this.chantier});

  @override
  State<_ChantierBody> createState() => _ChantierBodyState();
}

class _ChantierBodyState extends State<_ChantierBody> {
  final _locationService = LocationStatusService();
  EtaEstimate? _eta;
  bool _loadingEta = false;

  @override
  void initState() {
    super.initState();
    _loadEta();
  }

  Future<void> _loadEta() async {
    final chantier = widget.chantier;
    if (!chantier.aPosition) return;
    setState(() => _loadingEta = true);
    final position = await _locationService.getCurrentPosition();
    if (!mounted) return;
    if (position == null) {
      setState(() => _loadingEta = false);
      return;
    }
    final meters = Geolocator.distanceBetween(position.latitude, position.longitude, chantier.latitude!, chantier.longitude!);
    setState(() {
      _eta = estimateEta(meters / 1000);
      _loadingEta = false;
    });
  }

  Future<void> _appeler(String telephone) async {
    final uri = Uri(scheme: 'tel', path: telephone);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _envoyerEmail(String email) async {
    final uri = Uri(scheme: 'mailto', path: email);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _ouvrirItineraire() async {
    final chantier = widget.chantier;
    if (!chantier.aPosition) return;
    final uri = Uri.parse('https://www.google.com/maps/dir/?api=1&destination=${chantier.latitude},${chantier.longitude}');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  Future<void> _ouvrirDocument(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    final chantier = widget.chantier;

    return ListView(
      padding: const EdgeInsets.all(20),
      children: [
        Text(chantier.nom, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
        if (chantier.adresse != null || chantier.ville != null) ...[
          const SizedBox(height: 4),
          Text(
            [chantier.adresse, chantier.ville].where((v) => v != null && v.isNotEmpty).join(', '),
            style: const TextStyle(color: AppColors.neutral, fontSize: 13),
          ),
        ],
        const SizedBox(height: 20),

        // Étape 5.1 : contact responsable — nom + téléphone (tel:) + email.
        if (chantier.responsable != null || chantier.aContact) _ContactCard(chantier: chantier, onCall: _appeler, onEmail: _envoyerEmail),

        // Étape 5.2/5.3 : mini-carte + itinéraire + distance/temps estimé.
        if (chantier.aPosition) ...[
          const SizedBox(height: 20),
          const Text('Localisation', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
          const SizedBox(height: 8),
          MiniLocationMap(latitude: chantier.latitude!, longitude: chantier.longitude!),
          const SizedBox(height: 10),
          Row(
            children: [
              if (_loadingEta)
                const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))
              else if (_eta != null)
                Expanded(
                  child: Text(
                    '${_eta!.distanceLabel} · ${_eta!.dureeLabel} (estimation)',
                    style: const TextStyle(fontSize: 12, color: AppColors.neutral),
                  ),
                )
              else
                const Spacer(),
              OutlinedButton.icon(
                onPressed: _ouvrirItineraire,
                icon: const Icon(Icons.directions_outlined, size: 18),
                label: const Text('Itinéraire'),
              ),
            ],
          ),
        ],

        // Étape 5.4 : documents/plans techniques du chantier.
        if (chantier.documents.isNotEmpty) ...[
          const SizedBox(height: 20),
          const Text('Documents', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
          const SizedBox(height: 8),
          ...chantier.documents.map((doc) => Card(
                margin: const EdgeInsets.only(bottom: 6),
                child: ListTile(
                  dense: true,
                  leading: const Icon(Icons.insert_drive_file_outlined),
                  title: Text(doc.nomOriginal, overflow: TextOverflow.ellipsis),
                  subtitle: doc.typeDocument != null ? Text(doc.typeDocument!) : null,
                  trailing: const Icon(Icons.open_in_new, size: 18),
                  onTap: () => _ouvrirDocument(doc.url),
                ),
              )),
        ],

        if (chantier.emplacements.isNotEmpty) ...[
          const SizedBox(height: 20),
          const Text('Emplacements', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
          const SizedBox(height: 8),
          ...chantier.emplacements.map((e) => ListTile(
                dense: true,
                contentPadding: EdgeInsets.zero,
                leading: const Icon(Icons.place_outlined, color: AppColors.neutral),
                title: Text(e.nom),
              )),
        ],

        // Étape 6.1 (design de référence) : interventions Terminée/Validée
        // sur ce chantier, même source que la fiche chantier web.
        if (chantier.interventionsTerminees.isNotEmpty) ...[
          const SizedBox(height: 20),
          const Text('Interventions terminées', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
          const SizedBox(height: 8),
          ...chantier.interventionsTerminees.map((i) => _InterventionTermineeTile(intervention: i)),
        ],
      ],
    );
  }
}

class _InterventionTermineeTile extends StatelessWidget {
  final ChantierIntervention intervention;
  const _InterventionTermineeTile({required this.intervention});

  @override
  Widget build(BuildContext context) {
    final dateLabel = intervention.dateFin != null ? DateFormat('dd/MM/yyyy').format(intervention.dateFin!.toLocal()) : null;

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          const Icon(Icons.check_circle_outline, color: AppColors.success, size: 20),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  intervention.typeInterventionNom ?? intervention.codeIntervention,
                  style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                  overflow: TextOverflow.ellipsis,
                ),
                if (dateLabel != null)
                  Text(dateLabel, style: const TextStyle(color: AppColors.neutral, fontSize: 11)),
              ],
            ),
          ),
          const SizedBox(width: 8),
          StatusBadge(statut: intervention.statut),
        ],
      ),
    );
  }
}

class _ContactCard extends StatelessWidget {
  final Chantier chantier;
  final void Function(String) onCall;
  final void Function(String) onEmail;
  const _ContactCard({required this.chantier, required this.onCall, required this.onEmail});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Contact responsable', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.neutral)),
          const SizedBox(height: 8),
          if (chantier.responsable != null)
            Text(chantier.responsable!, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
          const SizedBox(height: 8),
          if (chantier.aContact)
            InkWell(
              onTap: () => onCall(chantier.telephoneResponsable!),
              child: Row(
                children: [
                  const Icon(Icons.phone_outlined, size: 18, color: AppColors.brand),
                  const SizedBox(width: 8),
                  Text(chantier.telephoneResponsable!, style: const TextStyle(color: AppColors.brand, fontWeight: FontWeight.w600)),
                ],
              ),
            ),
          if (chantier.emailResponsable != null && chantier.emailResponsable!.isNotEmpty) ...[
            const SizedBox(height: 6),
            InkWell(
              onTap: () => onEmail(chantier.emailResponsable!),
              child: Row(
                children: [
                  const Icon(Icons.email_outlined, size: 18, color: AppColors.neutral),
                  const SizedBox(width: 8),
                  Text(chantier.emailResponsable!, style: const TextStyle(color: AppColors.neutral)),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _ErrorView extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;
  const _ErrorView({required this.message, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.error_outline, color: AppColors.danger, size: 40),
            const SizedBox(height: 12),
            Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.neutral)),
            const SizedBox(height: 16),
            FilledButton(
              style: FilledButton.styleFrom(backgroundColor: AppColors.brand),
              onPressed: onRetry,
              child: const Text('Réessayer'),
            ),
          ],
        ),
      ),
    );
  }
}
