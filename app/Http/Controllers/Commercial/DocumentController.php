<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Devis;
use App\Models\Document;
use App\Models\Prospect;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentService $documentService,
    ) {
    }

    public function index(Request $request): View
    {
        $commercialId = auth()->id();
        $search = $request->input('search');

        $documents = Document::with(['prospect', 'client', 'devis', 'chantier'])
            ->where('user_id', $commercialId)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom_original', 'like', "%{$search}%")
                        ->orWhere('type_document', 'like', "%{$search}%")
                        ->orWhereHas('prospect', fn ($q2) => $q2->where('nom_entreprise', 'like', "%{$search}%"))
                        ->orWhereHas('client', fn ($q2) => $q2->where('nom', 'like', "%{$search}%"))
                        ->orWhereHas('devis', fn ($q2) => $q2->where('reference', 'like', "%{$search}%"))
                        ->orWhereHas('chantier', fn ($q2) => $q2->where('nom', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->get();

        $prospects = Prospect::where('commercial_id', $commercialId)->where('statut', '!=', 'Converti')->get();
        $clients = Client::where('commercial_id', $commercialId)->get();
        $devisList = Devis::where('commercial_id', $commercialId)->get();
        $chantiers = Chantier::whereHas('client', fn ($q) => $q->where('commercial_id', $commercialId))->get();

        return view('commercial.documents.index', compact('documents', 'search', 'prospects', 'clients', 'devisList', 'chantiers'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $this->documentService->store($request->validated(), $request->file('file'), auth()->id());

        return redirect()->route('commercial.documents.index')->with('success', 'Document importé avec succès.');
    }

    public function download(Document $document)
    {
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé à ce document.');
        }
        $filePath = storage_path('app/public/' . $document->chemin);
        if (!file_exists($filePath)) {
            abort(404, "Le fichier physique n'existe pas.");
        }
        return response()->download($filePath, $document->nom_original);
    }

    public function destroy(Document $document)
    {
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        $this->documentService->delete($document);

        return redirect()->route('commercial.documents.index')->with('success', 'Document supprimé avec succès.');
    }
}
