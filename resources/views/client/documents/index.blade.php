<x-client-layout>
    <x-slot name="header">Documents & Contrats</x-slot>

    <div class="space-y-6">
        @php
            $categories = [
                'contrats' => [
                    'label' => 'Contrats & SLA',
                    'icon' => 'fa-file-signature',
                    'vide' => "Les contrats et SLA associés à votre compte apparaîtront ici une fois établis par votre responsable commercial.",
                ],
                'techniques' => [
                    'label' => 'Documents techniques',
                    'icon' => 'fa-file-lines',
                    'vide' => "Les documents techniques liés à vos interventions (schémas, plans, comptes-rendus) apparaîtront ici.",
                ],
                'autres' => [
                    'label' => 'Autres',
                    'icon' => 'fa-folder',
                    'vide' => "Les autres documents partagés par votre responsable commercial apparaîtront ici.",
                ],
            ];
        @endphp

        <!-- Sous-catégories -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($categories as $key => $meta)
                    <a href="{{ route('client.documents.index', array_filter(['categorie' => $key, 'chantier_id' => $chantierFiltre])) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition {{ $categorie === $key ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                        <i class="fas {{ $meta['icon'] }}"></i>
                        {{ $meta['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-extrabold {{ $categorie === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $compteurs[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            <form method="GET" class="flex items-center">
                <input type="hidden" name="categorie" value="{{ $categorie }}">
                <select name="chantier_id" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Tous les chantiers</option>
                    @foreach($chantiersDuClient as $c)
                        <option value="{{ $c->id }}" {{ (string) $chantierFiltre === (string) $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($chantierActif)
            <div class="flex items-center gap-2 text-xs">
                <span class="text-slate-500">Filtré sur le chantier :</span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                    <i class="fas fa-city"></i> {{ $chantierActif->nom }}
                    <a href="{{ route('client.documents.index', ['categorie' => $categorie]) }}" class="text-emerald-500 hover:text-emerald-700"><i class="fas fa-xmark"></i></a>
                </span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 pb-4">
                <h1 class="text-base font-bold text-slate-900">{{ $categories[$categorie]['label'] ?? 'Documents' }}</h1>
                <p class="text-xs text-slate-400 mt-1">Consultation et téléchargement des pièces de cette catégorie</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Nom du document</th>
                            <th class="py-3 px-4">Intervention / Origine</th>
                            <th class="py-3 px-4">Extension</th>
                            <th class="py-3 px-4">Date d'ajout</th>
                            <th class="py-3 pr-6 pl-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-file text-xs"></i>
                                        </div>
                                        <p class="font-bold text-slate-900 truncate">{{ $doc->nom_original }}</p>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($doc->rapport && $doc->rapport->intervention)
                                        <a href="{{ route('client.interventions.show', $doc->rapport->intervention) }}" class="font-semibold text-emerald-600 hover:text-emerald-700">
                                            {{ $doc->rapport->intervention->code_intervention }}
                                        </a>
                                        <div class="text-[11px] text-slate-400">Chantier : {{ $doc->rapport->intervention->chantier?->nom }}</div>
                                    @elseif($doc->chantier)
                                        <a href="{{ route('client.chantiers.show', $doc->chantier) }}" class="font-semibold text-emerald-600 hover:text-emerald-700">
                                            Chantier {{ $doc->chantier->nom }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">Document client</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 uppercase">
                                        {{ pathinfo($doc->nom_original, PATHINFO_EXTENSION) ?: 'Fichier' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3.5 pr-6 pl-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('client.documents.viewer', $doc) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                                            <i class="fas fa-eye"></i> Aperçu
                                        </a>
                                        <a href="{{ route('client.documents.download', $doc) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                            <i class="fas fa-download"></i> Télécharger
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 italic max-w-md mx-auto">
                                    {{ $categories[$categorie]['vide'] ?? 'Aucun document dans cette catégorie.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($documents, 'hasPages') && $documents->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
