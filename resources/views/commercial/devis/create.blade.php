<x-commercial-layout>
    <x-slot name="header">Créer un Devis</x-slot>

    <div style="max-width: 1100px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <div>
                <h2 style="font-size:18px; font-weight:700; color:#181c32;">Nouveau Devis</h2>
                <p style="font-size:12px; color:#a1a5b7; margin-top:3px;">Remplissez les informations pour créer un devis</p>
            </div>
            <a href="{{ route('commercial.devis.index') }}" class="kt-btn kt-btn-light">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
        </div>

        <form action="{{ route('commercial.devis.store') }}" method="POST" id="devis-form">
            @csrf

            <div class="kt-card" style="margin-bottom:20px;">
                <div class="kt-card-header">
                    <div class="kt-card-title">Informations générales</div>
                </div>
                <div class="kt-card-body">
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                        <!-- Type de destinataire -->
                        <div class="kt-form-group">
                            <label class="kt-form-label">Type de destinataire <span class="required">*</span></label>
                            <select id="destinataire_type" class="kt-form-control">
                                <option value="prospect" {{ $selectedProspectId ? 'selected' : '' }}>Prospect</option>
                                <option value="client"   {{ !$selectedProspectId ? 'selected' : '' }}>Client</option>
                            </select>
                        </div>

                        <!-- Prospect select -->
                        <div class="kt-form-group" id="prospect_select_group" style="{{ !$selectedProspectId ? 'display:none' : '' }}">
                            <label class="kt-form-label">Prospect <span class="required">*</span></label>
                            <select name="prospect_id" class="kt-form-control">
                                <option value="">Sélectionner un prospect</option>
                                @foreach($prospects as $prospect)
                                    <option value="{{ $prospect->id }}" {{ $selectedProspectId == $prospect->id ? 'selected' : '' }}>{{ $prospect->nom_entreprise }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Client select -->
                        <div class="kt-form-group" id="client_select_group" style="{{ $selectedProspectId ? 'display:none' : '' }}">
                            <label class="kt-form-label">Client <span class="required">*</span></label>
                            <select name="client_id" class="kt-form-control">
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="kt-form-group">
                            <label class="kt-form-label">Statut <span class="required">*</span></label>
                            <select name="statut" required class="kt-form-control">
                                <option value="Brouillon" selected>Brouillon</option>
                                <option value="Envoyé">Envoyé</option>
                                <option value="Accepté">Accepté</option>
                                <option value="Refusé">Refusé</option>
                            </select>
                        </div>
                    </div>

                    <div class="kt-form-group" style="margin-top:4px;">
                        <label class="kt-form-label">Demande d'intervention liée (optionnel)</label>
                        <select name="demande_intervention_id" class="kt-form-control">
                            <option value="">Aucune</option>
                            @foreach($demandesInterventions as $demande)
                                <option value="{{ $demande->id }}">{{ $demande->reference }} — {{ $demande->objet }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:4px;">
                        <div class="kt-form-group">
                            <label class="kt-form-label">Date d'émission <span class="required">*</span></label>
                            <input type="date" name="date_emission" required value="{{ date('Y-m-d') }}" class="kt-form-control">
                        </div>

                        <div class="kt-form-group">
                            <label class="kt-form-label">Date d'expiration</label>
                            <input type="date" name="date_expiration" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="kt-form-control">
                        </div>

                        <div class="kt-form-group">
                            <label class="kt-form-label">Taux de TVA (%) <span class="required">*</span></label>
                            <input type="number" name="taux_tva" step="0.01" min="0" value="20.00" id="taux_tva" class="kt-form-control">
                        </div>
                    </div>

                    <div class="kt-form-group" style="margin-bottom:0;">
                        <label class="kt-form-label">Observations</label>
                        <textarea name="observations" rows="3" class="kt-form-control" placeholder="Notes additionnelles, conditions de paiement..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Lignes -->
            <div class="kt-card" style="margin-bottom:20px;">
                <div class="kt-card-header">
                    <div class="kt-card-title">Articles & Prestations</div>
                    <button type="button" id="add-line-btn" class="kt-btn kt-btn-light-primary kt-btn-sm">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Ajouter une ligne
                    </button>
                </div>
                <div style="overflow-x:auto;">
                    <table class="kt-table" id="lignes-table">
                        <thead>
                            <tr>
                                <th style="padding-left:20px; width:30%;">Désignation</th>
                                <th>Description</th>
                                <th style="width:80px; text-align:center;">Qté</th>
                                <th style="width:130px; text-align:right;">Prix Unit. HT ({{ currency_symbol() }})</th>
                                <th style="width:120px; text-align:right; padding-right:12px;">Total HT ({{ currency_symbol() }})</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="lignes-container">
                            <tr class="ligne-devis">
                                <td style="padding-left:20px; padding-right:8px; padding-top:10px; padding-bottom:10px;">
                                    <input type="text" name="lignes[0][designation]" value="{{ old('lignes.0.designation', $besoinsProspect ?? '') }}" required class="kt-form-control" style="margin:0;" placeholder="Prestation de service">
                                </td>
                                <td style="padding:10px 8px;">
                                    <input type="text" name="lignes[0][description]" class="kt-form-control" style="margin:0;" placeholder="Détail (optionnel)">
                                </td>
                                <td style="padding:10px 8px;">
                                    <input type="number" name="lignes[0][quantite]" step="0.01" min="0.01" value="1" required class="kt-form-control qty-input" style="margin:0; text-align:center;">
                                </td>
                                <td style="padding:10px 8px;">
                                    <input type="number" name="lignes[0][prix_unitaire]" step="0.01" min="0" value="0.00" required class="kt-form-control price-input" style="margin:0; text-align:right;">
                                </td>
                                <td style="padding:10px 12px 10px 8px; text-align:right; font-weight:700; color:#181c32;" class="subtotal-col">0,00 {{ currency_symbol() }}</td>
                                <td style="padding:10px 8px; text-align:center;">
                                    <button type="button" class="remove-line-btn" style="background:rgba(241,65,108,0.1); border:none; cursor:pointer; color:#f1416c; border-radius:6px; width:28px; height:28px; display:flex; align-items:center; justify-content:center; margin:auto; transition:all 0.2s;" onmouseover="this.style.background='#f1416c'; this.style.color='white';" onmouseout="this.style.background='rgba(241,65,108,0.1)'; this.style.color='#f1416c';">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totaux -->
                <div class="kt-card-footer">
                    <div style="display:flex; justify-content:flex-end;">
                        <div style="width:300px; background:white; border-radius:10px; padding:16px 20px; border:1px solid #eff2f5;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
                                <span style="color:#7e8299;">Sous-total HT</span>
                                <span id="total_ht" style="font-weight:600; color:#181c32;">0,00 {{ currency_symbol() }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:13px;">
                                <span style="color:#7e8299;">TVA</span>
                                <span id="total_tva" style="font-weight:600; color:#181c32;">0,00 {{ currency_symbol() }}</span>
                            </div>
                            <hr class="kt-separator" style="margin-bottom:12px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span style="font-size:14px; font-weight:700; color:#181c32;">Total TTC</span>
                                <span id="total_ttc" style="font-size:18px; font-weight:800; color:#3e97ff;">0,00 {{ currency_symbol() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <a href="{{ route('commercial.devis.index') }}" class="kt-btn kt-btn-light">Annuler</a>
                <button type="submit" class="kt-btn kt-btn-primary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Enregistrer le devis
                </button>
            </div>
        </form>
    </div>

    <script>
        const CURRENCY = @json(currency_symbol());
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle prospect/client
            const destType = document.getElementById('destinataire_type');
            const prospectGroup = document.getElementById('prospect_select_group');
            const clientGroup = document.getElementById('client_select_group');

            destType.addEventListener('change', function() {
                if (this.value === 'prospect') {
                    prospectGroup.style.display = '';
                    clientGroup.style.display = 'none';
                    clientGroup.querySelector('select').value = '';
                } else {
                    clientGroup.style.display = '';
                    prospectGroup.style.display = 'none';
                    prospectGroup.querySelector('select').value = '';
                }
            });

            // Initialize display
            if (destType.value === 'prospect') {
                prospectGroup.style.display = '';
                clientGroup.style.display = 'none';
            } else {
                clientGroup.style.display = '';
                prospectGroup.style.display = 'none';
            }

            const container = document.getElementById('lignes-container');
            const addBtn = document.getElementById('add-line-btn');
            let lineIndex = 1;

            function createRowHTML(idx) {
                return `
                    <tr class="ligne-devis">
                        <td style="padding-left:20px; padding-right:8px; padding-top:10px; padding-bottom:10px;">
                            <input type="text" name="lignes[${idx}][designation]" required class="kt-form-control" style="margin:0;" placeholder="Prestation de service">
                        </td>
                        <td style="padding:10px 8px;">
                            <input type="text" name="lignes[${idx}][description]" class="kt-form-control" style="margin:0;" placeholder="Détail (optionnel)">
                        </td>
                        <td style="padding:10px 8px;">
                            <input type="number" name="lignes[${idx}][quantite]" step="0.01" min="0.01" value="1" required class="kt-form-control qty-input" style="margin:0; text-align:center;">
                        </td>
                        <td style="padding:10px 8px;">
                            <input type="number" name="lignes[${idx}][prix_unitaire]" step="0.01" min="0" value="0.00" required class="kt-form-control price-input" style="margin:0; text-align:right;">
                        </td>
                        <td style="padding:10px 12px 10px 8px; text-align:right; font-weight:700; color:#181c32;" class="subtotal-col">0,00 ${CURRENCY}</td>
                        <td style="padding:10px 8px; text-align:center;">
                            <button type="button" class="remove-line-btn" style="background:rgba(241,65,108,0.1); border:none; cursor:pointer; color:#f1416c; border-radius:6px; width:28px; height:28px; display:flex; align-items:center; justify-content:center; margin:auto; transition:all 0.2s;" onmouseover="this.style.background='#f1416c'; this.style.color='white';" onmouseout="this.style.background='rgba(241,65,108,0.1)'; this.style.color='#f1416c';">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </td>
                    </tr>
                `;
            }

            addBtn.addEventListener('click', function() {
                container.insertAdjacentHTML('beforeend', createRowHTML(lineIndex++));
                calculateTotals();
            });

            container.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-line-btn');
                if (btn) {
                    const rows = container.querySelectorAll('.ligne-devis');
                    if (rows.length > 1) {
                        btn.closest('tr').remove();
                        calculateTotals();
                    }
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                    calculateTotals();
                }
            });

            document.getElementById('taux_tva').addEventListener('input', calculateTotals);

            function calculateTotals() {
                let totalHT = 0;
                container.querySelectorAll('.ligne-devis').forEach(row => {
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const sub = qty * price;
                    totalHT += sub;
                    row.querySelector('.subtotal-col').textContent = sub.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ' + CURRENCY;
                });
                const tva = parseFloat(document.getElementById('taux_tva').value) || 0;
                const totalTVA = totalHT * (tva / 100);
                const totalTTC = totalHT + totalTVA;
                document.getElementById('total_ht').textContent  = totalHT.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ' + CURRENCY;
                document.getElementById('total_tva').textContent = totalTVA.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ' + CURRENCY;
                document.getElementById('total_ttc').textContent = totalTTC.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ' + CURRENCY;
            }

            calculateTotals();
        });
    </script>
</x-commercial-layout>
