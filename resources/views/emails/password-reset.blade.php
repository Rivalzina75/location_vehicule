<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Réinitialisation de mot de passe - Machina</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; -webkit-font-smoothing: antialiased;">
    
    <!-- Wrapper Table -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fa;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 40px 40px 35px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <span style="font-size: 48px;">🚗</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 15px;">
                                        <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: 2px; text-transform: uppercase;">MACHINA</h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Accent Bar -->
                    <tr>
                        <td style="height: 4px; background: linear-gradient(90deg, #e94560 0%, #00d9a5 100%);"></td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 50px 40px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <!-- Lock Icon -->
                                <tr>
                                    <td align="center" style="padding-bottom: 25px;">
                                        <span style="font-size: 64px;">🔐</span>
                                    </td>
                                </tr>
                                
                                <!-- Title -->
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <h2 style="margin: 0; font-size: 26px; font-weight: 700; color: #1a1a2e;">Réinitialisation de mot de passe</h2>
                                    </td>
                                </tr>
                                
                                <!-- Message -->
                                <tr>
                                    <td style="padding-bottom: 15px;">
                                        <p style="margin: 0; font-size: 16px; line-height: 1.7; color: #555555; text-align: center;">
                                            Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding-bottom: 35px;">
                                        <p style="margin: 0; font-size: 16px; line-height: 1.7; color: #555555; text-align: center;">
                                            Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
                                        </p>
                                    </td>
                                </tr>
                                
                                <!-- CTA Button -->
                                <tr>
                                    <td align="center" style="padding-bottom: 35px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="border-radius: 10px; background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%); box-shadow: 0 4px 15px rgba(233, 69, 96, 0.35);">
                                                    <a href="{{ $url }}" target="_blank" style="display: inline-block; padding: 16px 40px; font-size: 16px; font-weight: 600; color: #ffffff; text-decoration: none; text-transform: uppercase; letter-spacing: 1px;">
                                                        🔑 Changer mon mot de passe
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <!-- Expiration Warning -->
                                <tr>
                                    <td style="padding: 20px 25px; background: linear-gradient(135deg, #fff3cd, #ffeeba); border-radius: 10px; border-left: 4px solid #ffc107;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <span style="font-size: 24px;">⏱️</span>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #856404;">
                                                        <strong>Attention :</strong> Ce lien expirera dans <strong>{{ $count }} minutes</strong>.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <!-- Secondary Info -->
                                <tr>
                                    <td style="padding-top: 25px;">
                                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6c757d; text-align: center;">
                                            Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action n'est requise.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Fallback Link -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="border-top: 1px solid #e2e8f0; padding-top: 25px;">
                                        <p style="margin: 0 0 10px; font-size: 12px; color: #94a3b8; text-align: center;">
                                            Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :
                                        </p>
                                        <p style="margin: 0; font-size: 11px; word-break: break-all; text-align: center;">
                                            <a href="{{ $url }}" style="color: #e94560;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 10px; font-size: 14px; font-weight: 600; color: #1a1a2e;">
                                            🚗 Machina Location
                                        </p>
                                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                            &copy; {{ date('Y') }} Tous droits réservés
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>