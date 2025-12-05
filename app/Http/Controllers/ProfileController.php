<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Rules\PasswordRobustness;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher le profil de l'utilisateur
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'date_of_birth' => ['required', 'date_format:d/m/Y', 'before:today', 'after:1900-01-01'],
            'phone_number' => ['required', 'string', 'regex:/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'city' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
        ], [
            'date_of_birth.date_format' => __('Le format de date doit être JJ/MM/AAAA.'),
            'date_of_birth.before' => __('La date de naissance doit être dans le passé.'),
            'phone_number.regex' => __('Le numéro de téléphone n\'est pas au bon format.'),
            'postal_code.regex' => __('Le code postal doit contenir 5 chiffres.'),
            'city.regex' => __('Le nom de la ville ne doit contenir que des lettres, des espaces ou des tirets.'),
            'email.unique' => __('Cette adresse email est déjà utilisée.'),
        ]);

        try {
            $user->update([
                'first_name' => trim($validated['first_name']),
                'last_name' => trim($validated['last_name']),
                'email' => strtolower(trim($validated['email'])),
                'date_of_birth' => $validated['date_of_birth'],
                'phone_number' => $validated['phone_number'],
                'address_line1' => trim($validated['address_line1']),
                'postal_code' => $validated['postal_code'],
                'city' => trim($validated['city']),
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('Profil mis à jour avec succès.'));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('Erreur lors de la mise à jour du profil: ') . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', new PasswordRobustness, 'confirmed'],
        ], [
            'current_password.required' => __('Le mot de passe actuel est requis.'),
            'password.confirmed' => __('Les mots de passe ne correspondent pas.'),
        ]);

        // Vérifier que le mot de passe actuel est correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => __('Le mot de passe actuel est incorrect.')
            ])->withInput();
        }

        // Vérifier que le nouveau mot de passe est différent de l'ancien
        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => __('Le nouveau mot de passe doit être différent de l\'ancien.')
            ])->withInput();
        }

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', __('Mot de passe mis à jour avec succès.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Erreur lors de la mise à jour du mot de passe: ') . $e->getMessage());
        }
    }

    /**
     * Supprimer le compte (optionnel)
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Vérifier le mot de passe pour confirmation
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => __('Le mot de passe est incorrect.')
            ]);
        }

        // Vérifier qu'il n'y a pas de réservations actives
        if ($user->reservations()->whereIn('status', ['active', 'confirmed'])->exists()) {
            return back()->with('error', __('Impossible de supprimer le compte : vous avez des réservations actives.'));
        }

        try {
            // Déconnecter l'utilisateur
            Auth::logout();

            // Supprimer le compte
            $user->delete();

            return redirect('/')->with('success', __('Votre compte a été supprimé avec succès.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Erreur lors de la suppression du compte: ') . $e->getMessage());
        }
    }
}
