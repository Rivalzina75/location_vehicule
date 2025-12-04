<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
    /**
     * Afficher la page d'inspection
     */
    public function index()
    {
        $user = Auth::user();

        // Réservations nécessitant une inspection
        $needingStartInspection = Reservation::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('start_inspection_done', false)
            ->with('vehicle')
            ->get();

        $needingEndInspection = Reservation::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_inspection_done', false)
            ->with('vehicle')
            ->get();

        return view('dashboard.inspection', compact('needingStartInspection', 'needingEndInspection'));
    }

    /**
     * Créer une inspection de départ
     */
    public function storeStart(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'mileage' => 'required|integer|min:0',
            'fuel_level' => 'required|in:empty,quarter,half,three_quarters,full',
            'cleanliness' => 'required|in:dirty,acceptable,clean,very_clean',
            'exterior_ok' => 'boolean',
            'interior_ok' => 'boolean',
            'tires_ok' => 'boolean',
            'lights_ok' => 'boolean',
            'documents_ok' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'file|mimes:jpg,jpeg,png|max:5120',
            'general_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Uploader les photos
            $photoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('inspections', $filename, 'public');
                    $photoPaths[] = $path;
                }
            }

            // Créer l'inspection
            $inspection = Inspection::create([
                'reservation_id' => $reservation->id,
                'user_id' => Auth::id(),
                'type' => 'start',
                'inspection_date' => now(),
                'mileage' => $request->mileage,
                'fuel_level' => $request->fuel_level,
                'cleanliness' => $request->cleanliness,
                'exterior_ok' => $request->exterior_ok ?? true,
                'interior_ok' => $request->interior_ok ?? true,
                'tires_ok' => $request->tires_ok ?? true,
                'lights_ok' => $request->lights_ok ?? true,
                'documents_ok' => $request->documents_ok ?? true,
                'photos' => $photoPaths,
                'general_notes' => $request->general_notes,
            ]);

            // Mettre à jour la réservation
            $reservation->update([
                'start_inspection_done' => true,
                'mileage_start' => $request->mileage,
            ]);

            return back()->with('success', 'Inspection de départ enregistrée!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Créer une inspection de retour
     */
    public function storeEnd(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'mileage' => 'required|integer|min:' . ($reservation->mileage_start ?? 0),
            'fuel_level' => 'required|in:empty,quarter,half,three_quarters,full',
            'cleanliness' => 'required|in:dirty,acceptable,clean,very_clean',
            'exterior_ok' => 'boolean',
            'interior_ok' => 'boolean',
            'tires_ok' => 'boolean',
            'lights_ok' => 'boolean',
            'documents_ok' => 'boolean',
            'damages' => 'nullable|array',
            'photos' => 'nullable|array',
            'photos.*' => 'file|mimes:jpg,jpeg,png|max:5120',
            'damage_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Uploader les photos
            $photoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('inspections', $filename, 'public');
                    $photoPaths[] = $path;
                }
            }

            // Créer l'inspection
            $inspection = Inspection::create([
                'reservation_id' => $reservation->id,
                'user_id' => Auth::id(),
                'type' => 'end',
                'inspection_date' => now(),
                'mileage' => $request->mileage,
                'fuel_level' => $request->fuel_level,
                'cleanliness' => $request->cleanliness,
                'exterior_ok' => $request->exterior_ok ?? true,
                'interior_ok' => $request->interior_ok ?? true,
                'tires_ok' => $request->tires_ok ?? true,
                'lights_ok' => $request->lights_ok ?? true,
                'documents_ok' => $request->documents_ok ?? true,
                'damages' => $request->damages ?? [],
                'photos' => $photoPaths,
                'damage_notes' => $request->damage_notes,
            ]);

            // Mettre à jour la réservation
            $reservation->update([
                'end_inspection_done' => true,
                'mileage_end' => $request->mileage,
            ]);

            return back()->with('success', 'Inspection de retour enregistrée!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
