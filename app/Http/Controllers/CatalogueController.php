<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CatalogueController extends Controller
{
    /**
     * Afficher le catalogue de véhicules
     */
    public function index(Request $request)
    {
        $priceUnit = $request->get('price_unit', 'day');
        $priceField = match ($priceUnit) {
            'week' => 'price_per_week',
            'month' => 'price_per_month',
            default => 'price_per_day',
        };

        $priceStatsQuery = Vehicle::query()
            ->where('status', 'available')
            ->whereNotNull($priceField);

        $minPriceDb = (float) ($priceStatsQuery->min($priceField) ?? 0);
        $maxPriceDb = (float) ($priceStatsQuery->max($priceField) ?? 500);

        $minPriceDb = floor($minPriceDb);
        $maxPriceDb = ceil($maxPriceDb);

        $requestedMinPrice = (float) $request->get('min_price', $minPriceDb);
        $requestedMaxPrice = (float) $request->get('max_price', $maxPriceDb);

        $selectedMinPrice = max($minPriceDb, min($requestedMinPrice, $requestedMaxPrice));
        $selectedMaxPrice = min($maxPriceDb, max($requestedMinPrice, $requestedMaxPrice));

        $priceBounds = [
            'day' => $this->priceBoundsForField('price_per_day'),
            'week' => $this->priceBoundsForField('price_per_week'),
            'month' => $this->priceBoundsForField('price_per_month'),
        ];

        $query = Vehicle::query()->where('status', 'available');

        // Filtrer par type
        if ($request->filled('type')) {
            $types = is_array($request->type) ? $request->type : [$request->type];
            $query->whereIn('type', $types);
        }

        // Filtrer par transmission
        if ($request->filled('transmission')) {
            $transmissions = is_array($request->transmission) ? $request->transmission : [$request->transmission];
            $query->whereIn('transmission', $transmissions);
        }

        // Filtrer par carburant
        if ($request->filled('fuel_type')) {
            $fuelTypes = is_array($request->fuel_type) ? $request->fuel_type : [$request->fuel_type];
            $query->whereIn('fuel_type', $fuelTypes);
        }

        // Filtrer par caractéristiques (tags)
        $activeFeatures = $request->get('features', []);
        if (! is_array($activeFeatures)) {
            $activeFeatures = [$activeFeatures];
        }
        $activeFeatures = array_filter($activeFeatures);

        foreach ($activeFeatures as $feature) {
            if (in_array($feature, ['bluetooth', 'air_conditioning', 'gps_available', 'child_seat_available', 'cruise_control', 'parking_sensors', 'backup_camera'])) {
                $query->where($feature, true);
            }
        }

        // Filtrer par prix min/max
        $query->whereBetween($priceField, [$selectedMinPrice, $selectedMaxPrice]);

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Trier les résultats
        $sortBy = $request->get('sort', 'price_asc');
        switch ($sortBy) {
            case 'price_desc':
                $priceCol = $priceField;
                $query->orderBy($priceCol, 'desc');
                break;
            case 'name':
                $query->orderBy('brand')->orderBy('model');
                break;
            case 'year':
                $query->orderBy('year', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            default: // price_asc
                $priceCol = $priceField;
                $query->orderBy($priceCol, 'asc');
        }

        $vehicles = $query->paginate(12);
        $vehicles->getCollection()->transform(function (Vehicle $vehicle) {
            $vehicle->setAttribute('resolved_image_url', $this->resolveVehiclePrimaryImage($vehicle));

            return $vehicle;
        });

        $types = Vehicle::types();
        $allFeatures = self::featuresList();

        return view('dashboard.catalogue', compact(
            'vehicles',
            'types',
            'minPriceDb',
            'maxPriceDb',
            'selectedMinPrice',
            'selectedMaxPrice',
            'priceBounds',
            'priceUnit',
            'activeFeatures',
            'allFeatures'
        ));
    }

    /**
     * Liste des caractéristiques filtrables
     */
    public static function featuresList(): array
    {
        return [
            'bluetooth' => ['label' => __('Bluetooth'), 'category' => 'comfort'],
            'air_conditioning' => ['label' => __('Climatisation'), 'category' => 'comfort'],
            'gps_available' => ['label' => __('GPS'), 'category' => 'tech'],
            'child_seat_available' => ['label' => __('Siège enfant'), 'category' => 'safety'],
            'cruise_control' => ['label' => __('Régulateur'), 'category' => 'comfort'],
            'parking_sensors' => ['label' => __('Capteurs parking'), 'category' => 'tech'],
            'backup_camera' => ['label' => __('Caméra de recul'), 'category' => 'tech'],
        ];
    }

    /**
     * Afficher les détails d'un véhicule
     */
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Vérifier si le véhicule est disponible
        if (! $vehicle->isAvailable()) {
            return redirect()->route('dashboard.catalogue')
                ->with('error', __('Ce véhicule n\'est pas disponible actuellement.'));
        }

        $galleryImages = $this->resolveVehicleGallery($vehicle);

        return view('dashboard.catalogue-show', compact('vehicle', 'galleryImages'));
    }

    private function priceBoundsForField(string $field): array
    {
        $query = Vehicle::query()
            ->where('status', 'available')
            ->whereNotNull($field);

        $min = floor((float) ($query->min($field) ?? 0));
        $max = ceil((float) ($query->max($field) ?? 0));

        if ($max < $min) {
            $max = $min;
        }

        return ['min' => $min, 'max' => $max];
    }

    private function imageDirectories(): array
    {
        return array_values(array_filter([
            'images/vehicles',
            'images/vehicule',
            'images/véhicule',
        ], fn (string $dir) => is_dir(public_path($dir))));
    }

    private function allVehicleImageFiles(): Collection
    {
        $files = collect();
        foreach ($this->imageDirectories() as $directory) {
            $glob = glob(public_path($directory.'/*.{png,jpg,jpeg,webp}'), GLOB_BRACE) ?: [];
            foreach ($glob as $absolutePath) {
                $relativePath = str_replace(public_path().DIRECTORY_SEPARATOR, '', $absolutePath);
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $files->push($relativePath);
            }
        }

        return $files->unique()->values();
    }

    private function normalizeName(string $value): string
    {
        return Str::slug(Str::ascii(pathinfo($value, PATHINFO_FILENAME)));
    }

    private function vehicleKey(Vehicle $vehicle): string
    {
        return Str::slug(Str::ascii($vehicle->brand.'-'.$vehicle->model));
    }

    private function resolveVehiclePrimaryImage(Vehicle $vehicle): ?string
    {
        if (! empty($vehicle->image_path)) {
            return Str::startsWith($vehicle->image_path, 'images/')
                ? asset($vehicle->image_path)
                : asset('storage/'.$vehicle->image_path);
        }

        $vehicleKey = $this->vehicleKey($vehicle);
        $allFiles = $this->allVehicleImageFiles();

        $matched = $allFiles->first(function (string $relativePath) use ($vehicleKey) {
            $normalized = $this->normalizeName($relativePath);

            return $normalized === $vehicleKey
                || Str::startsWith($normalized, $vehicleKey.'-')
                || Str::contains($normalized, $vehicleKey);
        });

        return $matched ? asset($matched) : null;
    }

    private function resolveVehicleGallery(Vehicle $vehicle): array
    {
        $vehicleKey = $this->vehicleKey($vehicle);
        $allFiles = $this->allVehicleImageFiles();

        $matchedFiles = $allFiles->filter(function (string $relativePath) use ($vehicleKey) {
            $normalized = $this->normalizeName($relativePath);

            return $normalized === $vehicleKey
                || Str::startsWith($normalized, $vehicleKey.'-')
                || Str::contains($normalized, $vehicleKey);
        })->values();

        $angles = [
            'front' => ['front', 'face', 'avant', 'devant'],
            'right' => ['right', 'droite'],
            'rear' => ['rear', 'back', 'arriere'],
            'left' => ['left', 'gauche'],
        ];

        $gallery = [];

        $primaryImage = $this->resolveVehiclePrimaryImage($vehicle);
        if ($primaryImage) {
            $gallery[] = $primaryImage;
        }

        foreach ($angles as $keywords) {
            $angleMatch = $matchedFiles->first(function (string $relativePath) use ($keywords) {
                $normalized = $this->normalizeName($relativePath);
                foreach ($keywords as $keyword) {
                    if (Str::contains($normalized, $keyword)) {
                        return true;
                    }
                }

                return false;
            });

            if ($angleMatch) {
                $gallery[] = asset($angleMatch);
            }
        }

        foreach ($matchedFiles as $relativePath) {
            $gallery[] = asset($relativePath);
        }

        return array_values(array_unique(array_filter($gallery)));
    }
}
