<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\HomeController;

// --- AJOUT DES NOUVEAUX CONTRÔLEURS POUR LE DASHBOARD ---
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| C'est ici que vous pouvez enregistrer les routes web pour votre application.
|
*/

// =========================================================================
// ROUTES PUBLIQUES (Accessibles par tous)
// =========================================================================

// --- SÉLECTEUR DE LANGUE ---
// Doit être accessible pour que même les invités puissent changer la langue
Route::get('lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');

// --- PAGE D'ACCUEIL (VITRINE) ---
Route::get('/', function () {
    return view('home');
});

// --- ROUTES D'AUTHENTIFICATION ---
// Gère /login, /register, /password/reset, etc.
// L'option 'verify' => true active les routes de vérification d'e-mail.
Auth::routes(['verify' => true]);

// --- CORRECTION DU 404 APRÈS VÉRIFICATION E-MAIL ---
// Redirige l'ancienne route /home (que Laravel cherche) vers /dashboard
Route::get('/home', function () {
    return redirect('/dashboard');
})->name('home');


// =========================================================================
// ROUTES PROTÉGÉES (Nécessite d'être connecté ET vérifié)
// =========================================================================

// Tout ce qui est dans ce groupe est protégé.
Route::middleware(['auth', 'verified'])->group(function () {

    // --- PAGE PRINCIPALE DU DASHBOARD ---
    // C'est la route qui charge votre fichier dashboard.blade.php
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // --- PROFIL UTILISATEUR (Section 7) ---
    // Route pour la mise à jour du profil
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // --- GESTION DES RÉSERVATIONS (Sections 3 & 4) ---
    // On groupe les routes qui commencent par /reservations
    Route::prefix('reservations')->name('reservations.')->group(function () {

        // GET /reservations (Pour lister les réservations dans "Mes Réservations")
        Route::get('/', [ReservationController::class, 'index'])->name('index');

        // POST /reservations (Pour le formulaire "Nouvelle Réservation")
        Route::post('/', [ReservationController::class, 'store'])->name('store');

        // GET /reservations/{id} (Pour voir les détails d'une réservation)
        Route::get('/{id}', [ReservationController::class, 'show'])->name('show');

        // PUT /reservations/{id} (Pour modifier une réservation)
        Route::put('/{id}', [ReservationController::class, 'update'])->name('update');

        // DELETE /reservations/{id} (Pour annuler une réservation)
        Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('destroy');
    });

    // --- GESTION DES DOCUMENTS (Section 5) ---
    // On groupe les routes qui commencent par /documents
    Route::prefix('documents')->name('documents.')->group(function () {

        // GET /documents (Pour lister les documents)
        Route::get('/', [DocumentController::class, 'index'])->name('index');

        // POST /documents (Pour uploader un nouveau document)
        Route::post('/', [DocumentController::class, 'store'])->name('store');
    });

    // --- GESTION DES INSPECTIONS (Section 6) ---
    // On groupe les routes qui commencent par /inspections
    Route::prefix('inspections')->name('inspections.')->group(function () {

        // POST /inspections (Pour enregistrer une nouvelle inspection)
        Route::post('/', [InspectionController::class, 'store'])->name('store');

        // GET /inspections/{reservation_id} (Pour voir les inspections d'une réservation)
        Route::get('/{reservation_id}', [InspectionController::class, 'show'])->name('show');
    });
}); // <-- Fin du groupe de routes protégées