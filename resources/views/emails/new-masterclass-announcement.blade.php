<!DOCTYPE html>
<html lang="fr" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Nouvelle masterclass — Sophie Weddings Dream</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        /* Reset */
        *, *::before, *::after { box-sizing: border-box; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0; mso-table-rspace: 0; }
        img { -ms-interpolation-mode: bicubic; border: 0; display: block; }
        body { margin: 0; padding: 0; background-color: #f5ede0; font-family: 'Georgia', 'Times New Roman', serif; }
        a { color: #aa6808; }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper { width: 100% !important; }
            .email-body { padding: 28px 20px !important; }
            .hero-pad { padding: 40px 20px !important; }
            .detail-cell { display: block !important; width: 100% !important; padding: 8px 0 !important; border-right: none !important; border-bottom: 1px solid #f0e3cc !important; }
            .detail-cell:last-child { border-bottom: none !important; }
            .btn-td { display: block !important; text-align: center !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f5ede0;">

{{-- Outer wrapper --}}
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f5ede0;">
<tr><td align="center" style="padding: 32px 16px;">

{{-- Email card --}}
<table class="email-wrapper" role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"
    style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(100,60,0,0.10);">

    {{-- ══ HERO ══ --}}
    <tr>
        <td class="hero-pad" align="center"
            style="background: linear-gradient(145deg, #7a4a05 0%, #aa6808 45%, #d1922f 100%);
                   padding: 52px 40px 44px;
                   position: relative;">

            {{-- Decorative top line --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td align="center" style="padding-bottom:20px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="width:40px;height:1px;background:rgba(255,255,255,0.3);"></td>
                                <td style="width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,0.6);margin:0 8px;display:inline-block;"></td>
                                <td style="width:40px;height:1px;background:rgba(255,255,255,0.3);"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Logo --}}
            <img src="{{ app()->environment('production') ? url('/sophieWeeding/public/images/logo.png') : asset('images/logo.png') }}" alt="Sophie Weddings Dream" style="max-width:160px;display:block;margin:0 auto 16px;">

            {{-- Brand name --}}
            <p style="margin:0 0 6px;font-family:'Georgia',serif;font-size:13px;letter-spacing:4px;text-transform:uppercase;color:rgba(255,255,255,0.7);">
                Sophie Weddings Dream
            </p>

            {{-- Main headline --}}
            <h1 style="margin:0 0 10px;font-family:'Georgia',serif;font-size:30px;font-weight:700;color:#ffffff;line-height:1.2;letter-spacing:0.5px;">
                Nouvelle Masterclass
            </h1>

            <p style="margin:0 0 20px;font-size:15px;color:rgba(255,255,255,0.85);line-height:1.5;">
                Une nouvelle formation est disponible
            </p>

            {{-- Badge --}}
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                <tr>
                    <td style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.35);border-radius:30px;padding:5px 18px;">
                        <span style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#fff;font-family:Arial,sans-serif;">
                            ✦ Formation Professionnelle ✦
                        </span>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- ══ BODY ══ --}}
    <tr>
        <td class="email-body" style="padding:40px 44px 32px;">

            {{-- Greeting --}}
            <p style="margin:0 0 20px;font-size:16px;color:#5a3e1b;font-family:'Georgia',serif;font-style:italic;">
                @if($recipientName)
                    Bonjour {{ $recipientName }},
                @else
                    Bonjour,
                @endif
            </p>

            {{-- Intro text --}}
            <p style="margin:0 0 28px;font-size:15px;line-height:1.8;color:#555;font-family:Arial,sans-serif;">
                Nous avons le plaisir de vous annoncer l'ouverture d'une <strong style="color:#1a1a1a;">nouvelle masterclass</strong>
                sur la plateforme Sophie Weddings Dream. Une opportunité exclusive pour développer votre expertise dans le wedding planning.
            </p>

            {{-- Masterclass card --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="background:#fdf6ec;border-radius:12px;overflow:hidden;border:1px solid #f0dfc0;margin-bottom:28px;">

                {{-- Card top accent --}}
                <tr>
                    <td style="height:4px;background:linear-gradient(90deg,#aa6808,#d1922f);"></td>
                </tr>

                {{-- Masterclass image --}}
                @if($masterclass->image_url)
                <tr>
                    <td style="padding:0;">
                        <img src="{{ url($masterclass->image_url) }}" alt="{{ $masterclass->title }}" style="width:100%;max-height:220px;object-fit:cover;display:block;">
                    </td>
                </tr>
                @endif

                {{-- Card content --}}
                <tr>
                    <td style="padding:28px 28px 24px;">

                        {{-- Niveau badge --}}
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:14px;">
                            <tr>
                                <td style="background:#aa6808;border-radius:20px;padding:4px 14px;">
                                    <span style="font-size:11px;font-family:Arial,sans-serif;color:#fff;letter-spacing:1px;text-transform:uppercase;font-weight:600;">
                                        {{ $masterclass->niveau }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        {{-- Title --}}
                        <h2 style="margin:0 0 12px;font-family:'Georgia',serif;font-size:22px;font-weight:700;color:#3d2200;line-height:1.3;">
                            {{ $masterclass->title }}
                        </h2>

                        @if($masterclass->description)
                        {{-- Description --}}
                        <p style="margin:0 0 0;font-size:14px;line-height:1.7;color:#6b5135;font-family:Arial,sans-serif;">
                            {{ Str::limit($masterclass->description, 220) }}
                        </p>
                        @endif

                    </td>
                </tr>

                {{-- Card footer --}}
                <tr>
                    <td style="padding:14px 28px;background:#f7ead6;border-top:1px solid #f0dfc0;">
                        <p style="margin:0;font-size:12px;color:#a07040;font-family:Arial,sans-serif;">
                            📅 Consultez la page de la masterclass pour découvrir les dates et tarifs des sessions disponibles.
                        </p>
                    </td>
                </tr>
            </table>

            {{-- CTA button --}}
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="margin-bottom:32px;">
                <tr>
                    <td align="center">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                            href="{{ route('masterclass.show', $masterclass->slug) }}"
                            style="height:50px;v-text-anchor:middle;width:260px;"
                            arcsize="16%" stroke="f" fillcolor="#aa6808">
                        <w:anchorlock/>
                        <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;">
                            Découvrir la masterclass →
                        </center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="{{ route('masterclass.show', $masterclass->slug) }}"
                            style="display:inline-block;background:linear-gradient(135deg,#aa6808 0%,#c97f1e 100%);
                                   color:#ffffff;text-decoration:none;font-family:Arial,sans-serif;font-size:15px;
                                   font-weight:700;letter-spacing:0.3px;padding:15px 40px;border-radius:8px;
                                   box-shadow:0 4px 12px rgba(170,104,8,0.35);">
                            Découvrir la masterclass &nbsp;→
                        </a>
                        <!--<![endif]-->
                    </td>
                </tr>
            </table>

            {{-- Divider --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <td style="height:1px;background:linear-gradient(90deg,transparent,#e8d5b0,transparent);"></td>
                </tr>
            </table>

            {{-- Why section --}}
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:12px;">
                <tr>
                    <td width="36" valign="top" style="padding-top:2px;">
                        <div style="width:32px;height:32px;background:#fdf0db;border-radius:8px;text-align:center;line-height:32px;font-size:16px;">🎓</div>
                    </td>
                    <td style="padding-left:14px;">
                        <p style="margin:0 0 3px;font-size:13px;font-weight:700;color:#3d2200;font-family:Arial,sans-serif;">Formation d'excellence</p>
                        <p style="margin:0;font-size:12px;color:#888;font-family:Arial,sans-serif;line-height:1.5;">Enseignements pratiques dispensés par Sophie, experte en wedding planning.</p>
                    </td>
                </tr>
                <tr><td colspan="2" style="height:14px;"></td></tr>
                <tr>
                    <td width="36" valign="top" style="padding-top:2px;">
                        <div style="width:32px;height:32px;background:#fdf0db;border-radius:8px;text-align:center;line-height:32px;font-size:16px;">📜</div>
                    </td>
                    <td style="padding-left:14px;">
                        <p style="margin:0 0 3px;font-size:13px;font-weight:700;color:#3d2200;font-family:Arial,sans-serif;">Attestation de participation</p>
                        <p style="margin:0;font-size:12px;color:#888;font-family:Arial,sans-serif;line-height:1.5;">Recevez une attestation officielle téléchargeable depuis votre espace personnel.</p>
                    </td>
                </tr>
                <tr><td colspan="2" style="height:14px;"></td></tr>
                <tr>
                    <td width="36" valign="top" style="padding-top:2px;">
                        <div style="width:32px;height:32px;background:#fdf0db;border-radius:8px;text-align:center;line-height:32px;font-size:16px;">🔗</div>
                    </td>
                    <td style="padding-left:14px;">
                        <p style="margin:0 0 3px;font-size:13px;font-weight:700;color:#3d2200;font-family:Arial,sans-serif;">Accès au replay</p>
                        <p style="margin:0;font-size:12px;color:#888;font-family:Arial,sans-serif;line-height:1.5;">Les sessions en ligne sont enregistrées et accessibles depuis votre espace inscrit.</p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- ══ FOOTER ══ --}}
    <tr>
        <td style="background:#3d2200;padding:28px 44px;text-align:center;">
            <p style="margin:0 0 8px;font-family:'Georgia',serif;font-size:15px;color:#e8c98a;letter-spacing:1px;">
                Sophie Weddings Dream
            </p>
            <p style="margin:0 0 14px;font-size:11px;color:rgba(255,255,255,0.45);font-family:Arial,sans-serif;letter-spacing:0.5px;">
                Dakar, Sénégal
            </p>
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin-bottom:16px;">
                <tr>
                    <td style="padding:0 10px;">
                        <a href="{{ route('masterclasses') }}" style="font-size:11px;color:#c49a4a;text-decoration:none;font-family:Arial,sans-serif;">Nos masterclasses</a>
                    </td>
                    <td style="color:rgba(255,255,255,0.2);font-size:11px;">|</td>
                    <td style="padding:0 10px;">
                        <a href="{{ route('prospect.portal.login') }}" style="font-size:11px;color:#c49a4a;text-decoration:none;font-family:Arial,sans-serif;">Mon espace inscrit</a>
                    </td>
                    <td style="color:rgba(255,255,255,0.2);font-size:11px;">|</td>
                    <td style="padding:0 10px;">
                        <a href="{{ route('contact') }}" style="font-size:11px;color:#c49a4a;text-decoration:none;font-family:Arial,sans-serif;">Contact</a>
                    </td>
                </tr>
            </table>
            <p style="margin:0;font-size:10px;color:rgba(255,255,255,0.25);font-family:Arial,sans-serif;line-height:1.6;">
                Vous recevez cet email car vous êtes inscrit(e) à l'une de nos formations.<br>
                © {{ date('Y') }} Sophie Weddings Dream. Tous droits réservés.
            </p>
        </td>
    </tr>

</table>
{{-- End email card --}}

</td></tr>
</table>
{{-- End outer wrapper --}}

</body>
</html>
