<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel masterclass</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #aa6808; padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .content { padding: 30px; }
        .summary-box { background-color: #fff8ee; border: 2px solid #aa6808; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .summary-box h2 { color: #aa6808; font-size: 16px; margin-top: 0; margin-bottom: 12px; }
        .info-item { margin-bottom: 8px; }
        .label { font-weight: bold; color: #555; }
        .badge { display: inline-block; background-color: #aa6808; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 13px; }
        .highlight { color: #aa6808; font-weight: bold; font-size: 20px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .button { display: inline-block; background-color: #aa6808; color: white !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; margin-top: 15px; font-weight: bold; font-size: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ app()->environment('production') ? url('/sophieWeeding/public/images/logo.png') : asset('images/logo.png') }}" alt="Sophie Weddings Dreams" style="max-width: 180px; margin-bottom: 12px;">
        <h1>⏰ Rappel — Votre masterclass approche !</h1>
    </div>

    @if($masterclass->image_url)
    <img src="{{ url($masterclass->image_url) }}" alt="{{ $masterclass->title }}" style="width:100%;max-height:220px;object-fit:cover;display:block;">
    @endif

    <div class="content">
        <p>Bonjour <strong>{{ $registration->name }}</strong>,</p>
        <p>
            Nous vous rappelons que vous êtes inscrit(e) à la masterclass
            <strong>{{ $masterclass->title }}</strong>.
            Voici un résumé de votre session :
        </p>

        <div class="summary-box">
            <h2>Votre session</h2>
            <div class="info-item">
                <span class="label">Masterclass :</span>
                <span>{{ $masterclass->title }}</span>
            </div>
            <div class="info-item">
                <span class="label">Niveau :</span>
                <span class="badge">{{ $masterclass->niveau }}</span>
            </div>
            <div class="info-item">
                <span class="label">Date :</span>
                <span class="highlight">{{ $session->start_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Heure :</span>
                <span>{{ $session->start_date->format('H:i') }}
                    @if($session->end_date) – {{ $session->end_date->format('H:i') }} @endif
                </span>
            </div>
            <div class="info-item">
                <span class="label">Format :</span>
                <span>{{ $session->location_label }}</span>
            </div>
            @if($session->adresse)
            <div class="info-item">
                <span class="label">Lieu :</span>
                <span>{{ $session->adresse }}</span>
            </div>
            @if($session->google_maps_url)
            <div class="info-item" style="margin-top: 8px;">
                <a href="{{ $session->google_maps_url }}" target="_blank" class="button" style="font-size: 13px; padding: 8px 16px;">
                    Voir sur Google Maps
                </a>
            </div>
            @endif
            @endif
            @if($session->online_link)
            <div class="info-item" style="margin-top: 8px;">
                <span class="label">Lien de connexion :</span><br>
                <a href="{{ $session->online_link }}" target="_blank" class="button" style="font-size: 13px; padding: 8px 16px;">
                    Rejoindre la session en ligne
                </a>
            </div>
            @endif
        </div>

        <p>En cas d'empêchement ou pour toute question, n'hésitez pas à nous contacter.</p>
        <p>Nous avons hâte de vous retrouver !<br><strong>Sophie Weddings Dreams</strong></p>
    </div>

    <div class="footer">
        <p><strong>Sophie Weddings Dreams</strong>
        @php $phone = \App\Models\Setting::get('contact_phone'); $phoneFixed = \App\Models\Setting::get('contact_phone_fixed'); @endphp
        @if($phone || $phoneFixed) — {{ implode(' · ', array_filter([$phone, $phoneFixed])) }} @endif
        </p>
        <p>© {{ date('Y') }} Sophie Weddings Dreams. Tous droits réservés.</p>
        <p>Ce message a été envoyé automatiquement suite à votre inscription.</p>
    </div>
</div>
</body>
</html>
