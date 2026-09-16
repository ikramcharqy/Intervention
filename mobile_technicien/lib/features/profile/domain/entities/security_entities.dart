import 'package:equatable/equatable.dart';

/// Miroir d'une ligne `App\Models\LoginHistory` — connexions API uniquement
/// (Api\AuthController::login, via ApiSessionService::enregistrerConnexion) ;
/// n'inclut pas les connexions web par session/cookie.
class LoginHistoryEntry extends Equatable {
  final DateTime loggedInAt;
  final String? ipAddress;
  final String? userAgent;

  const LoginHistoryEntry({required this.loggedInAt, required this.ipAddress, required this.userAgent});

  @override
  List<Object?> get props => [loggedInAt, ipAddress, userAgent];
}

/// Miroir d'un jeton Sanctum ("appareil connecté") — pas une session cookie
/// web (SessionSecurityService), un jeton = une connexion API distincte.
class ActiveSession extends Equatable {
  final int id;
  final String nom;
  final DateTime? derniereUtilisation;
  final DateTime creeLe;
  final bool estActuel;

  const ActiveSession({
    required this.id,
    required this.nom,
    required this.derniereUtilisation,
    required this.creeLe,
    required this.estActuel,
  });

  @override
  List<Object?> get props => [id, nom, derniereUtilisation, creeLe, estActuel];
}
