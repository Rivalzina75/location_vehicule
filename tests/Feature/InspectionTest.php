<?php

namespace Tests\Feature;

use App\Models\Inspection;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InspectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Vehicle $vehicle;

    private Reservation $confirmedReservation;

    private Reservation $activeReservation;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->vehicle = Vehicle::create([
            'brand' => 'Renault',
            'model' => 'Clio',
            'type' => 'citycar',
            'year' => 2024,
            'registration_number' => 'AA-111-BB',
            'transmission' => 'manual',
            'fuel_type' => 'gasoline',
            'seats' => 5,
            'price_per_day' => 40,
            'status' => 'available',
        ]);

        $this->confirmedReservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today(),
            'end_date' => today()->addDays(5),
            'duration_days' => 5,
            'base_price' => 200,
            'total_price' => 200,
            'status' => 'confirmed',
            'payment_status' => 'completed',
            'start_inspection_done' => false,
        ]);

        $this->activeReservation = Reservation::create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'start_date' => today()->subDays(3),
            'end_date' => today()->addDays(2),
            'duration_days' => 5,
            'base_price' => 200,
            'total_price' => 200,
            'status' => 'active',
            'payment_status' => 'completed',
            'start_inspection_done' => true,
            'end_inspection_done' => false,
            'mileage_start' => 10000,
        ]);
    }

    // ───────── Page Access ─────────

    public function test_inspection_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.inspection'));
        $response->assertStatus(200);
    }

    public function test_inspection_page_requires_auth(): void
    {
        $response = $this->get(route('dashboard.inspection'));
        $response->assertRedirect(route('login'));
    }

    public function test_inspection_page_shows_reservations_needing_inspection(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.inspection'));
        $response->assertSee('Renault Clio');
    }

    // ───────── Start Inspection ─────────

    public function test_user_can_submit_start_inspection(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.start', $this->confirmedReservation),
            [
                'mileage' => 15000,
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
                'general_notes' => 'Tout est en ordre',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('inspections', [
            'reservation_id' => $this->confirmedReservation->id,
            'type' => 'start',
            'mileage' => 15000,
            'fuel_level' => 'full',
        ]);
        $this->assertTrue($this->confirmedReservation->fresh()->start_inspection_done);
    }

    public function test_start_inspection_requires_mileage(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.start', $this->confirmedReservation),
            [
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $response->assertSessionHasErrors('mileage');
    }

    public function test_start_inspection_requires_fuel_level(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.start', $this->confirmedReservation),
            [
                'mileage' => 15000,
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $response->assertSessionHasErrors('fuel_level');
    }

    public function test_start_inspection_with_photos(): void
    {
        $photos = [
            UploadedFile::fake()->image('front.jpg'),
            UploadedFile::fake()->image('back.jpg'),
        ];

        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.start', $this->confirmedReservation),
            [
                'mileage' => 15000,
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
                'photos' => $photos,
            ]
        );

        $response->assertRedirect();
        $inspection = Inspection::where('reservation_id', $this->confirmedReservation->id)->first();
        $this->assertNotNull($inspection);
        $this->assertCount(2, $inspection->photos ?? []);
    }

    public function test_other_user_cannot_submit_start_inspection(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->post(
            route('dashboard.inspection.start', $this->confirmedReservation),
            [
                'mileage' => 15000,
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $response->assertStatus(403);
    }

    // ───────── End Inspection ─────────

    public function test_user_can_submit_end_inspection(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.end', $this->activeReservation),
            [
                'mileage' => 10500,
                'fuel_level' => 'three_quarters',
                'cleanliness' => 'acceptable',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
                'general_notes' => 'RAS',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('inspections', [
            'reservation_id' => $this->activeReservation->id,
            'type' => 'end',
            'mileage' => 10500,
        ]);
        $this->assertTrue($this->activeReservation->fresh()->end_inspection_done);
    }

    public function test_end_inspection_mileage_must_be_greater_than_start(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.end', $this->activeReservation),
            [
                'mileage' => 9000, // Less than mileage_start (10000)
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $response->assertSessionHasErrors('mileage');
    }

    public function test_end_inspection_with_damages(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('dashboard.inspection.end', $this->activeReservation),
            [
                'mileage' => 10500,
                'fuel_level' => 'half',
                'cleanliness' => 'dirty',
                'exterior_ok' => false,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
                'damage_notes' => 'Rayure sur le pare-chocs avant',
            ]
        );

        $response->assertRedirect();
        $inspection = Inspection::where('reservation_id', $this->activeReservation->id)->first();
        $this->assertTrue($inspection->hasIssues());
    }

    public function test_other_user_cannot_submit_end_inspection(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->post(
            route('dashboard.inspection.end', $this->activeReservation),
            [
                'mileage' => 10500,
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $response->assertStatus(403);
    }

    // ───────── Activity Logging ─────────

    public function test_start_inspection_creates_activity_log(): void
    {
        $this->actingAs($this->user)->post(
            route('dashboard.inspection.start', $this->confirmedReservation),
            [
                'mileage' => 15000,
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'inspection_start',
        ]);
    }

    public function test_end_inspection_creates_activity_log(): void
    {
        $this->actingAs($this->user)->post(
            route('dashboard.inspection.end', $this->activeReservation),
            [
                'mileage' => 10500,
                'fuel_level' => 'full',
                'cleanliness' => 'clean',
                'exterior_ok' => true,
                'interior_ok' => true,
                'tires_ok' => true,
                'lights_ok' => true,
                'documents_ok' => true,
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'inspection_end',
        ]);
    }
}
