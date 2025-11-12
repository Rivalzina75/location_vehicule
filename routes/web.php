<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- L'IMPORTATION REQUISE

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ÉTAPE 1 : Votre page d'accueil PUBLIQUE
Route::get('/', function () {
    return view('home');
});

// ÉTAPE 2 : Les routes d'authentification
// Active la vérification d'email
Auth::routes(['verify' => true]);

// ÉTAPE 3 : Votre NOUVEAU tableau de bord
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware('verified')
    ->name('dashboard');

// ÉTAPE 4 (CORRECTION 404) : Redirige l'ancienne route /home
// que Laravel cherche (et qui n'existe pas) vers /dashboard
Route::get('/home', function () {
    return redirect('/dashboard');
})->name('home');
