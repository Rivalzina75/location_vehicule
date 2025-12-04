<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Liste toutes les réservations de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();

        $reservations = Reservation::with(['vehicle'])
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        // Grouper par statut pour affichage
        $active = $reservations->where('status', 'active');
        $upcoming = $reservations->whereIn('status', ['pending', 'confirmed'])
            ->where('start_date', '>', now());
        $past = $reservations->where('status', 'completed');

        return view('reservations.index', compact('reservations', 'active', 'upcoming', 'past'));
    }

    /**
     * Afficher les détails d'une réservation
     */
    public function show($id)
    {
        $reservation = Reservation::with(['vehicle', 'user', 'inspections'])
            ->findOrFail($id);

        // Vérifier que l'utilisateur a accès à cette réservation
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'contract_type' => 'required|in:simple,professional',
            'rate_type' => 'required|in:daily,weekly,monthly',
            'child_seat' => 'boolean',
            'gps' => 'boolean',
            'additional_driver' => 'boolean',
            'insurance_full' => 'boolean',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Récupérer le véhicule
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            // Vérifier la disponibilité
            if (!$vehicle->isAvailable()) {
                return back()->with('error', 'Ce véhicule n\'est pas disponible.');
            }

            // Vérifier les conflits de dates
            $hasConflict = Reservation::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['confirmed', 'active'])
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                })
                ->exists();

            if ($hasConflict) {
                return back()->with('error', 'Le véhicule est déjà réservé pour ces dates.');
            }

            // Calculer la durée
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $days = $startDate->diffInDays($endDate);

            // Calculer le prix de base
            $basePrice = $vehicle->calculatePrice($days, $request->rate_type);

            // Calculer le prix des options
            $optionsPrice = 0;
            if ($request->child_seat && $vehicle->child_seat_available) {
                $optionsPrice += 5 * $days; // 5€/jour
            }
            if ($request->gps && $vehicle->gps_available) {
                $optionsPrice += 3 * $days; // 3€/jour
            }
            if ($request->additional_driver) {
                $optionsPrice += 10 * $days; // 10€/jour
            }

            // Prix de l'assurance
            $insurancePrice = 0;
            if ($request->insurance_full) {
                $insurancePrice = $basePrice * 0.15; // 15% du prix de base
            }

            // Prix total
            $totalPrice = $basePrice + $optionsPrice + $insurancePrice;

            // Créer la réservation
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicle->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_days' => $days,
                'contract_type' => $request->contract_type,
                'rate_type' => $request->rate_type,
                'base_price' => $basePrice,
                'insurance_price' => $insurancePrice,
                'options_price' => $optionsPrice,
                'total_price' => $totalPrice,
                'deposit_paid' => $vehicle->deposit,
                'child_seat' => $request->child_seat ?? false,
                'gps' => $request->gps ?? false,
                'additional_driver' => $request->additional_driver ?? false,
                'insurance_full' => $request->insurance_full ?? false,
                'customer_notes' => $request->customer_notes,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Mettre à jour le statut du véhicule
            $vehicle->update(['status' => 'rented']);

            DB::commit();

            return redirect()->route('reservations.show', $reservation->id)
                ->with('success', 'Réservation créée avec succès! Code de confirmation: ' . $reservation->confirmation_code);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la réservation: ' . $e->getMessage());
        }
    }

    /**
     * Modifier une réservation
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Vérifier les droits
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        // On ne peut modifier que les réservations en attente
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cette réservation ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'start_date' => 'sometimes|date|after:now',
            'end_date' => 'sometimes|date|after:start_date',
            'child_seat' => 'boolean',
            'gps' => 'boolean',
            'insurance_full' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Recalculer les prix si les dates ont changé
            if ($request->has('start_date') || $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date ?? $reservation->start_date);
                $endDate = Carbon::parse($request->end_date ?? $reservation->end_date);
                $days = $startDate->diffInDays($endDate);

                $basePrice = $reservation->vehicle->calculatePrice($days, $reservation->rate_type);

                $validated['duration_days'] = $days;
                $validated['base_price'] = $basePrice;
                $validated['total_price'] = $basePrice + $reservation->options_price + $reservation->insurance_price;
            }

            $reservation->update($validated);

            DB::commit();

            return back()->with('success', 'Réservation modifiée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Annuler une réservation
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Vérifier les droits
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        // On ne peut annuler que les réservations pending/confirmed
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cette réservation ne peut plus être annulée.');
        }

        try {
            DB::beginTransaction();

            // Libérer le véhicule
            $reservation->vehicle->update(['status' => 'available']);

            // Marquer comme annulée
            $reservation->update([
                'status' => 'cancelled',
                'admin_notes' => 'Annulée par le client le ' . now()->format('d/m/Y à H:i'),
            ]);

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Réservation annulée. Le remboursement sera traité sous 48h.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Confirmer une réservation (Admin)
     */
    public function confirm($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Cette réservation n\'est pas en attente.');
        }

        $reservation->update([
            'status' => 'confirmed',
            'payment_status' => 'completed',
        ]);

        return back()->with('success', 'Réservation confirmée.');
    }

    /**
     * Démarrer une location (Admin)
     */
    public function start($id, Request $request)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Cette réservation n\'est pas confirmée.');
        }

        // Vérifier que l'inspection de départ a été faite
        if (!$reservation->start_inspection_done) {
            return back()->with('error', 'L\'inspection de départ doit être réalisée avant de démarrer la location.');
        }

        $validated = $request->validate([
            'mileage_start' => 'required|integer|min:0',
        ]);

        $reservation->update([
            'status' => 'active',
            'mileage_start' => $validated['mileage_start'],
        ]);

        return back()->with('success', 'Location démarrée.');
    }

    /**
     * Terminer une location (Admin)
     */
    public function complete($id, Request $request)
    {
        $reservation = Reservation::findOrFail($id);

        if (!in_array($reservation->status, ['active', 'late'])) {
            return back()->with('error', 'Cette réservation n\'est pas active.');
        }

        // Vérifier que l'inspection de fin a été faite
        if (!$reservation->end_inspection_done) {
            return back()->with('error', 'L\'inspection de retour doit être réalisée avant de clôturer la location.');
        }

        $validated = $request->validate([
            'mileage_end' => 'required|integer|min:' . ($reservation->mileage_start ?? 0),
            'damage_cost' => 'nullable|numeric|min:0',
        ]);

        $reservation->mileage_end = $validated['mileage_end'];
        $reservation->damage_cost = $validated['damage_cost'] ?? 0;

        // Calculer les pénalités de retard si nécessaire
        if ($reservation->is_late) {
            $reservation->late_penalty = $reservation->calculateLatePenalty();
        }

        // Finaliser
        $reservation->complete();

        return back()->with('success', 'Location terminée. Total final: ' . $reservation->formatted_total_price);
    }
}
