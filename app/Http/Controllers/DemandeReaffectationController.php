<?php

namespace App\Http\Controllers;

use App\Models\DemandeReaffectation;
use App\Models\User;
use App\Services\InterventionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DemandeReaffectationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected InterventionService $interventionService)
    {
    }

    /**
     * Liste des demandes de réaffectation pour l'Admin.
     */
    public function index(Request $request)
    {
        $this->authorize('arbitrateReassignment', \App\Models\Intervention::class);

        $demandes = DemandeReaffectation::with(['intervention', 'technicien', 'admin', 'nouveauTechnicien'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $techniciens = User::role(['Technicien', 'technicien'])->get();

        return view('demandes_reaffectation.index', compact('demandes', 'techniciens'));
    }

    /**
     * Arbitrer une demande de réaffectation (Accepter ou Refuser).
     */
    public function traiter(Request $request, DemandeReaffectation $demande)
    {
        $this->authorize('arbitrateReassignment', \App\Models\Intervention::class);

        $request->validate([
            'action'                 => 'required|in:accepter,refuser',
            'nouveau_technicien_id' => 'nullable|exists:users,id',
            'commentaire_admin'     => 'nullable|string',
        ]);

        $accepter = $request->input('action') === 'accepter';
        $nouveauTechnicienId = $request->input('nouveau_technicien_id');
        $commentaireAdmin = $request->input('commentaire_admin', '');

        try {
            $this->interventionService->traiterDemandeReaffectation(
                $demande,
                $accepter,
                $nouveauTechnicienId ? (int) $nouveauTechnicienId : null,
                $commentaireAdmin,
                $request->user()
            );

            $msg = $accepter
                ? 'La demande de réaffectation a été acceptée avec succès.'
                : 'La demande de réaffectation a été refusée. La mission est maintenue chez le technicien.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
