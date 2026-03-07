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
use App\Http\Controllers\PaymentMethodController;

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

        // Historique d'activité
        Route::get('/activity', [HomeController::class, 'activity'])->name('activity');

        // Voir le profil
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

        // Éditer le profil
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

        // Mettre à jour le profil
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Mettre à jour le mot de passe
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        // ========== MOYENS DE PAIEMENT ==========

        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods');
        Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::patch('/payment-methods/{id}/default', [PaymentMethodController::class, 'setDefault'])->name('payment-methods.default');
        Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');

        // Supprimer le compte
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| ROUTES ADMIN (Auth + Verified + Role:Admin)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\VehicleController;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // ========== DASHBOARD ADMIN ==========
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // ========== GESTION VÉHICULES ==========
    Route::resource('vehicles', VehicleController::class);

    // Changer le statut d'un véhicule
    Route::patch('vehicles/{id}/status', [VehicleController::class, 'updateStatus'])->name('vehicles.status');

    // ========== GESTION RÉSERVATIONS ==========

    // Liste toutes les réservations (admin)
    Route::get('reservations', [ReservationController::class, 'adminIndex'])->name('reservations.index');

    // Confirmer une réservation
    Route::patch('reservations/{id}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');

    // Démarrer une location
    Route::patch('reservations/{id}/start', [ReservationController::class, 'start'])->name('reservations.start');

    // Terminer une location
    Route::patch('reservations/{id}/complete', [ReservationController::class, 'complete'])->name('reservations.complete');

    // ========== GESTION DOCUMENTS ==========

    // TODO: Ajouter routes pour valider/rejeter documents
    // Route::patch('documents/{id}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
    // Route::patch('documents/{id}/reject', [DocumentController::class, 'reject'])->name('documents.reject');

    // ========== GESTION UTILISATEURS ==========

    // TODO: Ajouter routes pour gérer les utilisateurs
    // Route::get('users', [UserController::class, 'index'])->name('users.index');
    // Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
});
