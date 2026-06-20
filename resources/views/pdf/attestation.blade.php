<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat — {{ $registration->name }}</title>
    <style>
        @font-face {
            font-family: 'GreatVibes';
            src: url('{{ str_replace("\\", "/", public_path("fonts/GreatVibes-Regular.ttf")) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page { size: A4 landscape; margin: 0; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            font-family: 'DejaVu Serif', Georgia, serif;
            color: #1a1a1a;
            background: #ffffff;
            width: 297mm;
            height: 210mm;
        }

        /* Filigrane */
        .watermark {
            position: absolute;
            top: 85mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 72px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-weight: bold;
            color: rgba(201, 168, 76, 0.05);
            letter-spacing: 18px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Contenu principal */
        .page {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            padding: 10mm 28mm 8mm;
            text-align: center;
        }

        /* Logo */
        .logo {
            margin-bottom: 5mm;
        }
        .logo img {
            height: 150px;
        }

        /* Titre CERTIFICAT */
        .title-certificat {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 46px;
            font-weight: bold;
            letter-spacing: 14px;
            color: #1a1a1a;
            margin-bottom: 5mm;
            text-transform: uppercase;
        }

        /* Sous-titre formation */
        .subtitle {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5px;
            letter-spacing: 4px;
            color: #555;
            text-transform: uppercase;
            line-height: 1.6;
            margin-bottom: 7mm;
        }

        /* "Ce présent document atteste que :" */
        .intro-text {
            font-size: 11px;
            color: #999;
            font-style: italic;
            letter-spacing: 1px;
            margin-bottom: 3mm;
        }

        /* Nom du participant en cursive dorée */
        .participant-name {
            font-family: 'GreatVibes', cursive;
            font-size: 62px;
            color: #c9a84c;
            line-height: 1.15;
            margin-bottom: 5mm;
        }

        /* Texte de participation */
        .body-text {
            font-size: 12px;
            color: #333;
            line-height: 1.9;
        }
        .training-bold {
            font-weight: bold;
            font-style: italic;
        }

        /* Date et lieu */
        .date-location {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 4mm;
        }

        /* Zone signature — centrée en bas */
        .sig-section {
            position: absolute;
            bottom: 14mm;
            left: 0;
            right: 0;
            text-align: center;
        }
        .sig-inner {
            display: inline-block;
            text-align: center;
            min-width: 55mm;
        }
        .sig-img {
            height: 18mm;
            max-width: 55mm;
            object-fit: contain;
            display: block;
            margin: 0 auto 2mm;
        }
        .sig-line {
            border-top: 1px solid #666;
            padding-top: 2.5mm;
        }
        .sig-name {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 1px;
        }
        .sig-role {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8.5px;
            color: #888;
            letter-spacing: 1px;
            margin-top: 1mm;
        }

        /* QR Code — bas droit */
        .qr-section {
            position: absolute;
            bottom: 10mm;
            right: 18mm;
            text-align: center;
        }
        .qr-section img {
            width: 22mm;
            height: 22mm;
            display: block;
        }
        .qr-label {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 5.5px;
            color: #bbb;
            letter-spacing: 0.5px;
            margin-top: 1mm;
            text-transform: uppercase;
        }

        /* Pied de page discret */
        .footer {
            position: absolute;
            bottom: 4mm;
            left: 0;
            right: 0;
            text-align: center;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 6.5px;
            color: #ccc;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

{{-- Filigrane --}}
<div class="watermark">Sophie Weddings Dreams</div>

<div class="page">

    {{-- Logo --}}
    <div class="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="Sophie Weddings Dreams">
    </div>

    {{-- Titre --}}
    <div class="title-certificat">Certificat</div>

    {{-- Sous-titre : formation et niveau --}}
    <div class="subtitle">
        {{ strtoupper($masterclass->title) }}<br>
        ( Niveau {{ $masterclass->niveau }} )
    </div>

    {{-- Intro --}}
    <div class="intro-text">Ce présent document atteste que :</div>

    {{-- Nom en cursive --}}
    <div class="participant-name">{{ $registration->name }}</div>

    {{-- Corps --}}
    @php
        $daysFr   = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
        $monthsFr = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
        $d        = $session->start_date;
        $dateStr  = $daysFr[$d->dayOfWeek] . ' ' . $d->day . ' ' . $monthsFr[$d->month - 1] . ' ' . $d->year;
        $city     = \App\Models\Setting::get('attestation_city') ?? 'Dakar (Sénégal)';
    @endphp

    <div class="body-text">
        A participé à la formation
        <span class="training-bold">{{ strtoupper($masterclass->title) }}</span><br>
        organisée par <strong>Sophie Weddings Dreams</strong> / S.W.D. Academy
    </div>

    <div class="date-location">{{ $city }}, {{ $dateStr }}</div>

</div>

{{-- Signature --}}
<div class="sig-section">
    @php
        $signaturePath = \App\Models\Setting::get('attestation_signature');
        $signatureFile = $signaturePath ? storage_path('app/public/' . $signaturePath) : null;
        $directorName  = \App\Models\Setting::get('attestation_director_name') ?? 'Sophie Manca';
        $directorTitle = \App\Models\Setting::get('attestation_director_title') ?? 'Directrice';
    @endphp
    <div class="sig-inner">
        @if($signatureFile && file_exists($signatureFile))
            <img src="{{ $signatureFile }}" class="sig-img">
        @else
            <div style="height: 20mm;"></div>
        @endif
        <div class="sig-line">
            <div class="sig-name">{{ $directorName }}</div>
            <div class="sig-role">{{ $directorTitle }}</div>
        </div>
    </div>
</div>

{{-- QR Code d'authenticité --}}
<div class="qr-section">
    <img src="{{ $qrCodeUri }}">
    <div class="qr-label">Vérifier l'authenticité</div>
</div>

{{-- Pied de page --}}
<div class="footer">
    Certificat N° {{ str_pad($registration->id, 5, '0', STR_PAD_LEFT) }} &mdash; Sophie Weddings Dreams, Dakar, Sénégal
</div>

</body>
</html>
