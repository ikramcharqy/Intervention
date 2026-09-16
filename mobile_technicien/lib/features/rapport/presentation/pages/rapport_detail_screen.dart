import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/utils/web_file_opener.dart';
import 'package:mobile_technicien/core/widgets/mini_location_map.dart';
import 'package:mobile_technicien/core/widgets/network_image_with_fallback.dart';
import 'package:mobile_technicien/features/rapport/domain/entities/rapport.dart';
import 'package:mobile_technicien/features/rapport/presentation/bloc/rapport_detail_bloc.dart';
import 'package:mobile_technicien/injection_container.dart';
import 'package:url_launcher/url_launcher.dart';

/// Écran "Voir le rapport" (statut Terminée) — consomme la même structure de
/// données déjà normalisée côté web (Super Admin/Admin/Commercial/Client,
/// GET /api/interventions/{id}/rapport), pas une fiche reconstruite
/// différemment pour l'app Technicien.
class RapportDetailScreen extends StatelessWidget {
  final int interventionId;
  const RapportDetailScreen({super.key, required this.interventionId});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => sl<RapportDetailBloc>(param1: interventionId)..add(const RapportDetailRequested()),
      child: const _RapportDetailView(),
    );
  }
}

class _RapportDetailView extends StatelessWidget {
  const _RapportDetailView();

  void _telechargerPdf(Uint8List bytes, int rapportId) {
    // Téléchargement natif (<a download>) plutôt qu'un window.open() — ce
    // dernier survient après l'await réseau (récupération des octets) et se
    // fait donc bloquer silencieusement par le navigateur, hors du geste
    // utilisateur direct.
    downloadBytes(bytes, mimeType: 'application/pdf', filename: 'rapport_$rapportId.pdf');
  }

  Future<void> _ouvrirFichier(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Rapport d\'intervention')),
      body: BlocConsumer<RapportDetailBloc, RapportDetailState>(
        listenWhen: (previous, current) {
          if (current is! RapportDetailLoaded) return false;
          final previousLoaded = previous is RapportDetailLoaded ? previous : null;
          final newPdf = current.pdfBytes != null && current.pdfBytes != previousLoaded?.pdfBytes;
          final newError = current.pdfError != null && current.pdfError != previousLoaded?.pdfError;
          return newPdf || newError;
        },
        listener: (context, state) {
          if (state is! RapportDetailLoaded) return;
          if (state.pdfBytes != null) {
            _telechargerPdf(state.pdfBytes!, state.rapport.id);
          } else if (state.pdfError != null) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.pdfError!), backgroundColor: AppColors.danger),
            );
          }
        },
        builder: (context, state) {
          return switch (state) {
            RapportDetailLoading() => const Center(child: CircularProgressIndicator()),
            RapportDetailError(:final message) => _ErrorView(message: message),
            RapportDetailLoaded() => _RapportBody(
                state: state,
                onDownloadPdf: () => context.read<RapportDetailBloc>().add(const RapportPdfRequested()),
                onOpenFile: _ouvrirFichier,
              ),
          };
        },
      ),
    );
  }
}

class _RapportBody extends StatelessWidget {
  final RapportDetailLoaded state;
  final VoidCallback onDownloadPdf;
  final void Function(String url) onOpenFile;
  const _RapportBody({required this.state, required this.onDownloadPdf, required this.onOpenFile});

