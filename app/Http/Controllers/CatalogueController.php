<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /**
     * Afficher le catalogue de véhicules
     */
    public function index(Request $request)
    {
        $query = Vehicle::query()->where('status', 'available');

        // Filtrer par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrer par transmission
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        // Filtrer par carburant
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Filtrer par prix maximum
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Trier les résultats
        $sortBy = $request->get('sort', 'price_asc');
        switch ($sortBy) {
            case 'price_desc':
                $query->orderBy('price_per_day', 'desc');
                break;
            case 'name':
                $query->orderBy('brand')->orderBy('model');
                break;
            case 'year':
                $query->orderBy('year', 'desc');
                break;
            default: // price_asc
                $query->orderBy('price_per_day', 'asc');
        }

        // Paginer les résultats (12 par page)
        $vehicles = $query->paginate(12);

        // Types de véhicules pour le filtre
        $types = Vehicle::types();

        return view('dashboard.catalogue', compact('vehicles', 'types'));
    }

    /**
     * Afficher les détails d'un véhicule
     */
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Vérifier si le véhicule est disponible
        if (!$vehicle->isAvailable()) {
            return redirect()->route('dashboard.catalogue')
                ->with('error', 'Ce véhicule n\'est pas disponible actuellement.');
        }

        return view('dashboard.catalogue-show', compact('vehicle'));
    }
}
