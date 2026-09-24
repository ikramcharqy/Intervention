import 'package:flutter/material.dart';

/// Logo TechniTrack — mêmes fichiers que le web (public/images/), pour une
/// identité de marque identique entre le portail Laravel et l'app terrain.
enum AppLogoVariant {
  /// Pictogramme seul (flèche + signal), fond transparent.
  icon,

  /// Logo complet (pictogramme + texte "TechniTrack"), fond transparent.
  full,
}

class AppLogo extends StatelessWidget {
  final AppLogoVariant variant;
  final double height;

  const AppLogo({super.key, this.variant = AppLogoVariant.full, this.height = 40});

  @override
  Widget build(BuildContext context) {
    final asset = variant == AppLogoVariant.icon
        ? 'assets/images/logo-technitrack-icon.png'
        : 'assets/images/logo-technitrack-full.png';

    return Image.asset(asset, height: height, fit: BoxFit.contain);
  }
}
