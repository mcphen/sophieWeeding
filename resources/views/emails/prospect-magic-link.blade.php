<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès à votre espace inscrit</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #aa6808; padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .content { padding: 30px; }
        .btn { display: inline-block; background-color: #aa6808; color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: 600; margin: 20px 0; }
        .notice { background: #fff8ee; border: 1px solid #e5c87a; border-radius: 6px; padding: 14px 18px; font-size: 13px; color: #7a5200; margin-top: 20px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Sophie Weddings Dream" style="max-width: 180px; margin-bottom: 12px;">
        <h1>Accès à votre espace inscrit</h1>
    </div>
    <div class="content">
        <p>Bonjour,</p>
        <p>Vous avez demandé un lien de connexion à votre espace inscrit sur <strong>Sophie Weddings Dream</strong>.</p>
        <p>Cliquez sur le bouton ci-dessous pour accéder à vos inscriptions, télécharger vos attestations et consulter vos replays.</p>
        <p style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">Accéder à mon espace</a>
        </p>
        <div class="notice">
            <strong>Ce lien est valable 1 heure et ne peut être utilisé qu'une seule fois.</strong><br>
            Si vous n'avez pas demandé cet email, ignorez-le simplement.
        </div>
        <p style="margin-top: 20px;">À très bientôt,<br><strong>Sophie Weddings Dream</strong></p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Sophie Weddings Dream. Tous droits réservés.</p>
        <p>Si le bouton ne fonctionne pas, copiez ce lien : {{ $loginUrl }}</p>
    </div>
</div>
</body>
</html>
