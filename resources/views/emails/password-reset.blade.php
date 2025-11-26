<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        /* Design Machina */
        body { background-color: #f4f7f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #2c3e50; padding: 30px 0; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 2px; text-transform: uppercase; }
        .content { padding: 40px 40px; color: #555555; line-height: 1.6; }
        
        /* BOUTON CORRIGÉ */
        .btn { 
            display: inline-block; 
            background-color: #3498db; 
            color: #ffffff; 
            padding: 14px 35px; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: bold; 
            margin: 30px 0; 
            box-shadow: 0 4px 6px rgba(44, 62, 80, 0.3); 
            letter-spacing: 0.5px;
        }
        .btn:hover { background: linear-gradient(135deg, #3498db, #2980b9); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);  } 
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .link-fallback { font-size: 11px; color: #aaa; margin-top: 20px; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MACHINA</h1>
        </div>
        <div class="content">
            <h2 style="color:#ffffff; margin-top: 0;">Réinitialisation de mot de passe</h2>
            <p>Bonjour,</p>
            <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn" style="color:#ffffff">Changer mon mot de passe</a>
            </div>

            <p>Ce lien expirera dans <strong>{{ $count }} minutes</strong>.</p>
            <p>Si vous n'avez pas demandé de réinitialisation, aucune action n'est requise.</p>

            <div class="link-fallback">
                Si le bouton ne fonctionne pas, copiez ce lien :<br>
                <a href="{{ $url }}" style="color: #2c3e50;">{{ $url }}</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Machina Location. Tous droits réservés.
        </div>
    </div>
</body>
</html>