<x-client-layout>
    <x-slot name="header">Aperçu du document</x-slot>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- Barre d'outils du viewer -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white shadow-sm hover:shadow text-slate-600 text-xs font-bold transition shrink-0">
                    <i class="fas fa-arrow-left"></i> Retour aux documents
                </a>
                <p class="text-xs font-bold text-slate-700 truncate">{{ $document->nom_original }}</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($extension === 'pdf')
                    <div class="flex items-center gap-1 bg-white rounded-lg shadow-sm px-1 py-1">
                        <button id="btnZoomOut" title="Zoom arrière" class="w-7 h-7 rounded-md hover:bg-slate-100 text-slate-500 text-xs"><i class="fas fa-minus"></i></button>
                        <span id="zoomLevel" class="text-[11px] font-bold text-slate-500 w-10 text-center">100%</span>
                        <button id="btnZoomIn" title="Zoom avant" class="w-7 h-7 rounded-md hover:bg-slate-100 text-slate-500 text-xs"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="flex items-center gap-1 bg-white rounded-lg shadow-sm px-1 py-1">
                        <button id="btnPrev" title="Page précédente" class="w-7 h-7 rounded-md hover:bg-slate-100 text-slate-500 text-xs"><i class="fas fa-chevron-left"></i></button>
                        <span class="text-[11px] font-bold text-slate-600 px-1"><span id="pageNum">1</span> / <span id="pageCount">—</span></span>
                        <button id="btnNext" title="Page suivante" class="w-7 h-7 rounded-md hover:bg-slate-100 text-slate-500 text-xs"><i class="fas fa-chevron-right"></i></button>
                    </div>
                @endif
                <a href="{{ route('client.documents.download', $document) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
        </div>

        <!-- Zone de visualisation -->
        <div class="bg-slate-100 p-4 sm:p-8 flex justify-center overflow-auto" style="min-height: 70vh; max-height: 80vh;">
            @if($extension === 'pdf')
                <div id="pdfContainer" class="flex flex-col items-center gap-4">
                    <canvas id="pdfCanvas" class="shadow-lg rounded-sm bg-white"></canvas>
                    <p id="pdfLoading" class="text-xs text-slate-400">Chargement du document…</p>
                </div>
            @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                <img src="{{ route('client.documents.preview', $document) }}" alt="{{ $document->nom_original }}" class="max-w-full h-auto rounded-lg shadow-lg bg-white">
            @else
                <div class="flex flex-col items-center justify-center gap-3 py-16 text-center max-w-sm">
                    <div class="w-14 h-14 rounded-2xl bg-slate-200 text-slate-400 flex items-center justify-center text-xl">
                        <i class="fas fa-file"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Aperçu non disponible pour ce type de fichier.</p>
                    <a href="{{ route('client.documents.download', $document) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition">
                        <i class="fas fa-download"></i> Télécharger le fichier
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($extension === 'pdf')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const url = @json(route('client.documents.preview', $document));
            const canvas = document.getElementById('pdfCanvas');
            const ctx = canvas.getContext('2d');

            let pdfDoc = null, pageNum = 1, scale = 1.2;

            function renderPage(num) {
                pdfDoc.getPage(num).then(function (page) {
                    const viewport = page.getViewport({ scale });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    page.render({ canvasContext: ctx, viewport });
                    document.getElementById('pageNum').textContent = num;
                    document.getElementById('zoomLevel').textContent = Math.round(scale / 1.2 * 100) + '%';
                });
            }

            pdfjsLib.getDocument(url).promise.then(function (doc) {
                pdfDoc = doc;
                document.getElementById('pageCount').textContent = doc.numPages;
                document.getElementById('pdfLoading').style.display = 'none';
                renderPage(pageNum);
            }).catch(function () {
                document.getElementById('pdfLoading').textContent = "Impossible d'afficher l'aperçu — utilisez le téléchargement.";
            });

            document.getElementById('btnPrev').addEventListener('click', function () {
                if (pdfDoc && pageNum > 1) { pageNum--; renderPage(pageNum); }
            });
            document.getElementById('btnNext').addEventListener('click', function () {
                if (pdfDoc && pageNum < pdfDoc.numPages) { pageNum++; renderPage(pageNum); }
            });
            document.getElementById('btnZoomIn').addEventListener('click', function () {
                scale = Math.min(scale + 0.2, 3);
                if (pdfDoc) renderPage(pageNum);
            });
            document.getElementById('btnZoomOut').addEventListener('click', function () {
                scale = Math.max(scale - 0.2, 0.5);
                if (pdfDoc) renderPage(pageNum);
            });
        </script>
    @endif
</x-client-layout>
