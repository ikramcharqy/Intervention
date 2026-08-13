<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Prospect;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        $commercialId = auth()->id();
        
        $documents = Document::with(['prospect', 'client'])
            ->where('user_id', $commercialId)
            ->latest()
            ->get();

        $prospects = Prospect::where('commercial_id', $commercialId)->where('statut', '!=', 'Converti')->get();
        $clients = Client::where('commercial_id', $commercialId)->get();

        return view('commercial.documents.index', compact('documents', 'prospects', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'prospect_id' => 'nullable|exists:prospects,id',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Store file on public disk (mapped to storage/app/public/documents)
            $path = $file->store('documents', 'public');

            Document::create([
                'user_id' => auth()->id(),
                'prospect_id' => $request->input('prospect_id'),
                'client_id' => $request->input('client_id'),
                'nom_original' => $file->getClientOriginalName(),
                'chemin' => $path,
                'type_mime' => $file->getClientMimeType(),
            ]);

            return redirect()->route('commercial.documents.index')->with('success', 'Document importé avec succès.');
        }

        return redirect()->route('commercial.documents.index')->with('error', 'Aucun fichier fourni.');
    }

    public function download(Document $document)
    {
        // Enforce that a commercial can only download their own documents
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé à ce document.');
        }

        $filePath = storage_path('app/public/' . $document->chemin);

        if (!file_exists($filePath)) {
            abort(404, 'Le fichier physique n\'existe pas.');
        }

        return response()->download($filePath, $document->nom_original);
    }

    public function destroy(Document $document)
    {
        // Enforce delete authorization
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        // Delete from storage
        if (Storage::disk('public')->exists($document->chemin)) {
            Storage::disk('public')->delete($document->chemin);
        }

        // Delete record
        $document->delete();

        return redirect()->route('commercial.documents.index')->with('success', 'Document supprimé avec succès.');
    }
}
