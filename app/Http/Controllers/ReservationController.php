<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Liste toutes les réservations de l'utilisateur
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.reservations.index');
        }

        $reservations = Reservation::with(['vehicle'])
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $today = now()->startOfDay();

        $reservations->each(function (Reservation $reservation) use ($today) {
            $startDate = $reservation->start_date->copy()->startOfDay();
            $endDate = $reservation->end_date->copy()->startOfDay();

            if (in_array($reservation->status, ['completed', 'cancelled'], true) || $endDate->lt($today)) {
                $reservation->list_category = 'past';

                return;
            }

            if ($startDate->lte($today) && $endDate->gte($today)) {
                $reservation->list_category = 'active';

                return;
            }

            $reservation->list_category = 'upcoming';
        });

        $active = $reservations->where('list_category', 'active');
        $upcoming = $reservations->where('list_category', 'upcoming');
        $past = $reservations->where('list_category', 'past');

        return view('dashboard.reservations', compact('reservations', 'active', 'upcoming', 'past'));
    }

    /**
     * Formulaire de création de réservation
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $vehicleId = $request->get('vehicle_id');
        $vehicle = $vehicleId ? Vehicle::findOrFail($vehicleId) : null;
        $vehicles = Vehicle::where('status', 'available')->orderBy('brand')->get();
        $hasValidPaymentMethod = $user->hasValidPaymentMethod();

        return view('dashboard.reservation-create', compact('vehicle', 'vehicles', 'hasValidPaymentMethod'));
    }

    /**
     * Afficher les détails d'une réservation
     */
    public function show($id)
    {
        $reservation = Reservation::with(['vehicle', 'user', 'inspections'])
            ->findOrFail($id);

        /** @var User $currentUser */
        $currentUser = Auth::user();

        if ($reservation->user_id !== Auth::id() && ! $currentUser->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        return view('dashboard.reservation-show', compact('reservation'));
    }

    /**
     * ADMIN - Liste toutes les réservations
     */
    public function adminIndex(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->isAdmin()) {
            abort(403);
        }

        $query = Reservation::with(['vehicle', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                });
            });
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::where('status', 'confirmed')->count(),
            'active' => Reservation::where('status', 'active')->count(),
            'late' => Reservation::where('status', 'late')->count(),
            'total_revenue' => Reservation::where('status', 'completed')->sum('total_price'),
        ];

        return view('admin.reservations.index', compact('reservations', 'stats'));
    }

    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'child_seat' => 'boolean',
            'gps' => 'boolean',
            'additional_driver' => 'boolean',
            'insurance_full' => 'boolean',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = Auth::user();

            // Check if user has valid payment method for any reservation
            if (! $user->hasValidPaymentMethod()) {
                DB::rollBack();

                return redirect()->route('dashboard.payment-methods')
                    ->with('error', __('Réservation indisponible sans moyen de paiement.'));
            }

            $startDate = Carbon::parse($request->start_date);

            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            if (! $vehicle->isAvailable()) {
                return back()->with('error', __('Ce véhicule n\'est pas disponible.'));
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
                return back()->with('error', __('Le véhicule est déjà réservé pour ces dates.'));
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $days = max(1, $startDate->diffInDays($endDate));

            // Calcul prix
            $pricingBreakdown = Reservation::calculateOfferBreakdown($vehicle, $days);
            $basePrice = (float) $pricingBreakdown['total'];

            $optionsPrice = 0;
            if ($request->child_seat && $vehicle->child_seat_available) {
                $optionsPrice += 5 * $days;
            }
            if ($request->gps && $vehicle->gps_available) {
                $optionsPrice += 3 * $days;
            }
            if ($request->additional_driver) {
                $optionsPrice += 10 * $days;
            }

            $insurancePrice = 0;
            if ($request->insurance_full) {
                $insurancePrice = $basePrice * 0.15;
            }

            $totalPrice = $basePrice + $optionsPrice + $insurancePrice;

            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicle->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_days' => $days,
                'base_price' => $basePrice,
                'insurance_price' => $insurancePrice,
                'options_price' => $optionsPrice,
                'total_price' => $totalPrice,
                'deposit_amount' => $vehicle->deposit,
                'child_seat' => $request->child_seat ?? false,
                'gps' => $request->gps ?? false,
                'additional_driver' => $request->additional_driver ?? false,
                'insurance_full' => $request->insurance_full ?? false,
                'customer_notes' => $request->customer_notes,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Log activité
            ActivityLog::log(
                Auth::id(),
                'reservation_created',
                __('Réservation créée'),
                $vehicle->brand.' '.$vehicle->model.' • '.__('Du').' '.$startDate->format('d/m').' '.__('au').' '.$endDate->format('d/m'),
                [
                    'reservation_id' => $reservation->id,
                    'vehicle_id' => $vehicle->id,
                    'pricing_breakdown' => $pricingBreakdown,
                ]
            );

            DB::commit();

            return redirect()->route('dashboard.reservation.show', $reservation->id)
                ->with('success', __('Réservation créée avec succès ! Code :').' '.$reservation->confirmation_code);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Erreur lors de la création de la réservation: ').$e->getMessage());
        }
    }

    /**
     * Annuler une réservation
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', __('Cette réservation ne peut plus être annulée.'));
        }

        // Check if cancellation is at least 3 days in advance
        $minCancellationDate = now()->addDays(3)->startOfDay();
        if ($reservation->start_date->startOfDay()->lt($minCancellationDate)) {
            return back()->with('error', __('Une annulation doit se faire au minimum 3 jours à l\'avance.'));
        }

        try {
            DB::beginTransaction();

            $reservation->vehicle->update(['status' => 'available']);

            $reservation->update([
                'status' => 'cancelled',
                'admin_notes' => __('Annulée par le client le').' '.now()->format('d/m/Y à H:i'),
            ]);

            ActivityLog::log(
                Auth::id(),
                'reservation_cancelled',
                __('Réservation annulée'),
                $reservation->vehicle->brand.' '.$reservation->vehicle->model,
                ['reservation_id' => $reservation->id]
            );

            DB::commit();

            return redirect()->route('dashboard.reservations')
                ->with('success', __('Réservation annulée avec succès.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Erreur: ').$e->getMessage());
        }
    }

    /**
     * Confirmer une réservation (Admin)
     */
    public function confirm($id)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->isAdmin()) {
            abort(403);
        }

        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pending') {
            return back()->with('error', __('Cette réservation n\'est pas en attente.'));
        }

        $reservation->update([
            'status' => 'confirmed',
            'payment_status' => 'completed',
        ]);

        ActivityLog::log(
            $reservation->user_id,
            'reservation_confirmed',
            __('Réservation confirmée'),
            $reservation->vehicle->brand.' '.$reservation->vehicle->model.' • '.__('Du').' '.$reservation->start_date->format('d/m').' '.__('au').' '.$reservation->end_date->format('d/m'),
            ['reservation_id' => $reservation->id]
        );

        return back()->with('success', __('Réservation confirmée.'));
    }

    /**
     * Démarrer une location (Admin)
     */
    public function start($id, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->isAdmin()) {
            abort(403);
        }

        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'confirmed') {
            return back()->with('error', __('Cette réservation n\'est pas confirmée.'));
        }

        if (! $reservation->start_inspection_done) {
            return back()->with('error', __('L\'inspection de départ doit être réalisée.'));
        }

        $reservation->update([
            'status' => 'active',
        ]);

        return back()->with('success', __('Location démarrée.'));
    }

    /**
     * Terminer une location (Admin)
     */
    public function complete($id, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->isAdmin()) {
            abort(403);
        }

        $reservation = Reservation::findOrFail($id);

        if (! in_array($reservation->status, ['active', 'late'])) {
            return back()->with('error', __('Cette réservation n\'est pas active.'));
        }

        if (! $reservation->end_inspection_done) {
            return back()->with('error', __('L\'inspection de retour doit être réalisée.'));
        }

        $validated = $request->validate([
            'mileage_end' => 'required|integer|min:'.($reservation->mileage_start ?? 0),
            'damage_cost' => 'nullable|numeric|min:0',
        ]);

        $reservation->mileage_end = $validated['mileage_end'];
        $reservation->damage_cost = $validated['damage_cost'] ?? 0;

        if ($reservation->is_late) {
            $reservation->late_penalty = $reservation->calculateLatePenalty();
        }

        $reservation->save();
        $reservation->complete();

        ActivityLog::log(
            $reservation->user_id,
            'reservation_completed',
            __('Location terminée'),
            $reservation->vehicle->brand.' '.$reservation->vehicle->model,
            ['reservation_id' => $reservation->id]
        );

        return back()->with('success', __('Location terminée.'));
    }
}
