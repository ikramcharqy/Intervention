<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Chantier;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Sous-catégories "Documents & Contrats". Les catégories "Devis"/"Factures"
     * ont été retirées (option A validée) : elles ne montraient que des PDF
     * synchronisés en sous-produit de DevisService/FactureService::generatePdf()
     * (jamais les vraies données métier — montants, statuts, échéances — qui
     * vivent exclusivement dans "Facturation & Devis", laquelle a déjà son
     * propre bouton PDF indépendant). Documents & Contrats se limite désormais
     * aux pièces qui n'ont pas d'autre domicile : Contrats/SLA, Documents
     * techniques, Autres.
     */
    private const CATEGORIES = [
        'contrats' => ['Contrat'],
        'techniques' => ['Fiche technique'],
        'autres' => ["Pièce d'identité", 'Autre'],
    ];

    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    /**
     * Périmètre "documents de ce client" : regroupé dans UN SEUL where() imbriqué
     * (corrige un bug de priorité SQL préexistant où l'ancien orWhereHas() en tête
     * de requête faisait remonter tous les documents rattachés au client, quel que
     * soit le filtre appliqué ensuite) + ajoute la vérification directe du
     * document.chantier_id, absente jusqu'ici (un document tagué uniquement par
     * chantier_id, sans client_id ni rapport, était invisible côté portail Client).
     */
    private function baseQuery(Client $client)
    {
        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');

        return Document::where(function ($q) use ($client, $chantierIds) {
            $q->where('client_id', $client->id)
                ->orWhereIn('chantier_id', $chantierIds)
                ->orWhereHas('rapport.intervention', function ($qi) use ($chantierIds) {
                    $qi->whereIn('chantier_id', $chantierIds);
                });
        });
    }

    public function index(Request $request): View
    {
        $client = $this->getClient();
        $categorie = $request->input('categorie', 'contrats');
        $chantierFiltre = $request->input('chantier_id');

        if (!$client) {
            $documents = collect();
            $compteurs = array_fill_keys(array_keys(self::CATEGORIES), 0);
            $chantierActif = null;
            $chantiersDuClient = collect();
            return view('client.documents.index', compact('documents', 'categorie', 'compteurs', 'chantierFiltre', 'chantierActif', 'chantiersDuClient'));
        }

        $chantiersDuClient = Chantier::where('client_id', $client->id)->orderBy('nom')->get(['id', 'nom']);

        $compteurs = [];
        foreach (self::CATEGORIES as $key => $types) {
            $compteurs[$key] = (clone $this->baseQuery($client))->whereIn('type_document', $types)->count();
        }

        $documents = $this->baseQuery($client)
            ->with(['rapport.intervention.chantier', 'chantier', 'devis', 'facture'])
            ->whereIn('type_document', self::CATEGORIES[$categorie] ?? self::CATEGORIES['autres'])
            ->when($chantierFiltre, function ($q) use ($chantierFiltre) {
                $q->where(function ($qq) use ($chantierFiltre) {
                    $qq->where('chantier_id', $chantierFiltre)
                        ->orWhereHas('rapport.intervention', fn ($qi) => $qi->where('chantier_id', $chantierFiltre));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $chantierActif = $chantierFiltre ? Chantier::find($chantierFiltre) : null;

        return view('client.documents.index', compact('documents', 'categorie', 'compteurs', 'chantierFiltre', 'chantierActif', 'chantiersDuClient'));
    }

    public function download(Document $document)
    {
        $this->autoriser($document);

        return Storage::disk('public')->download($document->chemin, $document->nom_original);
    }

    /**
     * Aperçu brut (Content-Disposition: inline), servant de source aux visionneuses
     * (viewer PDF.js, balise <img>) — jamais ouvert directement par l'utilisateur.
     */
    public function preview(Document $document)
    {
        $this->autoriser($document);

        return Storage::disk('public')->response($document->chemin, $document->nom_original);
    }

    /**
     * Page d'aperçu intégrée au portail (header, bouton retour/téléchargement,
     * viewer PDF.js ou image), au lieu d'ouvrir le PDF brut dans un onglet natif.
     */
    public function viewer(Document $document): View
    {
        $this->autoriser($document);

        $extension = strtolower(pathinfo($document->nom_original, PATHINFO_EXTENSION));

        return view('client.documents.viewer', compact('document', 'extension'));
    }

    private function autoriser(Document $document): void
    {
        $client = $this->getClient();

        $belongsToClient = $client && (
            $document->client_id === $client->id ||
            ($document->chantier && $document->chantier->client_id === $client->id) ||
            ($document->rapport && $document->rapport->intervention && $document->rapport->intervention->chantier && $document->rapport->intervention->chantier->client_id === $client->id)
        );

        if (!$belongsToClient) {
            abort(403, 'Accès non autorisé à ce document.');
        }

        if (!Storage::disk('public')->exists($document->chemin)) {
            abort(404, 'Fichier non trouvé.');
        }
    }
}
