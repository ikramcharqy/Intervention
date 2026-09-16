import 'package:flutter/material.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Étape 3 : remplace le texte brut d'erreur réseau (ex : `HTTP request
/// failed, statusCode: 0`) par un état de repli standard — icône "image
/// indisponible" + libellé court — pour TOUT affichage de média réseau dans
/// l'app (pas seulement l'écran de rapport), afin d'éviter la même
/// régression ailleurs si un média échoue à charger (CORS, fichier supprimé,
/// réseau coupé, etc.).
class NetworkImageWithFallback extends StatelessWidget {
  final String url;
  final double? width;
  final double? height;
  final BoxFit fit;
  final BorderRadius? borderRadius;

  const NetworkImageWithFallback({
    super.key,
    required this.url,
    this.width,
    this.height,
    this.fit = BoxFit.cover,
    this.borderRadius,
  });

  @override
  Widget build(BuildContext context) {
    final image = Image.network(
      url,
      width: width,
      height: height,
      fit: fit,
      errorBuilder: (context, error, stackTrace) => _MediaFallback(width: width, height: height),
      loadingBuilder: (context, child, progress) {
        if (progress == null) return child;
        return SizedBox(
          width: width,
          height: height,
          child: const Center(child: CircularProgressIndicator(strokeWidth: 2)),
        );
      },
    );

    return borderRadius != null ? ClipRRect(borderRadius: borderRadius!, child: image) : image;
  }
}

class _MediaFallback extends StatelessWidget {
  final double? width;
  final double? height;
  const _MediaFallback({this.width, this.height});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: width,
      height: height,
      constraints: const BoxConstraints(minHeight: 60, minWidth: 60),
      color: const Color(0xFFF1F5F9),
      alignment: Alignment.center,
      child: const Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.image_not_supported_outlined, color: AppColors.neutral, size: 22),
          SizedBox(height: 4),
          Text('Média indisponible', style: TextStyle(fontSize: 10, color: AppColors.neutral), textAlign: TextAlign.center),
        ],
      ),
    );
  }
}