  @override
  Widget build(BuildContext context) {
    final rapport = state.rapport;

    return Column(
      children: [
        Expanded(
          child: ListView(
            padding: const EdgeInsets.all(20),
            children: [
              if (rapport.dureeReelle != null || rapport.statutEquipement != null) ...[
                Row(
                  children: [
                    if (rapport.dureeReelle != null)
                      Expanded(child: _MetricCard(label: 'Durée réelle', value: '${rapport.dureeReelle} min')),
                    if (rapport.dureeReelle != null && rapport.statutEquipement != null) const SizedBox(width: 10),
                    if (rapport.statutEquipement != null)
                      Expanded(child: _MetricCard(label: 'État équipement', value: rapport.statutEquipement!)),
                  ],
                ),
                const SizedBox(height: 20),
              ],

              _TextSection(title: 'Travaux effectués', text: rapport.travauxEffectues),
              _TextSection(title: 'Observations', text: rapport.observations),
              _TextSection(title: 'Recommandations', text: rapport.recommandations),
              _TextSection(title: 'Commentaire', text: rapport.commentaire),

              if (rapport.reponses.isNotEmpty) ...[
                const Text('Réponses au formulaire', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const SizedBox(height: 8),
                ...rapport.reponses.map((r) => _ReponseTile(reponse: r, onOpenFile: onOpenFile)),
                const SizedBox(height: 12),
              ],

              if (rapport.photos.isNotEmpty) ...[
                const Text('Photos', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const SizedBox(height: 8),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: rapport.photos
                      .map((p) => NetworkImageWithFallback(
                            url: p.url,
                            width: 90,
                            height: 90,
                            borderRadius: BorderRadius.circular(8),
                          ))
                      .toList(),
                ),
                const SizedBox(height: 20),
              ],

              if (rapport.documents.isNotEmpty) ...[
                const Text('Documents', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const SizedBox(height: 8),
                ...rapport.documents.map((d) => Card(
                      margin: const EdgeInsets.only(bottom: 6),
                      child: ListTile(
                        dense: true,
                        leading: const Icon(Icons.insert_drive_file_outlined),
                        title: Text(d.nomOriginal, overflow: TextOverflow.ellipsis),
                        trailing: const Icon(Icons.open_in_new, size: 18),
                        onTap: () => onOpenFile(d.url),
                      ),
                    )),
                const SizedBox(height: 20),
              ],

              if (rapport.signatureTechnicienUrl != null || rapport.signatureClientUrl != null) ...[
                const Text('Signatures', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const SizedBox(height: 8),
                Row(
                  children: [
                    if (rapport.signatureTechnicienUrl != null)
                      Expanded(child: _SignatureCard(label: 'Technicien', url: rapport.signatureTechnicienUrl!)),
                    if (rapport.signatureTechnicienUrl != null && rapport.signatureClientUrl != null)
                      const SizedBox(width: 10),
                    if (rapport.signatureClientUrl != null)
                      Expanded(child: _SignatureCard(label: 'Client', url: rapport.signatureClientUrl!)),
                  ],
                ),
                const SizedBox(height: 20),
              ],

              if (rapport.aPosition) ...[
                const Text('Position GPS', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const SizedBox(height: 8),
                MiniLocationMap(latitude: rapport.gpsLatitude!, longitude: rapport.gpsLongitude!),
                const SizedBox(height: 4),
                Text(
                  'Lat : ${rapport.gpsLatitude!.toStringAsFixed(5)} · Lng : ${rapport.gpsLongitude!.toStringAsFixed(5)}',
                  style: const TextStyle(fontSize: 12, color: AppColors.neutral),
                ),
              ],
            ],
          ),
        ),
        SafeArea(
          top: false,
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 8, 20, 16),
            child: SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: state.downloadingPdf ? null : onDownloadPdf,
                icon: state.downloadingPdf
                    ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Icon(Icons.picture_as_pdf_outlined),
                label: const Text('Télécharger le PDF'),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _MetricCard extends StatelessWidget {
  final String label;
  final String value;
  const _MetricCard({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10), border: Border.all(color: const Color(0xFFE6E9F4))),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 11, color: AppColors.neutral)),
          const SizedBox(height: 2),
          Text(value, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}

class _TextSection extends StatelessWidget {
  final String title;
  final String? text;
  const _TextSection({required this.title, required this.text});

  @override
  Widget build(BuildContext context) {
    if (text == null || text!.trim().isEmpty) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
          const SizedBox(height: 6),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(8), border: Border.all(color: const Color(0xFFE6E9F4))),
            child: Text(text!, style: const TextStyle(fontSize: 13, height: 1.4)),
          ),
        ],
      ),
    );
  }
}

class _ReponseTile extends StatelessWidget {
  final RapportReponse reponse;
  final void Function(String url) onOpenFile;
  const _ReponseTile({required this.reponse, required this.onOpenFile});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(color: const Color(0xFFF5F6FA), borderRadius: BorderRadius.circular(8), border: Border.all(color: const Color(0xFFE6E9F4))),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(reponse.question, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
          const SizedBox(height: 6),
          if (reponse.estTypeFichier && reponse.reponseFichierUrl != null)
            reponse.type == 'Document'
                ? InkWell(
                    onTap: () => onOpenFile(reponse.reponseFichierUrl!),
                    child: const Row(
                      children: [
                        Icon(Icons.attach_file, size: 16, color: AppColors.brand),
                        SizedBox(width: 4),
                        Text('Ouvrir le fichier', style: TextStyle(color: AppColors.brand, fontSize: 13)),
                      ],
                    ),
                  )
                : NetworkImageWithFallback(
                    url: reponse.reponseFichierUrl!,
                    height: 100,
                    borderRadius: BorderRadius.circular(6),
                  )
          else
            Text(reponse.valeurAffichee, style: const TextStyle(fontSize: 13, color: AppColors.neutral)),
        ],
      ),
    );
  }
}

class _SignatureCard extends StatelessWidget {
  final String label;
  final String url;
  const _SignatureCard({required this.label, required this.url});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, color: AppColors.neutral)),
        const SizedBox(height: 4),
        Container(
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(8)),
          width: double.infinity,
          height: 90,
          child: NetworkImageWithFallback(url: url, fit: BoxFit.contain, borderRadius: BorderRadius.circular(8)),
        ),
      ],
    );
  }
}

class _ErrorView extends StatelessWidget {
  final String message;
  const _ErrorView({required this.message});

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
          ],
        ),
      ),
    );
  }
}
