<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre attestation est disponible</title>
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
        .btn { display: inline-block; background-color: #aa6808; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 15px; font-weight: 600; margin: 16px 0; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .highlight { color: #aa6808; font-weight: bold; }
        .check-icon { font-size: 40px; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ app()->environment('production') ? url('/sophieWeeding/public/images/logo.png') : asset('images/logo.png') }}" alt="Sophie Weddings Dream" style="max-width: 180px; margin-bottom: 12px;">
        <div class="check-icon">✓</div>
        <h1>Votre participation est confirmée !</h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $registration->name }}</strong>,</p>
        <p>
            Votre participation à la masterclass <span class="highlight">{{ $masterclass->title }}</span>
            a été confirmée par notre équipe. <strong>Votre attestation de participation est maintenant disponible.</strong>
        </p>

        <div class="summary-box">
            <h2>Récapitulatif</h2>
            <div class="info-item">
                <span class="label">Masterclass :</span> {{ $masterclass->title }}
            </div>
            <div class="info-item">
                <span class="label">Niveau :</span>
                <span class="badge">{{ $masterclass->niveau }}</span>
            </div>
            <div class="info-item">
                <span class="label">Date :</span> {{ $session->start_date->format('d/m/Y') }} à {{ $session->start_date->format('H:i') }}
            </div>
            <div class="info-item">
                <span class="label">Format :</span> {{ $session->location_label }}
            </div>
        </div>

        <p style="text-align: center;">
            <a href="{{ route('prospect.portal.login') }}" class="btn">Accéder à mon espace & télécharger l'attestation</a>
        </p>

        <p style="font-size: 13px; color: #777; margin-top: 16px;">
            Connectez-vous avec votre adresse email <strong>{{ $registration->email }}</strong> pour télécharger votre attestation PDF.
        </p>

        <p>Merci pour votre participation,<br><strong>Sophie Weddings Dream</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Sophie Weddings Dream. Tous droits réservés.</p>
    </div>
</div>
</body>
</html>
