<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Devis;
use App\Models\Emplacement;
use App\Models\Facture;
use App\Models\Intervention;
use App\Services\EmplacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Étape 3 — Supervision Métier transverse pour le Super Admin : consultation en lecture
 * sur les entités déjà gérées au quotidien par Admin/Commercial, sans dupliquer ni
 * modifier leurs Controllers/vues/routes existants (ChantierController,
 * InterventionController, EmplacementController, ClientController restent intacts).
 */
class SupervisionController extends Controller
{
    public function chantiers(Request $request): View
    {
        $query = Chantier::with('client')->withCount(['interventions', 'emplacements']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code_chantier', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%");
            });
        }

        // "Chantiers sans activité" : emplacements configurés mais zéro intervention —
        // signal de risque opérationnel à faire remonter.
        if ($request->boolean('sans_activite')) {
            $query->has('emplacements')->doesntHave('interventions');
        }

        $tri = in_array($request->input('tri'), ['nom', 'ville', 'interventions_count', 'is_active'], true) ? $request->input('tri') : null;
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if ($tri) {
            $query->orderBy($tri, $direction);
        } else {
            // Par défaut : les chantiers dormants remontent en tête de liste.
            $query->orderByRaw('(emplacements_count > 0 AND interventions_count = 0) DESC')->latest();
        }

        $chantiers = $query->paginate(15)->withQueryString();
        $clients = Client::orderBy('nom')->get(['id', 'nom']);

        $kpis = [
            'chantiers_actifs' => Chantier::where('is_active', true)->count(),
            'total_emplacements' => \App\Models\Emplacement::count(),
            'total_interventions' => Intervention::count(),
            'sans_activite' => Chantier::has('emplacements')->doesntHave('interventions')->count(),
        ];

        return view('superadmin.supervision.chantiers', compact('chantiers', 'clients', 'kpis', 'tri', 'direction'));
    }

    // Étape 3.3 : fiche détail en lecture seule — la gestion reste sur Admin/Commercial,
    // cette vue ne fait qu'exposer les mêmes données en consultation pour le Super Admin.
    public function chantierShow(Chantier $chantier): View
    {
        $chantier->load(['client', 'emplacements', 'interventions' => fn ($q) => $q->latest()->limit(15), 'interventions.technicien', 'interventions.typeIntervention']);

        return view('superadmin.supervision.chantier-show', compact('chantier'));
    }

    public function interventions(Request $request): View
    {
        $query = Intervention::with(['chantier.client', 'technicien', 'typeIntervention']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->input('priorite'));
        }

        if ($request->filled('client_id')) {
            $query->whereHas('chantier', fn ($q) => $q->where('client_id', $request->input('client_id')));
        }

        if ($request->filled('technicien_id')) {
            $query->where('technicien_id', $request->input('technicien_id'));
        }

        if ($request->filled('emplacement_id')) {
            $query->where('emplacement_id', $request->input('emplacement_id'));
        }

        if ($request->boolean('bloquees')) {
            $query->bloquees();
        }

        $tri = in_array($request->input('tri'), ['statut', 'priorite', 'date_prevue_debut'], true) ? $request->input('tri') : null;
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if ($tri) {
            $query->orderBy($tri, $direction);
        } else {
            $query->latest();
        }

        $interventions = $query->paginate(15)->withQueryString();
        $clients = Client::orderBy('nom')->get(['id', 'nom']);
        $techniciens = \App\Models\User::role(['technicien', 'Technicien'])->orderBy('name')->get(['id', 'name', 'prenom']);
        $statuts = [
            Intervention::STATUT_PLANIFIEE, Intervention::STATUT_AFFECTEE, Intervention::STATUT_ACCEPTEE,
            Intervention::STATUT_EN_COURS, Intervention::STATUT_FORM_REMPLI, Intervention::STATUT_TERMINEE,
            Intervention::STATUT_VALIDEE, Intervention::STATUT_ANNULEE, Intervention::STATUT_REFUSEE,
        ];
        $priorites = [Intervention::PRIORITE_FAIBLE, Intervention::PRIORITE_NORMALE, Intervention::PRIORITE_HAUTE, Intervention::PRIORITE_URGENTE];

        $kpis = [
            'total' => Intervention::count(),
            'par_statut' => Intervention::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut'),
            'bloquees' => Intervention::bloquees()->count(),
        ];

        return view('superadmin.supervision.interventions', compact('interventions', 'clients', 'techniciens', 'statuts', 'priorites', 'tri', 'direction', 'kpis'));
    }

    // Étape 5.2 : fiche détail en lecture seule (référence cliquable depuis la liste),
    // inclut le rapport si l'intervention est terminée.
    public function interventionShow(Intervention $intervention): View
    {
        $intervention->load(['chantier.client', 'technicien', 'typeIntervention', 'rapport', 'historiques' => fn ($q) => $q->latest()->limit(20)]);

        return view('superadmin.supervision.intervention-show', compact('intervention'));
    }

    public function emplacements(Request $request, EmplacementService $emplacementService): View
    {
        $query = Emplacement::with('chantier.client')->withCount('interventions');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('qr_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('chantier_id')) {
            $query->where('chantier_id', $request->input('chantier_id'));
        }

        // "Jamais utilisé" : emplacement configuré mais aucune intervention n'y a jamais
        // été rattachée — signal de risque opérationnel, même logique que Chantiers
        // "sans activité".
        if ($request->boolean('jamais_utilise')) {
            $query->doesntHave('interventions');
        }

        $tri = in_array($request->input('tri'), ['nom', 'interventions_count', 'is_active', 'created_at'], true) ? $request->input('tri') : null;
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if ($tri) {
            $query->orderBy($tri, $direction);
        } else {
            $query->latest();
        }

        $emplacements = $query->paginate(15)->withQueryString();
        $chantiers = Chantier::orderBy('nom')->get(['id', 'nom']);

        // Miniatures + aperçu précalculés ici plutôt que résolus dans la vue — un seul
        // rendu SVG par emplacement et par taille, pour la page courante (max 15 lignes).
        $qrThumbs = $emplacements->getCollection()->mapWithKeys(fn (Emplacement $e) => [
            $e->id => [
                'thumb' => base64_encode($emplacementService->qrSvg($e, 80)),
                'full' => base64_encode($emplacementService->qrSvg($e, 320)),
            ],
        ]);

        $kpis = [
            'total' => Emplacement::count(),
            'actifs' => Emplacement::where('is_active', true)->count(),
            'jamais_utilises' => Emplacement::doesntHave('interventions')->count(),
            'total_chantiers' => Chantier::has('emplacements')->count(),
        ];

        return view('superadmin.supervision.emplacements', compact('emplacements', 'chantiers', 'tri', 'direction', 'qrThumbs', 'kpis'));
    }

    // Business logic (génération de la nouvelle référence + journalisation) déplacée dans
    // EmplacementService::regenerateQrCode() — ce Controller reste un simple point d'entrée
    // Super Admin pour un support imprimé perdu/dégradé.
    public function regenerateQrCode(Emplacement $emplacement, EmplacementService $emplacementService): RedirectResponse
    {
        $refs = $emplacementService->regenerateQrCode($emplacement);

        return redirect()->back()->with('success', "Le QR Code de « {$emplacement->nom} » a été régénéré ({$refs['ancien']} → {$refs['nouveau']}). L'ancien support imprimé n'est plus valide.");
    }

    // Téléchargement du QR Code réel (SVG) d'un emplacement — fichier imprimable individuel.
    public function emplacementQrDownload(Emplacement $emplacement, EmplacementService $emplacementService)
    {
        $svg = $emplacementService->qrSvg($emplacement, 480);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $emplacement->qr_code . '.svg"',
        ]);
    }

    // Export PDF groupé (planche) de tous les QR Codes d'un chantier — prêt à imprimer.
    public function emplacementsQrExport(Chantier $chantier, EmplacementService $emplacementService)
    {
        return $emplacementService->exportQrCodesPdf($chantier)
            ->download("QR-Codes-{$chantier->code_chantier}.pdf");
    }

    public function clients(Request $request): View
    {
        $query = Client::query()
            ->withCount('chantiers')
            ->addSelect([
                'interventions_count' => Intervention::selectRaw('count(*)')
                    ->join('chantiers', 'chantiers.id', '=', 'interventions.chantier_id')
                    ->whereColumn('chantiers.client_id', 'clients.id'),
                'devis_count' => Devis::selectRaw('count(*)')
                    ->whereColumn('devis.client_id', 'clients.id'),
                'factures_count' => Facture::selectRaw('count(*)')
                    ->whereColumn('factures.client_id', 'clients.id'),
                // Étape 4 : valeur financière agrégée depuis le module Facturation déjà
                // existant — Annulée exclue du CA généré comme du montant en attente.
                'ca_genere' => Facture::selectRaw('COALESCE(SUM(montant_ttc), 0)')
                    ->whereColumn('factures.client_id', 'clients.id')
                    ->where('statut', '!=', Facture::STATUT_ANNULEE),
                'montant_en_attente' => Facture::selectRaw('COALESCE(SUM(GREATEST(montant_ttc - montant_paye, 0)), 0)')
                    ->whereColumn('factures.client_id', 'clients.id')
                    ->where('statut', '!=', Facture::STATUT_ANNULEE),
            ]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code_client', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // "Sans activité" : client enregistré mais aucun chantier jamais créé — même
        // logique que "Chantiers sans activité" / "Emplacements jamais utilisés".
        if ($request->boolean('sans_activite')) {
            $query->doesntHave('chantiers');
        }

        $tri = in_array($request->input('tri'), ['nom', 'chantiers_count', 'interventions_count', 'ca_genere'], true) ? $request->input('tri') : null;
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if ($tri) {
            $query->orderBy($tri, $direction);
        } else {
            $query->latest();
        }

        $clients = $query->paginate(15)->withQueryString();

        $kpis = [
            'total' => Client::count(),
            'ca_genere' => Facture::where('statut', '!=', Facture::STATUT_ANNULEE)->sum('montant_ttc'),
            'montant_en_attente' => Facture::where('statut', '!=', Facture::STATUT_ANNULEE)->selectRaw('COALESCE(SUM(GREATEST(montant_ttc - montant_paye, 0)), 0) as total')->value('total'),
            'sans_activite' => Client::doesntHave('chantiers')->count(),
        ];

        return view('superadmin.supervision.clients', compact('clients', 'tri', 'direction', 'kpis'));
    }

    // Étape 5.2 : fiche détail en lecture seule (référence cliquable depuis la liste).
    public function clientShow(Client $client): View
    {
        $client->load(['commercial', 'chantiers', 'contacts']);
        $client->loadCount('chantiers');

        $devis = Devis::where('client_id', $client->id)->latest()->limit(15)->get();
        $factures = Facture::where('client_id', $client->id)->latest()->limit(15)->get();
        $interventionsCount = Intervention::whereHas('chantier', fn ($q) => $q->where('client_id', $client->id))->count();

        $facturesActives = Facture::where('client_id', $client->id)->where('statut', '!=', Facture::STATUT_ANNULEE);
        $caGenere = (clone $facturesActives)->sum('montant_ttc');
        $montantEnAttente = (clone $facturesActives)->selectRaw('COALESCE(SUM(GREATEST(montant_ttc - montant_paye, 0)), 0) as total')->value('total');

        return view('superadmin.supervision.client-show', compact('client', 'devis', 'factures', 'interventionsCount', 'caGenere', 'montantEnAttente'));
    }

    // Étape 5.2 : fiche détail en lecture seule pour un emplacement, dans le même esprit
    // que chantierShow/interventionShow — ne réutilise pas emplacements.show (vue Admin
    // avec actions d'écriture, layout différent), pour ne pas mélanger les permissions.
    public function emplacementShow(Emplacement $emplacement): View
    {
        $emplacement->load(['chantier.client', 'interventions' => fn ($q) => $q->latest('date_prevue_debut')->limit(15), 'interventions.technicien', 'interventions.typeIntervention']);

        return view('superadmin.supervision.emplacement-show', compact('emplacement'));
    }
}
