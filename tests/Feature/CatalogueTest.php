<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function createVehicle(array $attrs = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'brand' => 'Peugeot',
            'model' => '208',
            'type' => 'citycar',
            'year' => 2024,
            'registration_number' => 'AA-'.rand(100, 999).'-BB',
            'transmission' => 'manual',
            'fuel_type' => 'gasoline',
            'seats' => 5,
            'doors' => 5,
            'price_per_day' => 35.00,
            'status' => 'available',
        ], $attrs));
    }

    public function test_catalogue_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue'));
        $response->assertStatus(200);
    }

    public function test_catalogue_shows_available_vehicles(): void
    {
        $available = $this->createVehicle(['brand' => 'Toyota', 'model' => 'Yaris']);
        $maintenance = $this->createVehicle(['brand' => 'BMW', 'model' => 'X3', 'status' => 'maintenance']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue'));

        $response->assertSee('Toyota');
        $response->assertSee('Yaris');
        $response->assertDontSee('BMW');
    }

    public function test_catalogue_filters_by_type(): void
    {
        $this->createVehicle(['brand' => 'Peugeot', 'model' => '208', 'type' => 'citycar']);
        $this->createVehicle(['brand' => 'BMW', 'model' => 'X5', 'type' => 'suv']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['type' => 'suv']));
        $response->assertSee('BMW');
        $response->assertDontSee('Peugeot');
    }

    public function test_catalogue_filters_by_transmission(): void
    {
        $this->createVehicle(['brand' => 'Manual', 'model' => 'Car', 'transmission' => 'manual']);
        $this->createVehicle(['brand' => 'Auto', 'model' => 'Car', 'transmission' => 'automatic']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['transmission' => 'automatic']));
        $response->assertSee('Auto');
        $response->assertDontSee('Manual');
    }

    public function test_catalogue_filters_by_fuel_type(): void
    {
        $this->createVehicle(['brand' => 'Gas', 'model' => 'Car', 'fuel_type' => 'gasoline']);
        $this->createVehicle(['brand' => 'Elec', 'model' => 'Car', 'fuel_type' => 'electric']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['fuel_type' => 'electric']));
        $response->assertSee('Elec');
        $response->assertDontSee('Gas');
    }

    public function test_catalogue_filters_by_max_price(): void
    {
        $this->createVehicle(['brand' => 'Cheap', 'model' => 'Car', 'price_per_day' => 25]);
        $this->createVehicle(['brand' => 'Expensive', 'model' => 'Car', 'price_per_day' => 200]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['max_price' => 50]));
        $response->assertSee('Cheap');
        $response->assertDontSee('Expensive');
    }

    public function test_catalogue_filters_by_feature_tags(): void
    {
        $this->createVehicle(['brand' => 'With Bluetooth', 'model' => 'Car', 'bluetooth' => true]);
        $this->createVehicle(['brand' => 'No Bluetooth', 'model' => 'Car', 'bluetooth' => false]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['features' => ['bluetooth']]));
        $response->assertSee('With Bluetooth');
        $response->assertDontSee('No Bluetooth');
    }

    public function test_catalogue_filters_by_max_price_with_week_unit(): void
    {
        $this->createVehicle(['brand' => 'Weekly Cheap', 'model' => 'Car', 'price_per_week' => 200]);
        $this->createVehicle(['brand' => 'Weekly Expensive', 'model' => 'Car', 'price_per_week' => 450]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', [
            'max_price' => 250,
            'price_unit' => 'week',
        ]));

        $response->assertSee('Weekly Cheap');
        $response->assertDontSee('Weekly Expensive');
    }

    public function test_catalogue_sorts_by_week_price_ascending(): void
    {
        $this->createVehicle(['brand' => 'Weekly Expensive', 'price_per_week' => 900]);
        $this->createVehicle(['brand' => 'Weekly Cheap', 'price_per_week' => 250]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', [
            'sort' => 'price_asc',
            'price_unit' => 'week',
        ]));

        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Weekly Cheap') < strpos($content, 'Weekly Expensive'));
    }

    public function test_catalogue_search_by_brand(): void
    {
        $this->createVehicle(['brand' => 'Mercedes', 'model' => 'Classe A']);
        $this->createVehicle(['brand' => 'Renault', 'model' => 'Clio']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['search' => 'Mercedes']));
        $response->assertSee('Mercedes');
        $response->assertDontSee('Renault');
    }

    public function test_catalogue_sorts_by_price_ascending(): void
    {
        $this->createVehicle(['brand' => 'Expensive', 'price_per_day' => 200]);
        $this->createVehicle(['brand' => 'Cheap', 'price_per_day' => 25]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['sort' => 'price_asc']));
        $response->assertStatus(200);
        // Verify Cheap appears before Expensive
        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Cheap') < strpos($content, 'Expensive'));
    }

    public function test_catalogue_sorts_by_price_descending(): void
    {
        $this->createVehicle(['brand' => 'Expensive', 'price_per_day' => 200]);
        $this->createVehicle(['brand' => 'Cheap', 'price_per_day' => 25]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue', ['sort' => 'price_desc']));
        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Expensive') < strpos($content, 'Cheap'));
    }

    public function test_catalogue_show_displays_vehicle_details(): void
    {
        $vehicle = $this->createVehicle(['brand' => 'Tesla', 'model' => 'Model 3']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue.show', $vehicle->id));
        $response->assertStatus(200);
        $response->assertSee('Tesla');
        $response->assertSee('Model 3');
    }

    public function test_catalogue_show_redirects_for_unavailable_vehicle(): void
    {
        $vehicle = $this->createVehicle(['status' => 'maintenance']);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue.show', $vehicle->id));
        $response->assertRedirect(route('dashboard.catalogue'));
    }

    public function test_catalogue_dynamic_max_price(): void
    {
        $this->createVehicle(['price_per_day' => 750]);

        $response = $this->actingAs($this->user)->get(route('dashboard.catalogue'));
        $response->assertSee('750'); // The max price slider should show 750
    }

    public function test_catalogue_requires_authentication(): void
    {
        $response = $this->get(route('dashboard.catalogue'));
        $response->assertRedirect('/login');
    }
}
