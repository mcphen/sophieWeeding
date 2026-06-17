<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle inscription masterclass</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #aa6808; padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .content { padding: 30px; }
        .info-block { margin-bottom: 25px; }
        .info-block h2 { font-size: 16px; margin-top: 0; margin-bottom: 10px; color: #aa6808; border-bottom: 1px solid #aa6808; padding-bottom: 5px; }
        .info-item { margin-bottom: 8px; }
        .label { font-weight: bold; color: #555; }
        .badge { display: inline-block; background-color: #aa6808; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 13px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .button { display: inline-block; background-color: #aa6808; color: white !important; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-top: 15px; font-weight: bold; }
        .message-box { background-color: #f9f9f9; border-left: 4px solid #aa6808; padding: 12px; margin-top: 6px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Sophie Weddings Dream" style="max-width: 180px; margin-bottom: 12px;">
        <h1>Nouvelle inscription Masterclass</h1>
    </div>

    <div class="content">
        <p>Bonjour,</p>
        <p>Une nouvelle inscription vient d'être enregistrée pour l'une de vos masterclasses.</p>

        <div class="info-block">
            <h2>Masterclass concernée</h2>
            <div class="info-item">
                <span class="label">Titre :</span>
                <span>{{ $masterclass->title }}</span>
            </div>
            <div class="info-item">
                <span class="label">Niveau :</span>
                <span class="badge">{{ $masterclass->niveau }}</span>
            </div>
        </div>

        <div class="info-block">
            <h2>Session choisie</h2>
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
                <span class="label">Adresse :</span>
                <span>{{ $session->adresse }}</span>
            </div>
            @endif
            <div class="info-item">
                <span class="label">Prix :</span>
                <span>{{ $session->formatted_price }}</span>
            </div>
            <div class="info-item">
                <span class="label">Inscrits :</span>
                <span>{{ $session->registration_count }}
                    @if($session->max_participants) / {{ $session->max_participants }} @endif
                </span>
            </div>
        </div>

        <div class="info-block">
            <h2>Informations du prospect</h2>
            <div class="info-item">
                <span class="label">Nom :</span>
                <span>{{ $registration->name }}</span>
            </div>
            <div class="info-item">
                <span class="label">Email :</span>
                <span>{{ $registration->email }}</span>
            </div>
            @if($registration->phone)
            <div class="info-item">
                <span class="label">Téléphone :</span>
                <span>{{ $registration->phone }}</span>
            </div>
            @endif
            @if($registration->message)
            <div class="info-item">
                <span class="label">Message :</span>
                <div class="message-box">{{ $registration->message }}</div>
            </div>
            @endif
            <div class="info-item">
                <span class="label">Date d'inscription :</span>
                <span>{{ $registration->created_at->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Sophie Weddings Dream. Tous droits réservés.</p>
        <p>Ce message a été envoyé automatiquement depuis votre site web.</p>
    </div>
</div>
</body>
</html>
