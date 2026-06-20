<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #aa6808; padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .content { padding: 30px; }
        .summary-box { background-color: #fff8ee; border: 1px solid #aa6808; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .summary-box h2 { color: #aa6808; font-size: 16px; margin-top: 0; }
        .info-item { margin-bottom: 8px; }
        .label { font-weight: bold; color: #555; }
        .badge { display: inline-block; background-color: #aa6808; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 13px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .highlight { color: #aa6808; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ app()->environment('production') ? url('/sophieWeeding/public/images/logo.png') : asset('images/logo.png') }}" alt="Sophie Weddings Dreams" style="max-width: 180px; margin-bottom: 12px;">
        <h1>Inscription confirmée ✓</h1>
    </div>

    @if($masterclass->image_url)
    <img src="{{ url($masterclass->image_url) }}" alt="{{ $masterclass->title }}" style="width:100%;max-height:220px;object-fit:cover;display:block;">
    @endif

    <div class="content">
        <p>Bonjour <strong>{{ $registration->name }}</strong>,</p>
        <p>
            Nous avons bien reçu votre inscription à la masterclass
            <span class="highlight">{{ $masterclass->title }}</span>.
            Nous vous contacterons prochainement pour vous communiquer tous les détails.
        </p>

        <div class="summary-box">
            <h2>Récapitulatif de votre inscription</h2>
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
                <span>{{ $session->start_date->format('d/m/Y') }}</span>
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
            @endif
            <div class="info-item">
                <span class="label">Prix :</span>
                <span>{{ $session->formatted_price }}</span>
            </div>
        </div>

        <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
        <p>À très bientôt,<br><strong>Sophie Weddings Dreams</strong></p>
    </div>

    <div class="footer">
        <p><strong>Sophie Weddings Dreams</strong>
        @php $phone = \App\Models\Setting::get('contact_phone'); $phoneFixed = \App\Models\Setting::get('contact_phone_fixed'); @endphp
        @if($phone || $phoneFixed) — {{ implode(' · ', array_filter([$phone, $phoneFixed])) }} @endif
        </p>
        <p>© {{ date('Y') }} Sophie Weddings Dreams. Tous droits réservés.</p>
        <p>Ce message a été envoyé automatiquement suite à votre inscription sur notre site.</p>
    </div>
</div>
</body>
</html>
