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
    <style type="text/css">
        /* Reset styles */
        body, table, td, p, a, li { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        /* Mobile styles */
        @media only screen and (max-width: 600px) {
            .email-container { width: 100% !important; max-width: 100% !important; }
            .mobile-padding { padding: 20px 15px !important; }
            .mobile-padding-header { padding: 25px 15px 20px !important; }
            .mobile-text-center { text-align: center !important; }
            .mobile-text-size { font-size: 14px !important; line-height: 1.5 !important; }
            .mobile-title { font-size: 20px !important; }
            .mobile-button { padding: 14px 28px !important; font-size: 14px !important; }
            .mobile-icon { font-size: 36px !important; }
            .mobile-fallback { font-size: 10px !important; }
            .mobile-warning { padding: 12px 10px !important; }
            .mobile-warning-text { font-size: 12px !important; }
            .mobile-footer { padding: 20px 15px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; -webkit-font-smoothing: antialiased;">
    
    <!-- Wrapper Table -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fa;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td class="mobile-padding-header" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 30px 30px 25px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <span class="mobile-icon" style="font-size: 40px;">🚗</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 10px;">
                                        <h1 style="margin: 0; font-size: 22px; font-weight: 700; color: #ffffff; letter-spacing: 2px; text-transform: uppercase;">MACHINA</h1>
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
                        <td class="mobile-padding" style="padding: 30px 25px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <!-- Icon -->
                                <tr>
                                    <td align="center" style="padding-bottom: 15px;">
                                        <span class="mobile-icon" style="font-size: 45px;">🔐</span>
                                    </td>
                                </tr>
                                
                                <!-- Title -->
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <h2 class="mobile-title" style="margin: 0; font-size: 22px; font-weight: 700; color: #1a1a2e;">Réinitialisation de mot de passe</h2>
                                    </td>
                                </tr>
                                
                                <!-- Message -->
                                <tr>
                                    <td class="mobile-text-size" style="padding-bottom: 20px;">
                                        <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #555555; text-align: center;">
                                            Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
                                        </p>
                                    </td>
                                </tr>
                                
                                <!-- Warning Box -->
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td class="mobile-warning" style="background: linear-gradient(135deg, #fff8e1, #ffecb3); border-left: 3px solid #ff9800; border-radius: 6px; padding: 12px 15px; text-align: center;">
                                                    <p class="mobile-warning-text" style="margin: 0; font-size: 13px; color: #e65100;">
                                                        ⏱️ Expire dans <strong>{{ $count }} min</strong>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <!-- CTA Button -->
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="border-radius: 8px; background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%); box-shadow: 0 4px 12px rgba(233, 69, 96, 0.3);">
                                                    <a href="{{ $url }}" target="_blank" class="mobile-button" style="display: inline-block; padding: 14px 30px; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none; letter-spacing: 0.5px;">
                                                        🔑 Changer mon mot de passe
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <!-- Secondary Info -->
                                <tr>
                                    <td style="padding: 12px 15px; background-color: #f8fafc; border-radius: 6px;">
                                        <p class="mobile-text-size" style="margin: 0; font-size: 12px; line-height: 1.5; color: #6c757d; text-align: center;">
                                            Pas vous ? Ignorez cet email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Fallback Link -->
                    <tr>
                        <td style="padding: 0 25px 15px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="border-top: 1px solid #e2e8f0; padding-top: 15px;">
                                        <p class="mobile-fallback" style="margin: 0 0 5px; font-size: 10px; color: #94a3b8; text-align: center;">
                                            Lien de secours :
                                        </p>
                                        <p class="mobile-fallback" style="margin: 0; font-size: 9px; word-break: break-all; text-align: center;">
                                            <a href="{{ $url }}" style="color: #e94560;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="mobile-footer" style="background-color: #f8fafc; padding: 18px 25px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 3px; font-size: 12px; font-weight: 600; color: #1a1a2e;">
                                            🚗 Machina
                                        </p>
                                        <p style="margin: 0; font-size: 10px; color: #94a3b8;">
                                            &copy; {{ date('Y') }}
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