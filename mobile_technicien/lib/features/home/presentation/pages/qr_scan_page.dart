import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Étape 4 : scan QR accessible directement depuis l'accueil (FAB), sans
/// passer par l'onglet "Missions" — celui-ci n'existe pas encore dans ce
/// nouveau projet Flutter (seul l'écran d'accueil est construit pour l'instant,
/// cf. cadrage "écran par écran"). Cette page reproduit donc la FONCTION déjà
/// présente côté PWA (resources/views/mobile/app.blade.php,
/// `scanQrCodeForRapport()` / `scanQrForField()`) — le code JS lui-même ne peut
/// pas être "réutilisé" tel quel dans une app Flutter, seul le comportement
/// (scanner puis renvoyer le code capturé à l'appelant) est repris.
/// Le rattachement automatique à une mission/emplacement viendra avec l'écran
/// "Missions".
class QrScanPage extends StatefulWidget {
  const QrScanPage({super.key});

  @override
  State<QrScanPage> createState() => _QrScanPageState();
}

class _QrScanPageState extends State<QrScanPage> {
  final MobileScannerController _controller = MobileScannerController();
  bool _handled = false;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _onDetect(BarcodeCapture capture) {
    if (_handled) return;
    final code = capture.barcodes.firstOrNull?.rawValue;
    if (code == null) return;
    _handled = true;
    Navigator.of(context).pop(code);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.black,
        foregroundColor: Colors.white,
        title: const Text('Scanner un QR Code'),
      ),
      body: Stack(
        children: [
          MobileScanner(controller: _controller, onDetect: _onDetect),
          Align(
            alignment: Alignment.center,
            child: Container(
              width: 220,
              height: 220,
              decoration: BoxDecoration(
                border: Border.all(color: AppColors.brand, width: 3),
                borderRadius: BorderRadius.circular(16),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

extension _FirstOrNull<T> on List<T> {
  T? get firstOrNull => isEmpty ? null : first;
}
