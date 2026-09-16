@extends('pdf.layout')

@section('meta')
    @if(!empty($destinataire))
        <strong>Destinataire :</strong> {{ $destinataire }}
    @endif
@endsection

@section('content')
    <div class="section">
        @if(!empty($sousTitre))
            <div class="section-header">{{ $sousTitre }}</div>
        @endif
        <div class="info-box" style="font-size:10px; line-height:1.7;">
            {!! nl2br(e($corps)) !!}
        </div>
    </div>

    @isset($tableau)
        <div class="section">
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach($tableau['colonnes'] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($tableau['lignes'] as $ligne)
                        <tr>
                            @foreach($ligne as $cellule)
                                <td>{{ $cellule }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
@endsection
