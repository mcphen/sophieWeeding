<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle demande de contact</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #aa6808; /* Rose pastel */
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .info-block {
            margin-bottom: 25px;
        }
        .info-block h2 {
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #aa6808; /* Rose pastel */
            border-bottom: 1px solid #aa6808;
            padding-bottom: 5px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .message-box {
            background-color: #f9f9f9;
            border-left: 4px solid #aa6808;
            padding: 15px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            background-color: #aa6808;
            color: white !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Sophie Weddings Dream Logo" style="max-width: 200px; margin-bottom: 15px;">
            <h1>Nouvelle demande de contact</h1>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            <p>Vous avez reçu une nouvelle demande de contact via le formulaire de votre site web Sophie Weddings Dream.</p>

            <div class="info-block">
                <h2>Informations du client</h2>
                <div class="info-item">
                    <span class="label">Nom:</span>
                    <span class="value">{{ $clientData['last_name'] }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Prénom:</span>
                    <span class="value">{{ $clientData['first_name'] }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Email:</span>
                    <span class="value">{{ $clientData['email'] }}</span>
                </div>
                @if(isset($clientData['phone']) && $clientData['phone'])
                <div class="info-item">
                    <span class="label">Téléphone:</span>
                    <span class="value">{{ $clientData['phone'] }}</span>
                </div>
                @endif
            </div>

            <div class="info-block">
                <h2>Détails du message</h2>
                <div class="info-item">
                    <span class="label">Sujet:</span>
                    <span class="value">{{ $contact->subject }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Message:</span>
                    <div class="message-box">
                        {{ $contact->description }}
                    </div>
                </div>
                <div class="info-item">
                    <span class="label">Date d'envoi:</span>
                    <span class="value">{{ $contact->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            <a href="{{ route('admin.contacts.show', $contact->id) }}" class="button">Voir le message dans l'administration</a>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Sophie Weddings Dream. Tous droits réservés.</p>
            <p>Ce message a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
