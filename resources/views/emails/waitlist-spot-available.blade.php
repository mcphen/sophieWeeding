<!DOCTYPE html>
<html lang="fr" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Une place s'est libérée</title>
    <style>
        body { margin:0;padding:0;background:#f5ede0;font-family:'Georgia','Times New Roman',serif; }
        a { color:#aa6808; }
        @media only screen and (max-width:600px) {
            .email-wrapper { width:100% !important; }
            .email-body { padding:24px 18px !important; }
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
        <td align="center" style="background:linear-gradient(145deg,#1a5e1a 0%,#2d8c2d 50%,#4aab4a 100%);padding:48px 40px 40px;">
            <div style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:50%;margin:0 auto 16px;line-height:64px;text-align:center;font-size:28px;">🎉</div>
            <h1 style="margin:0 0 8px;font-family:'Georgia',serif;font-size:26px;font-weight:700;color:#fff;line-height:1.2;">
                Une place vient de se libérer !
            </h1>
            <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.85);line-height:1.5;">
                Vous étiez sur liste d'attente — agissez vite
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
                Bonne nouvelle ! Une <strong style="color:#1a1a1a;">place vient de se libérer</strong> dans la session
                que vous attendiez. Connectez-vous dès maintenant pour finaliser votre inscription avant qu'elle ne soit prise.
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
                                <td width="20" valign="top"><span style="font-size:14px;">📅</span></td>
                                <td style="padding-left:8px;font-size:13px;color:#6b5135;font-family:Arial,sans-serif;line-height:1.5;">
                                    {{ $session->start_date->format('d/m/Y') }} à {{ $session->start_date->format('H:i') }}
                                    @if($session->end_date) – {{ $session->end_date->format('H:i') }} @endif
                                </td>
                            </tr>
                            <tr><td colspan="2" style="height:8px;"></td></tr>
                            <tr>
                                <td width="20" valign="top"><span style="font-size:14px;">📍</span></td>
                                <td style="padding-left:8px;font-size:13px;color:#6b5135;font-family:Arial,sans-serif;line-height:1.5;">
                                    {{ $session->location_label }}
                                    @if($session->adresse) — {{ $session->adresse }} @endif
                                </td>
                            </tr>
                            <tr><td colspan="2" style="height:8px;"></td></tr>
                            <tr>
                                <td width="20" valign="top"><span style="font-size:14px;">💰</span></td>
                                <td style="padding-left:8px;font-size:13px;font-weight:700;color:#aa6808;font-family:Arial,sans-serif;">
                                    {{ $session->formatted_price }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Urgency notice --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="background:#fff8ec;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;margin-bottom:28px;">
                <tr>
                    <td style="padding:14px 18px;">
                        <p style="margin:0;font-size:13px;line-height:1.6;color:#7a5020;font-family:Arial,sans-serif;">
                            ⚠️ <strong>Cette place est disponible dès maintenant.</strong>
                            Les places partent vite — inscrivez-vous rapidement pour la réserver.
                        </p>
                    </td>
                </tr>
            </table>

            {{-- CTA --}}
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="margin-bottom:28px;">
                <tr>
                    <td align="center">
                        <a href="{{ route('masterclass.show', $masterclass->slug) }}"
                            style="display:inline-block;background:linear-gradient(135deg,#aa6808 0%,#c97f1e 100%);
                                   color:#fff;text-decoration:none;font-family:Arial,sans-serif;font-size:15px;
                                   font-weight:700;padding:15px 40px;border-radius:8px;
                                   box-shadow:0 4px 12px rgba(170,104,8,0.35);">
                            Finaliser mon inscription &nbsp;→
                        </a>
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
