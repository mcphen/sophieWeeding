<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de participation</title>
    <style>
        @page { size: A3 landscape; margin: 0; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            font-family: 'DejaVu Serif', Georgia, serif;
            color: #1a1a1a;
            background: #fff;
            width: 420mm;
            height: 297mm;
        }

        .frame-outer {
            position: absolute;
            top: 7mm; left: 7mm; right: 7mm; bottom: 7mm;
            border: 5px solid #aa6808;
        }
        .frame-inner {
            position: absolute;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            border: 1px solid #e5c87a;
        }
        .watermark {
            position: absolute;
            top: 108mm; left: 55mm;
            font-size: 100px;
            color: rgba(170, 104, 8, 0.04);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 10px;
            white-space: nowrap;
        }

        /* Centrage vertical via table — réservé pour le contenu sans les signatures */
        .page-table {
            position: absolute;
            top: 16mm; left: 16mm; right: 16mm; bottom: 55mm;
            display: table;
            width: calc(100% - 32mm);
        }
        .page-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .page {
            text-align: center;
        }

        /* Logo */
        .logo { margin-bottom: 5mm; }
        .logo img { height: 150px; }

        /* Séparateur */
        .divider {
            border: none;
            border-top: 1.5px solid #aa6808;
            margin: 3mm 0;
        }
        .divider-thin {
            border: none;
            border-top: 1px solid #e5c87a;
            margin: 2mm 20mm;
        }

        /* Titre */
        .title {
            font-size: 40px;
            font-weight: bold;
            color: #aa6808;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin: 4mm 0 1mm;
        }
        .issued {
            font-size: 9px;
            color: #aaa;
            font-style: italic;
            margin-bottom: 5mm;
        }

        /* Corps */
        .body-text {
            font-size: 14px;
            line-height: 1.8;
            color: #444;
        }
        .participant-name {
            font-size: 38px;
            font-weight: bold;
            color: #1a1a1a;
            display: inline-block;
            border-bottom: 2px solid #aa6808;
            padding: 0 15mm;
            margin: 3mm 0 2mm;
            letter-spacing: 1px;
        }
        .masterclass-title {
            font-size: 22px;
            font-weight: bold;
            color: #aa6808;
            font-style: italic;
        }
        .niveau-badge {
            display: inline-block;
            background: #aa6808;
            color: #fff;
            font-size: 12px;
            padding: 2px 11px;
            border-radius: 10px;
            margin-top: 1mm;
        }

        /* Détails en ligne */
        .details-row {
            margin: 5mm 10mm 0;
            border-top: 1px solid #f0e8d5;
            border-bottom: 1px solid #f0e8d5;
            padding: 3mm 0;
        }
        .detail-cell {
            display: inline-block;
            width: 22%;
            text-align: center;
            vertical-align: top;
            padding: 0 2mm;
        }
        .detail-label {
            font-size: 8px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin-top: 1mm;
        }

        /* Signatures — épinglées en bas de page */
        .sig-section {
            position: absolute;
            bottom: 24mm;
            left: 25mm;
            right: 25mm;
        }
        .sig-left  { float: left;  width: 35%; }
        .sig-right { float: right; width: 35%; }
        .clearfix::after { content: ''; display: block; clear: both; }
        .sig-space { height: 12mm; }
        .sig-line {
            border-top: 1px solid #777;
            padding-top: 2mm;
            font-size: 9px;
            color: #555;
        }

        /* Pied */
        .footer {
            position: absolute;
            bottom: 13mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #ccc;
            border-top: 1px solid #f5f5f5;
            padding-top: 2mm;
        }
    </style>
</head>
<body>
<div class="frame-outer"></div>
<div class="frame-inner"></div>
<div class="watermark">Sophie Weddings</div>

<div class="page-table">
<div class="page-cell">
<div class="page">

    <!-- Logo -->
    <div class="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="Sophie Weddings Dream">
    </div>

    <hr class="divider">

    <!-- Titre -->
    <div class="title">Attestation de Participation</div>
    <div class="issued">Émise le {{ now()->format('d/m/Y') }}</div>

    <hr class="divider-thin">

    <!-- Corps -->
    <div class="body-text">
        <p>Nous attestons par la présente que</p>
        <p><span class="participant-name">{{ $registration->name }}</span></p>
        <p>a participé à la masterclass</p>
        <p style="margin-top: 2mm;"><span class="masterclass-title">« {{ $masterclass->title }} »</span></p>
        <p style="margin-top: 1mm;"><span class="niveau-badge">Niveau : {{ $masterclass->niveau }}</span></p>
        <p style="margin-top: 3mm;">organisée par <strong>Sophie Weddings Dream</strong>.</p>
    </div>

    <!-- Détails de la session -->
    <div class="details-row">
        <span class="detail-cell">
            <div class="detail-label">Date</div>
            <div class="detail-value">{{ $session->start_date->format('d/m/Y') }}</div>
        </span>
        <span class="detail-cell">
            <div class="detail-label">Horaire</div>
            <div class="detail-value">
                {{ $session->start_date->format('H:i') }}@if($session->end_date) – {{ $session->end_date->format('H:i') }}@endif
            </div>
        </span>
        <span class="detail-cell">
            <div class="detail-label">Format</div>
            <div class="detail-value">{{ $session->location_label }}</div>
        </span>
        @if($session->adresse)
        <span class="detail-cell">
            <div class="detail-label">Lieu</div>
            <div class="detail-value">{{ $session->adresse }}</div>
        </span>
        @endif
    </div>

</div>
</div>
</div>

<!-- Signatures épinglées en bas -->
<div class="sig-section clearfix">
    <div class="sig-left">
        <div class="sig-space"></div>
        <div class="sig-line">
            Date d'émission<br><strong>{{ now()->format('d/m/Y') }}</strong>
        </div>
    </div>
    <div class="sig-right">
        @php
            $signaturePath = \App\Models\Setting::get('attestation_signature');
            $signatureFile = $signaturePath ? storage_path('app/public/' . $signaturePath) : null;
        @endphp
        @if($signatureFile && file_exists($signatureFile))
            <img src="{{ $signatureFile }}" style="height: 12mm; max-width: 80%; object-fit: contain; display: block; margin: 0 auto;">
        @else
            <div class="sig-space"></div>
        @endif
        <div class="sig-line">
            Signature &amp; Cachet<br><strong>Sophie Weddings Dream</strong>
        </div>
    </div>
</div>

<!-- Pied de page -->
<div class="footer">
    Document officiel — N° d'inscription #{{ str_pad($registration->id, 5, '0', STR_PAD_LEFT) }} — Sophie Weddings Dream, Dakar, Sénégal
</div>

</body>
</html>
