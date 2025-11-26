<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        /* Styles Machina */
        body { background-color: #f4f7f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #2c3e50; padding: 30px 0; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 2px; text-transform: uppercase; }
        .content { padding: 40px 40px; color: #555555; line-height: 1.6; }
        
        /* BOUTON CORRIGÉ : Plus sombre, plus lisible */
        .btn { 
            display: inline-block; 
            background-color: #3498db; /* Bleu nuit (couleur Machina) au lieu du bleu vif */
            color: #ffffff; 
            padding: 14px 35px; 
            text-decoration: none; 
            border-radius: 6px; /* Coins un peu moins ronds pour faire plus sérieux */
            font-weight: bold; 
            margin: 30px 0; 
            box-shadow: 0 4px 6px rgba(44, 62, 80, 0.3); 
            letter-spacing: 0.5px;
        }
        .btn:hover { background: linear-gradient(135deg, #3498db, #2980b9); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);  } /* Encore plus sombre au survol */
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .link-fallback { font-size: 11px; color: #aaa; margin-top: 20px; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>MACHINA</h1>
        </div>

        <!-- Contenu Principal -->
        <div class="content">
            <h2 style="color: #2c3e50; margin-top: 0;">Bienvenue à bord ! 🚗</h2>
            <p>Merci de vous être inscrit sur Machina.</p>
            <p>Pour activer votre compte et commencer à louer des véhicules, veuillez confirmer votre adresse e-mail en cliquant sur le bouton ci-dessous.</p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn" style="color:#ffffff">Confirmer mon adresse e-mail</a>
            </div>

            <p>Si vous n'avez pas créé de compte, vous pouvez ignorer cet e-mail en toute sécurité.</p>

            <!-- Lien de secours si le bouton ne marche pas -->
            <div class="link-fallback">
                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                <a href="{{ $url }}" style="color: #2c3e50;">{{ $url }}</a>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            &copy; {{ date('Y') }} Machina Location. Tous droits réservés.
        </div>
    </div>
</body>
</html>