import 'package:flutter_test/flutter_test.dart';
import 'package:mobile_technicien/features/home/data/models/mission_model.dart';

void main() {
  // Charge utile RÉELLE capturée via `GET /api/interventions` sur l'API de dev
  // (InterventionController::index avec `with(['chantier','emplacement',
  // 'typeIntervention'])`) le 2026-09-12 — tronquée aux champs consommés.
  final realInterventionJson = {
    'id': 2,
    'code_intervention': 'INT-ABC-002',
    'priorite': 'Normale',
    'statut': 'En cours',
    'date_prevue_debut': '2026-09-05T10:19:37.000000Z',
    'description': 'Configuration des switchs réseau et optimisation bande passante.',
    'chantier': {'id': 1, 'nom': 'Siège Social Casablanca'},
    'emplacement': {'id': 1, 'nom': 'Bâtiment Principal - Etage 1'},
    'type_intervention': {'id': 1, 'nom': 'Installation Fibre'},
  };

  test('MissionModel.fromJson parses the real API payload', () {
    final model = MissionModel.fromJson(realInterventionJson);

    expect(model.id, 2);
    expect(model.codeIntervention, 'INT-ABC-002');
    expect(model.statut, 'En cours');
    expect(model.priorite, 'Normale');
    expect(model.chantierNom, 'Siège Social Casablanca');
    expect(model.emplacementNom, 'Bâtiment Principal - Etage 1');
    expect(model.typeInterventionNom, 'Installation Fibre');
    expect(model.datePrevueDebut, isNotNull);
    expect(model.estCloturee, isFalse);
  });

  test('estCloturee is true for a finished mission', () {
    final model = MissionModel.fromJson({...realInterventionJson, 'statut': 'Terminee'});
    expect(model.estCloturee, isTrue);
  });
}
