<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# Location Vehicule

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

## Note Importante

Ce projet a été réalisé dans le cadre d'un cours et est destiné uniquement à des fins de démonstration. Il est publié sur GitHub pour permettre aux recruteurs de consulter mon travail.

**Veuillez noter :**
- Ce projet n'est pas destiné à être cloné, forké ou utilisé à des fins commerciales ou personnelles.
- Toute utilisation du code sans autorisation explicite est interdite.
