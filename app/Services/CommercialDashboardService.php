<?php

namespace App\Services;

use App\Models\Devis;
use App\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Données du dashboard Commercial : tuiles KPI comparées à la période précédente,
 * tendance des devis et top clients. Toute la logique de calcul vit ici pour garder
 * CommercialModule\DashboardController fin, conformément à Controller → Service → Model.
 */
class CommercialDashboardService
{
    private const PERIODES_VALIDES = ['week', 'month', 'quarter', 'custom'];

    public function buildDashboardData(int $commercialId, ?string $period, ?string $from, ?string $to): array
    {
        $period = in_array($period, self::PERIODES_VALIDES, true) ? $period : 'week';

        [$start, $end, $prevStart, $prevEnd, $periodLabel] = $this->resolvePeriodRange($period, $from, $to);

        $revenueActuel = $this->sommeDevisAcceptes($commercialId, $start, $end);
        $revenuePrecedent = $this->sommeDevisAcceptes($commercialId, $prevStart, $prevEnd);

        $ventesActuelles = $this->compteDevisAcceptes($commercialId, $start, $end);
        $ventesPrecedentes = $this->compteDevisAcceptes($commercialId, $prevStart, $prevEnd);

        $prospectsActuels = Prospect::where('commercial_id', $commercialId)
            ->whereBetween('created_at', [$start, $end])->count();
        $prospectsPrecedents = Prospect::where('commercial_id', $commercialId)
            ->whereBetween('created_at', [$prevStart, $prevEnd])->count();

        return [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'revenueTile' => $this->buildTile($revenueActuel, $revenuePrecedent),
            'salesTile' => $this->buildTile($ventesActuelles, $ventesPrecedentes),
            'prospectsTile' => $this->buildTile($prospectsActuels, $prospectsPrecedents),
            'devisARelancer' => Devis::where('commercial_id', $commercialId)
                ->whereIn('statut', ['Envoyé', 'En attente'])
                ->where('date_emission', '<=', now()->subDays(\App\Http\Controllers\Commercial\DashboardController::DEVIS_RELANCE_JOURS))
                ->count(),
            'prospectsInactifs' => Prospect::where('commercial_id', $commercialId)
                ->whereNotIn('statut', ['Converti', 'Client'])
                ->where('updated_at', '<=', now()->subDays(\App\Http\Controllers\Commercial\DashboardController::PROSPECT_INACTIVITE_JOURS))
                ->count(),
            'parStatutDevis' => Devis::where('commercial_id', $commercialId)
                ->selectRaw('statut, count(*) as total')
                ->groupBy('statut')
                ->pluck('total', 'statut'),
            ...$this->tendanceDevis($commercialId),
            ...$this->topClients($commercialId),
            'revenueSparkline' => $this->revenueSparkline($commercialId),
        ];
    }

    private function resolvePeriodRange(string $period, ?string $from, ?string $to): array
    {
        $now = now();

        if ($period === 'custom' && $from && $to) {
            try {
                $start = Carbon::parse($from)->startOfDay();
                $end = Carbon::parse($to)->endOfDay();
            } catch (\Exception) {
                $period = 'week';
            }
        }

        if ($period !== 'custom' || !isset($start, $end)) {
            $start = match ($period) {
                'month' => $now->copy()->startOfMonth(),
                'quarter' => $now->copy()->startOfQuarter(),
                default => $now->copy()->startOfWeek(),
            };
            $end = $now->copy();
        }

        // Période précédente : le même nombre de jours, immédiatement avant $start.
        $dureeJours = max(1, $start->diffInDays($end) + 1);
        $prevEnd = $start->copy()->subSecond();
        $prevStart = $prevEnd->copy()->subDays($dureeJours - 1)->startOfDay();

        $label = match ($period) {
            'month' => 'le mois précédent',
            'quarter' => 'le trimestre précédent',
            'custom' => 'la période précédente',
            default => 'la semaine précédente',
        };

        return [$start, $end, $prevStart, $prevEnd, $label];
    }

    private function sommeDevisAcceptes(int $commercialId, Carbon $start, Carbon $end): float
    {
        return (float) Devis::where('commercial_id', $commercialId)
            ->whereIn('statut', ['Accepté', 'Accepte', 'Validé'])
            ->whereBetween('date_emission', [$start, $end])
            ->sum('montant_ttc');
    }

    private function compteDevisAcceptes(int $commercialId, Carbon $start, Carbon $end): int
    {
        return Devis::where('commercial_id', $commercialId)
            ->whereIn('statut', ['Accepté', 'Accepte', 'Validé'])
            ->whereBetween('date_emission', [$start, $end])
            ->count();
    }

    /**
     * Tuile KPI avec variation vs période précédente. "comparable" quand la période
     * précédente a des données à comparer ; "is_new" quand la valeur actuelle est la
     * première (évite une division par zéro et un pourcentage trompeur).
     */
    private function buildTile(int|float $current, int|float $previous): array
    {
        if ($previous > 0) {
            $pct = round((($current - $previous) / $previous) * 100, 1);

            return ['value' => $current, 'pct' => abs($pct), 'up' => $pct >= 0, 'comparable' => true, 'is_new' => false];
        }

        if ($current > 0) {
            return ['value' => $current, 'pct' => 0, 'up' => true, 'comparable' => false, 'is_new' => true];
        }

        return ['value' => $current, 'pct' => 0, 'up' => false, 'comparable' => false, 'is_new' => false];
    }

    private function tendanceDevis(int $commercialId): array
    {
        $moisLabelsFr = [];
        $devisTendanceSeries = collect();

        for ($i = 5; $i >= 0; $i--) {
            $mois = now()->copy()->subMonths($i);
            $moisLabelsFr[] = ucfirst($mois->locale('fr')->translatedFormat('M'));

            $devisTendanceSeries->push(
                Devis::where('commercial_id', $commercialId)
                    ->whereYear('date_emission', $mois->year)
                    ->whereMonth('date_emission', $mois->month)
                    ->count()
            );
        }

        return ['moisLabelsFr' => $moisLabelsFr, 'devisTendanceSeries' => $devisTendanceSeries];
    }

    private function topClients(int $commercialId): array
    {
        $topClients = Devis::where('commercial_id', $commercialId)
            ->whereIn('statut', ['Accepté', 'Accepte', 'Validé'])
            ->whereNotNull('client_id')
            ->selectRaw('client_id, SUM(montant_ttc) as total')
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('client')
            ->get();

        return ['topClients' => $topClients, 'topClientMax' => max(1, (float) $topClients->max('total'))];
    }

    /**
     * Mini-graphique du CA (tuile "CA de la Période") : 7 derniers jours, indépendant
     * du filtre de période sélectionné, pour donner une tendance récente stable.
     */
    private function revenueSparkline(int $commercialId): Collection
    {
        $valeurs = collect();

        for ($i = 6; $i >= 0; $i--) {
            $jour = now()->copy()->subDays($i);

            $valeurs->push(
                (float) Devis::where('commercial_id', $commercialId)
                    ->whereIn('statut', ['Accepté', 'Accepte', 'Validé'])
                    ->whereDate('date_emission', $jour)
                    ->sum('montant_ttc')
            );
        }

        return $valeurs;
    }
}
