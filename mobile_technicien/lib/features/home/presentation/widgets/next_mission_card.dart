import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/utils/relative_time.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/home/presentation/widgets/priority_badge.dart';
import 'package:mobile_technicien/features/missions/presentation/widgets/status_badge.dart';

/// Étape 1.1/1.2 : carte "Prochaine mission" en tête de l'écran d'accueil,
/// avec état vide informatif et orienté action quand `mission` est null.
///
/// Étape 4 (revue design) : fond neutre plutôt que le dégradé magenta plein
/// d'origine — sur fond saturé, le badge de priorité "Urgente" (rouge)
/// perdait tout son impact visuel, la couleur de marque se retrouvant
/// reléguée à un simple accent (bordure gauche + bouton).
class NextMissionCard extends StatelessWidget {
  final Mission? mission;

  /// Seul cas qui déclenche réellement POST /interventions/{id}/start —
  /// jamais utilisé pour "Continuer" (mission déjà "En cours" : ce statut
  /// n'est pas dans Intervention::peutEtreDemarree(), l'appel serait rejeté
  /// par le backend).
  final VoidCallback onStart;

  final VoidCallback onItineraire;

  /// "Continuer" (En cours) et "Finaliser le rapport" (Formulaire rempli)
  /// mènent tous les deux vers l'écran "Détails Mission"/formulaire dynamique
  /// — pas encore construit, donc même callback provisoire que l'appelant
  /// peut distinguer via `mission.statut` s'il le souhaite.
  final VoidCallback onContinue;

  /// Distance à vol d'oiseau jusqu'au chantier, en km — null si le GPS est
  /// désactivé ou les coordonnées du chantier absentes (Étape 5 : jamais de
  /// valeur par défaut trompeuse). Volontairement PAS un temps de trajet en
  /// voiture : aucune intégration de routage (Google Directions/OSRM...)
  /// n'existe ailleurs dans le projet, en inventer un serait fabriquer une
  /// estimation non fiable.
  final double? distanceKm;
  final bool isActionLoading;

  const NextMissionCard({
    super.key,
    required this.mission,
    required this.onStart,
    required this.onItineraire,
    required this.onContinue,
    this.distanceKm,
    this.isActionLoading = false,
  });

  @override
  Widget build(BuildContext context) {
    final m = mission;

    if (m == null) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFFE6E9F4)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: const [
                Icon(Icons.event_available_outlined, color: AppColors.neutral),
                SizedBox(width: 8),
                Text('Aucune mission aujourd\'hui', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              ],
            ),
            const SizedBox(height: 8),
            const Text(
              'Aucune mission programmée aujourd\'hui. Contactez votre responsable si vous attendiez une affectation.',
              style: TextStyle(color: AppColors.neutral, fontSize: 13, height: 1.4),
            ),
          ],
        ),
      );
    }

    final heure = m.datePrevueDebut != null ? DateFormat('HH:mm').format(m.datePrevueDebut!.toLocal()) : '—';
    final relatif = relativeTimeLabel(m.datePrevueDebut);

    final String label;
    final VoidCallback action;
    if (m.statut == 'En cours') {
      label = 'Continuer';
      action = onContinue;
    } else if (m.formulaireRempli) {
      label = 'Finaliser le rapport';
      action = onContinue;
    } else if (m.peutDemarrer) {
      label = 'Démarrer';
      action = onStart;
    } else {
      label = 'Itinéraire';
      action = onItineraire;
    }

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: const Border(left: BorderSide(color: AppColors.brand, width: 4)),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 12, offset: const Offset(0, 4)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              StatusBadge(statut: m.statut),
              if (m.priorite != 'Normale') ...[
                const SizedBox(width: 6),
                PriorityBadge(priorite: m.priorite),
              ],
            ],
          ),
          const SizedBox(height: 10),
          Text(
            'Prochaine mission · $heure${relatif != null ? ' · $relatif' : ''}',
            style: const TextStyle(color: AppColors.neutral, fontSize: 12, fontWeight: FontWeight.w600),
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 6),
          Row(
            children: [
              const Icon(Icons.build_circle_outlined, color: AppColors.brand, size: 20),
              const SizedBox(width: 8),
              Expanded(
                child: Text(m.chantierNom,
                    style: const TextStyle(color: Colors.black87, fontSize: 18, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
          const SizedBox(height: 2),
          Text(m.typeInterventionNom, style: const TextStyle(color: AppColors.neutral, fontSize: 13)),
          if (m.emplacementNom != null || distanceKm != null) ...[
            const SizedBox(height: 6),
            Row(
              children: [
                const Icon(Icons.place_outlined, size: 14, color: AppColors.neutral),
                const SizedBox(width: 4),
                if (m.emplacementNom != null)
                  Expanded(
                    child: Text(m.emplacementNom!,
                        style: const TextStyle(color: AppColors.neutral, fontSize: 12), overflow: TextOverflow.ellipsis),
                  ),
                if (distanceKm != null) ...[
                  if (m.emplacementNom != null) const SizedBox(width: 8),
                  Text('≈ ${distanceKm!.toStringAsFixed(1)} km à vol d\'oiseau',
                      style: const TextStyle(color: AppColors.neutral, fontSize: 12, fontWeight: FontWeight.w600)),
                ],
              ],
            ),
          ],
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: FilledButton(
              style: FilledButton.styleFrom(
                backgroundColor: AppColors.brand,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 12),
              ),
              onPressed: isActionLoading ? null : action,
              child: isActionLoading
                  ? const SizedBox(
                      width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : Text(label),
            ),
          ),
        ],
      ),
    );
  }
}
