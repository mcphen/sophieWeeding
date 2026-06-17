<!DOCTYPE html>
<html lang="fr" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste d'attente confirmée</title>
    <style>
        body { margin:0;padding:0;background:#f5ede0;font-family:'Georgia','Times New Roman',serif; }
        a { color:#aa6808; }
        @media only screen and (max-width:600px) {
            .email-wrapper { width:100% !important; }
            .email-body { padding:24px 18px !important; }
            .hero-pad { padding:36px 20px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#f5ede0;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f5ede0;">
<tr><td align="center" style="padding:32px 16px;">

<table class="email-wrapper" role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"
    style="max-width:600px;width:100%;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(100,60,0,0.10);">

    {{-- HERO --}}
    <tr>
        <td class="hero-pad" align="center" style="background:linear-gradient(145deg,#7a4a05 0%,#aa6808 45%,#d1922f 100%);padding:48px 40px 40px;">
            <div style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:50%;margin:0 auto 16px;line-height:64px;text-align:center;font-size:28px;">⏳</div>
            <h1 style="margin:0 0 8px;font-family:'Georgia',serif;font-size:26px;font-weight:700;color:#fff;line-height:1.2;">
                Vous êtes sur liste d'attente
            </h1>
            <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.80);line-height:1.5;">
                Nous vous contacterons dès qu'une place se libère
            </p>
        </td>
    </tr>

    {{-- BODY --}}
    <tr>
        <td class="email-body" style="padding:36px 40px 28px;">

            <p style="margin:0 0 20px;font-size:16px;color:#5a3e1b;font-family:'Georgia',serif;font-style:italic;">
                Bonjour {{ $entry->name }},
            </p>

            <p style="margin:0 0 24px;font-size:14px;line-height:1.8;color:#555;font-family:Arial,sans-serif;">
                Votre demande d'inscription sur liste d'attente a bien été enregistrée pour la masterclass suivante.
                Dès qu'une place se libère, vous recevrez une notification par email pour finaliser votre inscription.
            </p>

            {{-- Masterclass card --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="background:#fdf6ec;border-radius:12px;border:1px solid #f0dfc0;margin-bottom:24px;overflow:hidden;">
                <tr><td style="height:4px;background:linear-gradient(90deg,#aa6808,#d1922f);"></td></tr>
                <tr>
                    <td style="padding:22px 24px;">
                        <p style="margin:0 0 4px;font-size:11px;font-family:Arial,sans-serif;color:#aa6808;letter-spacing:1.5px;text-transform:uppercase;font-weight:700;">
                            {{ $masterclass->niveau }}
                        </p>
                        <h2 style="margin:0 0 14px;font-family:'Georgia',serif;font-size:19px;color:#3d2200;font-weight:700;line-height:1.3;">
                            {{ $masterclass->title }}
                        </h2>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td width="20" valign="top" style="padding-top:1px;">
                                    <span style="font-size:14px;">📅</span>
                                </td>
                                <td style="padding-left:8px;font-size:13px;color:#6b5135;font-family:Arial,sans-serif;line-height:1.5;">
                                    {{ $session->start_date->format('d/m/Y') }} à {{ $session->start_date->format('H:i') }}
                                    @if($session->end_date) – {{ $session->end_date->format('H:i') }} @endif
                                </td>
                            </tr>
                            <tr><td colspan="2" style="height:8px;"></td></tr>
                            <tr>
                                <td width="20" valign="top" style="padding-top:1px;">
                                    <span style="font-size:14px;">📍</span>
                                </td>
                                <td style="padding-left:8px;font-size:13px;color:#6b5135;font-family:Arial,sans-serif;line-height:1.5;">
                                    {{ $session->location_label }}
                                    @if($session->adresse) — {{ $session->adresse }} @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Info box --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="background:#fff8ec;border:1px solid #f0dfc0;border-radius:10px;margin-bottom:28px;">
                <tr>
                    <td style="padding:16px 20px;">
                        <p style="margin:0;font-size:13px;line-height:1.7;color:#7a5020;font-family:Arial,sans-serif;">
                            <strong>Comment ça fonctionne ?</strong><br>
                            Dès qu'une place se libère dans cette session, vous serez le(la) premier(e) notifié(e) par email.
                            Vous aurez alors la possibilité de finaliser votre inscription.
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- FOOTER --}}
    <tr>
        <td style="background:#3d2200;padding:24px 40px;text-align:center;">
            <p style="margin:0 0 6px;font-family:'Georgia',serif;font-size:14px;color:#e8c98a;">Sophie Weddings Dream</p>
            <p style="margin:0 0 12px;font-size:11px;color:rgba(255,255,255,0.40);font-family:Arial,sans-serif;">Dakar, Sénégal</p>
            <p style="margin:0;font-size:10px;color:rgba(255,255,255,0.25);font-family:Arial,sans-serif;">
                © {{ date('Y') }} Sophie Weddings Dream. Tous droits réservés.
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
