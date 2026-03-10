<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's payment methods
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods()->orderByDesc('is_default')->orderByDesc('created_at')->get();

        return view('dashboard.payment-methods', compact('paymentMethods'));
    }

    /**
     * Store a new payment method
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'card_holder_name' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'string', 'regex:/^\d{13,19}$/'],
            'card_brand' => ['required', 'string', 'in:visa,mastercard,amex'],
            'expiry_month' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])$/'],
            'expiry_year' => ['required', 'string', 'regex:/^20\d{2}$/'],
        ], [
            'card_number.regex' => __('Le numéro de carte doit contenir entre 13 et 19 chiffres.'),
            'expiry_month.regex' => __('Le mois d\'expiration est invalide.'),
            'expiry_year.regex' => __('L\'année d\'expiration est invalide.'),
        ]);

        // Validate expiry date is in the future
        $expiryDate = \Carbon\Carbon::createFromFormat('m/Y', $validated['expiry_month'].'/'.$validated['expiry_year'])->endOfMonth();
        if ($expiryDate->isPast()) {
            return back()->withErrors(['expiry_month' => __('La carte est expirée.')])->withInput();
        }

        // If this is the first card, make it default
        $isDefault = $user->paymentMethods()->count() === 0;

        // Handle "set as default" checkbox
        if ($request->has('is_default') && $request->is_default) {
            $user->paymentMethods()->update(['is_default' => false]);
            $isDefault = true;
        }

        $user->paymentMethods()->create([
            'card_brand' => $validated['card_brand'],
            'card_last_four' => substr($validated['card_number'], -4),
            'card_holder_name' => trim($validated['card_holder_name']),
            'expiry_month' => $validated['expiry_month'],
            'expiry_year' => $validated['expiry_year'],
            'is_default' => $isDefault,
        ]);

        return redirect()->route('dashboard.payment-methods')
            ->with('success', __('Moyen de paiement ajouté avec succès.'));
    }

    /**
     * Set a payment method as default
     */
    public function setDefault($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $paymentMethod = $user->paymentMethods()->findOrFail($id);

        // Remove default from all others
        $user->paymentMethods()->update(['is_default' => false]);
        $paymentMethod->update(['is_default' => true]);

        return back()->with('success', __('Moyen de paiement par défaut mis à jour.'));
    }

    /**
     * Delete a payment method
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $paymentMethod = $user->paymentMethods()->findOrFail($id);

        $wasDefault = $paymentMethod->is_default;
        $paymentMethod->delete();

        // If deleted card was default, make the next one default
        if ($wasDefault) {
            $nextCard = $user->paymentMethods()->first();
            if ($nextCard) {
                $nextCard->update(['is_default' => true]);
            }
        }

        return back()->with('success', __('Moyen de paiement supprimé.'));
    }
}
