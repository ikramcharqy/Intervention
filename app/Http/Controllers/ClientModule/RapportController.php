<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Rapport;
use App\Models\Chantier;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RapportController extends Controller
{
    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    public function index(): View
    {
        $client = $this->getClient();

        if (!$client) {
            $rapports = collect();
            return view('client.rapports.index', compact('rapports'));
        }

        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');

        $rapports = Rapport::with(['intervention.chantier', 'intervention.technicien', 'intervention.typeIntervention'])
            ->whereHas('intervention', function ($q) use ($chantierIds) {
                $q->whereIn('chantier_id', $chantierIds);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.rapports.index', compact('rapports'));
    }

    public function show(Rapport $rapport): View
    {
        $client = $this->getClient();

        if (!$client || !$rapport->intervention || !$rapport->intervention->chantier || $rapport->intervention->chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à ce rapport.');
        }

        $rapport->load([
            'intervention.technicien',
            'intervention.chantier.client',
            'intervention.emplacement',
            'intervention.typeIntervention',
            'intervention.taches.tache',
            'intervention.materiaux.materiau',
            'intervention.trackingSessions',
            'photos',
            'videos',
            'documents',
            'reponses.question',
            'validateur',
        ]);

        return view('client.rapports.show', compact('rapport'));
    }

    /**
     * Workflow réel de validation client (point #6 du cahier des charges) : le champ
     * statut_validation existait déjà en base mais n'était utilisé nulle part.
     */
    public function valider(Rapport $rapport): RedirectResponse
    {
        $client = $this->getClient();

        if (!$client || !$rapport->intervention || !$rapport->intervention->chantier || $rapport->intervention->chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à ce rapport.');
        }

        if ($rapport->estValide()) {
            return redirect()->route('client.rapports.show', $rapport)
                ->with('success', 'Ce rapport a déjà été validé.');
        }

        $rapport->update([
            'statut_validation' => Rapport::STATUT_VALIDATION_VALIDE,
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        return redirect()->route('client.rapports.show', $rapport)
            ->with('success', 'Rapport validé avec succès.');
    }

    public function pdf(Rapport $rapport)
    {
        $client = $this->getClient();

        if (!$client || !$rapport->intervention || !$rapport->intervention->chantier || $rapport->intervention->chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à ce rapport.');
        }

        $rapport->load([
            'intervention.technicien',
            'intervention.chantier.client',
            'intervention.emplacement',
            'intervention.typeIntervention',
            'intervention.taches.tache',
            'intervention.materiaux.materiau',
            'photos',
            'documents',
            'reponses.question.choix',
        ]);

        $pdf = Pdf::loadView('rapports.pdf', compact('rapport'));

        return $pdf->stream("rapport-{$rapport->intervention->code_intervention}.pdf");
    }
}
