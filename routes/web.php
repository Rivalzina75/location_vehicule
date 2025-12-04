<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    return view('home');
})->name('home');

// Sélecteur de langue
Route::get('lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');

// Routes d'authentification
Auth::routes(['verify' => true]);

// Redirection /home vers /dashboard
Route::get('/home', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES (Auth + Verified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ========== DASHBOARD ==========
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // ========== CATALOGUE ==========
    Route::prefix('dashboard')->name('dashboard.')->group(function () {

        // Liste des véhicules
        Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue');

        // Détails d'un véhicule
        Route::get('/catalogue/{id}', [CatalogueController::class, 'show'])->name('catalogue.show');

        // ========== RÉSERVATIONS ==========

        // Liste des réservations
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations');

        // Créer une réservation (formulaire)
        Route::get('/reservation/create', [ReservationController::class, 'create'])->name('reservation.create');

        // Enregistrer une réservation
        Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');

        // Détails d'une réservation
        Route::get('/reservation/{id}', [ReservationController::class, 'show'])->name('reservation.show');

        // Annuler une réservation
        Route::delete('/reservation/{id}', [ReservationController::class, 'destroy'])->name('reservation.destroy');

        // ========== DOCUMENTS ==========

        // Liste des documents
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents');

        // Uploader un document
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

        // Supprimer un document
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // ========== INSPECTION ==========

        // Page d'inspection
        Route::get('/inspection', [InspectionController::class, 'index'])->name('inspection');

        // Inspection de départ
        Route::post('/inspection/start/{reservation}', [InspectionController::class, 'storeStart'])->name('inspection.start');

        // Inspection de retour
        Route::post('/inspection/end/{reservation}', [InspectionController::class, 'storeEnd'])->name('inspection.end');

        // ========== PROFIL ==========

        // Mettre à jour le profil
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});
