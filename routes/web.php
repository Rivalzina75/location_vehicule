<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- AJOUTEZ CETTE LIGNE

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ÉTAPE 1 : Votre page d'accueil PUBLIQUE (pointe vers home.blade.php)
Route::get('/', function () {
    return view('home');
});

// ÉTAPE 2 : Les routes d'authentification (Login, Register, etc.)
// C'est la ligne qui active la vérification d'email
Auth::routes(['verify' => true]);

// ÉTAPE 3 : Votre NOUVEAU tableau de bord (protégé)
// Il pointe vers HomeController, qui pointe vers dashboard.blade.php
// Nous renommons l'ancienne route /home en /dashboard
// Ce que vous devez avoir
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware('verified') // Protège la route
    ->name('dashboard');
