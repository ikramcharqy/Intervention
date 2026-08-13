<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Chantier;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
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
            $documents = collect();
            return view('client.documents.index', compact('documents'));
        }

        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');

        $documents = Document::where('client_id', $client->id)
            ->orWhereHas('rapport.intervention', function ($q) use ($chantierIds) {
                $q->whereIn('chantier_id', $chantierIds);
            })
            ->latest()
            ->paginate(15);

        return view('client.documents.index', compact('documents'));
    }

    public function download(Document $document)
    {
        $client = $this->getClient();

        $belongsToClient = $client && (
            $document->client_id === $client->id ||
            ($document->rapport && $document->rapport->intervention && $document->rapport->intervention->chantier && $document->rapport->intervention->chantier->client_id === $client->id)
        );

        if (!$belongsToClient) {
            abort(403, 'Accès non autorisé à ce document.');
        }

        if (!Storage::disk('public')->exists($document->chemin)) {
            abort(404, 'Fichier non trouvé.');
        }

        return Storage::disk('public')->download($document->chemin, $document->nom_original);
    }
}
