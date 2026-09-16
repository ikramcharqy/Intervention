<x-client-layout>
    <x-slot name="header">Support</x-slot>

    <div class="space-y-6">
        <!-- Aide à l'orientation : Support vs Mes Demandes -->
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-800 flex items-start gap-3">
            <i class="fas fa-circle-info mt-0.5"></i>
            <p>
                <strong>Support</strong> est réservé aux questions administratives, de facturation ou d'accès à votre compte.
                Pour toute nouvelle intervention technique sur un chantier, utilisez plutôt
                <a href="{{ route('client.demandes.create') }}" class="font-bold underline hover:text-indigo-900">Nouvelle Demande</a>.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-slate-900 mb-1">Votre interlocuteur commercial</h2>
                <p class="text-xs text-slate-400 mb-5">Point de contact direct pour toute question sur vos chantiers, devis ou interventions.</p>

                @if($commercial)
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="w-12 h-12 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($commercial->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">{{ $commercial->name }}</p>
                            <a href="mailto:{{ $commercial->email }}" class="text-xs text-slate-500 hover:text-emerald-700 block">{{ $commercial->email }}</a>
                            @if($commercial->telephone ?? false)
                                <a href="tel:{{ $commercial->telephone }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold block">{{ $commercial->telephone }}</a>
                            @endif
                        </div>
                    </div>
                    <a href="mailto:{{ $commercial->email }}" class="mt-4 w-full inline-flex items-center justify-center gap-2 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition">
                        <i class="fas fa-envelope"></i> Contacter par e-mail
                    </a>
                @else
                    <div class="py-8 text-center text-slate-400 italic text-sm">
                        Aucun commercial assigné pour le moment.
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-slate-900 mb-1">Nous écrire</h2>
                <p class="text-xs text-slate-400 mb-5">Votre message est enregistré et transmis à votre commercial assigné — vous recevez une référence de suivi.</p>

                @if($commercial)
                    <form action="{{ route('client.support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase">Catégorie</label>
                            <select name="categorie" class="mt-1 w-full text-xs rounded-lg border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                <option value="">-- Sélectionner --</option>
                                @foreach(\App\Models\TicketSupport::CATEGORIES as $cat)
                                    <option value="{{ $cat }}" {{ old('categorie') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase">Objet</label>
                            <input type="text" name="objet" value="{{ old('objet') }}" required class="mt-1 w-full text-xs rounded-lg border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/40" placeholder="Objet de votre message">
                            @error('objet') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase">Message</label>
                            <textarea name="message" rows="4" required class="mt-1 w-full text-xs rounded-lg border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/40" placeholder="Votre message…">{{ old('message') }}</textarea>
                            @error('message') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase">Pièce jointe <span class="text-slate-400 font-normal">(optionnel)</span></label>
                            <input type="file" name="piece_jointe" class="mt-1 w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-600 file:text-xs file:font-bold hover:file:bg-slate-200">
                            @error('piece_jointe') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition">
                            <i class="fas fa-paper-plane"></i> Envoyer
                        </button>
                        <p class="text-[10px] text-slate-400 text-center">
                            Ou <a href="mailto:{{ $commercial->email }}" class="underline hover:text-emerald-700">contactez directement par email</a>.
                        </p>
                    </form>
                @else
                    <p class="text-sm text-slate-400 italic">Aucun commercial assigné pour recevoir votre message.</p>
                @endif
            </div>
        </div>

        <!-- Historique des tickets -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 pb-4">
                <h2 class="text-sm font-extrabold text-slate-900">Mes tickets de support</h2>
                <p class="text-xs text-slate-400 mt-1">Historique de vos échanges avec votre commercial.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Référence</th>
                            <th class="py-3 px-4">Catégorie</th>
                            <th class="py-3 px-4">Objet</th>
                            <th class="py-3 px-4">Envoyé le</th>
                            <th class="py-3 pr-6 pl-4 text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-slate-900">{{ $ticket->reference }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $ticket->categorie ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $ticket->objet }}</td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3.5 pr-6 pl-4 text-right"><x-soft-badge :status="$ticket->statut" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 italic">Aucun ticket envoyé pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($tickets, 'hasPages') && $tickets->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
