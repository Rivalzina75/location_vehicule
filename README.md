# Location Vehicule

Ce projet est un projet de cours et n'est pas destiné à être forké ou cloné. Il est publié sur GitHub uniquement pour permettre à mes futurs recruteurs de le consulter.

## Attention

Ce projet **n'est pas sous licence MIT** ou toute autre licence open source. Toute utilisation non autorisée est interdite.

## Description
Location Vehicule est une application web permettant la gestion de la location de véhicules. Elle inclut des fonctionnalités telles que la gestion des utilisateurs, des réservations, des véhicules, des paiements, et bien plus encore.

## Fonctionnalités principales
- Gestion des utilisateurs (création, mise à jour, suppression, rôles, etc.)
- Gestion des véhicules (ajout, modification, suppression, etc.)
- Réservations de véhicules
- Paiements et méthodes de paiement
- Notifications (réinitialisation de mot de passe, vérification d'email, etc.)
- Logs d'activité
- Multilingue (anglais, français)

## Prérequis
- PHP >= 8.0
- Composer
- Node.js & npm
- MySQL ou tout autre système de gestion de base de données compatible

## Installation

1. Clonez le dépôt :
   ```bash
   git clone <url-du-repo>
   ```

2. Accédez au dossier du projet :
   ```bash
   cd location_vehicule
   ```

3. Installez les dépendances PHP :
   ```bash
   composer install
   ```

4. Installez les dépendances JavaScript :
   ```bash
   npm install
   ```

5. Configurez le fichier `.env` :
   - Copiez le fichier `.env.example` en `.env`
   - Configurez les variables d'environnement (base de données, mail, etc.)

6. Générez la clé de l'application :
   ```bash
   php artisan key:generate
   ```

7. Exécutez les migrations et les seeders :
   ```bash
   php artisan migrate --seed
   ```

8. Compilez les assets front-end :
   ```bash
   npm run build
   ```

9. Lancez le serveur de développement :
   ```bash
   php artisan serve
   ```

## Structure du projet
- **app/** : Contient la logique métier (contrôleurs, modèles, etc.)
- **config/** : Contient les fichiers de configuration
- **database/** : Contient les migrations, seeders et factories
- **public/** : Contient les fichiers accessibles publiquement (index.php, assets, etc.)
- **resources/** : Contient les vues, fichiers CSS et JS
- **routes/** : Contient les fichiers de routes (web.php, api.php, etc.)
- **tests/** : Contient les tests unitaires et fonctionnels

## Tests
Pour exécuter les tests, utilisez la commande suivante :
```bash
php artisan test
```

## Contribution
1. Forkez le projet
2. Créez une branche pour votre fonctionnalité :
   ```bash
   git checkout -b ma-fonctionnalite
   ```
3. Commitez vos modifications :
   ```bash
   git commit -m "Ajout d'une nouvelle fonctionnalité"
   ```
4. Poussez vos modifications :
   ```bash
   git push origin ma-fonctionnalite
   ```
5. Créez une Pull Request