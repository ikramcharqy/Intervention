@php
    $documentTitle = 'Export QR Codes';
    $documentReference = $chantier->code_chantier ?? (string) $chantier->id;
@endphp
@extends('pdf.layout')

@section('meta')
    <strong>Chantier :</strong> {{ $chantier->nom }}<br>
    <strong>Client :</strong> {{ $chantier->client?->nom ?? '—' }}
@endsection

@push('styles')
    .qr-grid { width: 100%; }
    .qr-cell {
        display: inline-block;
        width: 47%;
        vertical-align: top;
        box-sizing: border-box;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 14px;
        margin: 0 1.5% 14px 0;
        text-align: center;
        page-break-inside: avoid;
    }
    .qr-cell img { width: 150px; height: 150px; }
    .qr-nom { font-size: 11px; font-weight: 800; color: #0f172a; margin-top: 8px; }
    .qr-ref { font-size: 9.5px; font-family: 'DejaVu Sans Mono', monospace; color: #64748b; margin-top: 2px; }
@endpush

@section('content')
    <div class="section">
        <div class="section-header">QR Codes — {{ $emplacements->count() }} emplacement(s)</div>
        <div class="qr-grid">
            @forelse($emplacements as $e)
                <div class="qr-cell">
                    <img src="data:image/svg+xml;base64,{{ $qrByEmplacement[$e->id] }}" alt="QR {{ $e->qr_code }}">
                    <div class="qr-nom">{{ $e->nom }}</div>
                    <div class="qr-ref">{{ $e->qr_code }}</div>
                </div>
            @empty
                <p style="text-align:center; color:#64748b;">Aucun emplacement configuré sur ce chantier.</p>
            @endforelse
        </div>
    </div>
@endsection
