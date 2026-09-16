import 'package:flutter_test/flutter_test.dart';
import 'package:mobile_technicien/features/auth/data/models/technicien_model.dart';

void main() {
  // Charge utile RÉELLE capturée via `POST /api/login` sur l'API de dev
  // (AuthController::login) le 2026-09-12 — pas une charge utile inventée.
  const realLoginResponseUser = {
    'id': 52,
    'name': 'Qa Mobile Test',
    'prenom': 'Einar',
    'email': 'qa.mobile.test@intervention.ma',
    'roles': ['technicien'],
  };

  test('TechnicienModel.fromJson parses the real API payload', () {
    final model = TechnicienModel.fromJson(realLoginResponseUser);

    expect(model.id, 52);
    expect(model.name, 'Qa Mobile Test');
    expect(model.prenom, 'Einar');
    expect(model.email, 'qa.mobile.test@intervention.ma');
    expect(model.roles, ['technicien']);
  });

  test('TechnicienModel.fromJson tolerates a missing prenom', () {
    final model = TechnicienModel.fromJson({
      'id': 1,
      'name': 'Sans Prénom',
      'email': 'x@y.ma',
      'roles': [],
    });

    expect(model.prenom, isNull);
  });
}
