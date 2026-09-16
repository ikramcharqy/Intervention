import 'package:flutter/material.dart';

/// Palette de l'app Technicien.
///
/// L'accent bleu/indigo (#4F46E5) remplace l'ancien rose/magenta (#cb0c9f) —
/// décision explicite pour aligner l'écran de connexion sur le mockup fourni.
/// La PWA existante (resources/views/mobile/app.blade.php, config Tailwind
/// `brand.*`) a été mise à jour avec la même palette pour rester cohérente
/// entre web et mobile pour ce rôle Technicien.
class AppColors {
  AppColors._();

  static const Color brand = Color(0xFF4F46E5);
  static const Color brandDark = Color(0xFF4338CA);
  static const Color brandLight = Color(0xFFE0E7FF);

  static const Color success = Color(0xFF06A561);
  static const Color danger = Color(0xFFF0142F);
  static const Color warning = Color(0xFFF5A623);
  static const Color info = Color(0xFF1E5EFF);
  static const Color neutral = Color(0xFF5A607F);

  static const Color background = Color(0xFFF8FAFC);
}

class AppTheme {
  AppTheme._();

  static ThemeData light() {
    return ThemeData(
      useMaterial3: true,
      fontFamily: 'OpenSans',
      scaffoldBackgroundColor: AppColors.background,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.brand,
        brightness: Brightness.light,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.white,
        foregroundColor: Colors.black87,
        elevation: 0,
      ),
    );
  }
}
