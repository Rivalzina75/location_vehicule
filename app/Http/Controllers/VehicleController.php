<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     * Ce controller est réservé aux admins
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Liste tous les véhicules (admin)
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrer par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Recherche par marque/modèle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $types = Vehicle::types();
        return view('admin.vehicles.create', compact('types'));
    }

    /**
     * Enregistrer un nouveau véhicule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Vehicle::types()))],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'license_plate' => ['required', 'string', 'max:20', 'unique:vehicles,license_plate'],
            'color' => ['required', 'string', 'max:50'],
            'transmission' => ['required', 'in:manual,automatic'],
            'fuel_type' => ['required', 'in:gasoline,diesel,electric,hybrid'],
            'seats' => ['required', 'integer', 'min:1', 'max:50'],
            'doors' => ['required', 'integer', 'min:2', 'max:10'],
            'mileage' => ['required', 'integer', 'min:0'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'price_per_week' => ['required', 'numeric', 'min:0'],
            'price_per_month' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'insurance_daily' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,rented,maintenance,unavailable'],
            'child_seat_available' => ['boolean'],
            'gps_available' => ['boolean'],
            'bluetooth' => ['boolean'],
            'air_conditioning' => ['boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            // Upload des images
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('vehicles', $filename, 'public');
                    $imagePaths[] = $path;
                }
            }

            $validated['images'] = $imagePaths;
            $validated['child_seat_available'] = $request->has('child_seat_available');
            $validated['gps_available'] = $request->has('gps_available');
            $validated['bluetooth'] = $request->has('bluetooth');
            $validated['air_conditioning'] = $request->has('air_conditioning');

            $vehicle = Vehicle::create($validated);

            return redirect()->route('admin.vehicles.index')
                ->with('success', 'Véhicule créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du véhicule: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un véhicule
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['reservations' => function ($query) {
            $query->orderBy('start_date', 'desc')->limit(10);
        }])->findOrFail($id);

        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $types = Vehicle::types();

        return view('admin.vehicles.edit', compact('vehicle', 'types'));
    }

    /**
     * Mettre à jour un véhicule
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Vehicle::types()))],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'license_plate' => ['required', 'string', 'max:20', Rule::unique('vehicles')->ignore($vehicle->id)],
            'color' => ['required', 'string', 'max:50'],
            'transmission' => ['required', 'in:manual,automatic'],
            'fuel_type' => ['required', 'in:gasoline,diesel,electric,hybrid'],
            'seats' => ['required', 'integer', 'min:1', 'max:50'],
            'doors' => ['required', 'integer', 'min:2', 'max:10'],
            'mileage' => ['required', 'integer', 'min:0'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'price_per_week' => ['required', 'numeric', 'min:0'],
            'price_per_month' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'insurance_daily' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,rented,maintenance,unavailable'],
            'child_seat_available' => ['boolean'],
            'gps_available' => ['boolean'],
            'bluetooth' => ['boolean'],
            'air_conditioning' => ['boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            // Supprimer les images demandées
            if ($request->filled('remove_images')) {
                $currentImages = $vehicle->images ?? [];
                foreach ($request->remove_images as $imageToRemove) {
                    if (in_array($imageToRemove, $currentImages)) {
                        Storage::disk('public')->delete($imageToRemove);
                        $currentImages = array_values(array_diff($currentImages, [$imageToRemove]));
                    }
                }
                $validated['images'] = $currentImages;
            } else {
                $validated['images'] = $vehicle->images ?? [];
            }

            // Ajouter de nouvelles images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('vehicles', $filename, 'public');
                    $validated['images'][] = $path;
                }
            }

            $validated['child_seat_available'] = $request->has('child_seat_available');
            $validated['gps_available'] = $request->has('gps_available');
            $validated['bluetooth'] = $request->has('bluetooth');
            $validated['air_conditioning'] = $request->has('air_conditioning');

            $vehicle->update($validated);

            return redirect()->route('admin.vehicles.show', $vehicle->id)
                ->with('success', 'Véhicule mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un véhicule
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Vérifier qu'il n'y a pas de réservations actives
        if ($vehicle->reservations()->whereIn('status', ['active', 'confirmed'])->exists()) {
            return back()->with('error', 'Impossible de supprimer ce véhicule : il a des réservations actives.');
        }

        try {
            // Supprimer les images
            if ($vehicle->images) {
                foreach ($vehicle->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $vehicle->delete();

            return redirect()->route('admin.vehicles.index')
                ->with('success', 'Véhicule supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'un véhicule (disponible, maintenance, etc.)
     */
    public function updateStatus(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:available,rented,maintenance,unavailable'],
        ]);

        $vehicle->update(['status' => $validated['status']]);

        return back()->with('success', 'Statut mis à jour: ' . $validated['status']);
    }
}
